<?php
use Core\Csrf;
use Core\View;
?>
<section>
    <div class="section-header">
        <h1>Productos</h1>
        <a href="/products/create" class="btn btn-primary">+ Nuevo producto</a>
    </div>

    <?php if (!empty($success)): ?>
        <div class="alert alert-success"><?= View::e($success) ?></div>
    <?php endif; ?>
    <?php if (!empty($error)): ?>
        <div class="alert alert-error"><?= View::e($error) ?></div>
    <?php endif; ?>

    <?php if (empty($products)): ?>
        <p class="empty">No hay productos registrados.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th class="right">Precio</th>
                        <th class="right">Stock</th>
                        <th>Actualizado</th>
                        <th class="right">Acciones</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($products as $p): ?>
                        <tr>
                            <td><?= View::e($p['id']) ?></td>
                            <td><?= View::e($p['nombre']) ?></td>
                            <td><?= View::e(mb_strimwidth((string) $p['descripcion'], 0, 60, '…')) ?></td>
                            <td class="right">$<?= View::e(number_format((float) $p['precio'], 2)) ?></td>
                            <td class="right"><?= View::e($p['stock']) ?></td>
                            <td><?= View::e($p['updated_at']) ?></td>
                            <td class="right actions">
                                <a href="/products/<?= urlencode((string) $p['id']) ?>/edit" class="btn btn-secondary btn-sm">Editar</a>
                                <form
                                    action="/products/<?= urlencode((string) $p['id']) ?>/delete"
                                    method="post"
                                    class="inline-form"
                                    onsubmit="return confirm('¿Eliminar este producto?');"
                                >
                                    <?= Csrf::field() ?>
                                    <button type="submit" class="btn btn-danger btn-sm">Eliminar</button>
                                </form>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>
