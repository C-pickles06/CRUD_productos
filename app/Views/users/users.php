<?php
use Core\View;
?>
<section>
    <div class="section-header">
        <h1>Usuarios</h1>
    </div>

    <?php if (empty($users)): ?>
        <p class="empty">No hay usuarios registrados.</p>
    <?php else: ?>
        <div class="table-wrapper">
            <table class="table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Usuario</th>
                        <th>Nombre</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($users as $user): ?>
                        <tr>
                            <td><?= View::e($user['id']) ?></td>
                            <td><?= View::e($user['username']) ?></td>
                            <td><?= View::e($user['name']) ?></td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    <?php endif; ?>
</section>