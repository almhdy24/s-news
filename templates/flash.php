<?php if ($msg = flash('success')): ?>
    <div class="container"><div class="alert alert-success"><?= htmlspecialchars($msg) ?></div></div>
<?php endif; ?>

<?php if ($msg = flash('error')): ?>
    <div class="container"><div class="alert alert-danger"><?= htmlspecialchars($msg) ?></div></div>
<?php endif; ?>