<?php
require_once '../functions.php';
logout_user();
flash('success', 'You are logged out.');
redirect('login.php');