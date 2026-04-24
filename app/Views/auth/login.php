<?php
use Core\Csrf;
use Core\View;
?>
<section class="card card-narrow">
    <h1>Iniciar sesión</h1>

    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= View::e($error) ?></div>
    <?php endif; ?>

    <form action="/login" method="post" class="form">
        <?= Csrf::field() ?>

        <label for="username">Usuario</label>
        <input
            type="text"
            id="username"
            name="username"
            value="<?= View::e($old ?? '') ?>"
            required
            autofocus
        >

        <label for="password">Contraseña</label>
        <input type="password" id="password" name="password" required>

        <button type="submit" class="btn btn-primary">Entrar</button>
    </form>

    <p class="hint">
        Credenciales de prueba: <code>admin</code> / <code>admin123</code>
    </p>
</section>
