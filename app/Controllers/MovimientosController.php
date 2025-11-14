<?php

namespace App\Controllers;

use App\Models\VentasMaestroModel;
use App\Models\VentaDetalleModel;

class MovimientosController extends BaseController
{
    protected $ventasMaestroModel;
    protected $ventaDetalleModel;

    public function __construct()
    {
        $this->ventasMaestroModel = new VentasMaestroModel();
        $this->ventaDetalleModel = new VentaDetalleModel();
        helper('auth');
    }

    public function index()
    {
        // Siempre devolver array, nunca null
        $movimientos = $this->ventasMaestroModel->getMovimientos() ?? [];

        $data = [
            'titulo' => 'Movimientos de Ventas',
            'movimientos' => $movimientos
        ];

        echo view('templates/header');
        return view('dashboards/movimientos', $data);
    }

    // ---------------------------------------------------------------------
    // AJAX: tooltip detalles
    // ---------------------------------------------------------------------
    public function detalles($ventaId)
    {
        $detalles = $this->ventaDetalleModel
            ->select('libros.titulo, ventas_detalle.cantidad, ventas_detalle.precio_unitario, ventas_detalle.subtotal')
            ->join('libros', 'libros.id = ventas_detalle.libro_id')
            ->where('venta_id', $ventaId)
            ->findAll();

        return $this->response->setJSON($detalles);
    }

    // ---------------------------------------------------------------------
    // Set payment method (carrito)
    // ---------------------------------------------------------------------
    public function set_payment_method()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $paymentMethod = $this->request->getPost('payment_method');

        if ($paymentMethod && in_array($paymentMethod, ['efectivo', 'transferencia'])) {
            session()->set('selected_payment_method', $paymentMethod);
            return $this->response->setJSON(['success' => true]);
        }

        return $this->response->setJSON(['error' => 'Método de pago inválido']);
    }
}