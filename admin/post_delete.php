<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
if ($id) {
    // Optionally delete cover image
    $post = $conn->query("SELECT cover_image FROM posts WHERE id = $id")->fetch_assoc();
    if ($post && $post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])) {
        unlink(UPLOAD_DIR . $post['cover_image']);
    }
    $conn->query("DELETE FROM posts WHERE id = $id");
    $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post deleted.'];
}
redirect('admin/posts.php');