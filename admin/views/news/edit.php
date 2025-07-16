<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-pencil-square me-2"></i>Edit News</h2>
        <a href="/admin/news" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left"></i> Back to News
        </a>
    </div>

    <?php if (!empty($errors)): ?>
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
            <label class="form-label" for="title">Title</label>
            <input type="text" id="title" name="title" class="form-control" required
                   value="<?= htmlspecialchars($title ?? '') ?>" placeholder="Enter article title">
        </div>

        <div class="mb-3">
            <label class="form-label" for="content">Content</label>
            <textarea id="content" name="content" class="form-control" rows="6" required
                      placeholder="Update the content"><?= htmlspecialchars($content ?? '') ?></textarea>
        </div>

        <div class="mb-3">
            <label class="form-label" for="category_id">Category</label>
            <select id="category_id" name="category_id" class="form-select" required>
                <option value="">-- Select Category --</option>
                <?php foreach ($categories as $cat): ?>
                    <option value="<?= $cat['id'] ?>" <?= (isset($category_id) && $category_id == $cat['id']) ? 'selected' : '' ?>>
                        <?= htmlspecialchars($cat['name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>

        <div class="mb-3">
            <label class="form-label" for="status">Status</label>
            <select id="status" name="status" class="form-select" required>
                <option value="draft" <?= ($status ?? '') === 'draft' ? 'selected' : '' ?>>Draft</option>
                <option value="published" <?= ($status ?? '') === 'published' ? 'selected' : '' ?>>Published</option>
            </select>
        </div>

        <div class="d-flex justify-content-start gap-2">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Update
            </button>
            <a href="/admin/news" class="btn btn-secondary">
                <i class="bi bi-x-circle me-1"></i> Cancel
            </a>
        </div>
    </form>
</div>