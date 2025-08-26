<?php namespace App\Models;
//Modelo para admin keys
use CodeIgniter\Model;

class AdminKeyModel extends Model
{
    protected $table = 'admin_keys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['admin_key_hash', 'description', 'created_at'];
    protected $returnType = 'array';

    public function getCurrentKey()//→ Obtiene la última clave guardada (por si se cambia con el tiempo).
    {
        return $this->orderBy('id', 'DESC')->first();
    }
}