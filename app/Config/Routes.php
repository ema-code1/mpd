<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Ruta principal para usuarios autenticados
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

// Bloqueo de rutas sensibles
$routes->get('/admin', function() {
    return redirect()->to('/panel');
});

$routes->get('/home', function() {
    return redirect()->to('/panel');
});

// Rutas de autenticación
$routes->get('/register', 'Auth::register');
$routes->post('/register-post', 'Auth::registerPost');
$routes->get('/login', 'Auth::login');
$routes->post('/login-post', 'Auth::loginPost');
$routes->get('/logout', 'Auth::logout');

// Ruta por defecto
$routes->get('/', 'Home::index');
