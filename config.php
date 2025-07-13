<?php
$configFile = __DIR__ . '/data/config.json';
$config = [];

if (file_exists($configFile)) {
    $config = json_decode(file_get_contents($configFile), true);
}

if (empty($config['site_name'])) {
    // Not installed yet, redirect to install
    header('Location: install.php');
    exit;
}