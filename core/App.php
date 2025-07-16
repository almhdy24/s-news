<?php
declare(strict_types=1);

namespace Core;

class App
{
    private static ?string $basePath = null;

    public static function init(string $basePath): void
    {
        self::$basePath = rtrim($basePath, DIRECTORY_SEPARATOR);
    }

    public static function basePath(string $append = ''): string
    {
        if (!self::$basePath) {
            throw new \RuntimeException("App base path is not initialized.");
        }
        return self::$basePath . ($append ? DIRECTORY_SEPARATOR . ltrim($append, DIRECTORY_SEPARATOR) : '');
    }

    public static function dataPath(string $append = ''): string
    {
        return self::basePath('data' . ($append ? DIRECTORY_SEPARATOR . ltrim($append, DIRECTORY_SEPARATOR) : ''));
    }

    public static function viewPath(string $append = ''): string
    {
        return self::basePath('app/views' . ($append ? DIRECTORY_SEPARATOR . ltrim($append, DIRECTORY_SEPARATOR) : ''));
    }

    public static function configPath(string $append = ''): string
    {
        return self::basePath('config' . ($append ? DIRECTORY_SEPARATOR . ltrim($append, DIRECTORY_SEPARATOR) : ''));
    }
}