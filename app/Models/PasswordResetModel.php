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
     * Token válido por 12 horas
     */
    public function createToken($email)
    {
        // Generar token único y seguro (64 caracteres)
        $token = bin2hex(random_bytes(32));
        
        // Token válido por 12 horas
        $expiresAt = date('Y-m-d H:i:s', strtotime('+12 hours'));
        
        // Invalidar tokens anteriores del mismo email que no se hayan usado
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
     * Retorna los datos del token si es válido, null si no lo es
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
     * Limpiar tokens expirados (mantenimiento)
     * Opcional: puedes programar esto con un cron job
     */
    public function cleanExpiredTokens()
    {
        $now = date('Y-m-d H:i:s');
        return $this->where('expires_at <', $now)->delete();
    }
}