    <?php

    use CodeIgniter\Router\RouteCollection;

    /**
     * @var RouteCollection $routes
     */

    // -----------------------------
    // 🔐 RUTA PRINCIPAL: /panel
    // -----------------------------
    // Aquí llegan todos los usuarios autenticados (admin o comprador)
    $routes->get('/panel', function () {
        $session = session();
        if (! $session->get('isLoggedIn')) {
            return redirect()->to('/login');
        }

        if ($session->get('role') === 'administrador') {
            $controller = new \App\Controllers\Admin();
            return $controller->index();
        } else {
            $controller = new \App\Controllers\Home();
            return $controller->index();
        }
    });

    // -----------------------------
    // 🔒 BLOQUEO DE RUTAS OBSOLETAS
    // -----------------------------
    $routes->get('/admin', function () {
        return redirect()->to('/panel');
    });
    $routes->get('/home', function () {
        return redirect()->to('/panel');
    });

    // -----------------------------
    // 🔐 RUTAS DE AUTENTICACIÓN
    // -----------------------------
    $routes->get('/register', 'Auth::register');
    $routes->post('/register-post', 'Auth::registerPost');
    $routes->get('/login', 'Auth::login');
    $routes->post('/login-post', 'Auth::loginPost');
    $routes->get('/logout', 'Auth::logout');

    // -----------------------------
    // 🏠 RUTA PRINCIPAL (página pública)
    // -----------------------------
    $routes->get('/', 'Home::index');

    // -----------------------------
    // 🔐 RUTAS PARA ADMINISTRADORES (protegidas)
    // -----------------------------
    $routes->get('upload_book', 'LibroController::crear', ['filter' => 'admin']);
    $routes->post('crearLibro', 'LibroController::crearLibro', ['filter' => 'admin']);
    $routes->get('admin_home', 'AdminController::admin_home', ['filter' => 'admin']);



    // Ruta para ver detalle de c/libro
    $routes->get('libro/(:num)', 'LibroController::detalles/$1');




    // Ruta para editar libros
    $routes->get('libro/editar/(:num)', 'LibroController::editar/$1', ['filter' => 'admin']);
    $routes->post('libro/actualizar/(:num)', 'LibroController::actualizar/$1', ['filter' => 'admin']);
    $routes->match(['delete', 'post'], 'libro/eliminar/(:num)', 'LibroController::eliminar/$1', ['filter' => 'admin']);


    $routes->post('libro/eliminar_imagen/(:num)', 'LibroController::eliminarImagen/$1', ['filter' => 'admin']);

    // Rutas para el carrito
    $routes->get('cart', 'CartController::index');
    $routes->post('cart/add/(:num)', 'CartController::add/$1');
    $routes->post('cart/update', 'CartController::update');
    $routes->post('cart/delete', 'CartController::delete');

    $routes->get('stock_spreadsheet', 'StockController::index', ['filter' => 'admin']);

    // funciones para crear y modificar columnas de ingresos y egresos

    $routes->get('stock', 'StockController::index');
    $routes->post('stock/createColumn', 'StockController::createColumn');
    $routes->post('stock/updateCell', 'StockController::updateCell');
    $routes->post('stock/getStock', 'StockController::getStock');
    $routes->post('stock/deleteColumn', 'StockController::deleteColumn');
    $routes->post('stock/toggleLock', 'StockController::toggleLock'); // 🔒 NUEVA RUTA

    $routes->get('/perfil', 'Perfil::index');
    $routes->post('/perfil/actualizar', 'Perfil::actualizar');
    $routes->post('/perfil/uploadTempImage', 'Perfil::uploadTempImage');

    $routes->post('libro/agregarResena', 'LibroController::agregarResena');
    $routes->get('libro/editarResena/(:num)', 'LibroController::editarResena/$1');
    $routes->post('libro/actualizarResena/(:num)', 'LibroController::actualizarResena/$1');
    $routes->post('libro/eliminarResena/(:num)', 'LibroController::eliminarResena/$1');



// Movimientos - Ver listado (solo admin)
$routes->get('movimientos', 'MovimientosController::index', ['filter' => 'admin']);

// Movimientos - Procesar compra (compradores)
$routes->post('movimientos/procesarCompra', 'MovimientosController::procesarCompra');

// Movimientos - Detalles AJAX (admin)
$routes->get('movimientos/detalles/(:num)', 'MovimientosController::detalles/$1', ['filter' => 'admin']);

// Movimientos - Set payment method (compradores)
$routes->post('movimientos/set_payment_method', 'MovimientosController::set_payment_method');


// ===== CHECKOUT =====
$routes->get('checkout', 'CheckoutController::index');
$routes->post('cart/checkout', 'CheckoutController::procesar');
$routes->get('checkout/json/(:num)', 'CheckoutController::detalleJson/$1');

// ===== CHECKOUT (GET) =====
$routes->get('checkout', 'CheckoutController::index');
$routes->get('checkout/json/(:num)', 'CheckoutController::detalleJson/$1');

// ===== CART CHECKOUT (POST) =====
$routes->post('cart/checkout', 'CheckoutController::procesar');

// ===== API CHECKOUT =====
$routes->post('api/checkout/crear', 'ApiCheckoutController::crear');
$routes->get('api/checkout/detalle/(:num)', 'ApiCheckoutController::detalle/$1');
    // -----------------------------
    // 🚨 ÚLTIMA RUTA: maneja cualquier otra URL
    // -----------------------------
    $routes->get('(:any)', 'Home::index/$1');
