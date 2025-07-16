<?php
namespace Admin\Controllers;

use App\Models\Category;
use Core\AdminViewManager;
use Core\Flash;

class CategoryController
{
    public function __construct()
    {
        // Set layout once for all controller methods
        AdminViewManager::setLayout('partials/layout.php');
    }

    public function index(): void
    {
        $categories = (new Category())->all();
        AdminViewManager::render('categories/index.php', ['categories' => $categories]);
    }

    public function createForm(): void
    {
        AdminViewManager::render('categories/create.php', [
            'name' => '',
            'errors' => []
        ]);
    }

    public function create(): void
{
    $name = trim($_POST['name'] ?? '');
    $errors = [];

    if ($name === '') {
        $errors[] = 'Category name is required.';
    }

    if ($errors) {
        foreach ($errors as $error) {
            Flash::add('danger', $error);
        }
        AdminViewManager::render('categories/create.php', [
            'name' => $name,
            'errors' => $errors,
        ]);
        return;
    }

    (new Category())->create([
        'name' => $name,
        'created_at' => date('Y-m-d H:i:s'),  // <-- Add this line
    ]);
    Flash::add('success', 'Category created successfully.');
    header('Location: /admin/categories');
    exit;
}

    public function editForm(int $id): void
    {
        $category = (new Category())->find($id);
        if (!$category) {
            http_response_code(404);
            echo "Category not found.";
            exit;
        }
        AdminViewManager::render('categories/edit.php', [
            'category' => $category,
            'errors' => []
        ]);
    }

    public function update(int $id): void
    {
        $name = trim($_POST['name'] ?? '');
        $errors = [];

        if ($name === '') {
            $errors[] = 'Category name is required.';
        }

        if ($errors) {
            foreach ($errors as $error) {
                Flash::add('danger', $error);
            }
            $category = (new Category())->find($id);
            AdminViewManager::render('categories/edit.php', [
                'category' => $category,
                'errors' => $errors,
            ]);
            return;
        }

        (new Category())->update($id, ['name' => $name]);
        Flash::add('success', 'Category updated successfully.');
        header('Location: /admin/categories');
        exit;
    }

    public function delete(int $id): void
    {
        (new Category())->delete($id);
        Flash::add('success', 'Category deleted successfully.');
        header('Location: /admin/categories');
        exit;
    }
}