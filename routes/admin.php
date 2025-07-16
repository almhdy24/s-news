<?php

use Core\Router;
use Admin\Controllers\AuthController;
use Admin\Controllers\NewsController;
use Admin\Controllers\CategoryController;
use Admin\Controllers\InstallController;
use Admin\Controllers\DashboardController;
use Admin\Controllers\VisitorLogController;
use Admin\Controllers\SettingsController;

// ============================
// 🔓 Public Routes (No Auth)
// ============================

// Auth
Router::get('/admin/login', [AuthController::class, 'loginForm']);
Router::post('/admin/login', [AuthController::class, 'login']);

// Installer
Router::get('/install', [InstallController::class, 'form']);
Router::post('/install', [InstallController::class, 'install']);

// ============================
// 🔐 Admin Routes (Require Auth)
// ============================
Router::group('/admin', ['middleware' => ['auth']], function () {

    // Dashboard
    Router::get('/', [DashboardController::class, 'index']);

    // News CRUD
    Router::get('/news', [NewsController::class, 'index']);
    Router::get('/news/create', [NewsController::class, 'createForm']);
    Router::post('/news/create', [NewsController::class, 'create']);
    Router::get('/news/edit/{id}', [NewsController::class, 'editForm']);
    Router::post('/news/edit/{id}', [NewsController::class, 'update']);
    Router::get('/news/delete/{id}', [NewsController::class, 'delete']);

    // Categories CRUD
    Router::get('/categories', [CategoryController::class, 'index']);
    Router::get('/categories/create', [CategoryController::class, 'createForm']);
    Router::post('/categories/create', [CategoryController::class, 'create']);
    Router::get('/categories/edit/{id}', [CategoryController::class, 'editForm']);
    Router::post('/categories/edit/{id}', [CategoryController::class, 'update']);
    Router::get('/categories/delete/{id}', [CategoryController::class, 'delete']);

    // Visitors
    Router::get('/visitors', [VisitorLogController::class, 'index']);

    // Settings (config.json)
    Router::get('/settings', [SettingsController::class, 'index']);
    Router::post('/settings/update', [SettingsController::class, 'update']);

    // Logout
    Router::get('/logout', [AuthController::class, 'logout']);
});