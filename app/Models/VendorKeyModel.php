<?php namespace App\Models;
//Modelo para vendor keys

use CodeIgniter\Model;

class VendorKeyModel extends Model
{
    protected $table = 'vendor_keys';
    protected $primaryKey = 'id';
    protected $allowedFields = ['vendor_key_hash','description','created_at'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    // Obtener el hash actual (suponiendo una sola clave en uso)
    public function getCurrentKey()
    {
        return $this->orderBy('id','DESC')->first();
    }
}
