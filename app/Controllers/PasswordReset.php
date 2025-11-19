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
        $data = ['title' => 'Recuperar Contraseña'];
        
        echo view('templates/header', $data);
        echo view('password_reset/request', $data);
        echo view('templates/footer');
    }

    /**
     * Procesar solicitud de recuperación
     */
    public function sendResetLink()
    {
        $email = $this->request->getPost('email');
        
        // Validar email
        if (!$email || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Por favor ingresa un email válido',
                'field' => 'email'
            ]);
        }

        // Verificar que el email exista en la base de datos
        $userModel = new UserModel();
        $user = $userModel->where('email', $email)->first();

        if (!$user) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'No existe una cuenta registrada con ese email',
                'field' => 'email'
            ]);
        }

        // Crear token de recuperación
        $resetModel = new PasswordResetModel();
        $token = $resetModel->createToken($email);

        // Enviar email
        if ($this->sendResetEmail($email, $token, $user['name'])) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Se ha enviado un enlace de recuperación a tu email. Revisa tu bandeja de entrada.'
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error al enviar el email. Por favor, intenta nuevamente.'
            ]);
        }
    }

    /**
     * Enviar email de recuperación
     */
    private function sendResetEmail($email, $token, $userName)
    {
        $emailService = \Config\Services::email();
        
        // Limpiar configuración previa
        $emailService->clear();
        
        // Configurar como HTML
        $emailService->setMailType('html');
        
        $emailService->setTo($email);
        $emailService->setSubject('Recuperación de Contraseña - Movimiento de la Palabra de Dios');

        // Link de recuperación
        $resetLink = base_url("password-reset/reset/{$token}");

        // Cargar template del email
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
            // Token inválido o expirado
            $data = [
                'title' => 'Enlace Inválido',
                'error' => true,
                'message' => 'El enlace de recuperación es inválido o ha expirado. Por favor, solicita uno nuevo.'
            ];
            
            echo view('templates/header', $data);
            echo view('password_reset/invalid_token', $data);
            echo view('templates/footer');
            return;
        }

        // Token válido - mostrar formulario
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

        // Validar que las contraseñas no estén vacías
        if (empty($password) || empty($passwordConfirm)) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Debes completar ambos campos de contraseña',
                'field' => 'password'
            ]);
        }

        // Validar longitud mínima
        if (strlen($password) < 6) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'La contraseña debe tener al menos 6 caracteres',
                'field' => 'password'
            ]);
        }

        // Validar que coincidan
        if ($password !== $passwordConfirm) {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Las contraseñas no coinciden',
                'field' => 'password_confirm'
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
                'message' => 'Contraseña actualizada correctamente. Serás redirigido al login...',
                'redirect' => base_url('login')
            ]);
        }

        return $this->response->setJSON([
            'success' => false,
            'message' => 'Error al actualizar la contraseña. Intenta nuevamente.'
        ]);
    }
}