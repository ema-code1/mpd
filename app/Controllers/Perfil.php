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

    // Validaciones
    $rules = [
        'name' => "required|min_length[2]|max_length[150]|is_unique[users.name,id,{$userId}]",
        'email' => "required|valid_email|is_unique[users.email,id,{$userId}]"
    ];

    if ($this->request->getPost('password')) {
        $rules['password'] = 'min_length[6]';
        $rules['password_confirm'] = 'matches[password]';
    }

    if (!$this->validate($rules)) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => $this->validator->getErrors()
        ]);
    }

    // Preparar datos
    $data = [
        'name' => $this->request->getPost('name'),
        'email' => $this->request->getPost('email')
    ];

    // Actualizar contraseña si se proporcionó
    if ($this->request->getPost('password')) {
        $data['password'] = password_hash($this->request->getPost('password'), PASSWORD_DEFAULT);
    }

    // Procesar imagen temporal si existe
    $tempFile = $this->request->getPost('foto_temp');
    if ($tempFile && file_exists(ROOTPATH . 'public/ppimages/temp/' . $tempFile)) {
        $newFilename = $userId . '_' . bin2hex(random_bytes(8)) . '.' . pathinfo($tempFile, PATHINFO_EXTENSION);
        $tempPath = ROOTPATH . 'public/ppimages/temp/' . $tempFile;
        $finalPath = ROOTPATH . 'public/ppimages/' . $newFilename;
        
        // Mover de temp a final
        if (rename($tempPath, $finalPath)) {
            $data['foto_perfil'] = $newFilename;
            // Limpiar archivos temporales viejos
            $this->cleanTempFiles();
        }
    }

    // Actualizar usuario
    if ($userModel->update($userId, $data)) {
        // Actualizar sesión
        $session->set('name', $data['name']);
        $session->set('email', $data['email']);
        if (isset($data['foto_perfil'])) {
            $session->set('foto_perfil', $data['foto_perfil']);
        }

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

public function uploadTempImage()
{
    $session = session();
    if (!$session->get('isLoggedIn')) {
        return $this->response->setJSON(['success' => false, 'message' => 'No autorizado']);
    }

    $file = $this->request->getFile('foto_perfil');
    
    if (!$file->isValid()) {
        return $this->response->setJSON(['success' => false, 'message' => $file->getErrorString()]);
    }

    // Validaciones
    if (!in_array($file->getMimeType(), ['image/jpeg', 'image/png', 'image/gif', 'image/webp'])) {
        return $this->response->setJSON(['success' => false, 'message' => 'Solo se permiten imágenes JPEG, PNG, GIF o WebP']);
    }

    if ($file->getSize() > 5 * 1024 * 1024) {
        return $this->response->setJSON(['success' => false, 'message' => 'La imagen no debe superar los 5MB']);
    }

    // Crear directorio temp si no existe
    $tempPath = ROOTPATH . 'public/ppimages/temp';
    if (!is_dir($tempPath)) {
        mkdir($tempPath, 0755, true);
    }

    // Generar nombre único para archivo temporal
    $tempFilename = 'temp_' . bin2hex(random_bytes(16)) . '.' . $file->getExtension();
    
    // Mover a temp
    if ($file->move($tempPath, $tempFilename)) {
        return $this->response->setJSON([
            'success' => true,
            'temp_filename' => $tempFilename
        ]);
    }

    return $this->response->setJSON(['success' => false, 'message' => 'Error al subir la imagen']);
}

private function cleanTempFiles()
{
    $tempPath = ROOTPATH . 'public/ppimages/temp/';
    if (is_dir($tempPath)) {
        $files = glob($tempPath . 'temp_*');
        $now = time();
        foreach ($files as $file) {
            if (is_file($file) && ($now - filemtime($file)) > 3600) { // 1 hora
                unlink($file);
            }
        }
    }
}
}