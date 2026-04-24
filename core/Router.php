<?php

namespace Core;

/**
 * Router minimalista con soporte de parámetros {id} y middlewares.
 */
class Router
{
    private array $routes = [];

    public function get(string $path, array $action, array $middleware = []): void
    {
        $this->add('GET', $path, $action, $middleware);
    }

    public function post(string $path, array $action, array $middleware = []): void
    {
        $this->add('POST', $path, $action, $middleware);
    }

    public function any(array $methods, string $path, array $action, array $middleware = []): void
    {
        foreach ($methods as $method) {
            $this->add(strtoupper($method), $path, $action, $middleware);
        }
    }

    private function add(string $method, string $path, array $action, array $middleware): void
    {
        $path = '/' . trim($path, '/');
        if ($path === '/') {
            $path = '/';
        }
        $this->routes[] = [
            'method' => $method,
            'path' => $path,
            'action' => $action,
            'middleware' => $middleware,
        ];
    }

    public function dispatch(Request $request): void
    {
        $method = $request->method();
        $uri = $request->uri();

        foreach ($this->routes as $route) {
            if ($route['method'] !== $method) {
                continue;
            }

            $pattern = $this->compile($route['path']);
            if (preg_match($pattern, $uri, $matches)) {
                $params = array_filter(
                    $matches,
                    static fn($k) => !is_int($k),
                    ARRAY_FILTER_USE_KEY
                );

                foreach ($route['middleware'] as $middlewareClass) {
                    /** @var Middleware $mw */
                    $mw = new $middlewareClass();
                    $mw->handle($request);
                }

                [$controllerClass, $methodName] = $route['action'];
                $controller = new $controllerClass();
                $controller->{$methodName}($request, ...array_values($params));
                return;
            }
        }

        $this->notFound();
    }

    private function compile(string $path): string
    {
        $regex = preg_replace('#\{([a-zA-Z_][a-zA-Z0-9_]*)\}#', '(?P<$1>[^/]+)', $path);
        return '#^' . $regex . '$#';
    }

    private function notFound(): void
    {
        http_response_code(404);
        View::display('errors/404', [], 'layouts/main');
    }
}
