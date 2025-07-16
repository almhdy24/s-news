<?php
namespace App\Utils;

use App\Models\VisitorLog;

class VisitorLogger
{
    private VisitorLog $visitorLog;

    public function __construct(VisitorLog $visitorLog)
    {
        $this->visitorLog = $visitorLog;
    }

    public function logVisit(): void
    {
        if ($this->isAdmin() || $this->isBot()) {
            return;
        }

        $ip         = $_SERVER['REMOTE_ADDR'] ?? 'unknown';
        $url        = $_SERVER['REQUEST_URI'] ?? '';
        $today      = date('Y-m-d');

        // Prevent duplicate logging for the same IP + URL + Day
        if ($this->visitorLog->findByIpUrlDate($ip, $url, $today)) {
            return;
        }

        $this->visitorLog->create([
            'ip'         => $ip,
            'user_agent' => $_SERVER['HTTP_USER_AGENT'] ?? '',
            'url'        => $url,
            'timestamp'  => date('Y-m-d H:i:s'),
        ]);
    }

    private function isAdmin(): bool
    {
        return (isset($_SESSION['user']) && !empty($_SESSION['user'])) ||
               (isset($_SERVER['REQUEST_URI']) && str_starts_with($_SERVER['REQUEST_URI'], '/admin'));
    }

    private function isBot(): bool
    {
        $ua = strtolower($_SERVER['HTTP_USER_AGENT'] ?? '');

        return preg_match('/bot|crawl|spider|slurp|crawler|facebookexternalhit|bingpreview|mediapartners-google|googlebot|bingbot|yandex|baiduspider|duckduckbot|adsbot-google/i', $ua) === 1;
    }

    public function getLogs(): array
    {
        return $this->visitorLog->all();
    }

    public function getUniqueVisitorCount(): int
    {
        return count($this->visitorLog->findUniqueIpsAll());
    }

    public function getUniqueToday(): int
    {
        return count($this->visitorLog->findUniqueIpsToday());
    }
}