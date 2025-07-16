
<div class="container mt-4">
    <div class="d-flex justify-content-between align-items-center mb-3">
        <h2><i class="bi bi-gear-fill me-2"></i> Site Settings</h2>
        <a href="/admin" class="btn btn-outline-secondary">
            <i class="bi bi-arrow-left-circle"></i> Back to Dashboard
        </a>
    </div>

    <?= \Core\Flash::display() ?>

    <form method="POST" action="/admin/settings/update" class="border p-4 rounded shadow-sm bg-light">
        <div class="mb-3">
            <label for="site_name" class="form-label fw-semibold">Site Name</label>
            <input
                type="text"
                id="site_name"
                name="site_name"
                class="form-control"
                required
                value="<?= htmlspecialchars($settings['site_name'] ?? '') ?>"
            >
        </div>

<div class="mb-3">
    <label for="theme" class="form-label fw-semibold">Theme</label>
    <select id="theme" name="theme" class="form-select" required>
        <?php foreach ($availableThemes as $theme): ?>
            <option value="<?= htmlspecialchars($theme) ?>"
                <?= ($settings['theme'] ?? '') === $theme ? 'selected' : '' ?>>
                <?= ucfirst($theme) ?>
            </option>
        <?php endforeach; ?>
    </select>
</div>

        <div class="mb-3">
            <label for="installed" class="form-label fw-semibold">Installation Status</label>
            <select id="installed" name="installed" class="form-select" required>
                <option value="1" <?= !empty($settings['installed']) ? 'selected' : '' ?>>Installed</option>
                <option value="0" <?= empty($settings['installed']) ? 'selected' : '' ?>>Not Installed</option>
            </select>
        </div>

        <div class="d-flex justify-content-between">
            <button type="submit" class="btn btn-primary">
                <i class="bi bi-save me-1"></i> Save Settings
            </button>
            <a href="/admin" class="btn btn-secondary">
                <i class="bi bi-x-circle"></i> Cancel
            </a>
        </div>
    </form>
</div>

