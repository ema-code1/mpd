<?php

namespace App\Models;

use CodeIgniter\Model;

class VentaDetalleModel extends Model
{
    protected $table = 'ventas_detalle';
    protected $primaryKey = 'detalle_id';
    protected $allowedFields = [
        'venta_id',
        'libro_id',
        'cantidad',
        'precio_unitario',
        'subtotal'
    ];
    protected $returnType = 'array';
}