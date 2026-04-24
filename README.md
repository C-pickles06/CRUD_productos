# Proyecto Práctica — PHP MVC (POO, sin frameworks)

Aplicación web en PHP puro con arquitectura **MVC**, **POO** y autoloading manual
estilo PSR-4 (sin Composer). Incluye autenticación con sesiones seguras, CRUD de
productos, protección CSRF y consultas con PDO preparadas.

## 📦 Stack

- PHP 8.1+
- MySQL 5.7+ / MariaDB 10+
- HTML + CSS básico (sin frameworks)

## 📁 Estructura

```
proyecto_practica/
├── app/
│   ├── Controllers/
│   │   ├── AuthController.php
│   │   ├── HomeController.php
│   │   └── ProductController.php
│   ├── Models/
│   │   ├── Model.php
│   │   ├── Product.php
│   │   └── User.php
│   └── Views/
│       ├── auth/login.php
│       ├── errors/{404,500}.php
│       ├── layouts/main.php
│       └── products/{index,create,edit,_form}.php
├── core/
│   ├── Autoloader.php
│   ├── AuthMiddleware.php
│   ├── Controller.php
│   ├── Csrf.php
│   ├── Database.php
│   ├── Middleware.php
│   ├── Request.php
│   ├── Router.php
│   ├── Session.php
│   ├── Validator.php
│   └── View.php
├── config/
│   ├── config.php
│   └── routes.php
├── database/
│   └── schema.sql
├── public/
│   ├── .htaccess
│   ├── css/app.css
│   └── index.php
└── README.md
```

## 🚀 Instalación

### 1. Crear la base de datos

```bash
mysql -u root -p < database/schema.sql
```

El script crea la base `proyecto_practica`, las tablas `users` y `products`,
y carga datos de prueba.

### 2. Configurar credenciales

Edita `config/config.php` y ajusta la sección `database`:

```php
'database' => [
    'host' => 'localhost',
    'port' => 3306,
    'name' => 'proyecto_practica',
    'user' => 'root',
    'password' => '',
    'charset' => 'utf8mb4',
],
```

### 3. Levantar el servidor embebido

```bash
php -S localhost:8000 -t public
```

Abre: http://localhost:8000

### 4. Iniciar sesión

- Usuario: `admin`
- Contraseña: `admin123`

## 🔐 Seguridad

- Contraseñas hasheadas con `password_hash` (bcrypt).
- Sesiones con cookies `HttpOnly` + `SameSite=Lax` y regeneración de ID al
  autenticarse.
- Tokens CSRF en todos los formularios POST.
- Consultas con PDO preparadas (previene SQL Injection).
- Salida escapada con `htmlspecialchars` en vistas (previene XSS).

## 🧱 Arquitectura

- **Routing**: `core/Router.php` con soporte de parámetros `{id}` y middlewares.
- **Middleware**: `core/AuthMiddleware.php` protege las rutas bajo `/products`.
- **MVC**: controladores en `app/Controllers`, modelos en `app/Models`, vistas
  en `app/Views` con layout en `app/Views/layouts/main.php`.
- **Autoloader**: `core/Autoloader.php` mapea `Core\` y `App\` a sus carpetas.
- **Validación**: `core/Validator.php` con reglas estilo Laravel
  (`required|min:3|numeric`...).

## 🗺️ Rutas

| Método | Ruta                       | Descripción                |
|--------|----------------------------|----------------------------|
| GET    | `/`                        | Redirige según sesión      |
| GET    | `/login`                   | Formulario de login        |
| POST   | `/login`                   | Procesar login             |
| POST   | `/logout`                  | Cerrar sesión              |
| GET    | `/products`                | Listar productos           |
| GET    | `/products/create`         | Formulario crear           |
| POST   | `/products`                | Guardar nuevo              |
| GET    | `/products/{id}/edit`      | Formulario editar          |
| POST   | `/products/{id}/update`    | Guardar cambios            |
| POST   | `/products/{id}/delete`    | Eliminar                   |

## 📝 Convenciones

- Clases en **PascalCase** (`ProductController`).
- Métodos en **camelCase** (`findByUsername`).
- Tablas en plural (`products`, `users`), modelos en singular (`Product`, `User`).
- Un archivo por clase; ruta del archivo = namespace.
