<?php
namespace App\Models;

use CodeIgniter\Model;

class LibroModel extends Model
{
    protected $table = 'libros';
    protected $primaryKey = 'id';
    protected $allowedFields = [
        'titulo', 
        'descripcion', 
        'autor', 
        'edicion', 
        'precio', 
        'categoria', 
        'foto1', 
        'foto2',
        'stock'
    ];
    
    protected $useTimestamps = true;
    protected $createdField = 'created_at';
    protected $updatedField = 'updated_at';
}