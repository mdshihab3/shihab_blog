<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('index.php');

$cat = $conn->query("SELECT * FROM categories WHERE id = $id")->fetch_assoc();
if (!$cat) die('Category not found.');

$page_title = 'Category: ' . $cat['name'];

$posts = $conn->query("
    SELECT p.*, u.name AS author_name
    FROM posts p
    JOIN users u ON p.user_id = u.id
    WHERE p.category_id = $id AND p.status = 'published'
    ORDER BY p.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h2>Category: <?= e($cat['name']) ?></h2>
                <p><?= count($posts) ?> post(s) in this category</p>
            </div>
        </div>

        <?php if ($posts): ?>
            <div class="row">
                <?php foreach ($posts as $post): ?>
                    <div class="col-md-6 mb-3">
                        <div class="card h-100">
                            <?php if (!empty($post['cover_image']) && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
                                <img src="<?= UPLOAD_URL . e($post['cover_image']) ?>" class="card-img-top" alt="<?= e($post['title']) ?>" style="height:150px; object-fit:cover;">
                            <?php endif; ?>
                            <div class="card-body">
                                <h5 class="card-title">
                                    <a href="<?= base_url('post.php?slug=' . $post['slug']) ?>" class="text-decoration-none">
                                        <?= e($post['title']) ?>
                                    </a>
                                </h5>
                                <p class="card-text text-muted small">
                                    By <?= e($post['author_name']) ?> | <?= date('M d, Y', strtotime($post['created_at'])) ?>
                                </p>
                                <p class="card-text"><?= e(mb_strimwidth(strip_tags($post['content']), 0, 120, '...')) ?></p>
                            </div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        <?php else: ?>
            <div class="alert alert-info">No posts in this category yet.</div>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>