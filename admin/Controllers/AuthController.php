<?php

namespace Admin\Controllers;

use  App\Models\User;

class AuthController
{
    public static function loginForm()
    {
        if (self::isAuthenticated()) {
            header('Location: /admin');
            exit;
        }

        require __DIR__ . '/../views/auth/login.php';
    }

    public static function login(): void
{
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        header('Location: /admin/login');
        exit;
    }

    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if ($username === '' || $password === '') {
        self::setFlashError('Please enter both username and password.');
        self::redirectLogin();
    }

    $userModel = new User();

    if (!method_exists($userModel, 'findByUsername')) {
        self::setFlashError('Internal error: User lookup method missing.');
        self::redirectLogin();
    }

    $user = $userModel->findByUsername($username);
  
    if (!$user) {
        self::setFlashError('Invalid username or password.');
        self::redirectLogin();
    }

    if (!password_verify($password, $user['password'])) {
        self::setFlashError('Invalid username or password.');
        self::redirectLogin();
    }

    if ($user['role'] !== 'admin') {
        self::setFlashError('Access denied. Admins only.');
        self::redirectLogin();
    }

    // Regenerate session ID to prevent session fixation attacks
    session_regenerate_id(true);

    $_SESSION['user'] = $user;

    header('Location: /admin');
    exit;
}

private static function setFlashError(string $message): void
{
    $_SESSION['flash']['error'][] = $message;
}

private static function redirectLogin(): void
{
    header('Location: /admin/login');
    exit;
}

    public static function logout()
    {
        unset($_SESSION['user']);
        session_destroy();
        header('Location: /admin/login');
        exit;
    }

    private static function isAuthenticated(): bool
    {
        return isset($_SESSION['user']) && $_SESSION['user']['role'] === 'admin';
    }
}