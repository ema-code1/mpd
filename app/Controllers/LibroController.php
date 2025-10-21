<?php
namespace App\Controllers;

use CodeIgniter\Controller;
use App\Models\LibroModel;
use App\Models\ResenasModel;

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

    // Cargar modelo de reseñas y obtener datos
    $resenasModel = new ResenasModel();
    
    // CORREGIDO: Usar 'userId' en lugar de 'id'
    $user_id = session()->get('userId');
    
    $data = [
        'libro' => $libro,
        'resenas' => $resenasModel->getResenasByLibro($id),
        'stats_resenas' => $resenasModel->getStatsResenas($id),
        'user_ya_reseno' => session()->get('isLoggedIn') ? 
            $resenasModel->userYaReseno($user_id, $id) : false
    ];
    
    echo view('templates/header');
    return view('book_details', $data);
}

public function agregarResena()
{
    if (!$this->request->isAJAX() || !session()->get('isLoggedIn')) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => ['No autorizado o no es una petición AJAX']
        ]);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'libro_id' => 'required|integer',
        'rating' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'descripcion' => 'required|min_length[10]|max_length[500]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => $validation->getErrors(),
            'debug' => 'Error de validación'
        ]);
    }

    $resenasModel = new ResenasModel();
    $libro_id = $this->request->getPost('libro_id');
    
    // CORREGIDO: Usar 'userId' en lugar de 'id'
    $user_id = session()->get('userId');

    // Debug: Verificar datos recibidos
    $debug_data = [
        'libro_id' => $libro_id,
        'user_id' => $user_id,
        'rating' => $this->request->getPost('rating'),
        'descripcion_length' => strlen($this->request->getPost('descripcion'))
    ];

    // Verificar si el usuario ya reseñó este libro
    if ($resenasModel->userYaReseno($user_id, $libro_id)) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => ['Ya has publicado una reseña para este libro'],
            'debug' => $debug_data
        ]);
    }

    $resenaData = [
        'libro_id' => $libro_id,
        'user_id' => $user_id,
        'rating' => $this->request->getPost('rating'),
        'descripcion' => $this->request->getPost('descripcion')
    ];

    try {
        if ($resenasModel->insert($resenaData)) {
            return $this->response->setJSON([
                'success' => true,
                'message' => 'Reseña publicada exitosamente',
                'stats' => $resenasModel->getStatsResenas($libro_id),
                'debug' => $debug_data
            ]);
        } else {
            return $this->response->setJSON([
                'success' => false,
                'errors' => ['Error al insertar en la base de datos'],
                'debug' => $debug_data,
                'model_errors' => $resenasModel->errors()
            ]);
        }
    } catch (\Exception $e) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => ['Excepción: ' . $e->getMessage()],
            'debug' => $debug_data
        ]);
    }
}

public function editarResena($resena_id)
{
    if (!$this->request->isAJAX() || !session()->get('isLoggedIn')) {
        return $this->response->setStatusCode(403);
    }

    $resenasModel = new ResenasModel();
    $resena = $resenasModel->getResenaById($resena_id);
    
    // Verificar que la reseña existe y pertenece al usuario
    if (!$resena || $resena['user_id'] != session()->get('userId')) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => ['No tienes permiso para editar esta reseña']
        ]);
    }

    return $this->response->setJSON([
        'success' => true,
        'resena' => $resena
    ]);
}

public function actualizarResena($resena_id)
{
    if (!$this->request->isAJAX() || !session()->get('isLoggedIn')) {
        return $this->response->setStatusCode(403);
    }

    $validation = \Config\Services::validation();
    $validation->setRules([
        'rating' => 'required|integer|greater_than_equal_to[1]|less_than_equal_to[5]',
        'descripcion' => 'required|min_length[10]|max_length[500]'
    ]);

    if (!$validation->withRequest($this->request)->run()) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => $validation->getErrors()
        ]);
    }

    $resenasModel = new ResenasModel();
    $resena = $resenasModel->getResenaById($resena_id);
    
    // Verificar permisos
    if (!$resena || $resena['user_id'] != session()->get('userId')) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => ['No tienes permiso para editar esta reseña']
        ]);
    }

    $data = [
        'rating' => $this->request->getPost('rating'),
        'descripcion' => $this->request->getPost('descripcion'),
        'updated_at' => date('Y-m-d H:i:s')
    ];

    if ($resenasModel->actualizarResena($resena_id, $data)) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reseña actualizada exitosamente',
            'resena' => $resenasModel->getResenaById($resena_id)
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'errors' => ['Error al actualizar la reseña']
    ]);
}

public function eliminarResena($resena_id)
{
    if (!$this->request->isAJAX() || !session()->get('isLoggedIn')) {
        return $this->response->setStatusCode(403);
    }

    $resenasModel = new ResenasModel();
    $resena = $resenasModel->getResenaById($resena_id);
    
    // Verificar permisos
    if (!$resena || $resena['user_id'] != session()->get('userId')) {
        return $this->response->setJSON([
            'success' => false,
            'errors' => ['No tienes permiso para eliminar esta reseña']
        ]);
    }

    if ($resenasModel->delete($resena_id)) {
        return $this->response->setJSON([
            'success' => true,
            'message' => 'Reseña eliminada exitosamente'
        ]);
    }

    return $this->response->setJSON([
        'success' => false,
        'errors' => ['Error al eliminar la reseña']
    ]);
}

// EDITAR LIBRO

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

    // DEBUG: Verificar si llegan archivos
    // echo "Foto1 recibida: " . ($this->request->getFile('foto1') ? 'Sí' : 'No');
    // echo "Foto2 recibida: " . ($this->request->getFile('foto2') ? 'Sí' : 'No');

    // Procesar NUEVAS imágenes - MANERA CORRECTA
    $foto1File = $this->request->getFile('foto1');
    $foto2File = $this->request->getFile('foto2');

    // Procesar foto1 si se subió
    if ($foto1File && $foto1File->isValid() && !$foto1File->hasMoved()) {
        // Eliminar imagen anterior si existe
        $libroActual = $libroModel->find($id);
        if (!empty($libroActual['foto1'])) {
            $rutaAnterior = ROOTPATH . 'public/' . $libroActual['foto1'];
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }
        
        // Guardar nueva imagen
        $newName = $foto1File->getRandomName();
        $foto1File->move(ROOTPATH . 'public/imgs', $newName);
        $data['foto1'] = 'imgs/' . $newName;
    }

    // Procesar foto2 si se subió
    if ($foto2File && $foto2File->isValid() && !$foto2File->hasMoved()) {
        // Eliminar imagen anterior si existe
        $libroActual = $libroModel->find($id);
        if (!empty($libroActual['foto2'])) {
            $rutaAnterior = ROOTPATH . 'public/' . $libroActual['foto2'];
            if (file_exists($rutaAnterior)) {
                unlink($rutaAnterior);
            }
        }
        
        // Guardar nueva imagen
        $newName = $foto2File->getRandomName();
        $foto2File->move(ROOTPATH . 'public/imgs', $newName);
        $data['foto2'] = 'imgs/' . $newName;
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