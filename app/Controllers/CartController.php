<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\LibroModel;

class CartController extends BaseController
{
    public function index()
    {
        // Verificar que el usuario esté logueado y sea comprador
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión como comprador.');
        }

        $userId = session()->get('userId');
        $cartModel = new CartModel();
        $libroModel = new LibroModel();

        // Obtener todos los items del carrito para este usuario
        $cartItems = $cartModel->where('user_id', $userId)->findAll();

        // Obtener los detalles de cada libro
        foreach ($cartItems as &$item) {
            $libro = $libroModel->find($item['libro_id']);
            if ($libro) {
                $item = array_merge($item, $libro); // Combinamos los datos del carrito con los del libro
            }
        }

        // Pasar los datos a la vista
        $data['cartItems'] = $cartItems;
        echo view('templates/header');
        echo view('cart', $data);
        echo view('templates/footer');
    }

    // Acción para añadir un libro al carrito (llamada desde book_details y cart.php)
    public function add($libro) //$libro trae valor de cambio de stock
{
    $debug = []; // array para debug

    $debug['add_iniciado'] = true;
    $debug['libro'] = $libro;
    $debug['userId'] = session()->get('userId');

    if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
        $debug['error'] = 'No autorizado';
        return $this->response->setJSON($debug);
    }

    $userId = session()->get('userId');
    $cartModel = new CartModel();

    $change = (int) $this->request->getPost('change');
    if ($change === 0) $change = 1;
    $debug['change'] = $change;

    try {
        $existing = $cartModel->where('user_id', $userId)->where('libro_id', $libro)->first();
        $debug['existing'] = $existing;

        if ($existing) {
            $newCantidad = $existing['cantidad'] + $change;
        
            if ($newCantidad <= 0) {
                // Si la cantidad llega a 0 o menos, eliminamos el item usando la PK compuesta: $cartModel->where('user_id', $userId)->where('libro_id', $libro)->delete();
            } else {
                // Actualizamos la cantidad usando WHERE (no usamos update($id,...) porque no hay id único)
                $cartModel->set('cantidad', $newCantidad)
                          ->where('user_id', $userId)
                          ->where('libro_id', $libro)
                          ->update();
            }
        }
         else if ($change > 0) {
            $cartModel->insert([
                'user_id' => $userId,
                'libro_id' => $libro,
                'cantidad' => $change,
                'seleccionado' => 1
            ]);
            $debug['insertado'] = true;
        }
    } catch (\Exception $e) {
        $debug['exception'] = $e->getMessage();
        return $this->response->setJSON($debug);
    }

    $debug['success'] = true;
    return $this->response->setJSON($debug);
}


    // Acción para actualizar cantidad o selección (llamada por AJAX desde la vista)
    public function update()
{
    if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
        return $this->response->setJSON(['error' => 'No autorizado']);
    }

    $userId = session()->get('userId');
    $cartModel = new CartModel();

    // Ahora recibimos libro_id (no item_id) y action/value
    $libroId = $this->request->getPost('libro_id');
    $action = $this->request->getPost('action'); // 'cantidad' o 'seleccionado'
    $value = $this->request->getPost('value');

    if (!$libroId) {
        return $this->response->setJSON(['error' => 'libro_id no proporcionado']);
    }

    // Buscar el item por la PK compuesta
    $item = $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->first();
    if (!$item) {
        return $this->response->setJSON(['error' => 'Item no encontrado']);
    }

    if ($action === 'cantidad') {
        $value = max(1, (int)$value); // Mínimo 1
        $cartModel->set('cantidad', $value)
                  ->where('user_id', $userId)
                  ->where('libro_id', $libroId)
                  ->update();
    } elseif ($action === 'seleccionado') {
        $cartModel->set('seleccionado', (int)$value)
                  ->where('user_id', $userId)
                  ->where('libro_id', $libroId)
                  ->update();
    } else {
        return $this->response->setJSON(['error' => 'Acción no válida']);
    }

    return $this->response->setJSON(['success' => true]);
}

public function delete()
{
    if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
        return $this->response->setJSON(['error' => 'Debes iniciar sesión como comprador']);
    }

    $userId = session()->get('userId');
    $cartModel = new CartModel();
    $libroId = $this->request->getPost('libro_id');

    if (!$libroId) {
        return $this->response->setJSON(['error' => 'libro_id no proporcionado']);
    }

    // Verificar que exista para este usuario
    $item = $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->first();
    if (!$item) {
        return $this->response->setJSON(['error' => 'Item no encontrado o no autorizado']);
    }

    // Eliminar usando WHERE por la PK compuesta
    $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->delete();

    return $this->response->setJSON(['success' => true, 'msg' => 'Producto eliminado del carrito']);
}
}
