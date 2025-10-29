<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */

// Ruta principal - Mostrar formulario de registro
$routes->get('/', 'Home::index');

// Procesar formulario de registro
$routes->post('/', 'Home::register');

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
