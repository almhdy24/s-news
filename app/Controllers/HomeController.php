<?php
namespace App\Controllers;

use App\Models\News;

class HomeController
{
    public static function index(): void
    {
        $theme = $GLOBALS['theme'];  // Assuming ThemeManager instance is stored globally

        $page = isset($_GET['page']) ? max((int)$_GET['page'], 1) : 1;
        $perPage = 5;

        $newsModel = new News();
        $pagination = $newsModel->paginate($page, $perPage, 'published');

        $articles = $pagination['items'];

        $theme->render('index.php', [
            'articles'   => $articles,
            'pagination' => $pagination,
            'siteName'   => 'S-News',
        ]);
    }
}