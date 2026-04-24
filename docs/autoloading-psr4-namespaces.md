# 📚 Autoloading, PSR-4 y Namespaces en PHP

Guía práctica sobre tres conceptos fundamentales que hacen posible organizar
proyectos PHP modernos como este (`proyecto_practica`), sin escribir `require`
manualmente en cada archivo.

---

## 1. 🏷️ Namespaces

### ¿Qué es un namespace?

Un **namespace** (espacio de nombres) es un mecanismo para **agrupar clases,
funciones y constantes bajo un nombre jerárquico**, evitando colisiones de
nombres cuando dos clases distintas se llaman igual.

Piensa en un namespace como una "carpeta lógica" dentro de tu código.

### Problema que resuelve

Sin namespaces, si tienes dos clases `User` (una del sistema de autenticación y
otra de un módulo de administración), PHP lanzaría un error:

```
Fatal error: Cannot declare class User, because the name is already in use
```

### Sintaxis

**Declarar** un namespace (debe ser la primera línea del archivo, tras `<?php`):

```php
<?php

namespace App\Models;

class User
{
    // ...
}
```

La clase completa se llama ahora `App\Models\User`.

**Usar** una clase de otro namespace — dos opciones:

```php
// Opción A: con nombre completo (FQN = Fully Qualified Name)
$user = new \App\Models\User();

// Opción B: importando con use
use App\Models\User;
$user = new User();
```

### Ejemplo real del proyecto

En `app/Controllers/AuthController.php`:

```php
<?php

namespace App\Controllers;          // ← namespace del archivo

use App\Models\User;                // ← importa clase de otro namespace
use Core\Controller;                // ← clase base del framework
use Core\Session;

class AuthController extends Controller
{
    public function login(Request $request): void
    {
        $user = User::findByUsername($username);   // ← gracias al "use"
        // ...
    }
}
```

### Convenciones

| Elemento        | Convención         | Ejemplo                 |
|-----------------|--------------------|-------------------------|
| Separador       | Barra invertida `\` | `App\Models\User`       |
| Segmentos       | PascalCase         | `App`, `Controllers`    |
| Raíz de app     | Nombre del proyecto| `App`, `MyCompany`      |
| Un namespace    | Una carpeta        | `App\Models` → `app/Models/` |

---

## 2. 🔄 Autoloading

### ¿Qué es autoloading?

Es el mecanismo que **carga automáticamente el archivo PHP correspondiente a
una clase** la primera vez que se usa, sin que tengas que escribir `require` o
`include` manualmente.

### Antes del autoloading (la forma vieja)

```php
require 'core/Database.php';
require 'core/Session.php';
require 'core/Router.php';
require 'app/Models/User.php';
require 'app/Models/Product.php';
require 'app/Controllers/AuthController.php';
// ... cientos de líneas más
```

Problemas:
- Orden de carga importa (clase padre antes que hija).
- Cargas archivos que quizá no vas a usar.
- Mantenimiento insostenible en proyectos grandes.

### Con autoloading (la forma moderna)

```php
require 'core/Autoloader.php';
Autoloader::register();

$user = new App\Models\User();   // ← PHP carga el archivo automáticamente
```

### ¿Cómo funciona internamente?

PHP ofrece la función `spl_autoload_register()` que recibe una función
callback. **Cada vez que PHP encuentra una clase que no está cargada**, llama a
esa función pasándole el nombre de la clase. Tú decides cómo encontrarla y
cargarla.

Esquema:

```
new App\Models\User()
        ↓
PHP: "no conozco App\Models\User"
        ↓
Llama al autoloader registrado → le pasa "App\Models\User"
        ↓
Autoloader calcula ruta → app/Models/User.php
        ↓
require ese archivo
        ↓
La clase ya está disponible
```

### Ejemplo mínimo

```php
spl_autoload_register(function (string $class): void {
    $file = __DIR__ . '/' . str_replace('\\', '/', $class) . '.php';
    if (is_file($file)) {
        require $file;
    }
});
```

Con eso, `new App\Models\User()` buscaría `App/Models/User.php`.

---

## 3. 📐 PSR-4

### ¿Qué es PSR-4?

**PSR-4** es un estándar de la **PHP-FIG (PHP Framework Interop Group)** que
define **cómo mapear namespaces a rutas de archivos** para autoloading.

Es la convención que siguen **todos los frameworks modernos**: Laravel,
Symfony, etc. Si respetas PSR-4, Composer (u otro autoloader) puede cargar tu
código sin configuración especial.

### Reglas principales

1. Un **namespace prefix** (ej: `App\`) se mapea a una **carpeta base**
   (ej: `app/`).
2. La parte del nombre que viene **después del prefix** se traduce 1:1 a
   subcarpetas.
3. Los `\` del namespace se convierten en separadores de directorio `/`.
4. El nombre de la clase coincide exactamente con el nombre del archivo (con
   `.php` al final) — **sensible a mayúsculas**.

### Ejemplo visual

Con el mapeo `App\` → `app/`:

| Clase completa              | Archivo físico                  |
|-----------------------------|---------------------------------|
| `App\Models\User`           | `app/Models/User.php`           |
| `App\Models\Product`        | `app/Models/Product.php`        |
| `App\Controllers\AuthController` | `app/Controllers/AuthController.php` |
| `App\Services\Mail\Sender`  | `app/Services/Mail/Sender.php`  |

Con el mapeo `Core\` → `core/`:

| Clase completa      | Archivo físico       |
|---------------------|----------------------|
| `Core\Router`       | `core/Router.php`    |
| `Core\Database`     | `core/Database.php`  |

### El algoritmo PSR-4 paso a paso

Para la clase `App\Models\User`:

1. Buscar un prefix registrado que encaje → `App\` → carpeta `app/`
2. Quitar el prefix → queda `Models\User`
3. Reemplazar `\` por `/` → `Models/User`
4. Añadir `.php` → `Models/User.php`
5. Concatenar con la carpeta base → `app/Models/User.php`
6. `require` ese archivo

---

## 4. 🔧 Cómo está implementado en este proyecto

### Archivo `core/Autoloader.php`

```php
namespace Core;

class Autoloader
{
    private static array $prefixes = [];

    public static function register(): void
    {
        spl_autoload_register([self::class, 'load']);
    }

    public static function addNamespace(string $prefix, string $baseDir): void
    {
        $prefix = trim($prefix, '\\') . '\\';
        $baseDir = rtrim($baseDir, DIRECTORY_SEPARATOR) . DIRECTORY_SEPARATOR;
        self::$prefixes[$prefix] = $baseDir;
    }

    public static function load(string $class): void
    {
        foreach (self::$prefixes as $prefix => $baseDir) {
            $len = strlen($prefix);
            if (strncmp($prefix, $class, $len) !== 0) {
                continue;   // ← este prefix no encaja, probar el siguiente
            }

            $relative = substr($class, $len);                           // ← quita el prefix
            $file = $baseDir . str_replace('\\', DIRECTORY_SEPARATOR, $relative) . '.php';

            if (is_file($file)) {
                require_once $file;
                return;
            }
        }
    }
}
```

### Registro en `public/index.php`

```php
require __DIR__ . '/../core/Autoloader.php';

Autoloader::register();                                   // ← activa el callback
Autoloader::addNamespace('Core', __DIR__ . '/../core');   // ← Core\  → /core
Autoloader::addNamespace('App',  __DIR__ . '/../app');    // ← App\   → /app
```

A partir de ahí, cualquier `new App\...` o `new Core\...` carga solo su archivo
cuando se necesita — ni antes, ni después.

---

## 5. 🎯 Beneficios combinados

| Concepto     | Qué aporta                                                    |
|--------------|---------------------------------------------------------------|
| Namespace    | Evita colisiones; organiza el código jerárquicamente          |
| Autoloading  | Elimina los `require` manuales; carga perezosa (lazy)         |
| PSR-4        | Estándar compartido: todas las herramientas PHP lo entienden  |

**Los tres juntos** permiten que un proyecto tenga cientos de clases y el
punto de entrada (`public/index.php`) solo necesite **registrar el autoloader
una vez**. El resto es invisible.

---

## 6. ⚡ Diferencias con Composer

Composer es la herramienta oficial de PHP para gestionar dependencias.
Entre otras cosas, genera un autoloader PSR-4 automático.

| Aspecto                    | Autoloader manual (este proyecto) | Composer                    |
|----------------------------|-----------------------------------|-----------------------------|
| Configuración              | `addNamespace()` en código PHP    | Sección `autoload` en `composer.json` |
| Regeneración               | Automática al llamar `load()`     | `composer dump-autoload`    |
| Optimizaciones             | Básicas                           | Classmap, APCu, preload     |
| Librerías de terceros      | Manual                            | `composer require vendor/pkg` |
| Útil para aprender         | ✅ Sí                             | Abstrae todo                |

Este proyecto implementa un autoloader manual con fines **didácticos**. En
proyectos profesionales, lo normal es usar Composer.

---

## 7. 📌 Recordatorio de errores típicos

| Error                                          | Causa común                                    |
|------------------------------------------------|------------------------------------------------|
| `Class 'App\Models\User' not found`            | Archivo mal nombrado o autoloader no registrado |
| `Cannot declare class X, because the name is already in use` | Falta un namespace          |
| Funciona en Linux, falla en Windows/Mac        | Mayúsculas: `user.php` ≠ `User.php`            |
| Autoloader no encuentra la clase               | Prefix mal registrado o carpeta incorrecta     |

---

## 🔗 Referencias

- [PSR-4: Autoloader — PHP-FIG](https://www.php-fig.org/psr/psr-4/)
- [PHP Manual: Namespaces](https://www.php.net/manual/es/language.namespaces.php)
- [PHP Manual: spl_autoload_register](https://www.php.net/manual/es/function.spl-autoload-register.php)
