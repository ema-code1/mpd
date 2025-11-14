<?php

namespace App\Controllers;

use App\Models\CartModel;
use App\Models\LibroModel;
use App\Models\VentasMaestroModel;
use App\Models\VentasDetalleModel;

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
                $item = array_merge($item, $libro);
            }
        }

        // Pasar los datos a la vista
        $data['cartItems'] = $cartItems;
        echo view('templates/header');
        echo view('cart', $data);
    }

    public function add($libro)
    {
        $debug = [];
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
                    $cartModel->where('user_id', $userId)->where('libro_id', $libro)->delete();
                } else {
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
                    'seleccionado' => 0
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

    public function update()
    {
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
            return $this->response->setJSON(['error' => 'No autorizado']);
        }

        $userId = session()->get('userId');
        $cartModel = new CartModel();

        $libroId = $this->request->getPost('libro_id');
        $action = $this->request->getPost('action');
        $value = $this->request->getPost('value');

        if (!$libroId) {
            return $this->response->setJSON(['error' => 'libro_id no proporcionado']);
        }

        $item = $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->first();
        if (!$item) {
            return $this->response->setJSON(['error' => 'Item no encontrado']);
        }

        if ($action === 'cantidad') {
            $value = max(1, (int)$value);
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

        $item = $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->first();
        if (!$item) {
            return $this->response->setJSON(['error' => 'Item no encontrado o no autorizado']);
        }

        $cartModel->where('user_id', $userId)->where('libro_id', $libroId)->delete();

        return $this->response->setJSON(['success' => true, 'msg' => 'Producto eliminado del carrito']);
    }

    public function checkout()
    {
        // Verificar sesión
        if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
            return redirect()->to('/login')->with('error', 'Debes iniciar sesión como comprador.');
        }

        $userId = session()->get('userId');
        $metPago = $this->request->getPost('payment_method');
        $comprobante = null;

        // Validar método de pago
        if (!$metPago || !in_array($metPago, ['efectivo', 'transferencia'])) {
            return redirect()->back()->with('error', 'Debes seleccionar un método de pago válido.');
        }

        // Si es transferencia, validar y procesar archivo
        if ($metPago === 'transferencia') {
            $file = $this->request->getFile('comprobante');

            if (!$file || $file->getError() === UPLOAD_ERR_NO_FILE) {
                return redirect()->back()->with('error', 'Para transferencia, debes adjuntar un comprobante.');
            }

            if ($file->getError() !== UPLOAD_ERR_OK) {
                return redirect()->back()->with('error', 'Error al cargar el archivo. Intenta de nuevo.');
            }

            // Validar tipo de archivo
            $allowedMimes = ['image/jpeg', 'image/png', 'application/pdf', 'image/jpg'];
            if (!in_array($file->getMimeType(), $allowedMimes)) {
                return redirect()->back()->with('error', 'Solo se permiten: JPG, PNG, PDF.');
            }

            // Validar tamaño (máx 5MB)
            if ($file->getSize() > 5242880) {
                return redirect()->back()->with('error', 'El archivo no debe superar 5MB.');
            }

            // Generar nombre aleatorio con prefijo
            $randomName = 'Cpnte_' . bin2hex(random_bytes(8));
            $ext = $file->getClientExtension();
            $comprobante = 'comprobantes/' . $randomName . '.' . $ext;

            // Mover archivo a la carpeta writable
            if (!$file->move(WRITEPATH . 'comprobantes', $randomName . '.' . $ext)) {
                return redirect()->back()->with('error', 'No se pudo guardar el comprobante.');
            }
        }

        // Obtener items seleccionados del carrito
        $cartModel = new CartModel();
        $libroModel = new LibroModel();
        $cartItems = $cartModel->where('user_id', $userId)
                               ->where('seleccionado', 1)
                               ->findAll();

        if (empty($cartItems)) {
            return redirect()->back()->with('error', 'Debes seleccionar al menos un producto.');
        }

        // Calcular total y obtener detalles de libros
        $total = 0;
        $detalles = [];

        foreach ($cartItems as $item) {
            $libro = $libroModel->find($item['libro_id']);
            if ($libro) {
                $subtotal = $libro['precio'] * $item['cantidad'];
                $total += $subtotal;

                $detalles[] = [
                    'libro_id' => $item['libro_id'],
                    'cantidad' => $item['cantidad'],
                    'precio_unitario' => $libro['precio'],
                    'subtotal' => $subtotal
                ];
            }
        }

        // Iniciar transacción
        $db = \Config\Database::connect();
        $db->transStart();

        try {
            // Insertar en ventas_maestro
            $ventasMaestroModel = new VentasMaestroModel();
            $ventaData = [
                'comprador_id' => $userId,
                'fecha_de_pago' => date('Y-m-d'),
                'total_venta' => $total,
                'comprobante' => $comprobante,
                'met_pago' => $metPago,
                'estado' => 'pendiente'
            ];

            $ventasMaestroModel->insert($ventaData);
            $ventaId = $ventasMaestroModel->getInsertID();

            // Insertar detalles en ventas_detalle
            $ventasDetalleModel = new VentasDetalleModel();
            foreach ($detalles as $detalle) {
                $detalle['venta_id'] = $ventaId;
                $ventasDetalleModel->insert($detalle);
            }

            // Eliminar items seleccionados del carrito
            foreach ($cartItems as $item) {
                $cartModel->where('user_id', $userId)
                          ->where('libro_id', $item['libro_id'])
                          ->delete();
            }

            $db->transComplete();

            if ($db->transStatus() === false) {
                throw new \Exception('Error en la transacción de base de datos.');
            }

            return redirect()->to('/cart')->with('success', '¡Compra realizada con éxito! ID de venta: ' . $ventaId);

        } catch (\Exception $e) {
            $db->transRollback();
            return redirect()->back()->with('error', 'Error al procesar la compra: ' . $e->getMessage());
        }
    }
}