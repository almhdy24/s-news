<?php
require_once 'functions.php';
require_once 'config.php';
require_once 'classes/News.php';
require_once 'classes/Category.php';

$news = new News();
$categoryObj = new Category();

$page = isset($_GET['page']) ? max(1, (int)$_GET['page']) : 1;
$perPage = 5;

$pagination = $news->paginate($page, $perPage);
$articles = $pagination['items'] ?? [];

include 'templates/header.php';
?>

<div class="container my-4">
    <h1 class="mb-3"><?= htmlspecialchars(get_site_name()) ?> - Latest News</h1>

    <?php if (empty($articles)): ?>
        <div class="alert alert-info">No news articles found.</div>
    <?php else: ?>

<?php foreach ($articles as $article): ?>
    <?php
        $category = $categoryObj->find($article['category_id']);
        $categoryName = $category ? $category['name'] : 'Uncategorized';
    ?>
    <div class="card mb-3">
        <div class="card-body">
            <h3><?= htmlspecialchars($article['title']) ?></h3>
            <p><small class="text-muted"><?= htmlspecialchars($categoryName) ?></small></p>
            <p><?= htmlspecialchars(excerpt($article['content'], 150)) ?></p>
            <a href="view.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-primary">Read More</a>
        </div>
    </div>
<?php endforeach; ?>


        <!-- Pagination Controls -->
        <nav aria-label="News pagination">
          <ul class="pagination justify-content-center">
            <li class="page-item <?= ($pagination['currentPage'] <= 1) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $pagination['currentPage'] - 1 ?>" aria-label="Previous" tabindex="<?= ($pagination['currentPage'] <= 1) ? '-1' : '0' ?>">
                &laquo; Prev
              </a>
            </li>

            <?php for ($i = 1; $i <= $pagination['totalPages']; $i++): ?>
                <li class="page-item <?= ($i === $pagination['currentPage']) ? 'active' : '' ?>">
                  <a class="page-link" href="?page=<?= $i ?>"><?= $i ?></a>
                </li>
            <?php endfor; ?>

            <li class="page-item <?= ($pagination['currentPage'] >= $pagination['totalPages']) ? 'disabled' : '' ?>">
              <a class="page-link" href="?page=<?= $pagination['currentPage'] + 1 ?>" aria-label="Next" tabindex="<?= ($pagination['currentPage'] >= $pagination['totalPages']) ? '-1' : '0' ?>">
                Next &raquo;
              </a>
            </li>
          </ul>
        </nav>
    <?php endif; ?>
</div>

<?php include 'templates/footer.php'; ?>