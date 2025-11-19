<?php

namespace App\Models;

use CodeIgniter\Model;

class PasswordResetModel extends Model
{
    protected $table = 'password_reset_tokens';
    protected $primaryKey = 'id';
    protected $allowedFields = ['email', 'token', 'created_at', 'expires_at', 'used'];
    protected $useTimestamps = false;
    protected $returnType = 'array';

    /**
     * Crear un nuevo token de recuperación
     */
    public function createToken($email)
    {
        // Generar token único y seguro
        $token = bin2hex(random_bytes(32));
        
        // Token válido por 1 hora
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Invalidar tokens anteriores del mismo email
        $this->where('email', $email)
             ->where('used', 0)
             ->set(['used' => 1])
             ->update();
        
        // Crear nuevo token
        $data = [
            'email' => $email,
            'token' => $token,
            'created_at' => date('Y-m-d H:i:s'),
            'expires_at' => $expiresAt,
            'used' => 0
        ];
        
        $this->insert($data);
        
        return $token;
    }

    /**
     * Validar token
     */
    public function validateToken($token)
    {
        $now = date('Y-m-d H:i:s');
        
        return $this->where('token', $token)
                    ->where('used', 0)
                    ->where('expires_at >', $now)
                    ->first();
    }

    /**
     * Marcar token como usado
     */
    public function markAsUsed($token)
    {
        return $this->where('token', $token)
                    ->set(['used' => 1])
                    ->update();
    }

    /**
     * Limpiar tokens expirados (opcional - para mantenimiento)
     */
    public function cleanExpiredTokens()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('expires_at <', $now)->delete();
    }
}