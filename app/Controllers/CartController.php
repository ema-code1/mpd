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
    public function add($libroId){
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $userId = session()->get('userId');
        $cartModel = new CartModel();

        // Obtener el cambio desde la solicitud (por defecto +1)
        $change = (int) $this->request->getPost('change', FILTER_SANITIZE_NUMBER_INT);
        if ($change === 0) $change = 1; // Por seguridad

        // Buscar si el libro ya está en el carrito
        $existing = $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->first();

        if ($existing) {
            $newCantidad = $existing['cantidad'] + $change;

            if ($newCantidad <= 0) {
                // Si la cantidad llega a 0 o menos, eliminamos el item
                $cartModel->delete($existing['id']);
            } else {
                // Actualizamos la cantidad
                $cartModel->update($existing['id'], ['cantidad' => $newCantidad]);
            }
        } else {
            // Si no existe y el cambio es positivo, lo creamos
            if ($change > 0) {
                $cartModel->insert([
                    'user_id' => $userId,
                    'libro_id' => $libroId,
                    'cantidad' => $change, // En caso de que alguien pase change=3
                    'seleccionado' => 1
                ]);
            }
            // Si no existe y el cambio es negativo, no hacemos nada
        }

        return $this->response->setJSON(['success' => true]);
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

    public function delete(){
    if (!session()->get('isLoggedIn')|| session()->get('role') !== 'comprador') {
        return $this->response->setJSON(['error' => 'Debes iniciar sesión como comprador']);
    }
    $userId = session()->get('userId');
    $cartModel = new CartModel();
    $itemId = $this->request->getPost('item_id');
    if (!$itemId) {
        return $this->response->setJSON(['error' => 'ID del item no proporcionado']);
    }

    // ✅ Corrección: Primero encontrar el item, luego verificar y eliminar
    $item = $cartModel->where('id', $itemId)->where('user_id', $userId)->first();
    if ($item) {
        $cartModel->delete($itemId); // ✅ Esto SÍ elimina de la BD en CI4
    }

    return $this->response->setJSON(['success' => true, 'msg' => 'Producto eliminado del carrito']);
}
}
