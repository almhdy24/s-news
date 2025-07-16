<?php
declare(strict_types=1);

use Core\App;
use Core\System;
use Core\Router;

// Initialize base path
require_once __DIR__ . '/../core/App.php';
App::init(dirname(__DIR__));

// Bootstrap the system (handles config, session, install check, hooks, plugins, theme, visitor logging, etc.)
require_once App::basePath('bootstrap.php');

// Parse request path and method
$currentPath = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH) ?: '/';
$method = $_SERVER['REQUEST_METHOD'] ?? 'GET';

// Load core middleware (System may have already done this depending on setup)
require_once App::basePath('core/middleware.php');

// Dispatch routes
$router = new Router();
require_once App::basePath('routes/web.php');
require_once App::basePath('routes/admin.php');
$router->dispatch($currentPath, $method);