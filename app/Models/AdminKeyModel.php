<?php namespace App\Models;
//Modelo para vendor keys

use CodeIgniter\Model;

class AdminKeyModel extends Model
{
    protected $table = 'admin_keys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_key_hash', 'description', 'created_at'];
    protected $returnType = 'array';

    public function getCurrentKey()
    {
        return $this->orderBy('id', 'DESC')->first();
    }
}