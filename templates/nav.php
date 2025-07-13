<?php
if (!session_id()) session_start();
require_once __DIR__ . '/../functions.php';

$baseUrl = ''; // adjust to your app root or keep empty ''

function is_admin_area() {
    return strpos($_SERVER['REQUEST_URI'], '/admin') === 0;
}

$currentUser = current_user();
?>

<nav class="navbar navbar-expand-lg navbar-dark bg-dark mb-4">
    <div class="container-fluid">
        <a class="navbar-brand" href="<?= $baseUrl ?: '/' ?>"><?= htmlspecialchars(get_site_name()) ?></a>

        <?php if (is_admin_area() && $currentUser): // Show admin menu only if logged in ?>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#adminNavbar" aria-controls="adminNavbar" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse" id="adminNavbar">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <!-- Admin menu items -->
                    <li class="nav-item">
                        <a href="<?= $baseUrl ?>/admin/index.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'index.php' ? 'active' : '' ?>">Dashboard</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $baseUrl ?>/admin/categories/index.php" class="nav-link <?= strpos($_SERVER['PHP_SELF'], 'categories') !== false ? 'active' : '' ?>">Categories</a>
                    </li>
                    <li class="nav-item">
                        <a href="<?= $baseUrl ?>/admin/news/create.php" class="nav-link <?= basename($_SERVER['PHP_SELF']) === 'create.php' ? 'active' : '' ?>">Add News</a>
                    </li>
                </ul>

                <span class="navbar-text text-light me-3">
                    Logged in as <?= htmlspecialchars($currentUser['username'] ?? $currentUser) ?>
                </span>
                <a href="<?= $baseUrl ?>/admin/logout.php" class="btn btn-outline-light btn-sm">Logout</a>
            </div>
        <?php else: ?>
            <!-- Public nav: simple, brand only -->
        <?php endif; ?>
    </div>
</nav>
