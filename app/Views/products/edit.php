<section class="card">
    <h1>Editar producto</h1>

    <form action="/products/<?= urlencode((string) $product['id']) ?>/update" method="post" class="form">
        <?php require __DIR__ . '/_form.php'; ?>

        <div class="actions">
            <a href="/products" class="btn btn-secondary">Cancelar</a>
            <button type="submit" class="btn btn-primary">Actualizar</button>
        </div>
    </form>
</section>
