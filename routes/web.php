<?php
use Core\Router;

use App\Controllers\HomeController;
use App\Controllers\CategoryController;
use App\Controllers\ViewController;

Router::get('/', [HomeController::class, 'index']);
Router::get('/category', [CategoryController::class, 'show']);
Router::get('/view', [ViewController::class, 'show']);