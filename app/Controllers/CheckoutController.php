<?php

namespace App\Controllers;

use App\Models\VentasMaestroModel;

class CheckoutController extends BaseController
{
    protected $ventasModel;

    public function __construct()
    {
        $this->ventasModel = new VentasMaestroModel();
    }

    /**
     * Obtener detalles completos de una venta
     */
    private function obtenerDetalleVenta($idVenta)
    {
        try {
            // Obtener venta completa (maestro + comprador)
            $ventaCompleta = $this->ventasModel->obtenerVentaCompleta($idVenta);

            if (!$ventaCompleta) {
                return ['error' => true, 'mensaje' => 'Venta no encontrada'];
            }

            // Obtener detalles
            $detalleVenta = $this->ventasModel->obtenerDetalleVentaPorId($idVenta);

            // Datos del administrador
            $administrador = [
                'nombre' => 'Hernán Darío Mangold',
                'dni' => '23.268.265',
                'fechaNacimiento' => '18/2/1975',
                'foto' => base_url('public/img/admin-profile.jpg')
            ];

            // Calcular estado del pedido basado en enum
            $estadoPedido = $this->calcularEstadoPedido($ventaCompleta['estado']);

            return [
                'error' => false,
                'ventaMaestro' => $ventaCompleta,
                'comprador' => $ventaCompleta['comprador'],
                'detalleVenta' => $detalleVenta,
                'administrador' => $administrador,
                'estadoPedido' => $estadoPedido
            ];

        } catch (\Exception $e) {
            log_message('error', 'Error en obtenerDetalleVenta: ' . $e->getMessage());
            return ['error' => true, 'mensaje' => $e->getMessage()];
        }
    }

    /**
     * Calcular estado del pedido basado en el enum de BD
     * enum('pendiente', 'revisado', 'cancelado')
     */
    private function calcularEstadoPedido($estado)
    {
        $estadoMap = [
            'pendiente' => [
                'paso1' => ['completado' => true, 'titulo' => 'Pedido Realizado', 'status' => 'Completado'],
                'paso2' => ['completado' => false, 'titulo' => 'En Revisión', 'status' => 'Pendiente'],
                'paso3' => ['completado' => false, 'titulo' => 'Envío', 'status' => 'Pendiente']
            ],
            'revisado' => [
                'paso1' => ['completado' => true, 'titulo' => 'Pedido Realizado', 'status' => 'Completado'],
                'paso2' => ['completado' => true, 'titulo' => 'En Revisión', 'status' => 'Completado'],
                'paso3' => ['completado' => false, 'titulo' => 'Envío', 'status' => 'En Progreso']
            ],
            'cancelado' => [
                'paso1' => ['completado' => true, 'titulo' => 'Pedido Realizado', 'status' => 'Completado'],
                'paso2' => ['completado' => false, 'titulo' => 'En Revisión', 'status' => 'Cancelado'],
                'paso3' => ['completado' => false, 'titulo' => 'Envío', 'status' => 'Cancelado']
            ]
        ];

        return $estadoMap[$estado] ?? $estadoMap['pendiente'];
    }

    /**
     * Cargar la vista de checkout (GET)
     */
    public function index()
    {
        // Obtener el ID de la venta
        $idVenta = $this->request->getGet('id') ?? session()->get('idVenta') ?? null;

        if (!$idVenta) {
            return $this->mostrarCheckoutVacio();
        }

        $datos = $this->obtenerDetalleVenta($idVenta);

        if ($datos['error']) {
            return $this->mostrarCheckoutVacio();
        }

        // Pasar datos a la vista
        return view('checkout', $datos);
    }

    /**
     * Procesar el formulario POST del checkout
     */
    public function procesar()
    {
        if ($this->request->getMethod() !== 'post') {
            return redirect()->to('cart');
        }

        try {
            $idVenta = $this->request->getPost('venta_id');

            if (!$idVenta) {
                session()->setFlashdata('error', 'No se encontró la venta');
                return redirect()->to('cart');
            }

            // Verificar que la venta exista
            $venta = $this->ventasModel->obtenerVentaPorId($idVenta);
            if (!$venta) {
                session()->setFlashdata('error', 'Venta no encontrada');
                return redirect()->to('cart');
            }

            // Redirigir al checkout con el ID
            return redirect()->to('checkout?id=' . $idVenta);

        } catch (\Exception $e) {
            log_message('error', 'Error en CheckoutController@procesar: ' . $e->getMessage());
            session()->setFlashdata('error', 'Error al procesar checkout');
            return redirect()->to('cart');
        }
    }

    /**
     * Mostrar página de checkout vacío
     */
    private function mostrarCheckoutVacio()
    {
        return view('checkout_vacio');
    }

    /**
     * API: Obtener detalles de venta en JSON (opcional para AJAX)
     */
    public function detalleJson($id = null)
    {
        if (!$id) {
            return $this->response->setJSON(['error' => true, 'mensaje' => 'ID requerido']);
        }

        $datos = $this->obtenerDetalleVenta($id);
        return $this->response->setJSON($datos);
    }
}