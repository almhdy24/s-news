<?php
namespace Admin\Controllers;

use Core\AdminViewManager;
use App\Utils\VisitorLogger;
use App\Models\VisitorLog;
use App\Models\News;
use Core\DB\JSONDB;
use Core\App;

class DashboardController
{
    public static function index(): void
    {
        $user = $_SESSION['user'] ?? ['username' => 'Guest'];

        $schemas = [
            'visitor_logs' => [
                'id' => ['type' => 'int'],
                'ip' => ['type' => 'string'],
                'user_agent' => ['type' => 'string'],
                'url' => ['type' => 'string'],
                'timestamp' => ['type' => 'string'],
            ],
        ];

        $dataDir = App::dataPath();

        $jsonDb = new JSONDB($dataDir, $schemas);
        $visitorLogModel = new VisitorLog($jsonDb);
        $visitorLogger = new VisitorLogger($visitorLogModel);

        $logs = $visitorLogger->getLogs();

        $uniqueToday = self::countUniqueIpsByDate($logs, date('Y-m-d'));
        $uniqueTotal = self::countUniqueIps($logs);

        $files = glob($dataDir . '/*.json');
        $fileCount = count($files);
        $totalSizeBytes = self::getTotalFileSize($files);
        $healthStatus = self::checkFilesHealth($files) ? 'Healthy' : 'Warning';

        $newsModel = new News();
        $allNews = $newsModel->all();
        $totalNews = count($allNews);
        $publishedNews = count(array_filter($allNews, fn($item) => ($item['status'] ?? '') === 'published'));

        AdminViewManager::setLayout('partials/layout.php');
        AdminViewManager::render('dashboard/index.php', compact(
            'user',
            'uniqueToday',
            'uniqueTotal',
            'fileCount',
            'totalSizeBytes',
            'healthStatus',
            'totalNews',
            'publishedNews'
        ));
    }

    private static function countUniqueIpsByDate(array $logs, string $date): int
    {
        $filtered = array_filter($logs, fn($entry) => strpos($entry['timestamp'], $date) === 0);
        $ips = array_unique(array_column($filtered, 'ip'));
        return count($ips);
    }

    private static function countUniqueIps(array $logs): int
    {
        return count(array_unique(array_column($logs, 'ip')));
    }

    private static function getTotalFileSize(array $files): int
    {
        return array_sum(array_map('filesize', $files));
    }

    private static function checkFilesHealth(array $files): bool
    {
        foreach ($files as $file) {
            if (!is_readable($file) || filesize($file) === 0) {
                return false;
            }
        }
        return true;
    }
}