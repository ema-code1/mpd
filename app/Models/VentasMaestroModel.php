<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasMaestroModel extends Model
{
    protected $table = 'ventas_maestro';
    protected $primaryKey = 'venta_id';
    protected $allowedFields = [
        'comprador_id','fecha_de_pago','total_venta','met_pago','estado'
    ];

    public function getMovimientos()
    {
        return $this->select("
                ventas_maestro.venta_id,
                ventas_maestro.comprador_id,
                ventas_maestro.fecha_de_pago,
                ventas_maestro.total_venta,
                ventas_maestro.met_pago,
                ventas_maestro.estado,
                users.name AS comprador,
                GROUP_CONCAT(CONCAT(libros.titulo, ' (x', ventas_detalle.cantidad, ')') SEPARATOR '\n') AS libros_list
            ")
            ->join('users', 'users.id = ventas_maestro.comprador_id')
            ->join('ventas_detalle', 'ventas_detalle.venta_id = ventas_maestro.venta_id')
            ->join('libros', 'libros.id = ventas_detalle.libro_id')
            ->groupBy('ventas_maestro.venta_id')
            ->orderBy('ventas_maestro.venta_id', 'DESC')
            ->findAll();
    }
}
