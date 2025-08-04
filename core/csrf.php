<?php
if (session_status() === PHP_SESSION_NONE) session_start();
/**
 * Generate a CSRF token and store in session.
 */
function csrf_token(): string {
    if (empty($_SESSION['csrf_token'])) {
        $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
    }
    return $_SESSION['csrf_token'];
}
/**
 * Validate the CSRF token from POST against session.
 */
function csrf_validate($token): bool {
    return isset($_SESSION['csrf_token']) && hash_equals($_SESSION['csrf_token'], $token);
}