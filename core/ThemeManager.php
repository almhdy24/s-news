<?php

namespace Core;

class ThemeManager
{
    private string $themesDir;
    private string $activeTheme;
    private ?string $layout = null;
    private array $viewData = [];

    public function __construct(string $themesDir = __DIR__ . '/../themes')
    {
        $this->themesDir = rtrim($themesDir, '/');
        $this->activeTheme = $this->loadActiveTheme();
    }

    private function loadActiveTheme(): string
    {
        $theme = Config::get('theme', 'default');
        $path = $this->themePath($theme);

        if (!is_dir($path)) {
            error_log("[ThemeManager] Theme '$theme' not found, falling back to 'default'");
            $theme = 'default';
            if (!is_dir($this->themePath($theme))) {
                throw new \RuntimeException("Default theme not found in themes directory.");
            }
        }

        return $theme;
    }

    private function themePath(string $theme): string
    {
        return "{$this->themesDir}/{$theme}";
    }

    public function setActiveTheme(string $theme): void
    {
        if (!is_dir($this->themePath($theme))) {
            throw new \RuntimeException("Theme '{$theme}' not found.");
        }

        Config::set('theme', $theme);
        Config::save();
        $this->activeTheme = $theme;
    }

    public function setLayout(string $layoutFile): void
    {
        $this->layout = $layoutFile;
    }

    public function render(string $view, array $data = []): void
    {
        $this->viewData = $data;
        $this->triggerHook('before_render', $data);

        $content = $this->renderViewToString($view, $data);

        if ($this->layout) {
            $this->triggerHook('before_layout', ['content' => &$content, 'data' => $data]);
            echo $this->renderViewToString($this->layout, array_merge($data, ['content' => $content]));
            $this->triggerHook('after_layout', $data);
        } else {
            echo $content;
        }

        $this->triggerHook('after_render', $data);
    }

    public function partial(string $partial, array $data = []): void
    {
        $path = $this->themePath($this->activeTheme) . '/' . ltrim($partial, '/');

        if (!file_exists($path)) {
            throw new \RuntimeException("Partial '{$partial}' not found in theme '{$this->activeTheme}'.");
        }

        $this->triggerHook("before_partial_{$partial}", $data);
        extract(array_merge($this->viewData, $data));
        include $path;
        $this->triggerHook("after_partial_{$partial}", $data);
    }

    public function asset(string $assetPath): string
    {
        return "/themes/{$this->activeTheme}/" . ltrim($assetPath, '/');
    }

    public function triggerHook(string $hookName, array $params = []): void
    {
        if (class_exists(PluginManager::class)) {
            PluginManager::triggerHook($hookName, $params);
        }
    }

    private function renderViewToString(string $view, array $data = []): string
    {
        $path = $this->themePath($this->activeTheme) . '/' . ltrim($view, '/');
        if (!file_exists($path)) {
            throw new \RuntimeException("View file '{$view}' not found in theme '{$this->activeTheme}'.");
        }

        extract($data);
        ob_start();
        include $path;
        return ob_get_clean();
    }
}