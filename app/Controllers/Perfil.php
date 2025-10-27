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

    $password = $this->request->getPost('password');
    if (!empty($password)) {
        $data['password'] = password_hash($password, PASSWORD_DEFAULT);
    }

    $tempFile = $this->request->getPost('foto_temp'); // el nombre que vino del JS
    $tempDir = FCPATH . 'ppimages/temp/';
    $finalDir = FCPATH . 'ppimages/';

    if ($tempFile && file_exists($tempDir . $tempFile)) {
        // borrar todo lo que haya en temp primero
        $files = glob($tempDir . '*');
        foreach ($files as $f) {
            if (is_file($f)) @unlink($f);
        }

        // crear nombre random y mover
        $ext = pathinfo($tempFile, PATHINFO_EXTENSION);
        $newName = 'perfil_' . $userId . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
        $tempPath = $tempDir . $tempFile;
        $finalPath = $finalDir . $newName;

        if (!is_dir($finalDir)) mkdir($finalDir, 0755, true);

        $moved = @rename($tempPath, $finalPath);
        if (!$moved) {
            $moved = @copy($tempPath, $finalPath);
            if ($moved) @unlink($tempPath);
        }

        if ($moved) {
            // borrar foto anterior del usuario
            $oldPhoto = $session->get('foto_perfil');
            if ($oldPhoto && file_exists($finalDir . $oldPhoto)) {
                @unlink($finalDir . $oldPhoto);
            }
            $data['foto_perfil'] = $newName;
        } else {
            return $this->response->setJSON([
                'success' => false,
                'message' => 'Error moviendo imagen desde temp'
            ]);
        }
    }

    $userModel->update($userId, $data);
    $session->set($data);

    return $this->response->setJSON([
        'success' => true,
        'message' => 'Perfil actualizado correctamente'
    ]);
}


public function uploadTempImage()
{
    $file = $this->request->getFile('foto');
    if (!$file || !$file->isValid()) {
        return $this->response->setJSON(['success' => false, 'message' => 'Archivo inválido']);
    }

    $allowed = ['image/jpeg', 'image/png', 'image/webp'];
    if (!in_array($file->getMimeType(), $allowed)) {
        return $this->response->setJSON(['success' => false, 'message' => 'Tipo no permitido']);
    }

    $newName = 'temp_' . time() . '.' . $file->getExtension();
    $dir = FCPATH . 'ppimages/temp/';
    if (!is_dir($dir)) mkdir($dir, 0755, true);

    $file->move($dir, $newName, true);

    return $this->response->setJSON([
        'success' => true,
        'temp_filename' => $newName
    ]);
}

public function cleanTempFiles()
{
    $dir = FCPATH . 'ppimages/temp/';
    $files = glob($dir . '*');
    foreach ($files as $file) {
        if (is_file($file)) {
            @unlink($file);
        }
    }

    return $this->response->setJSON(['success' => true, 'message' => 'Temp limpiado']);
}
}