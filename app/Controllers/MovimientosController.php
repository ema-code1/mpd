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
}