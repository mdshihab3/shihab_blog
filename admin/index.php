<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$page_title = 'Dashboard';
$page_css = ['admin.css'];

$total_posts = $conn->query("SELECT COUNT(*) as c FROM posts")->fetch_assoc()['c'];
$total_comments = $conn->query("SELECT COUNT(*) as c FROM comments")->fetch_assoc()['c'];
$total_cats = $conn->query("SELECT COUNT(*) as c FROM categories")->fetch_assoc()['c'];

include __DIR__ . '/../includes/header.php';
?>

<div class="d-flex justify-content-between align-items-center mb-4">
    <h1 class="h3">Dashboard</h1>
    <a href="<?= base_url('admin/post_create.php') ?>" class="btn btn-primary"><i class="fas fa-plus"></i> New Post</a>
</div>

<div class="row">
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-primary">
            <div class="card-body">
                <h5 class="card-title">Total Posts</h5>
                <p class="display-4"><?= $total_posts ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-success">
            <div class="card-body">
                <h5 class="card-title">Total Comments</h5>
                <p class="display-4"><?= $total_comments ?></p>
            </div>
        </div>
    </div>
    <div class="col-md-4 mb-3">
        <div class="card text-white bg-info">
            <div class="card-body">
                <h5 class="card-title">Categories</h5>
                <p class="display-4"><?= $total_cats ?></p>
            </div>
        </div>
    </div>
</div>

<div class="card mt-4">
    <div class="card-body">
        <h5 class="card-title">Quick Actions</h5>
        <a href="<?= base_url('admin/posts.php') ?>" class="btn btn-outline-primary">Manage Posts</a>
        <a href="<?= base_url('admin/categories.php') ?>" class="btn btn-outline-primary">Manage Categories</a>
        <a href="<?= base_url('admin/comments.php') ?>" class="btn btn-outline-primary">Manage Comments</a>
        <a href="<?= base_url('admin/profile.php') ?>" class="btn btn-outline-primary">My Profile</a>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>