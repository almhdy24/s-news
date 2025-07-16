<?php

namespace Admin\Controllers;

use Core\AdminViewManager;
use App\Utils\VisitorLogger;
use App\Models\VisitorLog;

class VisitorLogController
{
    public static function index(): void
    {
        $visitorLog = new VisitorLog();
        $logger     = new VisitorLogger($visitorLog);

        $logs = $logger->getLogs();

        AdminViewManager::setLayout('partials/layout.php');
        AdminViewManager::render('visitor_logs/index.php', ['logs' => $logs]);
    }
}