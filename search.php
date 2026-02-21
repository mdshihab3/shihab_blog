<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/includes/functions.php';

$q = trim($_GET['q'] ?? '');
$page_title = $q ? "Search: $q" : 'Search';

$posts = [];
if ($q !== '') {
    $like = "%$q%";
    $stmt = $conn->prepare("
        SELECT p.*, c.name AS category_name, u.name AS author_name
        FROM posts p
        LEFT JOIN categories c ON p.category_id = c.id
        JOIN users u ON p.user_id = u.id
        WHERE p.status = 'published' AND (p.title LIKE ? OR p.content LIKE ?)
        ORDER BY p.created_at DESC
    ");
    $stmt->bind_param('ss', $like, $like);
    $stmt->execute();
    $posts = $stmt->get_result()->fetch_all(MYSQLI_ASSOC);
}

include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-12">
        <div class="card mb-4">
            <div class="card-body">
                <h2>Search</h2>
                <form action="<?= base_url('search.php') ?>" method="get" class="row g-2">
                    <div class="col-md-8">
                        <input type="search" name="q" class="form-control" placeholder="Enter keyword..." value="<?= e($q) ?>" required>
                    </div>
                    <div class="col-md-4">
                        <button type="submit" class="btn btn-primary w-100">Search</button>
                    </div>
                </form>
            </div>
        </div>

        <?php if ($q !== ''): ?>
            <div class="card mb-3">
                <div class="card-body">
                    <h5>Results for: "<?= e($q) ?>"</h5>
                    <p><?= count($posts) ?> post(s) found</p>
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
                                        <?php if (!empty($post['category_name'])): ?>
                                            <span class="badge bg-secondary"><?= e($post['category_name']) ?></span>
                                        <?php endif; ?>
                                    </p>
                                    <p class="card-text"><?= e(mb_strimwidth(strip_tags($post['content']), 0, 120, '...')) ?></p>
                                </div>
                            </div>
                        </div>
                    <?php endforeach; ?>
                </div>
            <?php else: ?>
                <div class="alert alert-warning">No posts found matching your query.</div>
            <?php endif; ?>
        <?php endif; ?>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>