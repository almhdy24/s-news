<?php
namespace Core;

class AdminViewManager
{
    protected static string $basePath = __DIR__ . '/../admin/views/';
    protected static ?string $layout = null;
    protected static array $sharedData = [];

    /**
     * Set base directory for views (optional override)
     */
    public static function setBasePath(string $path): void
    {
        self::$basePath = rtrim($path, '/') . '/';
    }

    /**
     * Set layout file relative to base path
     */
    public static function setLayout(string $layoutPath): void
    {
        self::$layout = $layoutPath;
    }

    /**
     * Share data with all views
     */
    public static function share(string $key, mixed $value): void
    {
        self::$sharedData[$key] = $value;
    }

    /**
     * Render a view with optional layout
     *
     * @param string $viewPath  Path relative to basePath (e.g. 'news/index.php')
     * @param array $data       Data variables for this view
     */
    public static function render(string $viewPath, array $data = []): void
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }

        $viewContent = self::renderToString($viewPath, $data);

        if (self::$layout) {
            $layoutData = array_merge(self::$sharedData, $data, ['content' => $viewContent]);
            echo self::renderToString(self::$layout, $layoutData);
        } else {
            echo $viewContent;
        }
    }

    /**
     * Render a view file to string
     *
     * @param string $path  Relative path to view file
     * @param array $data
     * @return string
     * @throws \RuntimeException if view file missing
     */
    protected static function renderToString(string $path, array $data): string
    {
        $fullPath = self::$basePath . ltrim($path, '/');
        if (!file_exists($fullPath)) {
            throw new \RuntimeException("Admin view '{$path}' not found at {$fullPath}.");
        }

        extract(array_merge(self::$sharedData, $data));
        ob_start();
        include $fullPath;
        return ob_get_clean();
    }

    /**
     * Render reusable partial inside any view
     */
    public static function partial(string $partialPath, array $data = []): void
    {
        echo self::renderToString($partialPath, $data);
    }

    /**
     * Clear all shared data (optional, for cleanup)
     */
    public static function clearSharedData(): void
    {
        self::$sharedData = [];
    }
}