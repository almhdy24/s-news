<?php
declare(strict_types=1);

namespace Core;

use Core\App;
use Core\Config;
use Core\Flash;
use Core\PluginManager;
use Core\ThemeManager;
use App\Utils\VisitorLogger;
use App\Models\VisitorLog;

/**
 * Core system class for initializing app components and managing hooks/events.
 */
final class System
{
    /**
     * @var array<string, list<callable>> Registered event hooks
     */
    private static array $listeners = [];

    /**
     * Initialize the system:
     * - Setup paths and session
     * - Check install status and redirect accordingly
     * - Load config, theme, plugins
     * - Setup event hooks
     * - Log visitors if installed
     */
    public static function initialize(): void
    {
        // Load helper 
        require_once App::basePath('core/functions.php');
        require_once App::basePath('core/hooks.php');

        App::init(dirname(__DIR__));

        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $requestPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
        $isInstallRoute = str_starts_with($requestPath, '/install');

        if (!Config::isInstalled()) {
            self::clearSession();
            self::deleteDataDirectory();

            if (!$isInstallRoute) {
                header('Location: /install');
                exit;
            }
        } elseif ($isInstallRoute) {
            header('Location: /');
            exit;
        }

        // Load full config to memory
        Config::all();

        // Setup theme manager
        $themeManager = new ThemeManager();
        try {
            $themeManager->setActiveTheme(Config::theme());
        } catch (\RuntimeException) {
            $themeManager->setActiveTheme('default');
        }
        $GLOBALS['theme'] = $themeManager;

        // Load plugins dynamically
        PluginManager::loadAll();

        // Trigger 'init' event for any extensions
        self::trigger('init');

        // Register hook to clear flash messages after rendering
        self::hook('afterRender', function () {
            Flash::clear();
        });

        // Log visitors only if site installed
        if (Config::isInstalled()) {
            try {
                $visitorLogger = new VisitorLogger(new VisitorLog());
                $visitorLogger->logVisit();
            } catch (\Throwable $e) {
                error_log('[VisitorLogger] ' . $e->getMessage());
            }
        }
    }

    /**
     * Register a callable hook listener for an event.
     * 
     * @param string $event Event name
     * @param callable $callback Listener callback
     */
    public static function hook(string $event, callable $callback): void
    {
        if (!isset(self::$listeners[$event])) {
            self::$listeners[$event] = [];
        }
        self::$listeners[$event][] = $callback;
    }

    /**
     * Trigger all listeners registered for an event.
     * 
     * @param string $event Event name
     * @param mixed ...$args Arguments passed to listeners
     */
    public static function trigger(string $event, mixed ...$args): void
    {
        if (empty(self::$listeners[$event])) {
            return;
        }

        foreach (self::$listeners[$event] as $listener) {
            try {
                $listener(...$args);
            } catch (\Throwable $e) {
                error_log("[System] Error in event listener for '{$event}': " . $e->getMessage());
            }
        }
    }

    /**
     * Recursively delete the data directory and its contents.
     */
    private static function deleteDataDirectory(): void
    {
        $dataDir = App::dataPath();
        if (is_dir($dataDir)) {
            self::deleteDirRecursive($dataDir);
        }
    }

    /**
     * Helper: Recursively delete a directory and its contents.
     */
    private static function deleteDirRecursive(string $dir): void
    {
        $items = scandir($dir);
        if ($items === false) return;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;

            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                self::deleteDirRecursive($path);
            } else {
                if (!@unlink($path)) {
                    error_log("[System] Failed to delete file: {$path}");
                }
            }
        }

        if (!@rmdir($dir)) {
            error_log("[System] Failed to remove directory: {$dir}");
        }
    }

    /**
     * Clear current session and delete session cookie safely.
     */
    private static function clearSession(): void
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return;
        }

        // Clear all session variables
        $_SESSION = [];

        // Delete session cookie if cookies used
        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        // Destroy the session
        session_destroy();
    }
}