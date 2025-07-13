<?php
require_once '../init.php';
require_once '../../classes/Category.php';

if (!isset($_GET['id'])) {
    flash('error', 'Category ID is required.');
    redirect('index.php');
}

$categoryObj = new Category();
$id = $_GET['id'];

// Check if category exists
$category = $categoryObj->find($id);
if (!$category) {
    flash('error', 'Category not found.');
    redirect('index.php');
}

// Delete category
$categoryObj->delete($id);

flash('success', 'Category deleted successfully.');
redirect('index.php');