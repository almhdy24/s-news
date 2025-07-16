<?php
if (session_status() === PHP_SESSION_NONE) session_start();

$siteName   = $_POST['site_name'] ?? '';
$adminUser  = $_POST['admin_user'] ?? '';
$adminEmail = $_POST['admin_email'] ?? '';
$themes     = $themes ?? [];
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Install S-News CMS</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container py-5">
    <div class="mx-auto bg-white shadow-sm p-4 rounded" style="max-width: 500px;">
        <h2 class="mb-4 text-center">Install S-News CMS</h2>

        <?php if (!empty($_SESSION['flash']['error'])): ?>
            <div class="alert alert-danger">
                <ul class="mb-0">
                    <?php foreach ($_SESSION['flash']['error'] as $e): ?>
                        <li><?= htmlspecialchars($e) ?></li>
                    <?php endforeach; unset($_SESSION['flash']['error']); ?>
                </ul>
            </div>
        <?php endif; ?>

        <form method="POST" action="/install">
            <div class="mb-3">
                <label class="form-label">Site Name</label>
                <input type="text" name="site_name" class="form-control" required value="<?= htmlspecialchars($siteName) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Admin Username</label>
                <input type="text" name="admin_user" class="form-control" required value="<?= htmlspecialchars($adminUser) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Admin Email</label>
                <input type="email" name="admin_email" class="form-control" required value="<?= htmlspecialchars($adminEmail) ?>">
            </div>

            <div class="mb-3">
                <label class="form-label">Admin Password</label>
                <input type="password" name="admin_pass" class="form-control" required>
            </div>

            <div class="mb-3">
                <label class="form-label">Theme</label>
                <select name="theme" class="form-select" required>
                    <?php foreach ($themes as $t): ?>
                        <option value="<?= htmlspecialchars($t) ?>" <?= $t === 'default' ? 'selected' : '' ?>>
                            <?= ucfirst(htmlspecialchars($t)) ?>
                        </option>
                    <?php endforeach; ?>
                </select>
            </div>

            <?php if (function_exists('do_action')): ?>
                <?= do_action('renderFormToken') ?>
            <?php endif; ?>

            <button class="btn btn-primary w-100">Install Now</button>
        </form>
    </div>
</div>
</body>
</html>