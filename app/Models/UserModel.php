<?php namespace App\Models;
//Modelo de usuarios

use CodeIgniter\Model;

class UserModel extends Model
{
    protected $table = 'users';
    protected $primaryKey = 'id';
    protected $allowedFields = ['name','email','password','role','created_at'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    public function findByEmail($email) //findByEmail($email) → Busca un usuario por su correo.
    {
        return $this->where('email', $email)->first();
    }
}
