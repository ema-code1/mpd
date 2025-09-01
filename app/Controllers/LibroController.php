<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LibroModel;

class LibroController extends Controller
{
    public function crear()
    {
        return view('upload_book');
    }
    
    public function crearLibro()
    {
        $libroModel = new LibroModel();
        
        // Validar datos
        $validation = \Config\Services::validation();
        $validation->setRules([
            'titulo' => 'required|min_length[3]',
            'autor' => 'required',
            'precio' => 'required|decimal',
            'categoria' => 'required'
        ]);
        
        if (!$validation->withRequest($this->request)->run()) {
            return redirect()->back()->withInput()->with('errors', $validation->getErrors());
        }
        
        // Procesar imágenes
        $foto1 = $this->processImage('foto1');
        $foto2 = $this->processImage('foto2');
        
        // Preparar datos
        $data = [
            'titulo' => $this->request->getPost('titulo'),
            'descripcion' => $this->request->getPost('descripcion'),
            'autor' => $this->request->getPost('autor'),
            'edicion' => $this->request->getPost('edicion'),
            'precio' => $this->request->getPost('precio'),
            'categoria' => $this->request->getPost('categoria'),
            'foto1' => $foto1,
            'foto2' => $foto2
        ];
        
        // Guardar en base de datos
        if ($libroModel->insert($data)) {
            return redirect()->to('/upload_book')->with('success', 'Libro cargado exitosamente');
        } else {
            return redirect()->back()->withInput()->with('error', 'Error al cargar el libro');
        }
    }
    
    private function processImage($fieldName)
    {
        $file = $this->request->getFile($fieldName);
        
        if ($file && $file->isValid() && !$file->hasMoved()) {
            $newName = $file->getRandomName();
            $file->move(ROOTPATH . 'public/uploads', $newName);
            return $newName;
        }
        
        return null;
    }
}