<?php

namespace App\Models;

use CodeIgniter\Model;

class VentasMaestroModel extends Model
{
    protected $table = 'ventas_maestro';
    protected $primaryKey = 'venta_id';
    protected $allowedFields = [
        'comprador_id',
        'fecha_de_pago',
        'total_venta',
        'met_pago',
        'estado',
        'comprobante'
    ];

    /**
     * Obtener todos los movimientos de ventas
     */
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

    /**
     * Obtener una venta por ID con todos sus detalles
     */
    public function obtenerVentaPorId($ventaId)
    {
        return $this->where('venta_id', $ventaId)->first();
    }

    /**
     * Obtener datos del comprador
     */
    public function obtenerCompradorPorId($userId)
    {
        $db = \Config\Database::connect();
        $result = $db->table('users')
            ->select('id, name, email')
            ->where('id', $userId)
            ->get()
            ->getResultArray();

        return !empty($result) ? $result[0] : false;
    }

    /**
     * Obtener detalles de una venta con información de libros
     */
    public function obtenerDetalleVentaPorId($ventaId)
    {
        $db = \Config\Database::connect();
        return $db->table('ventas_detalle vd')
            ->select('
                vd.detalle_id,
                vd.libro_id,
                vd.cantidad,
                vd.precio_unitario,
                vd.subtotal,
                l.titulo,
                l.autor,
                l.foto1
            ')
            ->join('libros l', 'vd.libro_id = l.id', 'inner')
            ->where('vd.venta_id', $ventaId)
            ->orderBy('vd.detalle_id', 'ASC')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener todas las ventas de un comprador
     */
    public function obtenerVentasPorComprador($compradorId)
    {
        return $this->where('comprador_id', $compradorId)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Crear nueva venta
     */
    public function crearVenta($compradorId, $totalVenta, $metPago, $fechaPago = null)
    {
        if (!$fechaPago) {
            $fechaPago = date('Y-m-d');
        }

        $data = [
            'comprador_id' => $compradorId,
            'total_venta' => $totalVenta,
            'met_pago' => $metPago,
            'fecha_de_pago' => $fechaPago,
            'estado' => 'pendiente'
        ];

        if ($this->insert($data)) {
            return $this->insertID();
        }

        return false;
    }

    /**
     * Agregar detalle a una venta
     */
    public function agregarDetalleVenta($ventaId, $libroId, $cantidad, $precioUnitario)
    {
        $subtotal = $cantidad * $precioUnitario;

        $db = \Config\Database::connect();
        $data = [
            'venta_id' => $ventaId,
            'libro_id' => $libroId,
            'cantidad' => $cantidad,
            'precio_unitario' => $precioUnitario,
            'subtotal' => $subtotal
        ];

        return $db->table('ventas_detalle')->insert($data);
    }

    /**
     * Actualizar estado de venta
     */
    public function actualizarEstadoVenta($ventaId, $estado)
    {
        $estadosValidos = ['pendiente', 'revisado', 'cancelado'];

        if (!in_array($estado, $estadosValidos)) {
            return false;
        }

        return $this->update($ventaId, ['estado' => $estado]);
    }

    /**
     * Subir/actualizar comprobante
     */
    public function subirComprobante($ventaId, $rutaComprobante)
    {
        return $this->update($ventaId, ['comprobante' => $rutaComprobante]);
    }

    /**
     * Eliminar venta (también elimina detalles por cascada en BD)
     */
    public function eliminarVenta($ventaId)
    {
        return $this->delete($ventaId);
    }

    /**
     * Obtener ventas por estado
     */
    public function obtenerVentasPorEstado($estado)
    {
        $estadosValidos = ['pendiente', 'revisado', 'cancelado'];

        if (!in_array($estado, $estadosValidos)) {
            return [];
        }

        return $this->where('estado', $estado)
            ->orderBy('created_at', 'DESC')
            ->findAll();
    }

    /**
     * Obtener resumen de ventas
     */
    public function obtenerResumenVentas()
    {
        $db = \Config\Database::connect();
        return $db->table('ventas_maestro')
            ->select('
                estado,
                COUNT(*) as cantidad,
                SUM(total_venta) as total
            ')
            ->groupBy('estado')
            ->get()
            ->getResultArray();
    }

    /**
     * Obtener venta con detalles completos
     */
    public function obtenerVentaCompleta($ventaId)
    {
        $venta = $this->obtenerVentaPorId($ventaId);

        if (!$venta) {
            return false;
        }

        $venta['comprador'] = $this->obtenerCompradorPorId($venta['comprador_id']);
        $venta['detalles'] = $this->obtenerDetalleVentaPorId($ventaId);

        return $venta;
    }
}