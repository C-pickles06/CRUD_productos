-- Script de creación de la base de datos y tablas
-- Ejecutar con: mysql -u root -p < database/schema.sql

CREATE DATABASE IF NOT EXISTS proyecto_practica
    CHARACTER SET utf8mb4
    COLLATE utf8mb4_unicode_ci;

USE proyecto_practica;

-- Tabla de usuarios
DROP TABLE IF EXISTS users;
CREATE TABLE users (
    id CHAR(36) DEFAULT (UUID()) PRIMARY KEY,
    username VARCHAR(50) NOT NULL UNIQUE,
    password VARCHAR(255) NOT NULL,
    name VARCHAR(100) NOT NULL,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Tabla de productos
DROP TABLE IF EXISTS products;
CREATE TABLE products (
    id CHAR(36) DEFAULT (UUID()) PRIMARY KEY,
    nombre VARCHAR(150) NOT NULL,
    descripcion TEXT NULL,
    precio DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    stock INT NOT NULL DEFAULT 0,
    created_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP,
    updated_at DATETIME NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;

-- Usuario de prueba: admin / admin123
-- Hash generado con password_hash('admin123', PASSWORD_DEFAULT)
INSERT INTO users (username, password, name) VALUES
('admin', '$2y$12$D0c96G.RYJPanfaC/QKjVuOFKGab61Ygk8wEXBUU/uz5ntCfzexkq', 'Administrador');

-- Productos de prueba
INSERT INTO products (nombre, descripcion, precio, stock) VALUES
('Teclado mecánico', 'Teclado mecánico retroiluminado RGB', 120.50, 15),
('Mouse inalámbrico', 'Mouse óptico 2.4GHz con batería recargable', 45.00, 30),
('Monitor 24 pulgadas', 'Monitor LED Full HD 1080p 75Hz', 320.99, 8),
('Audífonos Bluetooth', 'Audífonos con cancelación de ruido activa', 89.90, 25),
('Disco SSD 1TB', 'Unidad de estado sólido SATA III', 95.00, 20);
