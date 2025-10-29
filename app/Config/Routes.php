<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Ruta principal - Mostrar formulario de registro
$routes->get('/', 'Home::index');

// Procesar formulario de registro
$routes->post('register', 'Home::register');

// Endpoint para estadísticas básicas
$routes->get('stats', 'Home::stats');

// Health check endpoint
$routes->get('health', function() {
    return service('response')->setJSON([
        'status' => 'healthy',
        'timestamp' => date('Y-m-d H:i:s'),
        'version' => '1.0.0'
    ]);
});

// Rutas de administración
$routes->group('admin', ['namespace' => 'App\Controllers'], function($routes) {
    // Login
    $routes->get('login', 'Admin::login');
    $routes->post('authenticate', 'Admin::authenticate');
    
    // Dashboard protegido
    $routes->get('dashboard', 'Admin::dashboard');
    $routes->get('/', 'Admin::dashboard'); // Alias para /admin
    
    // API endpoints para DataTables
    $routes->post('datatables', 'Admin::datatables');
    $routes->get('view/(:num)', 'Admin::view/$1');
    $routes->delete('delete/(:num)', 'Admin::delete/$1');
    
    // Exportar datos
    $routes->get('export', 'Admin::export');
    
    // Logout
    $routes->get('logout', 'Admin::logout');
});
