<?php
/** @var Core\ThemeManager $theme */
$theme = $GLOBALS['theme'];
?>
<?php $theme->partial('header.php'); ?>

<div class="container my-5">
    <?php if (!$news): ?>
        <div class="alert alert-danger">Article not found.</div>
    <?php else: ?>
        <h1 class="mb-3"><?= htmlspecialchars($news['title']) ?></h1>

        <?php if (!empty($category)): ?>
            <p class="text-muted">Category: 
                <span class="badge bg-secondary"><?= htmlspecialchars($category['name']) ?></span>
            </p>
        <?php endif; ?>

        <div class="text-muted mb-3">
            Published on <?= date('F j, Y', strtotime($news['created_at'])) ?>
        </div>

        <div><?= nl2br(htmlspecialchars($news['content'])) ?></div>
    <?php endif; ?>
</div>

<?php $theme->partial('footer.php'); ?>