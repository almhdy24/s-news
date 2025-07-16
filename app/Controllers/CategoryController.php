<?php
namespace App\Controllers;

use Core\ThemeManager;
use App\Category;

class CategoryController
{
    public static function show(): void
    {
        $category = $_GET['name'] ?? null;
        $news = (new Category())->getNewsForCategory($category);

        (new ThemeManager())->render('category.php', [
            'category' => $category,
            'news'     => $news
        ]);
    }
}