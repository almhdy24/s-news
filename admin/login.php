<?php
require_once '../functions.php';
require_once '../classes/User.php';

$errors = [];

$auth = new User();

if (is_post()) {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';

    if (!$username || !$password) {
        $errors[] = 'Please enter both username and password.';
    } else {
        $user = $auth->attempt($username, $password);
        if ($user) {
            // Store essential user info in session for current_user() helper
            $_SESSION['user'] = [
                'id' => $user['id'],
                'username' => $user['username'],
                'role' => $user['role'] ?? 'user',
            ];
            flash('success', 'Welcome back, ' . htmlspecialchars($user['username']) . '!');
            redirect('/admin/index.php');
        } else {
            $errors[] = 'Invalid credentials.';
        }
    }
}

include '../templates/header.php';
?>

<div class="container" style="max-width: 400px;">
    <h2 class="my-4">Admin Login</h2>

    <?php if ($errors): ?>
        <div class="alert alert-danger">
            <?php foreach ($errors as $e): ?>
                <div><?= htmlspecialchars($e) ?></div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>

    <form method="POST" novalidate>
        <div class="mb-3">
            <label for="username" class="form-label">Username</label>
            <input id="username" type="text" name="username" class="form-control" value="<?= htmlspecialchars($_POST['username'] ?? '') ?>" required autofocus>
        </div>

        <div class="mb-3">
            <label for="password" class="form-label">Password</label>
            <input id="password" type="password" name="password" class="form-control" required>
        </div>

        <button type="submit" class="btn btn-primary w-100">Login</button>
    </form>
</div>

<?php include '../templates/footer.php'; ?>