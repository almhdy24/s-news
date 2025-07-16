<h2 class="d-flex justify-content-between align-items-center mb-3">
    <span><i class="bi bi-tags me-2"></i>Categories</span>
    <a href="/admin/categories/create" class="btn btn-success">
        <i class="bi bi-plus-circle"></i> Add Category
    </a>
</h2>

<?php if (empty($categories)): ?>
    <div class="alert alert-info text-center" role="alert">
        No categories found. <a href="/admin/categories/create" class="alert-link">Create your first category</a>.
    </div>
<?php else: ?>
    <div class="table-responsive">
        <table class="table table-bordered table-hover align-middle">
            <thead class="table-light text-center">
                <tr>
                    <th style="width: 60px;">ID</th>
                    <th>Name</th>
                    <th style="width: 180px;">Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php foreach ($categories as $cat): ?>
                    <tr>
                        <td class="text-center"><?= (int)$cat['id'] ?></td>
                        <td><?= htmlspecialchars($cat['name']) ?></td>
                        <td class="text-center">
                            <a href="/admin/categories/edit/<?= $cat['id'] ?>" 
                               class="btn btn-sm btn-primary me-1" 
                               aria-label="Edit category <?= htmlspecialchars($cat['name']) ?>">
                                <i class="bi bi-pencil-square"></i> Edit
                            </a>
                            <a href="/admin/categories/delete/<?= $cat['id'] ?>" 
                               class="btn btn-sm btn-danger"
                               onclick="return confirm('Are you sure you want to delete this category?')"
                               aria-label="Delete category <?= htmlspecialchars($cat['name']) ?>">
                                <i class="bi bi-trash"></i> Delete
                            </a>
                        </td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
<?php endif; ?>