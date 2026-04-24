<?php

namespace App\Models;

use Core\Database;
use PDO;

/**
 * Modelo base con acceso a PDO.
 */
abstract class Model
{
    protected static string $table;

    protected static function db(): PDO
    {
        return Database::connection();
    }
}
