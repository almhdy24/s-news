
<div class="container mt-4">
  <div class="d-flex justify-content-between align-items-center mb-3">
    <h2><i class="bi bi-pencil-square"></i> Edit Category</h2>
    <a href="/admin/categories" class="btn btn-outline-secondary">
      <i class="bi bi-arrow-left"></i> Back to Categories
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

  <form method="post" action="/admin/categories/edit/<?= htmlspecialchars($category['id']) ?>" class="card p-4 shadow-sm">
    <div class="mb-3">
      <label for="name" class="form-label">Category Name</label>
      <input type="text" id="name" name="name" class="form-control" value="<?= htmlspecialchars($category['name']) ?>" required>
    </div>

    <div class="d-flex gap-2">
      <button type="submit" class="btn btn-primary">
        <i class="bi bi-check-lg"></i> Update
      </button>
      <a href="/admin/categories" class="btn btn-secondary">
        <i class="bi bi-x-circle"></i> Cancel
      </a>
    </div>
  </form>
</div>
