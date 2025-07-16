<div class="d-flex justify-content-between align-items-center mt-4 mb-3">
    <h2>All News</h2>
    <a href="/admin/news/create" class="btn btn-success" aria-label="Add new news article">
        <i class="bi bi-plus-lg me-1"></i> Add News
    </a>
</div>

<?php if (empty($news)): ?>
    <div class="alert alert-info text-center" role="alert">
        No news articles found. <a href="/admin/news/create" class="alert-link">Create your first news article</a>.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th>ID</th>
                    <th>Title</th>
                    <th>Category</th>
                    <th>Status</th>
                    <th>Created At</th>
                    <th style="width: 160px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($news as $item): ?>
                    <tr>
                        <td class="text-center"><?= (int)$item['id'] ?></td>
                        <td><?= htmlspecialchars($item['title']) ?></td>
                        <td><?= htmlspecialchars($categories[$item['category_id']]['name'] ?? '—') ?></td>
                        <td class="text-center">
                            <span class="badge bg-<?= $item['status'] === 'published' ? 'success' : 'secondary' ?>"
                                  aria-label="Status: <?= htmlspecialchars(ucfirst($item['status'])) ?>">
                                <i class="bi bi-circle-fill me-1"></i><?= ucfirst($item['status']) ?>
                            </span>
                        </td>
                        <td class="text-center" title="<?= date('Y-m-d H:i:s', strtotime($item['created_at'])) ?>">
                            <?= date('Y-m-d', strtotime($item['created_at'])) ?>
                        </td>
                        <td class="text-center">
                            <a href="/admin/news/edit/<?= (int)$item['id'] ?>"
                               class="btn btn-sm btn-primary me-1"
                               aria-label="Edit news article <?= htmlspecialchars($item['title']) ?>">
                                <i class="bi bi-pencil me-1"></i>Edit
                            </a>
                            <a href="/admin/news/delete/<?= (int)$item['id'] ?>"
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this news item?');"
                               aria-label="Delete news article <?= htmlspecialchars($item['title']) ?>">
                                <i class="bi bi-trash me-1"></i>Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>