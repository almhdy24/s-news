<?php
session_start();

require_once 'classes/User.php';  // User extends JSONDB, uses users.json
require_once 'functions.php';

$configFile = __DIR__ . '/data/config.json';

// Check if installed (by config file and site_name)
function isInstalled(string $configFile): bool {
    if (!file_exists($configFile)) return false;
    $config = json_decode(file_get_contents($configFile), true);
    return !empty($config['site_name']);
}

if (isInstalled($configFile)) {
    header('Location: index.php');
    exit;
}

$errors = [];

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $siteName = trim($_POST['site_name'] ?? '');
    $siteDesc = trim($_POST['site_desc'] ?? '');
    $adminUser = trim($_POST['admin_user'] ?? '');
    $adminEmail = trim($_POST['admin_email'] ?? '');
    $adminPass = $_POST['admin_pass'] ?? '';
    $adminPassConfirm = $_POST['admin_pass_confirm'] ?? '';

    // Validate inputs
    if (!$siteName) $errors[] = "Site name is required.";
    if (!$adminUser) $errors[] = "Admin username is required.";
    if (!$adminEmail || !filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid admin email is required.";
    if (!$adminPass) $errors[] = "Admin password is required.";
    if ($adminPass !== $adminPassConfirm) $errors[] = "Passwords do not match.";

    if (empty($errors)) {
        try {
            // Ensure data directory exists
            $dataDir = __DIR__ . '/data';
            if (!is_dir($dataDir)) {
                if (!mkdir($dataDir, 0755, true) && !is_dir($dataDir)) {
                    throw new RuntimeException('Failed to create data directory.');
                }
            }

            // Save site config
            $configData = [
                'site_name' => $siteName,
                'site_description' => $siteDesc,
                'installed_at' => date('Y-m-d H:i:s'),
            ];
            file_put_contents($configFile, json_encode($configData, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE));

            // Create admin user with hashed password
            $user = new User(); // JSONDB based
            $user->create([
                'username' => $adminUser,
                'password' => password_hash($adminPass, PASSWORD_DEFAULT),
                'email' => $adminEmail,
                'role' => 'admin',
                'created_at' => date('Y-m-d H:i:s'),
                'id' => uniqid(),
            ]);

            // Redirect to login page
            header('Location: admin/login.php');
            exit;

        } catch (Exception $e) {
            $errors[] = "Installation failed: " . htmlspecialchars($e->getMessage());
        }
    }
}

include 'templates/header.php';
?>

<div class="container mt-5">
    <h2>Install S-News</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger" role="alert">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= $e ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-4" novalidate>
        <div class="mb-3">
            <label for="site_name" class="form-label">Site Name</label>
            <input type="text" name="site_name" id="site_name" class="form-control" value="<?= htmlspecialchars($_POST['site_name'] ?? '') ?>" required autofocus>
        </div>

        <div class="mb-3">
            <label for="site_desc" class="form-label">Site Description</label>
            <textarea name="site_desc" id="site_desc" class="form-control"><?= htmlspecialchars($_POST['site_desc'] ?? '') ?></textarea>
        </div>

        <hr>

        <h4>Admin Account</h4>

        <div class="mb-3">
            <label for="admin_user" class="form-label">Username</label>
            <input type="text" name="admin_user" id="admin_user" class="form-control" value="<?= htmlspecialchars($_POST['admin_user'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="admin_email" class="form-label">Email</label>
            <input type="email" name="admin_email" id="admin_email" class="form-control" value="<?= htmlspecialchars($_POST['admin_email'] ?? '') ?>" required>
        </div>

        <div class="mb-3">
            <label for="admin_pass" class="form-label">Password</label>
            <input type="password" name="admin_pass" id="admin_pass" class="form-control" required>
        </div>

        <div class="mb-3">
            <label for="admin_pass_confirm" class="form-label">Confirm Password</label>
            <input type="password" name="admin_pass_confirm" id="admin_pass_confirm" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary">Install</button>
    </form>
</div>

<?php include 'templates/footer.php'; ?>