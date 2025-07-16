<?php
namespace Admin\Controllers;

use App\Models\News;
use App\Models\Category;
use Core\AdminViewManager;
use Core\Flash;

class NewsController extends BaseAdminController
{
    public function __construct()
    {
        parent::__construct();
    }

    public function index(): void
    {
        $newsModel = new News();
        $categoryModel = new Category();

        $newsList = $newsModel->all();
        $categories = [];

        foreach ($categoryModel->all() as $cat) {
            $categories[$cat['id']] = $cat;
        }

        AdminViewManager::render('news/index.php', [
            'news' => $newsList,
            'categories' => $categories
        ]);
    }

    public function createForm(): void
    {
        $categories = (new Category())->all();

        AdminViewManager::render('news/create.php', [
            'categories' => $categories,
            'title' => '',
            'content' => '',
            'category_id' => 0,
            'status' => 'draft',
        ]);
    }

    public function create(): void
    {
        $newsModel = new News();
        $categoryModel = new Category();

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'draft';

        $categories = $categoryModel->all();

        $errors = [];

        $validStatuses = ['draft', 'published'];
        if (!in_array($status, $validStatuses, true)) {
            $errors[] = 'Invalid status selected.';
        }
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if ($content === '') {
            $errors[] = 'Content is required.';
        }
        if ($category_id <= 0) {
            $errors[] = 'Valid category is required.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::add('danger', $error);
            }

            // أعِد عرض النموذج مع البيانات القديمة
            AdminViewManager::render('news/create.php', [
                'categories' => $categories,
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'status' => $status,
            ]);
            return;
        }

        try {
            $newsModel->create([
                'title'       => $title,
                'content'     => $content,
                'category_id' => $category_id,
                'status'      => $status,
                'created_at'  => date('Y-m-d H:i:s'),
            ]);
            Flash::add('success', 'News created successfully.');
            header('Location: /admin/news');
            exit;
        } catch (\Throwable $e) {
            Flash::add('danger', 'Failed to create news: ' . $e->getMessage());
            AdminViewManager::render('news/create.php', [
                'categories' => $categories,
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'status' => $status,
            ]);
        }
    }

    public function editForm(int $id): void
    {
        $newsModel = new News();
        $categoryModel = new Category();

        $news = $newsModel->find($id);
        if (!$news) {
            http_response_code(404);
            echo "News not found.";
            exit;
        }

        $categories = $categoryModel->all();

        AdminViewManager::render('news/edit.php', [
            'news' => $news,
            'categories' => $categories,
            'title' => $news['title'],
            'content' => $news['content'],
            'category_id' => $news['category_id'],
            'status' => $news['status'],
        ]);
    }

    public function update(int $id): void
    {
        $newsModel = new News();
        $categoryModel = new Category();

        $news = $newsModel->find($id);
        if (!$news) {
            http_response_code(404);
            echo "News not found.";
            exit;
        }

        $title = trim($_POST['title'] ?? '');
        $content = trim($_POST['content'] ?? '');
        $category_id = (int)($_POST['category_id'] ?? 0);
        $status = $_POST['status'] ?? 'draft';

        $categories = $categoryModel->all();

        $errors = [];

        $validStatuses = ['draft', 'published'];
        if (!in_array($status, $validStatuses, true)) {
            $errors[] = 'Invalid status selected.';
        }
        if ($title === '') {
            $errors[] = 'Title is required.';
        }
        if ($content === '') {
            $errors[] = 'Content is required.';
        }
        if ($category_id <= 0) {
            $errors[] = 'Valid category is required.';
        }

        if (!empty($errors)) {
            foreach ($errors as $error) {
                Flash::add('danger', $error);
            }
            // إعادة عرض النموذج مع البيانات القديمة
            AdminViewManager::render('news/edit.php', [
                'news' => $news,
                'categories' => $categories,
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'status' => $status,
            ]);
            return;
        }

        try {
            $newsModel->update($id, [
                'title'       => $title,
                'content'     => $content,
                'category_id' => $category_id,
                'status'      => $status,
                'updated_at'  => date('Y-m-d H:i:s'),
            ]);
            Flash::add('success', 'News updated successfully.');
            header('Location: /admin/news');
            exit;
        } catch (\Throwable $e) {
            Flash::add('danger', 'Failed to update news: ' . $e->getMessage());
            AdminViewManager::render('news/edit.php', [
                'news' => $news,
                'categories' => $categories,
                'title' => $title,
                'content' => $content,
                'category_id' => $category_id,
                'status' => $status,
            ]);
        }
    }

    public function delete(int $id): void
    {
        $newsModel = new News();
        try {
            $newsModel->delete($id);
            Flash::add('success', 'News deleted successfully.');
        } catch (\Throwable $e) {
            Flash::add('danger', 'Failed to delete news: ' . $e->getMessage());
        }
        header('Location: /admin/news');
        exit;
    }
}