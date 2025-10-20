<?php
namespace App\Models;

use CodeIgniter\Model;

class CartModel extends Model
{
    protected $table = 'carrito';
    // protected $primaryKey = 'id'; // <-- eliminado porque ahora la tabla usa PK compuesta (user_id, libro_id)
    protected $allowedFields = ['user_id', 'libro_id', 'cantidad', 'seleccionado'];
    protected $returnType = 'array';
}