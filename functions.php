<?php

if (!session_id()) {
    session_start();
}

function redirect($url) {
    header("Location: $url");
    exit;
}

function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

function sanitize($value) {
    return htmlspecialchars(trim($value));
}

function flash($key, $message = null) {
    if (!isset($_SESSION['_flash'])) $_SESSION['_flash'] = [];

    if ($message !== null) {
        $_SESSION['_flash'][$key] = $message;
    } else {
        if (!empty($_SESSION['_flash'][$key])) {
            $msg = $_SESSION['_flash'][$key];
            unset($_SESSION['_flash'][$key]);
            return $msg;
        }
        return null;
    }
}

function excerpt($text, $length = 150) {
    $text = strip_tags($text);
    if (strlen($text) <= $length) return $text;
    return substr($text, 0, strrpos(substr($text, 0, $length), ' ')) . '...';
}

function require_login() {
    if (empty($_SESSION['user'])) {
        flash('error', 'Please log in.');
        redirect('/admin/login.php');
    }
}

function login_user($username) {
    $_SESSION['user'] = $username;
}

function logout_user() {
    unset($_SESSION['user']);
}

function current_user() {
    return $_SESSION['user'] ?? null;
}

function get_site_config() {
    static $config = null;
    if ($config === null) {
        $configFile = __DIR__ . '/data/config.json';
        if (file_exists($configFile)) {
            $config = json_decode(file_get_contents($configFile), true);
        } else {
            $config = []; // fallback empty
        }
    }
    return $config;
}

function get_site_name() {
    $config = get_site_config();
    return $config['site_name'] ?? 'S-News'; // default fallback
}