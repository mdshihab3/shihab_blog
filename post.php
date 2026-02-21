<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$slug = $_GET['slug'] ?? '';
if (!$slug) redirect('index.php');

// Fetch post
$stmt = $conn->prepare("
    SELECT p.*, c.name AS category_name, u.name AS author_name
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE p.slug = ? AND p.status = 'published'
");
$stmt->bind_param('s', $slug);
$stmt->execute();
$post = $stmt->get_result()->fetch_assoc();

if (!$post) {
    die('Post not found.');
}

$page_title = $post['title'];

// Handle comment submission
$comment_msg = '';
if (is_post()) {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $comment = trim($_POST['comment'] ?? '');

    if ($name && $comment) {
        $stmt = $conn->prepare("INSERT INTO comments (post_id, name, email, comment) VALUES (?, ?, ?, ?)");
        $stmt->bind_param('isss', $post['id'], $name, $email, $comment);
        $stmt->execute();
        $_SESSION['flash'] = ['type' => 'success', 'message' => 'Comment added successfully!'];
        redirect('post.php?slug=' . $slug);
    } else {
        $comment_msg = 'Name and comment are required.';
    }
}

// Fetch comments
$comments = $conn->query("SELECT * FROM comments WHERE post_id = {$post['id']} ORDER BY created_at DESC")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card mb-4">
            <?php if (!empty($post['cover_image']) && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
                <img src="<?= UPLOAD_URL . e($post['cover_image']) ?>" class="card-img-top" alt="<?= e($post['title']) ?>">
            <?php endif; ?>
            <div class="card-body">
                <h1 class="card-title"><?= e($post['title']) ?></h1>
                <p class="text-muted">
                    <i class="fas fa-user me-1"></i><?= e($post['author_name']) ?>
                    <i class="fas fa-calendar ms-3 me-1"></i><?= date('F d, Y', strtotime($post['created_at'])) ?>
                    <?php if (!empty($post['category_name'])): ?>
                        <a href="<?= base_url('category.php?id=' . $post['category_id']) ?>" class="badge bg-primary text-decoration-none ms-3"><?= e($post['category_name']) ?></a>
                    <?php endif; ?>
                </p>
                <div class="post-content">
                    <?= nl2br(e($post['content'])) ?>
                </div>
            </div>
        </div>

        <!-- Comments -->
        <div class="card mb-4">
            <div class="card-body">
                <h4>Comments (<?= count($comments) ?>)</h4>
                <?php if ($comment_msg): ?>
                    <div class="alert alert-danger"><?= e($comment_msg) ?></div>
                <?php endif; ?>
                <form method="post" class="mb-4">
                    <div class="row g-2">
                        <div class="col-md-6">
                            <input class="form-control" name="name" placeholder="Your name*" required>
                        </div>
                        <div class="col-md-6">
                            <input class="form-control" name="email" type="email" placeholder="Email (optional)">
                        </div>
                        <div class="col-12">
                            <textarea class="form-control" name="comment" rows="3" placeholder="Your comment*" required></textarea>
                        </div>
                        <div class="col-12">
                            <button class="btn btn-primary">Submit</button>
                        </div>
                    </div>
                </form>

                <?php if ($comments): ?>
                    <?php foreach ($comments as $c): ?>
                        <div class="border-bottom mb-3 pb-3">
                            <div class="fw-bold"><?= e($c['name']) ?></div>
                            <div class="text-muted small"><?= date('M d, Y h:i A', strtotime($c['created_at'])) ?></div>
                            <div class="mt-2"><?= nl2br(e($c['comment'])) ?></div>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p class="text-muted">No comments yet. Be the first to comment!</p>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>