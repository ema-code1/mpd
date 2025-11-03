<?php

namespace App\Models;

use CodeIgniter\Model;

class MovimientosModel extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'venta_id';
    protected $allowedFields = ['comprador_id', 'nombre_comprador', 'libro_id', 'cantidad', 'monto_venta', 'fecha_de_pago'];
    protected $useTimestamps = false;

    public function getMovimientos()
    {
        return $this->select('ventas.*, libros.titulo as libro_titulo')
                    ->join('libros', 'libros.id = ventas.libro_id', 'left')
                    ->orderBy('ventas.fecha_de_pago', 'DESC')
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
}