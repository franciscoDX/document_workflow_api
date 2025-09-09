<?php

use CodeIgniter\Router\RouteCollection;

/**
 * @var RouteCollection $routes
 */
$routes->get('/', 'Home::index');

$routes->group('api', function ($routes) {
    
    $routes->group('auth', function ($routes) {
        $routes->post('login', 'API\AuthController::login');

        
        $routes->get('me', 'API\AuthController::me', ['filter' => 'jwtauth']);
    });

    
});