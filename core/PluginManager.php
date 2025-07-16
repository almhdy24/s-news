<?php
namespace Core;

class PluginManager
{
    private static array $hooks = [];

    // Register a callback to a hook name
    public static function registerHook(string $hookName, callable $callback): void
    {
        if (!isset(self::$hooks[$hookName])) {
            self::$hooks[$hookName] = [];
        }
        self::$hooks[$hookName][] = $callback;
    }

    // Trigger all callbacks registered to a hook
    public static function triggerHook(string $hookName, ...$args): void
    {
        if (!empty(self::$hooks[$hookName])) {
            foreach (self::$hooks[$hookName] as $callback) {
                call_user_func_array($callback, $args);
            }
        }
    }

    // Load all plugins from a plugins directory
    public static function loadAll(string $pluginsDir = __DIR__ . '/../plugins'): void
    {
        if (!is_dir($pluginsDir)) return;

        foreach (scandir($pluginsDir) as $pluginFolder) {
            if ($pluginFolder === '.' || $pluginFolder === '..') continue;

            $pluginMainFile = $pluginsDir . '/' . $pluginFolder . '/plugin.php';
            if (file_exists($pluginMainFile)) {
                require_once $pluginMainFile;
            }
        }
    }
}