<?php
use Core\Csrf;
use Core\View;

$values = $product ?? [];
$old = $old ?? [];
$errors = $errors ?? [];

$get = function (string $field, mixed $default = '') use ($old, $values) {
    return $old[$field] ?? $values[$field] ?? $default;
};
?>
<?= Csrf::field() ?>

<label for="nombre">Nombre</label>
<input
    type="text"
    id="nombre"
    name="nombre"
    value="<?= View::e($get('nombre')) ?>"
    required
>
<?php if (!empty($errors['nombre'])): ?>
    <small class="error"><?= View::e($errors['nombre']) ?></small>
<?php endif; ?>

<label for="descripcion">Descripción</label>
<textarea id="descripcion" name="descripcion" rows="4"><?= View::e($get('descripcion')) ?></textarea>
<?php if (!empty($errors['descripcion'])): ?>
    <small class="error"><?= View::e($errors['descripcion']) ?></small>
<?php endif; ?>

<div class="row">
    <div class="col">
        <label for="precio">Precio</label>
        <input
            type="number"
            step="0.01"
            min="0"
            id="precio"
            name="precio"
            value="<?= View::e($get('precio', '0.00')) ?>"
            required
        >
        <?php if (!empty($errors['precio'])): ?>
            <small class="error"><?= View::e($errors['precio']) ?></small>
        <?php endif; ?>
    </div>

    <div class="col">
        <label for="stock">Stock</label>
        <input
            type="number"
            min="0"
            id="stock"
            name="stock"
            value="<?= View::e($get('stock', '0')) ?>"
            required
        >
        <?php if (!empty($errors['stock'])): ?>
            <small class="error"><?= View::e($errors['stock']) ?></small>
        <?php endif; ?>
    </div>
</div>
