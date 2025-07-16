<?php
namespace Plugins\CSRFPlugin;

class CSRFPlugin
{
    public function registerHooks(): array
    {
        return [
            'beforeRequest' => [$this, 'handleCSRF'],
            'renderFormToken' => [$this, 'renderTokenInput'],
        ];
    }

    public function handleCSRF(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if ($_SERVER['REQUEST_METHOD'] === 'GET') {
            if (empty($_SESSION['csrf_token'])) {
                $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
            }
            return;
        }

        $token = $_POST['csrf_token'] ?? $_SERVER['HTTP_X_CSRF_TOKEN'] ?? null;

        if (!$token || !hash_equals($_SESSION['csrf_token'] ?? '', $token)) {
            $_SESSION['flash']['error'][] = 'Invalid CSRF token. Please try again.';
            $redirect = $_SERVER['HTTP_REFERER'] ?? '/';
            header("Location: $redirect");
            exit;
        }
    }

    public function renderTokenInput(): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        if (empty($_SESSION['csrf_token'])) {
            $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
        }

        echo '<input type="hidden" name="csrf_token" value="' . htmlspecialchars($_SESSION['csrf_token']) . '">';
    }
}