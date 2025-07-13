<?php
require_once __DIR__ . '/init.php';
require_once '../classes/News.php';
require_once '../classes/Category.php';

$news = new News();
$categories = (new Category())->all();
$catMap = [];
foreach ($categories as $cat) {
    $catMap[$cat['id']] = $cat['name'];
}

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = trim($_GET['search'] ?? '');

// Get all news
$allNews = $news->all();

// Filter by search if provided
if ($search !== '') {
    $allNews = array_filter($allNews, function($article) use ($search) {
        return stripos($article['title'], $search) !== false;
    });
}

// Paginate filtered results manually
$total = count($allNews);
$perPage = 6;
$totalPages = (int) ceil($total / $perPage);
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;
$paginatedItems = array_slice(array_values($allNews), $offset, $perPage);

include '../templates/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6">News Management</h1>
        <a href="news/create.php" class="btn btn-primary shadow-sm">+ Add New Article</a>
    </div>

    <form method="GET" class="mb-4">
        <div class="input-group" style="max-width: 400px;">
            <input type="text" name="search" class="form-control" placeholder="Search news by title..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
        </div>
    </form>

    <?php if (empty($paginatedItems)): ?>
        <div class="alert alert-warning text-center">
            No news articles found<?= $search !== '' ? ' matching your search' : '' ?>.
        </div>
    <?php else: ?>
        <div class="row g-4">
            <?php foreach ($paginatedItems as $article): ?>
                <div class="col-md-6 col-xl-4">
                    <div class="card h-100 shadow-sm border-0">
                        <div class="card-body d-flex flex-column">
                            <h5 class="card-title mb-2"><?= htmlspecialchars($article['title']) ?></h5>
                            <p class="card-text text-muted small">
                                <?= excerpt($article['content'], 100) ?>
                            </p>
                            <div class="mt-auto d-flex justify-content-between align-items-center">
                                <span class="badge bg-secondary">
                                    <?= htmlspecialchars($catMap[$article['category_id']] ?? 'Uncategorized') ?>
                                </span>
                                <div>
                                    <a href="news/edit.php?id=<?= $article['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                                    <a href="news/delete.php?id=<?= $article['id'] ?>"
                                       class="btn btn-sm btn-outline-danger"
                                       onclick="return confirm('Are you sure you want to delete this article?');">Delete</a>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer text-muted small">
                            <?= date('M j, Y', strtotime($article['created_at'] ?? '')) ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav class="mt-5">
                <ul class="pagination justify-content-center">
                    <?php
                    // Preserve search parameter on pagination links
                    $queryBase = $search !== '' ? '&search=' . urlencode($search) : '';
                    for ($i = 1; $i <= $totalPages; $i++): ?>
                        <li class="page-item <?= $i === $page ? 'active' : '' ?>">
                            <a class="page-link" href="?page=<?= $i . $queryBase ?>"><?= $i ?></a>
                        </li>
                    <?php endfor; ?>
                </ul>
            </nav>
        <?php endif; ?>
    <?php endif; ?>
</div>

<?php include '../templates/footer.php'; ?>