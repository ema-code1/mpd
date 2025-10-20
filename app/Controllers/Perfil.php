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
            return redirect()->to('/login');
        }

        $rules = [
            'name' => 'required|min_length[2]|max_length[150]',
            'email' => "required|valid_email|is_unique[users.email,id,{$session->get('userId')}]"
        ];

        if ($this->request->getPost('password')) {
            $rules['password'] = 'min_length[6]';
            $rules['password_confirm'] = 'matches[password]';
        }

        if (!$this->validate($rules)) {
            return redirect()->back()->withInput()->with('errors', $this->validator->getErrors());
        }

        $userModel = new UserModel();
        $data = [
            'name' => $this->request->getPost('name'),
            'email' => $this->request->getPost('email')
        ];

        if ($this->request->getPost('password')) {
            $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
        }

        $userModel->update($session->get('userId'), $data);

        // Actualizar datos en sesión
        $session->set('name', $data['name']);
        $session->set('email', $data['email']);

        return redirect()->to('/perfil')->with('success', 'Perfil actualizado correctamente.');
    }

    public function uploadFoto()
    {
        $session = session();
        if (!$session->get('isLoggedIn')) {
            return response()->setJSON(['success' => false, 'message' => 'No autorizado']);
        }

        $file = $this->request->getFile('foto_perfil');
        
        if (!$file->isValid()) {
            return response()->setJSON(['success' => false, 'message' => $file->getErrorString()]);
        }

        // Validar tipo de archivo
        if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
            return response()->setJSON(['success' => false, 'message' => 'Solo se permiten imágenes JPEG, PNG, GIF o WebP']);
        }

        // Validar tamaño (máximo 5MB)
        if ($file->getSize() > 5 * 1024 * 1024) {
            return response()->setJSON(['success' => false, 'message' => 'La imagen no debe superar los 5MB']);
        }

        $userId = $session->get('userId');
        $extension = $file->getExtension();
        $newName = $userId . '_' . bin2hex(random_bytes(8)) . '.' . $extension;
        
        // Crear directorio si no existe
        $uploadPath = ROOTPATH . 'public/ppimages';
        if (!is_dir($uploadPath)) {
            mkdir($uploadPath, 0755, true);
        }

        // Mover archivo
        if ($file->move($uploadPath, $newName)) {
            // Actualizar en base de datos
            $userModel = new UserModel();
            $userModel->update($userId, ['foto_perfil' => $newName]);

            return response()->setJSON([
                'success' => true, 
                'message' => 'Foto actualizada correctamente',
                'image_url' => base_url('ppimages/' . $newName)
            ]);
        }

        return response()->setJSON(['success' => false, 'message' => 'Error al subir la imagen']);
    }
}