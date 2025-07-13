<?php
require_once '../init.php';
require_once '../../classes/News.php';
require_once '../../classes/Category.php';

$news = new News();
$categoryObj = new Category();
$categories = $categoryObj->all();

$errors = [];

// Helpers
function input($key) {
    return trim($_POST[$key] ?? '');
}
function old($key) {
    return htmlspecialchars($_POST[$key] ?? '');
}

if (is_post()) {
    $title = sanitize(input('title'));
    $content = $_POST['content'] ?? ''; // Allow raw HTML from CKEditor
    $category_id = input('category_id');

    // Validation
    if ($title === '') $errors[] = 'Title is required.';
    if (trim(strip_tags($content)) === '') $errors[] = 'Content is required.';
    if (!$category_id || !$categoryObj->find($category_id)) $errors[] = 'Valid category is required.';

    if (empty($errors)) {
        $news->create([
            'title' => $title,
            'content' => $content,
            'category_id' => $category_id,
            'created_at' => date('Y-m-d H:i:s')
        ]);
        flash('success', 'Article created successfully.');
        redirect('../../admin/index.php');
    }
}

include '../../templates/header.php';
?>

<div class="container">
    <h2 class="mb-4">Create News Article</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST" class="mt-3">
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control"
                value="<?= old('title') ?>" 
                required
            >
        </div>

        <div class="mb-3">
            <label for="category" class="form-label">Category <span class="text-danger">*</span></label>
            <select name="category_id" id="category" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option 
                        value="<?= htmlspecialchars($cat['id']) ?>" 
                        <?= (input('category_id') == $cat['id']) ? 'selected' : '' ?>
                    >
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

<div class="mb-3">
    <label for="content" class="form-label">Content <span class="text-danger">*</span></label>
 <textarea 
    name="content" 
    id="content" 
    class="form-control rich-editor" 
    rows="10"
><?= old('content') ?></textarea>
</div>

        <button type="submit" class="btn btn-primary">Publish</button>
    </form>
</div>


<?php include '../../templates/footer.php'; ?>