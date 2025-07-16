<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8" />
  <title>Admin Panel - S-News</title>
  <meta name="viewport" content="width=device-width, initial-scale=1" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet" />
  <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.10.5/font/bootstrap-icons.css" rel="stylesheet">
  <style>body { padding-top: 70px; }</style>
</head>
<body>

<?php require __DIR__ . '/header.php'; ?>

<div class="container">
  <?= \Core\Flash::display() ?>
  <?= $content ?? '' ?>
</div>

<?php require __DIR__ . '/footer.php'; ?>
</body>
</html>