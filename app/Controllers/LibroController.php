<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LibroModel;

class LibroController extends Controller
{
    public function crear()
    {
        echo view('templates/header');
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
        $file->move(ROOTPATH . 'public/imgs', $newName); // Cambiado a 'imgs'
        return 'imgs/' . $newName; // Guardar la ruta completa
    }
    
    return null;
}



    public function detalles($id)
{
    $libroModel = new LibroModel();
    $libro = $libroModel->find($id);
    
    if (!$libro) {
        throw new \CodeIgniter\Exceptions\PageNotFoundException('Libro no encontrado');
    }
    
    echo view('templates/header');
    return view('book_details', ['libro' => $libro]);
    
}



public function editar($id)
    {
        $libroModel = new LibroModel();
        $libro = $libroModel->find($id);
        
        if (!$libro) {
            return redirect()->back()->with('error', 'Libro no encontrado');
        }
        
        // Preparar array de imágenes para la vista
        $imagenes = [];
        if (!empty($libro['foto1'])) {
            $imagenes[] = $libro['foto1'];
        }
        if (!empty($libro['foto2'])) {
            $imagenes[] = $libro['foto2'];
        }
        
        $data['libro'] = $libro;
        $data['libro']['imagenes'] = $imagenes;
        
        echo view('templates/header');
        return view('edit_delete_book', $data);
    }
    
    // Función para actualizar el libro
    public function actualizar($id)
{
    $libroModel = new LibroModel();
    
    // Datos del formulario
    $data = [
        'titulo'      => $this->request->getPost('titulo'),
        'autor'       => $this->request->getPost('autor'),
        'descripcion' => $this->request->getPost('descripcion'),
        'edicion'     => $this->request->getPost('edicion'),
        'precio'      => $this->request->getPost('precio'),
        'categoria'   => $this->request->getPost('categoria')
    ];

    // Procesar eliminación de imágenes existentes
    if ($eliminarImagenes = $this->request->getPost('eliminarImagenes')) {
        $libroActual = $libroModel->find($id);
        
        foreach ($eliminarImagenes as $index) {
            $campoImagen = 'foto' . ($index + 1); // foto1, foto2
            
            if (!empty($libroActual[$campoImagen])) {
                // Eliminar archivo físico si existe
                $rutaImagen = ROOTPATH . 'public/' . $libroActual[$campoImagen];
                if (file_exists($rutaImagen)) {
                    unlink($rutaImagen);
                }
                // Marcar el campo como null para la base de datos
                $data[$campoImagen] = null;
            }
        }
    }

    // Procesar NUEVAS imágenes usando processImage() para consistencia
    $foto1 = $this->processImage('foto1');
    $foto2 = $this->processImage('foto2');
    
    // Solo actualizar si se subió una nueva imagen
    if ($foto1 !== null) {
        // Eliminar imagen anterior si existe
        $libroActual = $libroModel->find($id);
        if (!empty($libroActual['foto1'])) {
            $rutaAnterior = ROOTPATH . 'public/' . $libroActual['foto1'];
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }
        $data['foto1'] = $foto1;
    }
    
    if ($foto2 !== null) {
        // Eliminar imagen anterior si existe
        $libroActual = $libroModel->find($id);
        if (!empty($libroActual['foto2'])) {
            $rutaAnterior = ROOTPATH . 'public/' . $libroActual['foto2'];
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }
        $data['foto2'] = $foto2;
    }

    // Actualizar el libro
    if ($libroModel->update($id, $data)) {
        return redirect()->to('/')->with('success', 'Libro actualizado correctamente');
    } else {
        return redirect()->back()->with('error', 'Error al actualizar el libro')->withInput();
    }
}
    
    // Función para eliminar el libro
    public function eliminar($id)
    {
        $libroModel = new LibroModel();
        $libro = $libroModel->find($id);
        
        if ($libro) {
            // Eliminar imágenes físicas
            for ($i = 1; $i <= 2; $i++) {
                if (!empty($libro['foto' . $i])) {
                    $rutaImagen = ROOTPATH . 'public/' . $libro['foto' . $i];
                    if (file_exists($rutaImagen)) {
                        unlink($rutaImagen);
                    }
                }
            }
            
            // Eliminar de la base de datos
            if ($libroModel->delete($id)) {
                return redirect()->to('/')->with('success', 'Libro eliminado correctamente');
            }
        }
        
        return redirect()->back()->with('error', 'Error al eliminar el libro');
    }
}