<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title>Admin Login - S-News</title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
</head>
<body class="bg-light d-flex justify-content-center align-items-center" style="min-height: 100vh;">

<div class="card shadow p-4" style="width: 100%; max-width: 400px;">
    <h3 class="text-center mb-4">S-News Admin Login</h3>

    <?php if (!empty($_SESSION['flash']['error'])): ?>
        <div class="alert alert-danger">
            <?php foreach ($_SESSION['flash']['error'] as $error): ?>
                <div><?= htmlspecialchars($error) ?></div>
            <?php endforeach; ?>
        </div>
        <?php unset($_SESSION['flash']['error']); ?>
    <?php endif; ?>

    <form method="POST" action="/admin/login" novalidate>
        <?php do_action('renderFormToken'); ?>

        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input
                type="text"
                name="username"
                id="username"
                required
                autofocus
                autocomplete="username"
                class="form-control"
            />
        </div>

        <div class="mb-3">
            <label for="password" class="form-label d-flex justify-content-between">
                <span>Password</span>
            </label>
            <input
                type="password"
                name="password"
                id="password"
                required
                autocomplete="current-password"
                class="form-control"
            />
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>

    <div class="mt-3 text-center text-muted" style="font-size: 0.9rem;">
        &copy; <?= date('Y') ?> S-News CMS
    </div>
</div>

</body>
</html>