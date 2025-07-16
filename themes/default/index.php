<?php
/** @var Core\ThemeManager $theme */
$theme = $GLOBALS['theme'];
?>

<?php $theme->partial('header.php'); ?>

<div class="container my-5">
    <h1 class="mb-4">Latest Published News</h1>

    <?php if (empty($articles)): ?>
        <div class="alert alert-info">No news articles available.</div>
    <?php else: ?>
        <?php foreach ($articles as $article): ?>
            <div class="mb-4">
                <h3><?= htmlspecialchars($article['title']) ?></h3>
                <div class="text-muted small"><?= date('F j, Y', strtotime($article['created_at'])) ?></div>
                <p><?= mb_substr(strip_tags($article['content']), 0, 150) ?>...</p>
                <a href="/view?id=<?= $article['id'] ?>" class="btn btn-sm btn-primary">Read More</a>
            </div>
        <?php endforeach; ?>

        <!-- Pagination -->
        <nav>
            <ul class="pagination">
                <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                    <li class="page-item <?= $i === $pagination['currentPage'] ? 'active' : '' ?>">
                        <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                    </li>
                <?php endfor; ?>
            </ul>
        </nav>
    <?php endif; ?>
</div>

<?php $theme->partial('footer.php'); ?>