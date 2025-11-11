<?php

namespace App\Controllers;

use App\Models\MovimientosModel;

class MovimientosController extends BaseController
{
    protected $movimientosModel;

    public function __construct()
    {
        $this->movimientosModel = new MovimientosModel();
        helper('auth');
    }

    public function index()
    {
        $data = [
            'titulo' => 'Movimientos de Ventas',
            'movimientos' => $this->movimientosModel->getMovimientos(),
            'totalVentas' => $this->movimientosModel->getTotalVentas(),
            'ventasPorMes' => $this->movimientosModel->getVentasPorMes()
        ];

        echo view('templates/header');
        return view('dashboards/movimientos', $data);
    }

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