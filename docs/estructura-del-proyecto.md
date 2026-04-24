# 🗂️ Estructura del proyecto

Guía completa de la organización de carpetas y archivos de
`proyecto_practica`, explicando el propósito de cada pieza.

---

## 🌳 Árbol general

```
proyecto_practica/
├── app/                   ← Código de la aplicación (MVC)
│   ├── Controllers/       ← Controladores (lógica de cada ruta)
│   ├── Models/            ← Modelos (acceso a datos)
│   └── Views/             ← Vistas (plantillas HTML)
│       ├── auth/
│       ├── errors/
│       ├── layouts/
│       └── products/
├── core/                  ← Núcleo del "mini-framework"
├── config/                ← Configuración y registro de rutas
├── database/              ← Scripts SQL
├── docs/                  ← Documentación del proyecto
├── public/                ← Punto de entrada público (webroot)
│   └── css/
└── README.md              ← Guía de instalación y uso
```

**Principio de diseño:** sólo la carpeta `public/` debe ser accesible desde la
web. Todo lo demás queda fuera del alcance de peticiones HTTP directas.

---

## 📁 `/app` — Código específico de la aplicación

Todo el código **de negocio** vive aquí. Namespace raíz: `App\`.

### 📁 `/app/Controllers`

Reciben la petición, orquestan modelos y vistas, devuelven la respuesta.
Siguen la convención **un controlador por recurso**.

| Archivo                  | Propósito |
|--------------------------|-----------|
| `HomeController.php`     | Ruta `/` — redirige a login o productos según haya sesión |
| `AuthController.php`     | Login, logout y muestra del formulario de sesión |
| `ProductController.php`  | CRUD completo de productos (7 acciones: index/create/store/edit/update/destroy) |

**Patrón común** en cada acción:
1. Validar CSRF si es POST.
2. Validar datos con `Core\Validator`.
3. Llamar al modelo (`Product::create`, `User::findByUsername`, ...).
4. Renderizar vista o redirigir.

---

### 📁 `/app/Models`

Representan entidades del dominio y encapsulan el acceso a la base de datos.
Un modelo = una tabla (en singular: tabla `users` → modelo `User`).

| Archivo         | Propósito |
|-----------------|-----------|
| `Model.php`     | Clase base abstracta. Expone `db()` para obtener la conexión PDO. |
| `User.php`      | Consulta `users`. Métodos: `findByUsername`, `findById` |
| `Product.php`   | CRUD de `products`. Métodos: `all`, `find`, `create`, `update`, `delete`. Genera UUID v4 al crear. |

**Reglas:** los modelos **no** conocen HTTP, sesión ni vistas. Solo trabajan
con datos.

---

### 📁 `/app/Views`

Plantillas PHP que generan HTML. Separadas por recurso.

| Ruta                         | Propósito |
|------------------------------|-----------|
| `layouts/main.php`           | Layout maestro: `<head>`, topbar, footer, contiene `$content` |
| `auth/login.php`             | Formulario de inicio de sesión con token CSRF |
| `products/index.php`         | Listado tabular con botones Editar/Eliminar |
| `products/create.php`        | Formulario "nuevo producto" |
| `products/edit.php`          | Formulario "editar producto" |
| `products/_form.php`         | Parcial compartido entre create y edit (campos del formulario) |
| `errors/404.php`             | Página de "no encontrado" |
| `errors/500.php`             | Página de error interno (muestra stack trace en modo dev) |

**Convención:** archivos que empiezan con `_` son parciales (fragmentos
reutilizables incluidos con `require`).

---

## 📁 `/core` — Núcleo reutilizable

Clases genéricas que podrían servir para otro proyecto. Namespace raíz: `Core\`.
Hace el papel de un "mini-framework".

| Archivo               | Propósito |
|-----------------------|-----------|
| `Autoloader.php`      | Carga automática de clases estilo PSR-4 sin Composer. Ver `docs/autoloading-psr4-namespaces.md` |
| `Database.php`        | Singleton que crea y cachea la conexión PDO a MySQL |
| `Router.php`          | Registra rutas (`get`/`post`), resuelve parámetros `{id}`, aplica middlewares, despacha al controlador |
| `Request.php`         | Encapsula la petición: método HTTP, URI, `input()` para leer GET/POST |
| `Controller.php`      | Clase base con helpers: `view()`, `redirect()`, `back()`, `json()` |
| `View.php`            | Renderiza plantillas PHP con layout. Incluye helper `e()` para escapar HTML |
| `Session.php`         | Arranca sesión con cookies seguras; maneja `get/set/flash/destroy/regenerate` |
| `Csrf.php`            | Genera token aleatorio por sesión, método `field()` imprime el hidden input, `verify()` compara con `hash_equals` |
| `Validator.php`       | Validación por reglas estilo Laravel (`required|min:3|numeric`) |
| `Middleware.php`      | **Interfaz** — contrato `handle(Request $request): void` |
| `AuthMiddleware.php`  | Implementación: redirige a `/login` si no hay sesión. Se aplica a rutas protegidas |

---

## 📁 `/config` — Configuración

| Archivo        | Propósito |
|----------------|-----------|
| `config.php`   | Array de configuración: nombre de app, entorno (`development`/`production`), credenciales de BD |
| `routes.php`   | Callable que recibe el `Router` y registra todas las rutas de la app. Separar rutas en su propio archivo es práctica limpia de frameworks modernos |

**Cómo se usan:** `public/index.php` hace `require` de `config.php` (para
setup) y `routes.php` (para registrar rutas).

---

## 📁 `/database` — Scripts SQL

| Archivo       | Propósito |
|---------------|-----------|
| `schema.sql`  | Crea BD `proyecto_practica`, tablas `users` y `products` (con PK UUID), inserta usuario admin con contraseña hasheada y 5 productos seed |

Ejecutar con: `mysql -u <usuario> -p < database/schema.sql`.

---

## 📁 `/docs` — Documentación

| Archivo                             | Propósito |
|-------------------------------------|-----------|
| `autoloading-psr4-namespaces.md`    | Explica autoloading, PSR-4 y namespaces con ejemplos del proyecto |
| `estructura-del-proyecto.md`        | Este archivo — mapa de carpetas y responsabilidades |

---

## 📁 `/public` — Webroot (lo único expuesto a internet)

Carpeta pública que debe apuntarse desde el servidor web:
`php -S localhost:8000 -t public`.

| Archivo         | Propósito |
|-----------------|-----------|
| `index.php`     | **Front Controller**. Punto de entrada único. Registra autoloader, inicia sesión, configura manejo de errores, crea el router y despacha la petición |
| `.htaccess`     | Reescritura para Apache: envía todo a `index.php` salvo archivos reales existentes. Opcional con el servidor embebido de PHP |
| `css/app.css`   | Estilos de la interfaz. Sin frameworks CSS |

**¿Por qué una carpeta `public`?** Para que archivos sensibles (configuración,
código fuente, scripts SQL) **nunca** puedan ser descargados por el navegador.
Solo lo que está en `/public` es servible por la web.

---

## 🗃️ Archivos en la raíz

| Archivo               | Propósito |
|-----------------------|-----------|
| `README.md`           | Guía de instalación, ejecución, credenciales de prueba y mapa rápido |

---

## 🔄 Ciclo de una petición — cómo colabora todo

Ejemplo: el usuario envía el formulario de login.

```
1. Navegador → POST /login
2. public/index.php (front controller)
   ├─ Registra autoloader → las clases se cargarán on-demand
   ├─ Carga config/config.php → credenciales BD
   ├─ Inicia Session
   └─ Carga config/routes.php → registra rutas en Router

3. Router.dispatch(Request)
   ├─ Encuentra POST /login → [AuthController, 'login']
   ├─ No hay middleware para esta ruta
   └─ Instancia AuthController y llama login($request)

4. AuthController::login()
   ├─ Verifica token CSRF (Core\Csrf)
   ├─ Valida campos (Core\Validator)
   ├─ User::findByUsername($username)   ← modelo → Core\Database → PDO → MySQL
   ├─ password_verify(...)
   ├─ Session::regenerate() + Session::set('user_id', ...)
   └─ redirect('/products')

5. Navegador sigue la redirección → GET /products
   └─ AuthMiddleware detecta sesión válida → permite paso
   └─ ProductController::index() renderiza products/index.php dentro de layouts/main.php
```

**Observa cómo cada carpeta hace su parte:** `public/` arranca, `config/`
configura, `core/` provee la maquinaria, `app/` resuelve el caso concreto,
y `database/` persiste los datos.

---

## 🏛️ Principios que guían esta estructura

1. **Separación de responsabilidades** — cada carpeta tiene un único propósito.
2. **MVC** — Modelo (datos), Vista (presentación), Controlador (orquestación).
3. **Front Controller único** — todas las URLs pasan por `public/index.php`.
4. **Webroot aislado** — solo `public/` es accesible desde la web.
5. **Núcleo vs aplicación** — `core/` es reusable; `app/` es específico.
6. **Configuración fuera del código** — `config/` centraliza parámetros.
7. **Convenciones > configuración** — tablas plural, modelos singular, PascalCase, camelCase.
