<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosModel extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'venta_id';
    protected $allowedFields = ['comprador_id', 'libro_id', 'cantidad', 'monto_venta', 'fecha_de_pago'];
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';

    public function getMovimientos(){
    return $this->select('ventas.*, users.name as comprador_nombre, libros.titulo as libro_titulo, ventas.met_pago')
                ->join('users', 'users.id = ventas.comprador_id', 'left')
                ->join('libros', 'libros.id = ventas.libro_id', 'left')
                ->orderBy('ventas.fecha_de_pago', 'DESC')
                ->orderBy('ventas.venta_id', 'DESC')
                ->findAll();
}

    public function getTotalVentas()
    {
        $result = $this->selectSum('monto_venta')->get()->getRow();
        return $result->monto_venta ?? 0;
    }

    public function getVentasPorMes()
    {
        return $this->select("MONTH(fecha_de_pago) as mes, YEAR(fecha_de_pago) as año, SUM(monto_venta) as total")
                    ->groupBy("YEAR(fecha_de_pago), MONTH(fecha_de_pago)")
                    ->orderBy('año DESC, mes DESC')
                    ->findAll();
    }

    public function getVentasPorUsuario()
    {
        return $this->select('users.name as comprador, COUNT(ventas.venta_id) as total_ventas, SUM(ventas.monto_venta) as monto_total')
                    ->join('users', 'users.id = ventas.comprador_id')
                    ->groupBy('ventas.comprador_id')
                    ->orderBy('monto_total', 'DESC')
                    ->findAll();
    }
}