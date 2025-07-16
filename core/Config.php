<?php
declare(strict_types=1);

namespace Core;

use RuntimeException;
use InvalidArgumentException;

final class Config
{
    private const DEFAULTS = [
        'installed'  => false,
        'site_name'  => 'Square News',
        'theme'      => 'default',
    ];

    private static string $file;
    private static array $data = [];
    private static bool $loaded = false;

    private function __construct() {}

    /**
     * Initialize config system.
     */
    private static function initPath(): void
    {
        if (!isset(self::$file)) {
            self::$file = App::dataPath('config.json');
        }
    }

    /**
     * Load config from file, or handle missing config by destroying session and data dir.
     */
    private static function load(): void
    {
        self::initPath();
        if (self::$loaded) return;

        if (!file_exists(self::$file)) {
            self::handleMissingConfig();
            self::$data = self::DEFAULTS;
            self::$loaded = true;
            return;
        }

        $fp = @fopen(self::$file, 'r');
        if (!$fp) {
            throw new RuntimeException("Unable to open config file: " . self::$file);
        }

        if (flock($fp, LOCK_SH)) {
            $contents = stream_get_contents($fp);
            flock($fp, LOCK_UN);
        } else {
            fclose($fp);
            throw new RuntimeException("Could not acquire read lock on config file.");
        }

        fclose($fp);

        $parsed = json_decode($contents, true);
        self::$data = is_array($parsed) ? array_merge(self::DEFAULTS, $parsed) : self::DEFAULTS;
        self::$loaded = true;
    }

    /**
     * Handle missing config: destroy session & delete data directory.
     */
    private static function handleMissingConfig(): void
    {
        if (session_status() !== PHP_SESSION_NONE) {
            session_unset();
            session_destroy();
        }

        $dataDir = App::dataPath();
        if (is_dir($dataDir)) {
            self::deleteDir($dataDir);
        }
    }

    /**
     * Recursively delete a directory.
     */
    private static function deleteDir(string $dir): void
    {
        if (!is_dir($dir)) return;

        $items = scandir($dir);
        if (!$items) return;

        foreach ($items as $item) {
            if ($item === '.' || $item === '..') continue;
            $path = $dir . DIRECTORY_SEPARATOR . $item;

            if (is_dir($path)) {
                self::deleteDir($path);
            } else {
                @unlink($path);
            }
        }

        @rmdir($dir);
    }

    public static function all(): array
    {
        self::load();
        return self::$data;
    }

    public static function get(string $key, mixed $default = null): mixed
    {
        self::load();
        return self::$data[$key] ?? $default ?? self::DEFAULTS[$key] ?? null;
    }

    public static function set(string $key, mixed $value): void
    {
        self::load();
        self::validate($key, $value);
        self::$data[$key] = $value;
    }

    public static function setMany(array $pairs): void
    {
        foreach ($pairs as $k => $v) {
            self::set($k, $v);
        }
    }

    public static function save(): void
    {
        self::initPath();
        self::load();

        $dir = dirname(self::$file);
        if (!is_dir($dir) && !mkdir($dir, 0755, true) && !is_dir($dir)) {
            throw new RuntimeException("Failed to create config directory: {$dir}");
        }

        $json = json_encode(self::$data, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($json === false) {
            throw new RuntimeException("JSON encoding failed: " . json_last_error_msg());
        }

        $fp = @fopen(self::$file, 'c+');
        if (!$fp) {
            throw new RuntimeException("Unable to open config file for writing: " . self::$file);
        }

        if (!flock($fp, LOCK_EX)) {
            fclose($fp);
            throw new RuntimeException("Unable to lock config file for writing.");
        }

        ftruncate($fp, 0);
        rewind($fp);
        $written = fwrite($fp, $json);

        fflush($fp);
        flock($fp, LOCK_UN);
        fclose($fp);

        if ($written === false) {
            throw new RuntimeException("Failed to write config file.");
        }
    }

    public static function reload(): void
    {
        self::$loaded = false;
        self::$data = [];
        self::load();
    }

    public static function delete(): void
    {
        self::initPath();
        if (file_exists(self::$file) && !unlink(self::$file)) {
            throw new RuntimeException("Failed to delete config file: " . self::$file);
        }
        self::$loaded = false;
        self::$data = self::DEFAULTS;
    }

    public static function has(string $key): bool
    {
        self::load();
        return array_key_exists($key, self::$data);
    }

    public static function isInstalled(): bool
    {
        return (bool) self::get('installed');
    }

    public static function siteName(): string
    {
        return (string) self::get('site_name', self::DEFAULTS['site_name']);
    }

    public static function theme(): string
    {
        return (string) self::get('theme', self::DEFAULTS['theme']);
    }

    private static function validate(string $key, mixed $value): void
    {
        switch ($key) {
            case 'installed':
                if (!is_bool($value)) {
                    throw new InvalidArgumentException("Config 'installed' must be a boolean.");
                }
                break;
            case 'site_name':
            case 'theme':
                if (!is_string($value) || trim($value) === '') {
                    throw new InvalidArgumentException("Config '{$key}' must be a non-empty string.");
                }
                break;
        }
    }
}