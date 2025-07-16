<?php
// core/functions.php

use Core\Config;
use Core\App;

/**
 * Get the site name from config.
 */
function get_site_name(): string
{
    return Config::siteName();
}

/**
 * Get the active theme name from config.
 */
function get_theme_name(): string
{
    return Config::theme();
}

/**
 * Get the full filesystem path to a file in the current theme.
 * 
 * @param string $file Relative path inside the theme folder
 * @return string Absolute path to the theme file
 */
function theme_path(string $file): string
{
    return App::basePath('themes/' . get_theme_name() . '/' . ltrim($file, '/'));
}

/**
 * Get the URL path for an asset in the current theme.
 * 
 * @param string $asset Relative asset path inside the theme folder (e.g. 'css/style.css')
 * @return string URL path to the asset
 */
function theme_asset(string $asset): string
{
    return '/themes/' . get_theme_name() . '/' . ltrim($asset, '/');
}

/**
 * Escape a string for safe HTML output.
 * 
 * @param string $value Input string
 * @return string Escaped string
 */
function e(string $value): string
{
    return htmlspecialchars($value, ENT_QUOTES | ENT_SUBSTITUTE, 'UTF-8');
}


function redirect(string $url): void
{
    header('Location: ' . $url);
    exit;
}