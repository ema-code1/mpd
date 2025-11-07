<?php

namespace App\Models;

use CodeIgniter\Model;

class CheckoutModel extends Model
{
    protected $table = 'ventas';
    protected $primaryKey = 'venta_id';
    protected $allowedFields = [
        'comprador_id', 
        'libro_id', 
        'cantidad', 
        'monto_venta', 
        'fecha_de_pago'
    ];
    protected $returnType = 'array';
    
    /**
     * Registrar una nueva venta
     */
    public function registrarVenta($data)
    {
        return $this->insert($data);
    }
    
    /**
     * Obtener ventas por usuario
     */
    public function obtenerVentasPorUsuario($userId)
    {
        return $this->where('comprador_id', $userId)->findAll();
    }
    
    /**
     * Obtener todas las ventas
     */
    public function obtenerTodasLasVentas()
    {
        return $this->findAll();
    }
}