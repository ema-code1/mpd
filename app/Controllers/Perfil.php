<?php namespace App\Controllers;

use App\Models\UserModel;
use CodeIgniter\Controller;

class Perfil extends Controller
{
    protected $helpers = ['form', 'url', 'session'];

    public function index()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        $userModel = new UserModel();
        $user = $userModel->find($session->get('userId'));

        $data = [
            'user' => $user,
            'title' => 'Mi Perfil'
        ];

        echo view('templates/header', $data);
        echo view('perfil', $data);
        echo view('templates/footer');
    }
    
    public function actualizar()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        $userId = $session->get('userId');
        $userModel = new UserModel();
        
        // 🔐 OBTENER EL USUARIO ACTUAL
        $currentUser = $userModel->find($userId);
        if (!$currentUser) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Usuario no encontrado'
            ]);
        }

        // 🔐 VALIDAR CONTRASEÑA ACTUAL (OBLIGATORIA)
        $currentPassword = $this->request->getPost('current_password');
        
        if (empty($currentPassword)) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'Debes ingresar tu contraseña actual para confirmar los cambios',
                'field' => 'current_password'
            ]);
        }

        // Verificar que la contraseña actual sea correcta
        if (!password_verify($currentPassword, $currentUser['password'])) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'La contraseña actual es incorrecta',
                'field' => 'current_password'
            ]);
        }

        // 📝 PREPARAR DATOS PARA ACTUALIZAR
        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        // Validar que el email no esté en uso por otro usuario
        $emailExists = $userModel->where('email', $data['email'])
                                  ->where('id !=', $userId)
                                  ->first();
        if ($emailExists) {
            return $this->response->setJSON([
                'success' => false, 
                'message' => 'El email ya está en uso por otro usuario',
                'field' => 'email'
            ]);
        }

        // 📷 MANEJAR SUBIDA DE IMAGEN
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid()) {
            $uploadDir = FCPATH . 'ppimages/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            // Validar tipo y tamaño de imagen
            $validTypes = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif', 'image/webp'];
            if (!in_array($file->getMimeType(), $validTypes)) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Solo se permiten imágenes JPEG, PNG, GIF o WebP'
                ]);
            }

            if ($file->getSize() > 5 * 1024 * 1024) { // 5MB
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'La imagen no debe superar los 5MB'
                ]);
            }

            $newName = $userId . '_' . uniqid() . '.' . $file->getExtension();
            
            if ($file->move($uploadDir, $newName)) {
                // Eliminar foto anterior si existe y no es la default
                if (!empty($currentUser['foto_perfil']) && $currentUser['foto_perfil'] !== $newName) {
                    $oldImagePath = $uploadDir . $currentUser['foto_perfil'];
                    if (file_exists($oldImagePath)) {
                        @unlink($oldImagePath);
                    }
                }
                $data['foto_perfil'] = $newName;
            }
        }

        // 🔑 ACTUALIZAR CONTRASEÑA SI SE PROPORCIONÓ UNA NUEVA
        $newPassword = $this->request->getPost('password');
        $passwordConfirm = $this->request->getPost('password_confirm');
        
        if (!empty($newPassword)) {
            // Validar que las contraseñas coincidan
            if ($newPassword !== $passwordConfirm) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'Las contraseñas nuevas no coinciden',
                    'field' => 'password_confirm'
                ]);
            }

            // Validar longitud mínima
            if (strlen($newPassword) < 6) {
                return $this->response->setJSON([
                    'success' => false, 
                    'message' => 'La nueva contraseña debe tener al menos 6 caracteres',
                    'field' => 'password'
                ]);
            }

            $data['password'] = password_hash($newPassword, PASSWORD_DEFAULT);
        }

        // ✅ ACTUALIZAR USUARIO
        if ($userModel->update($userId, $data)) {
            // Actualizar datos en sesión si es necesario
            if (isset($data['name'])) {
                $session->set('name', $data['name']);
            }
            if (isset($data['email'])) {
                $session->set('email', $data['email']);
            }

            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Perfil actualizado correctamente',
                'foto_perfil' => isset($data['foto_perfil']) ? $data['foto_perfil'] : null
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Error al actualizar el perfil'
        ]);
    }
}