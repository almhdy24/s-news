<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <title><?= e(get_site_name()) ?></title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  
  <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
  <link href="<?= e(theme_asset('css/style.css')) ?>" rel="stylesheet">
  
  <?php do_action('head'); ?>
</head>
<body>

<?php include theme_path('nav.php'); ?>