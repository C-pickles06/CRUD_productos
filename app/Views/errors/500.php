<section class="card card-narrow center">
    <h1>500</h1>
    <p>Ocurrió un error interno en el servidor.</p>
    <?php if (!empty($message)): ?>
        <pre class="error-box"><?= htmlspecialchars($message, ENT_QUOTES, 'UTF-8') ?></pre>
    <?php endif; ?>
    <a href="/" class="btn btn-primary">Volver al inicio</a>
</section>
