<?php
namespace Admin\Controllers;

use Core\AdminViewManager;
use Core\Config;
use Core\Flash;

class SettingsController
{
    public static function index(): void
{
    $settings = \Core\Config::all();

    $themesDir = __DIR__ . '/../../themes';
    $availableThemes = array_values(array_filter(scandir($themesDir), function ($item) use ($themesDir) {
        return is_dir($themesDir . '/' . $item) && $item[0] !== '.';
    }));

    \Core\AdminViewManager::setLayout('partials/layout.php');
    \Core\AdminViewManager::render('settings/index.php', compact('settings', 'availableThemes'));
}

public static function update(): void
{
    $theme = $_POST['theme'] ?? '';
    $siteName = trim($_POST['site_name'] ?? '');

    if ($theme === '' || !is_dir(__DIR__ . "/../../themes/{$theme}")) {
        Flash::add('danger', "Theme '$theme' does not exist.");
        redirect('/admin/settings');
        return;
    }

    if ($siteName === '') {
        Flash::add('danger', "Site name cannot be empty.");
        redirect('/admin/settings');
        return;
    }

    try {
        Config::setMany([
            'theme' => $theme,
            'site_name' => $siteName,
        ]);
        Config::save();

        Flash::add('success', 'Settings updated successfully.');
    } catch (\Throwable $e) {
        Flash::add('danger', "Error updating settings: " . $e->getMessage());
    }

    redirect('/admin/settings');
}
}