<?php
require_once '../../functions.php';
require_once '../../classes/News.php';

if (!isset($_GET['id'])) {
    flash('error', 'No article ID provided.');
    redirect('../index.php');
}

$id = $_GET['id'];
$news = new News();
$article = $news->find($id);

if (!$article) {
    flash('error', 'Article not found.');
    redirect('../index.php');
}

$news->delete($id);
flash('success', 'Article deleted successfully.');
redirect('../index.php');