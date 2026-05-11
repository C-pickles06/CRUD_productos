<?php
use Core\Csrf;
use Core\Session;
use Core\View;

$title = $title ?? 'Proyecto Práctica';
$isAuthenticated = Session::has('user_id');
$userName = Session::get('user_name');
?><!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= View::e($title) ?> · Proyecto Práctica</title>
    <link rel="stylesheet" href="/css/app.css">
</head>
<body>
    <header class="topbar">
        <div class="container topbar-inner">
            <a href="/" class="brand">Proyecto Práctica</a>
            <nav class="nav">
                <?php if ($isAuthenticated): ?>
                    <a href="/products">Productos</a>
                    <a href="/users">Usuarios</a>
                    <span class="user">👤 <?= View::e($userName) ?></span>
                    <form action="/logout" method="post" class="inline-form">
                        <?= Csrf::field() ?>
                        <button type="submit" class="btn btn-link">Cerrar sesión</button>
                    </form>
                <?php else: ?>
                    <a href="/login">Iniciar sesión</a>
                <?php endif; ?>
            </nav>
        </div>
    </header>

    <main class="container content">
        <?= $content ?? '' ?>
    </main>

    <footer class="footer">
        <div class="container">
            <small>© <?= date('Y') ?> · PHP MVC desde cero</small>
        </div>
    </footer>
</body>
</html>
