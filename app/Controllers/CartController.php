<?php

namespace App\Controllers;
require_once 'C:\xampp\htdocs\mpd\vendor\autoload.php';

use MercadoPago\SDK;   // si estás usando la versión vieja puede variar pero con composer esto funciona
use App\Models\CartModel;
use App\Models\LibroModel;     
use MercadoPago\Preference;
use MercadoPago\Item;
use Exception;
use MercadoPago\Payment;
use App\Models\CheckoutModel;


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

//API
public function checkout()
{
    if (!session()->get('isLoggedIn') || session()->get('role') !== 'comprador') {
        return redirect()->to('/login')->with('error', 'Debes iniciar sesión como comprador.');
    }

    $userId = session()->get('userId');
    $cartModel = new CartModel();
    $libroModel = new LibroModel();

    // Obtener items seleccionados del carrito
    $cartItems = $cartModel->where('user_id', $userId)->where('seleccionado', 1)->findAll();
    
    if (empty($cartItems)) {
        return redirect()->back()->with('error', 'No hay productos seleccionados para comprar.');
    }

    try {
        // Configurar Mercado Pago
        SDK::setAccessToken(env('MERCADOPAGO_ACCESS_TOKEN'));
        
        // Crear preferencia
        $preference = new Preference();
        $items = [];

        foreach ($cartItems as $cartItem) {
            $libro = $libroModel->find($cartItem['libro_id']);
            if (!$libro) continue;
            
            $item = new Item();
            $item->id = (string)$libro['id'];
            $item->title = $libro['titulo'];
            $item->description = substr($libro['descripcion'] ?? 'Libro ' . $libro['titulo'], 0, 250);
            $item->quantity = (int)$cartItem['cantidad'];
            $item->unit_price = (float)$libro['precio'];
            $item->currency_id = 'ARS';
            
            $items[] = $item;

            // Guardar datos para después del pago
            $orderData[] = [
                'libro_id' => $libro['id'],
                'cantidad' => $cartItem['cantidad'],
                'precio_unitario' => $libro['precio']
            ];
        }

        // Verificar que hay items
        if (empty($items)) {
            return redirect()->back()->with('error', 'No se encontraron los libros seleccionados.');
        }

        $preference->items = $items;

        // Configurar URLs de retorno
        $preference->back_urls = [
            'success' => base_url('cart/success'),
            'failure' => base_url('cart/failure'),
            'pending' => base_url('cart/pending')
        ];
        
        $preference->auto_return = 'approved';
        $preference->binary_mode = true; // Importante para evitar pagos pendientes

        // Configurar datos adicionales
        $preference->external_reference = 'ORDER_' . $userId . '_' . time();
        $preference->notification_url = base_url('cart/ipn'); // Opcional: para webhooks

        // Guardar info de la orden en sesión
        session()->set('pending_order', [
            'user_id' => $userId,
            'items' => $orderData,
            'external_reference' => $preference->external_reference
        ]);

        // Guardar preferencia
        $preference->save();

        // DEBUG: Verificar que la preferencia se creó
        if (empty($preference->id)) {
            log_message('error', 'MP Preference ID vacío');
            throw new Exception('No se pudo crear la preferencia de pago');
        }

        if (empty($preference->init_point)) {
            log_message('error', 'MP init_point vacío. Preference ID: ' . ($preference->id ?? 'N/A'));
            throw new Exception('No se generó la URL de pago');
        }

        // Redirigir a Mercado Pago
        return redirect()->to($preference->init_point);

    } catch (Exception $e) {
        log_message('error', 'MP Error: ' . $e->getMessage());
        return redirect()->back()->with('error', 'Error al procesar el pago: ' . $e->getMessage());
    }
}

/**
 * URL de retorno: /cart/success
 * Mercado Pago redirige con parámetros GET: collection_id, collection_status, preference_id, etc.
 * Aquí validamos consultando la API el estado del payment para mayor seguridad.
 */
public function success()
{
    log_message('info', 'Success callback reached - Starting payment verification');
    
    $collection_id = $this->request->getGet('collection_id');
    $collection_status = $this->request->getGet('collection_status');
    $payment_id = $this->request->getGet('payment_id');
    $status = $this->request->getGet('status');
    $external_reference = $this->request->getGet('external_reference');
    
    log_message('info', "MP Callback Params - collection_id: {$collection_id}, collection_status: {$collection_status}, payment_id: {$payment_id}, status: {$status}, external_reference: {$external_reference}");

    // Si no hay collection_id, usar payment_id (a veces Mercado Pago usa uno u otro)
    $paymentId = $collection_id ?: $payment_id;
    
    if (!$paymentId) {
        log_message('error', 'No payment ID found in callback URL');
        return redirect()->to('/cart')->with('error', 'Pago no completado o no se encontró información de pago.');
    }

    // Verificar con la API de Mercado Pago el estado real del payment
    $accessToken = env('MERCADOPAGO_ACCESS_TOKEN');
    
    if (!$accessToken) {
        log_message('error', 'Mercado Pago access token not found');
        return redirect()->to('/cart')->with('error', 'Error de configuración del sistema.');
    }

    SDK::setAccessToken($accessToken);

    $paymentStatus = null;
    $paymentData = null;
    
    try {
        log_message('info', "Verifying payment with ID: {$paymentId}");
        
        // Usar el SDK de Mercado Pago en lugar de cURL para mayor simplicidad
        $payment = \MercadoPago\Payment::find_by_id($paymentId);
        $paymentStatus = $payment->status;
        $paymentData = $payment;
        
        log_message('info', "Payment verification result - Status: {$paymentStatus}");
        
    } catch (\Exception $e) {
        log_message('error', 'MP verify payment error: ' . $e->getMessage());
        
        // Fallback con cURL si el SDK falla
        try {
            $ch = curl_init();
            curl_setopt($ch, CURLOPT_URL, "https://api.mercadopago.com/v1/payments/{$paymentId}");
            curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
            curl_setopt($ch, CURLOPT_HTTPHEADER, [
                "Authorization: Bearer {$accessToken}",
                "Content-Type: application/json"
            ]);
            curl_setopt($ch, CURLOPT_TIMEOUT, 10);
            
            $res = curl_exec($ch);
            $httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
            $curlError = curl_error($ch);
            curl_close($ch);
            
            log_message('info', "cURL fallback - HTTP Code: {$httpCode}, Response: {$res}");
            
            if ($res && $httpCode === 200) {
                $data = json_decode($res, true);
                $paymentStatus = $data['status'] ?? null;
                log_message('info', "cURL fallback success - Payment Status: {$paymentStatus}");
            } else {
                log_message('error', "cURL fallback failed - HTTP: {$httpCode}, Error: {$curlError}");
            }
        } catch (\Exception $curlException) {
            log_message('error', 'cURL fallback also failed: ' . $curlException->getMessage());
        }
    }

    // Si no pudimos verificar el estado, usar el parámetro de la URL como fallback
    if (!$paymentStatus) {
        $paymentStatus = $collection_status ?: $status;
        log_message('warning', "Using URL parameter as fallback - Status: {$paymentStatus}");
    }

    log_message('info', "Final payment status: {$paymentStatus}");

    if ($paymentStatus !== 'approved') {
        $statusMessage = $paymentStatus ?: 'desconocido';
        log_message('warning', "Payment not approved - Status: {$statusMessage}");
        return redirect()->to('/cart')->with('error', 'Pago no aprobado (estado: ' . $statusMessage . ').');
    }

    // PAGO APROBADO - Proceder con el registro de la venta
    $userId = session()->get('userId');
    
    if (!$userId) {
        log_message('error', 'No user ID in session during payment success');
        return redirect()->to('/login')->with('error', 'Sesión expirada. Por favor inicia sesión nuevamente.');
    }

    $cartModel = new \App\Models\CartModel();
    $libroModel = new \App\Models\LibroModel();
    $db = \Config\Database::connect();

    // Obtener items del carrito seleccionados
    $cartItems = $cartModel->where('user_id', $userId)->where('seleccionado', 1)->findAll();
    
    log_message('info', "Cart items found for user {$userId}: " . count($cartItems));
    
    if (empty($cartItems)) {
        log_message('warning', 'No cart items found for completed payment');
        return redirect()->to('/')->with('success', 'Pago aprobado pero no se encontraron items en el carrito.');
    }

    $ventasTable = $db->table('ventas');

    $db->transStart();
    
    try {
        foreach ($cartItems as $ci) {
            $libro = $libroModel->find($ci['libro_id']);
            if (!$libro) {
                log_message('warning', "Book not found: {$ci['libro_id']}");
                continue;
            }
            
            $monto = ((float)$libro['precio']) * ((int)$ci['cantidad']);
            
            $ventaData = [
                'comprador_id' => $userId,
                'libro_id' => $libro['id'],
                'cantidad' => $ci['cantidad'],
                'monto_venta' => $monto,
                'fecha_de_pago' => date('Y-m-d')
            ];
            
            log_message('info', "Registering sale: " . print_r($ventaData, true));
            
            $ventasTable->insert($ventaData);
        }
        
        // Eliminar items comprados del carrito
        $deleteResult = $cartModel->where('user_id', $userId)->where('seleccionado', 1)->delete();
        log_message('info', "Deleted {$deleteResult} items from cart");
        
        $db->transComplete();

        if ($db->transStatus() === false) {
            log_message('error', 'Transaction failed during sale registration');
            return redirect()->to('/cart')->with('error', 'Pago aprobado pero falló el registro de la venta. Revisa logs.');
        }

        log_message('info', 'Payment and sale registration completed successfully');
        return redirect()->to('/')->with('success', 'Pago aprobado y venta registrada. ¡Gracias por tu compra!');

    } catch (\Exception $e) {
        log_message('error', 'Exception during sale registration: ' . $e->getMessage());
        $db->transRollback();
        return redirect()->to('/cart')->with('error', 'Pago aprobado pero ocurrió un error al registrar la venta.');
    }
}

/**
 * Webhook (opcional) - para recibir notificaciones de Mercado Pago.
 * Configura esta URL en tu panel de desarrollador de Mercado Pago o usando ngrok para pruebas locales.
 * Mercado Pago envía un POST JSON con topic/resource/id (dependiendo la versión).
 */
public function webhook()
{
    // Obtener raw body
    $raw = $this->request->getBody();
    log_message('info', 'MP webhook payload: ' . $raw);

    // Aquí deberías procesar según el payload de MP (consultar la API para el id recibido)
    // Ejemplo simple: responder 200 para que Mercado Pago no reintente
    return $this->response->setStatusCode(200)->setBody('OK');
}



}
