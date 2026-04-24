<?php

namespace App\Models;

class Product extends Model
{
    protected static string $table = 'products';

    public static function all(): array
    {
        $stmt = self::db()->query(
            'SELECT id, nombre, descripcion, precio, stock, created_at, updated_at
             FROM products ORDER BY created_at DESC'
        );
        return $stmt->fetchAll();
    }

    public static function find(string $id): ?array
    {
        $stmt = self::db()->prepare(
            'SELECT id, nombre, descripcion, precio, stock, created_at, updated_at
             FROM products WHERE id = :id LIMIT 1'
        );
        $stmt->execute(['id' => $id]);
        $product = $stmt->fetch();
        return $product ?: null;
    }

    public static function create(array $data): string
    {
        $id = self::uuid();
        $stmt = self::db()->prepare(
            'INSERT INTO products (id, nombre, descripcion, precio, stock)
             VALUES (:id, :nombre, :descripcion, :precio, :stock)'
        );
        $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'precio' => $data['precio'],
            'stock' => $data['stock'],
        ]);
        return $id;
    }

    public static function update(string $id, array $data): bool
    {
        $stmt = self::db()->prepare(
            'UPDATE products
             SET nombre = :nombre, descripcion = :descripcion, precio = :precio, stock = :stock
             WHERE id = :id'
        );
        return $stmt->execute([
            'id' => $id,
            'nombre' => $data['nombre'],
            'descripcion' => $data['descripcion'] ?? null,
            'precio' => $data['precio'],
            'stock' => $data['stock'],
        ]);
    }

    public static function delete(string $id): bool
    {
        $stmt = self::db()->prepare('DELETE FROM products WHERE id = :id');
        return $stmt->execute(['id' => $id]);
    }

    private static function uuid(): string
    {
        $data = random_bytes(16);
        $data[6] = chr((ord($data[6]) & 0x0f) | 0x40);
        $data[8] = chr((ord($data[8]) & 0x3f) | 0x80);
        return vsprintf('%s%s-%s-%s-%s-%s%s%s', str_split(bin2hex($data), 4));
    }
}
