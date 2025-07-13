<?php
require_once __DIR__ . '/../init.php';
require_once '../../classes/Category.php';

$categoryObj = new Category();
$errors = [];

if (is_post()) {
    $name = sanitize($_POST['name'] ?? '');
    if (!$name) {
        $errors[] = 'Category name is required.';
    }

    if (empty($errors)) {
        $categoryObj->create(['name' => $name]);
        flash('success', 'Category created successfully.');
        redirect('index.php');
    }
}

include '../../templates/header.php';
?>

<div class="container">
    <h2>Create Category</h2>

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
                   value="<?= htmlspecialchars($_POST['name'] ?? '') ?>" required>
        </div>
        <button class="btn btn-success">Create</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>