<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$page_title = 'Manage Posts';
$page_css = ['admin.css'];

$posts = $conn->query("
    SELECT p.*, c.name AS category_name, u.name AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    JOIN users u ON p.user_id = u.id
    ORDER BY p.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Posts</h1>
    <a href="<?= base_url('admin/post_create.php') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> New Post</a>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-striped admin-table">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Title</th>
                        <th>Category</th>
                        <th>Author</th>
                        <th>Status</th>
                        <th>Date</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php foreach ($posts as $p): ?>
                        <tr>
                            <td><?= $p['id'] ?></td>
                            <td><?= e($p['title']) ?></td>
                            <td><?= e($p['category_name'] ?? '-') ?></td>
                            <td><?= e($p['author_name']) ?></td>
                            <td>
                                <span class="status-badge status-<?= $p['status'] ?>">
                                    <?= ucfirst($p['status']) ?>
                                </span>
                            </td>
                            <td><?= date('M d, Y', strtotime($p['created_at'])) ?></td>
                            <td>
                                <a href="<?= base_url('admin/post_edit.php?id=' . $p['id']) ?>" class="btn btn-sm btn-warning"><i class="fas fa-edit"></i></a>
                                <a href="<?= base_url('admin/post_delete.php?id=' . $p['id']) ?>" class="btn btn-sm btn-danger" onclick="return confirm('Are you sure?')"><i class="fas fa-trash"></i></a>
                            </td>
                        </tr>
                    <?php endforeach; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>