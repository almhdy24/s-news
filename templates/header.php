<?php
require_once __DIR__ . '/../functions.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8" />
    <title><?= htmlspecialchars(get_site_name()) ?></title>
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
    <!-- CKEditor 5 Classic Build -->
<script src="https://cdn.ckeditor.com/ckeditor5/41.1.0/classic/ckeditor.js"></script>

</head>
<body>

<?php include __DIR__ . '/nav.php'; ?>
<?php include __DIR__ . '/flash.php'; ?>