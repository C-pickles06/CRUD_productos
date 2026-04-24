<?php

namespace Core;

/**
 * Permite sólo usuarios autenticados. Redirige a /login en caso contrario.
 */
class AuthMiddleware implements Middleware
{
    public function handle(Request $request): void
    {
        if (!Session::has('user_id')) {
            Session::flash('error', 'Debes iniciar sesión para continuar.');
            header('Location: /login');
            exit;
        }
    }
}
