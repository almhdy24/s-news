<div class="py-4">
  <div class="mb-4">
    <h1 class="h3">Welcome to the Admin Dashboard</h1>
    <p class="text-muted">Hello, <?= htmlspecialchars($user['username'] ?? 'Guest') ?></p>
  </div>

  <div class="row g-4">

    <!-- Visitor Stats -->
    <div class="col-md-4">
      <div class="card border-0 shadow h-100">
        <div class="card-header bg-primary text-white">
          <i class="bi bi-people-fill me-2"></i>Visitors Today
        </div>
        <div class="card-body">
          <h3 class="card-title mb-2"><?= $uniqueToday ?></h3>
          <p class="card-text text-muted">Unique visitors counted today.</p>
        </div>
      </div>
    </div>

    <div class="col-md-4">
      <div class="card border-0 shadow h-100">
        <div class="card-header bg-success text-white">
          <i class="bi bi-globe2 me-2"></i>Total Unique Visitors
        </div>
        <div class="card-body">
          <h3 class="card-title mb-2"><?= $uniqueTotal ?></h3>
          <p class="card-text text-muted">Since logging began.</p>
        </div>
      </div>
    </div>

    <!-- JSON DB Info -->
    <div class="col-md-4">
      <div class="card border-0 shadow h-100">
        <div class="card-header text-white <?= $healthStatus === 'Healthy' ? 'bg-info' : 'bg-warning' ?>">
          <i class="bi bi-hdd-fill me-2"></i>JSON Database Status
        </div>
        <div class="card-body">
          <p class="mb-1"><strong>Files:</strong> <?= $fileCount ?></p>
          <p class="mb-1"><strong>Total Size:</strong> <?= number_format($totalSizeBytes / 1024, 2) ?> KB</p>
          <p class="mb-0"><strong>Status:</strong> <?= $healthStatus ?></p>
        </div>
      </div>
    </div>

    <!-- News Info -->
    <div class="col-md-6">
      <div class="card border-0 shadow h-100">
        <div class="card-header bg-secondary text-white">
          <i class="bi bi-newspaper me-2"></i>News Articles
        </div>
        <div class="card-body">
          <p class="mb-2"><strong>Total Articles:</strong> <?= $totalNews ?></p>
          <p class="mb-0"><strong>Published:</strong> <?= $publishedNews ?></p>
        </div>
      </div>
    </div>

  </div>
</div>