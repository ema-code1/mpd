<?php

namespace App\Controllers;

use App\Models\VentasMaestroModel;

class ApiCheckoutController extends BaseController
{
    protected $ventasModel;

    public function __construct()
    {
        $this->ventasModel = new VentasMaestroModel();
    }

    /**
     * Crear venta desde AJAX del carrito
     * POST /api/checkout/crear
     */
    public function crear()
    {
        // Validar que sea POST
        if ($this->request->getMethod() !== 'post') {
            return $this->response
                ->setJSON(['success' => false, 'error' => 'Método no permitido'])
                ->setStatusCode(405);
        }

        try {
            // Obtener usuario de sesión
            $userId = session()->get('id');
            if (!$userId) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Usuario no autenticado'
                ])->setStatusCode(401);
            }

            // Obtener datos del request
            $items = json_decode($this->request->getPost('items'), true);
            $paymentMethod = $this->request->getPost('payment_method');
            $total = floatval($this->request->getPost('total'));
            $comprobante = $this->request->getFile('comprobante');

            // Validar datos básicos
            if (empty($items) || !in_array($paymentMethod, ['efectivo', 'transferencia'])) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Datos inválidos'
                ])->setStatusCode(400);
            }

            if ($paymentMethod === 'transferencia' && !$comprobante) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'Comprobante requerido para transferencia'
                ])->setStatusCode(400);
            }

            // ===== CREAR VENTA MAESTRO =====
            $ventaId = $this->ventasModel->crearVenta(
                $userId,
                $total,
                $paymentMethod,
                date('Y-m-d')
            );

            if (!$ventaId) {
                return $this->response->setJSON([
                    'success' => false,
                    'error' => 'No se pudo crear la venta'
                ])->setStatusCode(500);
            }

            // ===== AGREGAR DETALLES DE VENTA =====
            foreach ($items as $item) {
                $this->ventasModel->agregarDetalleVenta(
                    $ventaId,
                    intval($item['libro_id']),
                    intval($item['cantidad']),
                    floatval($item['precio'])
                );
            }

            // ===== PROCESAR COMPROBANTE (SI EXISTE) =====
            if ($comprobante && $comprobante->isValid() && !$comprobante->hasMoved()) {
                try {
                    $uploadPath = WRITEPATH . 'uploads/comprobantes/';
                    
                    // Crear directorio si no existe
                    if (!is_dir($uploadPath)) {
                        mkdir($uploadPath, 0755, true);
                    }

                    // Validar tamaño (máximo 5MB)
                    if ($comprobante->getSize() > 5242880) {
                        return $this->response->setJSON([
                            'success' => false,
                            'error' => 'Archivo muy grande (máximo 5MB)'
                        ])->setStatusCode(400);
                    }

                    // Validar tipo
                    $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf'];
                    if (!in_array($comprobante->getMimeType(), $allowedMimes)) {
                        return $this->response->setJSON([
                            'success' => false,
                            'error' => 'Tipo de archivo no permitido'
                        ])->setStatusCode(400);
                    }

                    // Generar nombre único
                    $newName = 'comprobante_venta_' . $ventaId . '_' . time() . '.' . $comprobante->getExtension();
                    $comprobante->move($uploadPath, $newName);

                    // Guardar ruta en BD (relativa)
                    $rutaRelativa = 'uploads/comprobantes/' . $newName;
                    $this->ventasModel->subirComprobante($ventaId, $rutaRelativa);

                } catch (\Exception $e) {
                    log_message('error', 'Error al procesar comprobante: ' . $e->getMessage());
                    // No interrumpir flujo, la venta se creó igual
                }
            }

            // ===== LIMPIAR CARRITO DEL USUARIO =====
            try {
                $db = \Config\Database::connect();
                $db->table('carrito')->where('user_id', $userId)->delete();
            } catch (\Exception $e) {
                log_message('error', 'Error al limpiar carrito: ' . $e->getMessage());
            }

            // ===== RESPUESTA EXITOSA =====
            return $this->response->setJSON([
                'success' => true,
                'venta_id' => $ventaId,
                'mensaje' => 'Venta creada exitosamente'
            ])->setStatusCode(201);

        } catch (\Exception $e) {
            log_message('error', 'Error en ApiCheckoutController@crear: ' . $e->getMessage());
            
            return $this->response->setJSON([
                'success' => false,
                'error' => 'Error interno del servidor'
            ])->setStatusCode(500);
        }
    }

    /**
     * Obtener detalles de venta (opcional para AJAX)
     * GET /api/checkout/detalle/:id
     */
    public function detalle($id = null)
    {
        if (!$id) {
            return $this->response
                ->setJSON(['success' => false, 'error' => 'ID requerido'])
                ->setStatusCode(400);
        }

        try {
            $ventaCompleta = $this->ventasModel->obtenerVentaCompleta($id);

            if (!$ventaCompleta) {
                return $this->response
                    ->setJSON(['success' => false, 'error' => 'Venta no encontrada'])
                    ->setStatusCode(404);
            }

            // Verificar que sea el usuario autenticado o admin
            $userId = session()->get('id');
            if ($ventaCompleta['comprador_id'] != $userId) {
                return $this->response
                    ->setJSON(['success' => false, 'error' => 'No tienes permisos'])
                    ->setStatusCode(403);
            }

            return $this->response->setJSON([
                'success' => true,
                'data' => $ventaCompleta
            ]);

        } catch (\Exception $e) {
            log_message('error', 'Error en ApiCheckoutController@detalle: ' . $e->getMessage());
            
            return $this->response
                ->setJSON(['success' => false, 'error' => 'Error interno'])
                ->setStatusCode(500);
        }
    }
}