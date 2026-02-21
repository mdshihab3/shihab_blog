<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';




$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);


$posts = $conn->query("
    SELECT p.*, c.name AS category_name, u.name AS author_name 
    FROM posts p
    LEFT JOIN categories c ON p.category_id = c.id
    JOIN users u ON p.user_id = u.id
    WHERE p.status = 'published'
    ORDER BY p.created_at DESC
")->fetch_all(MYSQLI_ASSOC);

include __DIR__ . '/includes/header.php';
?>

<!-- Hero Section -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card bg-dark text-white p-5 text-center">
            <h1 class="display-4">Welcome to <?= SITE_NAME ?></h1>
            <p class="lead">Share your thoughts, stories, and ideas with the world.</p>
            <?php if (!current_user()): ?>
                <div>
                    <a href="<?= base_url('login.php') ?>" class="btn btn-primary btn-lg">Login</a>
                    <a href="<?= base_url('register.php') ?>" class="btn btn-outline-light btn-lg">Register</a>
                </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<!-- Categories -->
<div class="row mb-4">
    <div class="col-12">
        <div class="card p-3">
            <h4>Categories</h4>
            <div class="d-flex flex-wrap gap-2">
                <?php foreach ($categories as $cat): ?>
                    <a href="<?= base_url('category.php?id=' . $cat['id']) ?>" class="badge bg-primary text-decoration-none p-2">
                        <?= e($cat['name']) ?>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>

<!-- Posts -->
<div class="row">
    <?php if (empty($posts)): ?>
        <div class="col-12">
            <div class="card p-5 text-center">
                <h3>No posts yet</h3>
                <p class="text-muted">Check back later for new content!</p>
            </div>
        </div>
    <?php else: ?>
        <?php foreach ($posts as $post): ?>
            <div class="col-md-6 col-lg-4 mb-4">
                <div class="card h-100">
                    <?php if (!empty($post['cover_image']) && file_exists(UPLOAD_DIR . $post['cover_image'])): ?>
                        <img src="<?= UPLOAD_URL . e($post['cover_image']) ?>" class="card-img-top" alt="<?= e($post['title']) ?>" style="height:200px; object-fit:cover;">
                    <?php else: ?>
                        <img src="https://via.placeholder.com/400x200?text=No+Image" class="card-img-top" alt="No image">
                    <?php endif; ?>
                    <div class="card-body">
                        <h5 class="card-title"><?= e($post['title']) ?></h5>
                        <p class="card-text text-muted small">
                            <i class="fas fa-user me-1"></i><?= e($post['author_name']) ?>
                            <i class="fas fa-calendar ms-2 me-1"></i><?= date('M d, Y', strtotime($post['created_at'])) ?>
                            <?php if (!empty($post['category_name'])): ?>
                                <span class="badge bg-secondary ms-2"><?= e($post['category_name']) ?></span>
                            <?php endif; ?>
                        </p>
                        <p class="card-text"><?= e(mb_strimwidth(strip_tags($post['content']), 0, 100, '...')) ?></p>
                        <a href="<?= base_url('post.php?slug=' . $post['slug']) ?>" class="btn btn-primary">Read More</a>
                    </div>
                </div>
            </div>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>