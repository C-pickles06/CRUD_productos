<?php
/**
 * Archivo de configuración de ejemplo.
 *
 * Cópialo como config/config.php y ajusta los valores a tu entorno local.
 * El archivo config/config.php está ignorado por Git (ver .gitignore) para
 * no exponer credenciales reales en el repositorio.
 *
 *   cp config/config.example.php config/config.php
 */

return [
    'app' => [
        'name' => 'Proyecto Práctica MVC',
        'base_url' => '/',
        'env' => 'development',
    ],
    'database' => [
        'host' => 'localhost',
        'port' => 3306,
        'name' => 'proyecto_practica',
        'user' => 'root',
        'password' => '',
        'charset' => 'utf8mb4',
    ],
];
