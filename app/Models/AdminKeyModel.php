<?php namespace App\Models;
//Modelo para admin keys
use CodeIgniter\Model;

class AdminKeyModel extends Model
{
    protected $table = 'admin_keys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['admin_key_hash', 'description', 'created_at']; // ← sigue siendo válido si el campo se llama así
    protected $returnType = 'array';

    public function getCurrentKey()
    {
        return $this->orderBy('id', 'DESC')->first();
    }
}