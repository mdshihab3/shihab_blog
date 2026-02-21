<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$user = current_user();
$page_title = 'My Profile';
$page_css = ['admin.css'];

$error = '';
$success = '';

if (is_post()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm = $_POST['confirm_password'] ?? '';

    if (!$name || !$email) {
        $error = 'Name and email are required.';
    } elseif ($password && $password !== $confirm) {
        $error = 'Passwords do not match.';
    } elseif ($password && strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } else {
        // Check if email already exists for another user
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ? AND id != ?");
        $stmt->bind_param('si', $email, $user['id']);
        $stmt->execute();
        if ($stmt->get_result()->num_rows > 0) {
            $error = 'Email already in use by another account.';
        } else {
            $conn->query("UPDATE users SET name = '$name', email = '$email' WHERE id = {$user['id']}");
            if ($password) {
                $hash = password_hash($password, PASSWORD_DEFAULT);
                $conn->query("UPDATE users SET password_hash = '$hash' WHERE id = {$user['id']}");
            }
            // Update session
            $_SESSION['user']['name'] = $name;
            $_SESSION['user']['email'] = $email;
            $success = 'Profile updated successfully.';
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">My Profile</h5>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <?php if ($success): ?>
                    <div class="alert alert-success"><?= e($success) ?></div>
                <?php endif; ?>
                <form method="post">
                    <div class="mb-3">
                        <label for="name" class="form-label">Name</label>
                        <input type="text" class="form-control" id="name" name="name" value="<?= e($user['name']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="email" class="form-label">Email</label>
                        <input type="email" class="form-control" id="email" name="email" value="<?= e($user['email']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="password" class="form-label">New Password (leave blank to keep current)</label>
                        <input type="password" class="form-control" id="password" name="password">
                    </div>
                    <div class="mb-3">
                        <label for="confirm_password" class="form-label">Confirm New Password</label>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password">
                    </div>
                    <button type="submit" class="btn btn-primary">Update Profile</button>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>