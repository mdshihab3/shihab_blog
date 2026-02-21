<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$page_title = 'Manage Categories';
$page_css = ['admin.css'];


if (is_post() && isset($_POST['name'])) {
    $name = trim($_POST['name']);
    if ($name) {
        $stmt = $conn->prepare("INSERT INTO categories (name) VALUES (?)");
        $stmt->bind_param('s', $name);
        $stmt->execute();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category added.'];
    }
    redirect('admin/categories.php');
}
if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM categories WHERE id = $id");
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Category deleted.'];
    redirect('admin/categories.php');
}

$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<div class="row">
    <div class="col-md-6">
        <div class="card mb-4">
            <div class="card-body">
                <h5 class="card-title">Add New Category</h5>
                <form method="post">
                    <div class="input-group">
                        <input type="text" class="form-control" name="name" placeholder="Category name" required>
                        <button class="btn btn-primary" type="submit">Add</button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <div class="col-md-6">
        <div class="card">
            <div class="card-body">
                <h5 class="card-title">Existing Categories</h5>
                <ul class="list-group">
                    <?php foreach ($categories as $cat): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <?= e($cat['name']) ?>
                            <a href="?delete=<?= $cat['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete category?')"><i class="fas fa-trash"></i></a>
                        </li>
                    <?php endforeach; ?>
                </ul>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>