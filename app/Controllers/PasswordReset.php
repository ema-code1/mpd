<?php

namespace App\Controllers;

use App\Models\UserModel;
use App\Models\PasswordResetModel;
use CodeIgniter\Controller;

class PasswordReset extends Controller
{
    protected $helpers = ['url', 'form'];

    /**
     * Vista: Solicitar recuperación de contraseña
     */
    public function index()
    {
        echo view('templates/header', ['title' => 'Recuperar Contraseña']);
        echo view('password_reset/request');
        echo view('templates/footer');
    }

    /**
     * Procesar solicitud de recuperación
     */
    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Por favor ingresa un email válido'
            ]);
        }

        // Verificar que el email exista
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No existe una cuenta con ese email'
            ]);
        }

        // Crear token de recuperación
        $resetModel = new PasswordResetModel();
        $token = $resetModel->createToken($email);

        // Enviar email
        if ($this->sendResetEmail($email, $token, $user['name'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al enviar el email. Intenta nuevamente.'
            ]);
        }
    }

    /**
     * Enviar email de recuperación
     */
    private function sendResetEmail($email, $token, $userName)
    {
        $emailService = \Config\Services::email();

        $resetLink = base_url("password-reset/reset/{$token}");

        $emailService->setTo($email);
        $emailService->setSubject('Recuperación de Contraseña - Movimiento de la Palabra de Dios');

        $message = view('password_reset/email_template', [
            'userName' => $userName,
            'resetLink' => $resetLink
        ]);

        $emailService->setMessage($message);

        return $emailService->send();
    }

    /**
     * Vista: Formulario para cambiar contraseña
     */
    public function reset($token)
    {
        // Validar token
        $resetModel = new PasswordResetModel();
        $tokenData = $resetModel->validateToken($token);

        if (!$tokenData) {
            return redirect()->to('/login')->with('error', 'El enlace de recuperación es inválido o ha expirado');
        }

        $data = [
            'title' => 'Nueva Contraseña',
            'token' => $token,
            'email' => $tokenData['email']
        ];

        echo view('templates/header', $data);
        echo view('password_reset/reset_form', $data);
        echo view('templates/footer');
    }

    /**
     * Procesar cambio de contraseña
     */
    public function updatePassword()
    {
        $token = $this->request->getPost('token');
        $password = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');

        // Validar contraseñas
        if (strlen($password) < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres'
            ]);
        }

        if ($password !== $passwordConfirm) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Las contraseñas no coinciden'
            ]);
        }

        // Validar token
        $resetModel = new PasswordResetModel();
        $tokenData = $resetModel->validateToken($token);

        if (!$tokenData) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'El enlace de recuperación es inválido o ha expirado'
            ]);
        }

        // Actualizar contraseña
        $userModel = new UserModel();
        $updated = $userModel->where('email', $tokenData['email'])
                             ->set(['password' => password_hash($password, PASSWORD_DEFAULT)])
                             ->update();

        if ($updated) {
            // Marcar token como usado
            $resetModel->markAsUsed($token);

            return $this->response->setJSON([
                'success' => true,
                'message' => 'Contraseña actualizada correctamente'
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al actualizar la contraseña'
        ]);
    }
}