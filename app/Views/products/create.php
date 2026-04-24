<section class="card">
    <h1>Nuevo producto</h1>

    <form action="/products" method="post" class="form">
        <?php require __DIR__ . '/_form.php'; ?>

        <div class="actions">
            <a href="/products" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Guardar</button>
        </div>
    </form>
</section>
