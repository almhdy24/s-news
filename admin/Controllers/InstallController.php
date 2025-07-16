<?php
namespace Admin\Controllers;

use App\Models\User;
use Core\Config;
use Core\Flash;

class InstallController
{
    public static function form(): void
    {
        if (Config::isInstalled()) {
            header('Location: /admin/login');
            exit;
        }

        $themesDir = dirname(__DIR__, 2) . '/themes/';
        $themes = is_dir($themesDir)
            ? array_filter(scandir($themesDir), fn($t) => $t[0] !== '.' && is_dir($themesDir . $t))
            : [];

        require __DIR__ . '/../views/install/form.php';
    }

    public static function install(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();

        if (Config::isInstalled()) {
            header('Location: /admin/login');
            exit;
        }

        $siteName   = trim($_POST['site_name'] ?? '');
        $adminUser  = trim($_POST['admin_user'] ?? '');
        $adminEmail = trim($_POST['admin_email'] ?? '');
        $adminPass  = $_POST['admin_pass'] ?? '';
        $theme      = $_POST['theme'] ?? 'default';

        $themesDir = dirname(__DIR__, 2) . '/themes/';
        $themes = is_dir($themesDir)
            ? array_filter(scandir($themesDir), fn($t) => $t[0] !== '.' && is_dir($themesDir . $t))
            : [];

        $errors = [];

        if ($siteName === '') $errors[] = 'Site name is required.';
        if ($adminUser === '') $errors[] = 'Admin username is required.';
        if (!filter_var($adminEmail, FILTER_VALIDATE_EMAIL)) $errors[] = 'Valid admin email is required.';
        if (strlen($adminPass) < 6) $errors[] = 'Password must be at least 6 characters.';
        if (!in_array($theme, $themes)) $errors[] = 'Invalid theme selected.';

        $userModel = new User();
        if (method_exists($userModel, 'findByEmail') && $userModel->findByEmail($adminEmail)) {
            $errors[] = 'Admin user with this email already exists.';
        }

        if ($errors) {
            Flash::add('danger', implode('<br>', $errors));
            header('Location: /install');
            exit;
        }

        try {
            // Set config and save
            Config::setMany([
                'installed' => true,
                'site_name' => $siteName,
                'theme'     => $theme
            ]);
            Config::save();
        } catch (\Throwable $e) {
            Flash::add('danger', 'Failed to write config: ' . $e->getMessage());
            header('Location: /install');
            exit;
        }

        // Create admin user
        $userModel->create([
            'username'   => $adminUser,
            'email'      => $adminEmail,
            'password'   => password_hash($adminPass, PASSWORD_DEFAULT),
            'role'       => 'admin',
            'created_at' => date('Y-m-d H:i:s')
        ]);

        Flash::add('success', 'Installation completed successfully.');
        header('Location: /admin/login');
        exit;
    }
}