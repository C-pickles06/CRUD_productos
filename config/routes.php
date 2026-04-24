<?php

use App\Controllers\AuthController;
use App\Controllers\HomeController;
use App\Controllers\ProductController;
use Core\AuthMiddleware;
use Core\Router;

/**
 * Registro de rutas de la aplicación.
 *
 * @param Router $router
 */
return static function (Router $router): void {
    // Público
    $router->get('/', [HomeController::class, 'index']);
    $router->get('/login', [AuthController::class, 'showLogin']);
    $router->post('/login', [AuthController::class, 'login']);
    $router->post('/logout', [AuthController::class, 'logout']);

    // Protegido por AuthMiddleware
    $auth = [AuthMiddleware::class];

    $router->get('/products', [ProductController::class, 'index'], $auth);
    $router->get('/products/create', [ProductController::class, 'create'], $auth);
    $router->post('/products', [ProductController::class, 'store'], $auth);
    $router->get('/products/{id}/edit', [ProductController::class, 'edit'], $auth);
    $router->post('/products/{id}/update', [ProductController::class, 'update'], $auth);
    $router->post('/products/{id}/delete', [ProductController::class, 'destroy'], $auth);
};
