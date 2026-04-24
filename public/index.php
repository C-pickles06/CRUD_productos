<?php

declare(strict_types=1);

/**
 * Punto de entrada único de la aplicación.
 * Se ejecuta con: php -S localhost:8000 -t public
 */

// Servir estáticos cuando se usa el servidor embebido de PHP.
if (PHP_SAPI === 'cli-server') {
    $requested = parse_url($_SERVER['REQUEST_URI'] ?? '/', PHP_URL_PATH) ?: '/';
    $file = __DIR__ . $requested;
    if ($requested !== '/' && is_file($file)) {
        return false;
    }
}

use Core\AuthMiddleware;
use Core\Autoloader;
use Core\Request;
use Core\Router;
use Core\Session;
use Core\View;

require __DIR__ . '/../core/Autoloader.php';

Autoloader::register();
Autoloader::addNamespace('Core', __DIR__ . '/../core');
Autoloader::addNamespace('App', __DIR__ . '/../app');

// Prevención: cargar AuthMiddleware por si el autoloader aún no lo ha leído
class_exists(AuthMiddleware::class);

Session::start();

// Reporte de errores para desarrollo; oculto en producción.
$config = require __DIR__ . '/../config/config.php';
if (($config['app']['env'] ?? 'production') === 'development') {
    error_reporting(E_ALL);
    ini_set('display_errors', '1');
} else {
    error_reporting(0);
    ini_set('display_errors', '0');
}

set_exception_handler(function (\Throwable $e) use ($config): void {
    http_response_code(500);
    $isDev = ($config['app']['env'] ?? 'production') === 'development';
    View::display('errors/500', [
        'title' => 'Error',
        'message' => $isDev ? $e->getMessage() . "\n\n" . $e->getTraceAsString() : null,
    ], 'layouts/main');
});

$router = new Router();
$registerRoutes = require __DIR__ . '/../config/routes.php';
$registerRoutes($router);

$router->dispatch(new Request());
