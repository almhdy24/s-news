<?php
namespace Core;

class Flash
{
    public static function add(string $type, string $message): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $_SESSION['flash'][$type][] = $message;
    }

    public static function get(?string $type = null): array
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $all = $_SESSION['flash'] ?? [];
        return $type ? ($all[$type] ?? []) : $all;
    }

    public static function has(string $type): bool
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        return !empty($_SESSION['flash'][$type]);
    }

    public static function clear(): void
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        unset($_SESSION['flash']);
    }

    public static function display(): string
    {
        if (session_status() === PHP_SESSION_NONE) session_start();
        $flashes = $_SESSION['flash'] ?? [];
        unset($_SESSION['flash']);

        if (empty($flashes)) return '';

        $iconMap = [
            'success' => 'check-circle',
            'danger'  => 'exclamation-triangle',
            'warning' => 'exclamation-circle',
            'info'    => 'info-circle',
        ];

        $output = "<div class='mt-3'>";
        foreach ($flashes as $type => $messages) {
            $icon = $iconMap[$type] ?? 'info-circle';
            $output .= "<div class='alert alert-{$type} alert-dismissible fade show d-flex align-items-start' role='alert'>";
            $output .= "<i class='bi bi-{$icon} me-2 mt-1 fs-5'></i>";
            $output .= "<div>";

            foreach ($messages as $msg) {
                $output .= "<div>" . htmlspecialchars($msg) . "</div>";
            }

            $output .= "</div>";
            $output .= "<button type='button' class='btn-close ms-auto' data-bs-dismiss='alert' aria-label='Close'></button>";
            $output .= "</div>";
        }
        $output .= "</div>";

        return $output;
    }
}