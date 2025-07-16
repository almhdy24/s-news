<?php
namespace App\Controllers;

use Core\ThemeManager;
use App\Models\News;
use App\Models\Category;

class ViewController
{
    public static function show(): void
    {
        $id = $_GET['id'] ?? null;
        $newsModel = new News();
        $item = $newsModel->find($id);

        if (!$item || ($item['status'] ?? '') !== 'published') {
            http_response_code(404);
            echo "News not found.";
            return;
        }

        $categoryModel = new Category();
        $category = $categoryModel->find($item['category_id']);

        (new ThemeManager())->render('view.php', [
            'news' => $item,
            'category' => $category
        ]);
    }
}