<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\LibroModel;
use App\Models\VentasMaestroModel;
use App\Models\VentaDetalleModel;
use CodeIgniter\Controller;

class CheckoutController extends BaseController
{
    protected $cartModel;
    protected $libroModel;
    protected $ventasMaestroModel;
    protected $ventaDetalleModel;

    public function __construct()
    {
        $this->cartModel         = new CartModel();
        $this->libroModel        = new LibroModel();
        $this->ventasMaestroModel = new VentasMaestroModel();
        $this->ventaDetalleModel = new VentaDetalleModel();

        helper(['url', 'form']);
    }

    /**
     * POST /cart/checkout
     * Procesa la compra:
     * - Lee carrito seleccionado del user
     * - Calcula total
     * - Guarda maestro + detalle
     * - Limpia carrito
     * - Devuelve JSON con redirect
     */
    public function procesar()
    {
        $session = session();

        if (! $session->get('isLoggedIn') || $session->get('role') !== 'comprador') {
            return $this->response->setStatusCode(401)
                ->setJSON(['success' => false, 'error' => 'No autorizado']);
        }

        // Ajustá este nombre si tu sesión guarda el ID con otra key
        $userId = $session->get('userId');

        if (! $userId) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Usuario no identificado'
            ]);
        }

        $paymentMethod = $this->request->getPost('payment_method');
        $totalForm     = (float) $this->request->getPost('total');

        if (! in_array($paymentMethod, ['efectivo', 'transferencia'])) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'Método de pago inválido'
            ]);
        }

        // 1) Traer ítems del carrito seleccionados
        $cartItems = $this->cartModel
            ->where('user_id', $userId)
            ->where('seleccionado', 1)
            ->findAll();

        if (empty($cartItems)) {
            return $this->response->setJSON([
                'success' => false,
                'error'   => 'No hay ítems seleccionados para comprar'
            ]);
        }

        // 2) Traer precios reales desde libros
        $libroIds = array_column($cartItems, 'libro_id');

        $libros = $this->libroModel
            ->whereIn('id', $libroIds)
            ->findAll();

        $preciosPorLibro = [];
        foreach ($libros as $libro) {
            $preciosPorLibro[$libro['id']] = (float) $libro['precio'];
        }

        // 3) Armar detalles y recalcular total
        $totalCalculado   = 0;
        $detallesParaInsert = [];

        foreach ($cartItems as $item) {
            $libroId = $item['libro_id'];
            $cantidad = (int) $item['cantidad'];

            $precioUnitario = $preciosPorLibro[$libroId] ?? 0;
            $subtotal       = $precioUnitario * $cantidad;
            $totalCalculado += $subtotal;

            $detallesParaInsert[] = [
                'libro_id'       => $libroId,
                'cantidad'       => $cantidad,
                'precio_unitario'=> $precioUnitario,
                'subtotal'       => $subtotal,
            ];
        }

        // Si querés, acá podrías validar contra $totalForm.
        // Pero mandamos siempre el calculado de la DB.
        $totalVenta = $totalCalculado;

        // 4) Manejo de comprobante (si transferencia)
        $rutaComprobante = null;

        if ($paymentMethod === 'transferencia') {
            $file = $this->request->getFile('comprobante');

            if (! $file || ! $file->isValid()) {
                return $this->response->setJSON([
                    'success' => false,
                    'error'   => 'Debés adjuntar el comprobante de transferencia'
                ]);
            }

            $uploadPath = FCPATH . 'uploads/comprobantes';

            if (! is_dir($uploadPath)) {
                mkdir($uploadPath, 0775, true);
            }

            $newName = $file->getRandomName();
            if (! $file->move($uploadPath, $newName)) {
                return $this->response->setJSON([
                    'success' => false,
                    'error'   => 'No se pudo guardar el comprobante'
                ]);
            }

            $rutaComprobante = 'uploads/comprobantes/' . $newName;
        }

        $db = \Config\Database::connect();
        $db->transBegin();

        try {
            // 5) Insertar maestro
            $ventaData = [
                'comprador_id' => $userId,
                'fecha_de_pago'=> date('Y-m-d'),
                'total_venta'  => $totalVenta,
                'met_pago'     => $paymentMethod,
                'estado'       => 'pendiente',
                'comprobante'  => $rutaComprobante,
            ];

            $ventaId = $this->ventasMaestroModel->insert($ventaData, true);

            if (! $ventaId) {
                throw new \Exception('No se pudo crear la venta (maestro)');
            }

            // 6) Insertar detalle
            foreach ($detallesParaInsert as $detalle) {
                $detalle['venta_id'] = $ventaId;
                if (! $this->ventaDetalleModel->insert($detalle)) {
                    throw new \Exception('No se pudo crear el detalle de venta');
                }
            }

            // 7) Limpiar carrito del usuario (solo seleccionados)
            $this->cartModel
                ->where('user_id', $userId)
                ->where('seleccionado', 1)
                ->delete();

            $db->transCommit();

            return $this->response->setJSON([
                'success'      => true,
                'venta_id'     => $ventaId,
                'redirect_url' => base_url('checkout?venta=' . $ventaId),
            ]);

        } catch (\Throwable $e) {
            $db->transRollback();

            return $this->response->setJSON([
                'success' => false,
                'error'   => 'No se pudo crear la venta: ' . $e->getMessage(),
            ]);
        }
    }

    /**
     * GET /checkout?venta=ID
     * Muestra la vista de resumen de pedido
     */
    public function index()
    {
        $ventaId = $this->request->getGet('venta');

        if (! $ventaId) {
            return view('checkout_vacio');
        }

        $ventaMaestro = $this->ventasMaestroModel->find($ventaId);

        if (! $ventaMaestro) {
            return view('checkout_vacio');
        }

        // Detalle + info de libros
        $detalleVenta = $this->ventaDetalleModel
            ->select('ventas_detalle.*, libros.titulo, libros.foto1')
            ->join('libros', 'libros.id = ventas_detalle.libro_id')
            ->where('venta_id', $ventaId)
            ->findAll();

        $estadoPedido = $this->buildEstadoPedido($ventaMaestro['estado'] ?? 'pendiente');

        echo view('templates/header');
        return view('checkout', [
            'ventaMaestro' => $ventaMaestro,
            'detalleVenta' => $detalleVenta,
            'estadoPedido' => $estadoPedido,
        ]);
    }

    /**
     * Armar estructura para el stepper del checkout
     */
    /**
 * Armar estructura para el stepper del checkout
 * Estados: pendiente / revisado / cancelado / entregado
 */
private function buildEstadoPedido(string $estado)
{
    $estado = strtolower($estado);

    // Paso 1 siempre completado (si existe la venta)
    $paso1Completado = true;

    // Paso 2 completado si ya salió del estado "pendiente"
    $paso2Completado = in_array($estado, ['revisado', 'cancelado', 'entregado'], true);

    // Paso 3: "Preparando pedido" solo tiene sentido si no se canceló
    $paso3Completado = ($estado === 'entregado');

    // Paso 4: Entregado
    $paso4Completado = ($estado === 'entregado');

    return [
        'paso1' => [
            'titulo'     => 'Pedido realizado',
            'status'     => 'Completado',
            'completado' => $paso1Completado,
        ],
        'paso2' => [
            'titulo'     => 'En revisión',
            'status'     => $estado === 'pendiente'
                ? 'Pendiente'
                : ($estado === 'cancelado' ? 'Cancelado' : 'Completado'),
            'completado' => $paso2Completado,
        ],
        'paso3' => [
            'titulo'     => 'Preparando pedido',
            'status'     => $estado === 'entregado'
                ? 'Completado'
                : ($estado === 'cancelado' ? 'No aplica' : 'Pendiente'),
            'completado' => $paso3Completado,
        ],
        'paso4' => [
            'titulo'     => 'Entregado',
            'status'     => $estado === 'entregado'
                ? 'Entregado'
                : ($estado === 'cancelado' ? 'Cancelado' : 'Pendiente'),
            'completado' => $paso4Completado,
        ],
    ];
}

}
