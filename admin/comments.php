<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$page_title = 'Manage Comments';
$page_css = ['admin.css'];

if (isset($_GET['delete'])) {
    $id = (int)$_GET['delete'];
    $conn->query("DELETE FROM comments WHERE id = $id");
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Comment deleted.'];
    redirect('admin/comments.php');
}

$comments = $conn->query("
    SELECT c.*, p.title AS post_title
    FROM comments c
    JOIN posts p ON c.post_id = p.id
    ORDER BY c.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/../includes/header.php';
?>

<div class="card">
    <div class="card-body">
        <h5 class="card-title">All Comments</h5>
        <?php if ($comments): ?>
            <div class="table-responsive">
                <table class="table table-striped admin-table">
                    <thead>
                        <tr>
                            <th>Post</th>
                            <th>Name</th>
                            <th>Comment</th>
                            <th>Date</th>
                            <th>Action</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php foreach ($comments as $c): ?>
                            <tr>
                                <td><?= e($c['post_title']) ?></td>
                                <td><?= e($c['name']) ?></td>
                                <td><?= e(mb_strimwidth($c['comment'], 0, 50, '...')) ?></td>
                                <td><?= date('M d, Y', strtotime($c['created_at'])) ?></td>
                                <td>
                                    <a href="?delete=<?= $c['id'] ?>" class="btn btn-sm btn-danger" onclick="return confirm('Delete comment?')"><i class="fas fa-trash"></i></a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    </tbody>
                </table>
            </div>
        <?php else: ?>
            <p class="text-muted">No comments yet.</p>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>