<?php
namespace Admin\Controllers;

use Core\AdminViewManager;

class BaseAdminController
{
    public function __construct()
    {
        // Set the admin layout once for all admin views
        AdminViewManager::setLayout('partials/layout.php');

        // Share logged-in user info globally to all views
        AdminViewManager::share('user', $_SESSION['user'] ?? null);
    }
}