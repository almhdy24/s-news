<?php
require_once __DIR__ . '/../init.php'; // fix path

require_once __DIR__ . '/../../classes/News.php';
require_once __DIR__ . '/../../classes/Category.php';

$news = new News();
$categoryObj = new Category();
$categories = $categoryObj->all();

$id = $_GET['id'] ?? null;
$article = $id ? $news->find($id) : null;

if (!$article) {
    flash('error', 'News article not found.');
    redirect('../../admin/index.php');
    exit;
}

$errors = [];

// Helper
function input($key) {
    return trim($_POST[$key] ?? '');
}

// Form state defaults
$title = $article['title'];
$content = $article['content'];
$category_id = $article['category_id'];

// Handle POST
if (is_post()) {
    $title = sanitize(input('title'));
    $content = $_POST['content'] ?? ''; // Allow HTML
    $category_id = input('category_id');

    // Validation
    if ($title === '') $errors[] = 'Title is required.';
    if (trim(strip_tags($content)) === '') $errors[] = 'Content is required.';
    if (!$category_id || !$categoryObj->find($category_id)) $errors[] = 'Valid category is required.';

    if (empty($errors)) {
        $news->update($id, [
            'title' => $title,
            'content' => $content,
            'category_id' => $category_id,
            'updated_at' => date('Y-m-d H:i:s')
        ]);
        flash('success', 'Article updated.');
        redirect('../../admin/index.php');
    }
}

include '../../templates/header.php';
?>

<div class="container">
    <h2 class="mb-4">Edit News Article</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <ul class="mb-0">
                <?php foreach ($errors as $e): ?>
                    <li><?= htmlspecialchars($e) ?></li>
                <?php endforeach; ?>
            </ul>
        </div>
    <?php endif; ?>

    <form method="POST">
        <div class="mb-3">
            <label for="title" class="form-label">Title <span class="text-danger">*</span></label>
            <input 
                type="text" 
                id="title" 
                name="title" 
                class="form-control"
                value="<?= htmlspecialchars($title) ?>" 
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
                        <?= ($category_id == $cat['id']) ? 'selected' : '' ?>
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
    ><?= htmlspecialchars_decode($content ?? '', ENT_QUOTES) ?></textarea>
</div>

        <button type="submit" class="btn btn-primary">Update Article</button>
        <a href="../../admin/index.php" class="btn btn-secondary">Cancel</a>
    </form>
</div>

<?php include '../../templates/footer.php'; ?>