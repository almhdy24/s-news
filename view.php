<?php
require_once 'functions.php'; 
require_once 'classes/News.php';
require_once 'classes/Category.php';

$news = new News();
$category = new Category();

$id = $_GET['id'] ?? '';
$article = $news->find($id);
include 'templates/header.php';
?>

<div class="container my-5">
    <?php if (!$article): ?>
        <div class="alert alert-danger">
            <h4>Article Not Found</h4>
            <p>The article you're looking for doesn't exist or has been deleted.</p>
        </div>
    <?php else: ?>
        <div class="mb-4">
            <h1><?= htmlspecialchars($article['title']) ?></h1>
            <div class="text-muted small">
                Posted on <?= date('F j, Y', strtotime($article['created_at'] ?? '')) ?>
                <?php if (!empty($article['category_id'])): ?>
                    <?php $cat = $category->find($article['category_id']); ?>
                    <?php if ($cat): ?>
                        &middot; Category: 
                        <span class="badge bg-secondary"><?= htmlspecialchars($cat['name']) ?></span>
                    <?php endif; ?>
                <?php endif; ?>
            </div>
        </div>

        <div class="article-body">
            <?= $article['content'] ?>
        </div>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>