<?php
require_once __DIR__ . '/../init.php';
require_once '../../classes/Category.php';

$categoryObj = new Category();

$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$search = trim($_GET['search'] ?? '');

$allCategories = $categoryObj->all();

// Filter by search term (on category name)
if ($search !== '') {
    $allCategories = array_filter($allCategories, function($cat) use ($search) {
        return stripos($cat['name'], $search) !== false;
    });
}

$total = count($allCategories);
$perPage = 8;
$totalPages = (int) ceil($total / $perPage);
$page = max(1, min($page, $totalPages));
$offset = ($page - 1) * $perPage;
$paginatedItems = array_slice(array_values($allCategories), $offset, $perPage);

include '../../templates/header.php';
?>

<div class="container py-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h1 class="display-6">Category Management</h1>
        <a href="create.php" class="btn btn-primary shadow-sm">+ Add New Category</a>
    </div>

    <form method="GET" class="mb-4" style="max-width: 400px;">
        <div class="input-group">
            <input type="text" name="search" class="form-control" placeholder="Search categories..." value="<?= htmlspecialchars($search) ?>">
            <button type="submit" class="btn btn-outline-secondary">Search</button>
        </div>
    </form>

    <?php if (empty($paginatedItems)): ?>
        <div class="alert alert-warning text-center">
            No categories found<?= $search !== '' ? ' matching your search' : '' ?>.
        </div>
    <?php else: ?>
        <table class="table table-striped table-hover align-middle">
            <thead class="table-light">
                <tr>
                    <th style="width: 80px;">#</th>
                    <th>Category Name</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($paginatedItems as $cat): ?>
                    <tr>
                        <td>
                            <span class="badge bg-secondary"><?= htmlspecialchars($cat['id']) ?></span>
                        </td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td>
                            <a href="edit.php?id=<?= $cat['id'] ?>" class="btn btn-sm btn-outline-primary">Edit</a>
                            <a href="delete.php?id=<?= $cat['id'] ?>"
                               class="btn btn-sm btn-outline-danger"
                               onclick="return confirm('Are you sure you want to delete this category?');">
                               Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>

        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
            <nav>
                <ul class="pagination justify-content-center">
                    <?php
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

<?php include '../../templates/footer.php'; ?>