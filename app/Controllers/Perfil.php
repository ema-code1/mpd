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

        $data = [
            'name'  => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        // Manejar subida de imagen directamente
        $file = $this->request->getFile('foto_perfil');
        if ($file && $file->isValid()) {
            $uploadDir = FCPATH . 'ppimages/';
            if (!is_dir($uploadDir)) mkdir($uploadDir, 0755, true);

            $newName = $file->getRandomName();
            
            if ($file->move($uploadDir, $newName)) {
                // Eliminar foto anterior si existe
                $user = $userModel->find($userId);
                if ($user && !empty($user['foto_perfil']) && $user['foto_perfil'] !== $newName) {
                    $oldImagePath = $uploadDir . $user['foto_perfil'];
                    if (file_exists($oldImagePath)) {
                        unlink($oldImagePath);
                    }
                }
                $data['foto_perfil'] = $newName;
            }
        }

        // Actualizar contraseña si se proporcionó
        $password = $this->request->getPost('password');
        if (!empty($password)) {
            $data['password'] = password_hash($password, PASSWORD_DEFAULT);
        }

        // Actualizar usuario
        if ($userModel->update($userId, $data)) {
            $session->set($data);
            return $this->response->setJSON([
                'success' => true, 
                'message' => 'Perfil actualizado correctamente'
            ]);
        }

        return $this->response->setJSON([
            'success' => false, 
            'message' => 'Error al actualizar el perfil'
        ]);
    }
}