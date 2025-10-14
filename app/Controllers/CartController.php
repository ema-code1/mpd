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
    public function add($libro)
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
            $debug['newCantidad'] = $newCantidad;

            if ($newCantidad <= 0) $cartModel->delete($existing['id']);
            else $cartModel->update($existing['id'], ['cantidad' => $newCantidad]);
        } else if ($change > 0) {
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

        $itemId = $this->request->getPost('item_id');
        $action = $this->request->getPost('action'); // 'cantidad' o 'seleccionado'
        $value = $this->request->getPost('value');

        $item = $cartModel->find($itemId);
        if (!$item || $item['user_id'] != $userId) {
            return $this->response->setJSON(['error' => 'Item no encontrado']);
        }

        if ($action === 'cantidad') {
            $value = max(1, (int)$value); // Mínimo 1
            $cartModel->update($itemId, ['cantidad' => $value]);
        } elseif ($action === 'seleccionado') {
            $cartModel->update($itemId, ['seleccionado' => (int)$value]);
        }

        return $this->response->setJSON(['success' => true]);
    }

    public function delete()
    {
        // Verifica login y rol
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
            return $this->response->setJSON(['error' => 'Debes iniciar sesión como comprador']);
        }

        $userId = session()->get('userId');
        $itemId = $this->request->getPost('libro_id'); // 👈 Asegurate de usar libro_id también en JS

        if (!$itemId) {
            return $this->response->setJSON(['error' => 'ID del item no proporcionado']);
        }

        $cartModel = new \App\Models\CartModel();

        // Verificamos que el ítem exista
        $item = $cartModel->where('libro_id', $itemId)->where('user_id', $userId)->first();

        if (!$item) {
            return $this->response->setJSON(['error' => 'El producto no existe en tu carrito']);
        }

        // Eliminamos el ítem del carrito del usuario
        $cartModel->where('libro_id', $itemId)->where('user_id', $userId)->delete();

        return $this->response->setJSON(['success' => true, 'msg' => 'Producto eliminado del carrito']);
    }
}
