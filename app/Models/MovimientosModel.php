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
}

