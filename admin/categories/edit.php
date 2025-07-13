<?php
require_once __DIR__ . '/../init.php';
require_once '../../classes/Category.php';

$categoryObj = new Category();
$id = $_GET['id'] ?? null;
$category = $categoryObj->find($id);

if (!$category) {
    flash('error', 'Category not found.');
    redirect('index.php');
}

$errors = [];

if (is_post()) {
    $name = sanitize($_POST['name'] ?? '');
    if (!$name) {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {
        $categoryObj->update($id, ['name' => $name]);
        flash('success', 'Category updated successfully.');
        redirect('index.php');
    }
}

include '../../templates/header.php';
?>

<div class="container">
    <h2>Edit Category</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-3" style="max-width:400px;">
        <div class="mb-3">
            <label for="name" class="form-label">Category Name</label>
            <input type="text" id="name" name="name" class="form-control"
                   value="<?= htmlspecialchars($_POST['name'] ?? $category['name']) ?>" required>
        </div>
        <button class="btn btn-primary">Update</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>