<?php

namespace Core;

/**
 * Contrato base para middlewares.
 * Cada middleware decide si deja pasar la petición o corta el flujo.
 */
interface Middleware
{
    public function handle(Request $request): void;
}
