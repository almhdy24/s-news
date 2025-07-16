<?php
namespace Core\Middleware;

function auth()
{

    if (!isset($_SESSION['user'])) {
        header('Location: /admin/login');
        exit;
    }
}

function require_admin()
{
    if (!isset($_SESSION['user']) || $_SESSION['user']['role'] !== 'admin') {
        header('Location: /admin/login');
        exit;
    }
}