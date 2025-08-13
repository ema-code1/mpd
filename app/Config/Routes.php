<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');
$routes->get('testdb', 'TestController::checkDBConnection');
// Auth
$routes->get('/', 'Auth::login'); // home -> login
$routes->get('register', 'Auth::register');
$routes->post('register-post', 'Auth::registerPost');
$routes->get('login', 'Auth::login');
$routes->post('login-post', 'Auth::loginPost');
$routes->get('logout', 'Auth::logout');

// Dashboards
$routes->get('admin', 'Admin::index');
$routes->get('vendor', 'Vendor::index');

// Ruta para comprobación (ejemplo)
$routes->get('home', 'Home::index');

