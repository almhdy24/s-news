<?php
// Make sure session is started and functions.php is included earlier in your workflow.
$user = current_user();
?>

<footer class="text-center py-4 bg-light mt-auto">
    <small>&copy; <?= date('Y') ?> <?= htmlspecialchars(get_site_name()) ?></small> |
    <?php if ($user): ?>
        <a href="/admin/index.php">Admin Dashboard</a> |
        <a href="/admin/logout.php">Logout</a>
    <?php else: ?>
        <a href="/admin/login.php">Login</a>
    <?php endif; ?>
</footer>

<script src="https://cdn.ckeditor.com/ckeditor5/39.0.1/classic/ckeditor.js"></script>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const editors = document.querySelectorAll('.rich-editor');
        editors.forEach(textarea => {
            ClassicEditor
                .create(textarea)
                .catch(error => console.error(error));
        });
    });
</script>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>