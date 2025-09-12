<?php
use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// -----------------------------
// 🔐 RUTA PRINCIPAL: /panel
// -----------------------------
// Aquí llegan todos los usuarios autenticados (admin o comprador)
$routes->get('/panel', function() {
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
$routes->get('/admin', function() {
    return redirect()->to('/panel');
});
$routes->get('/home', function() {
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




$routes->get('libro/(:num)', 'LibroController::detalles/$1', ['filter' => 'admin']);




// Agregar esta ruta para editar libros
$routes->get('libro/editar/(:num)', 'LibroController::editar/$1');
$routes->post('libro/actualizar/(:num)', 'LibroController::actualizar/$1');
$routes->match(['delete', 'post'], 'libro/eliminar/(:num)', 'LibroController::eliminar/$1');


$routes->post('libro/eliminar_imagen/(:num)', 'LibroController::eliminarImagen/$1');

// -----------------------------
// 🛠️ OTRAS RUTAS (si las necesitas en el futuro)
// -----------------------------
// $routes->get('home', 'Home::index'); // ya está en (:any)

// -----------------------------
// 🚨 ÚLTIMA RUTA: maneja cualquier otra URL
// -----------------------------
$routes->get('(:any)', 'Home::index/$1');