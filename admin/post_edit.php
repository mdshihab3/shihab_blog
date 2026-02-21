<?php
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/functions.php';
require_once __DIR__ . '/../includes/auth.php';

require_login();

$id = (int)($_GET['id'] ?? 0);
if (!$id) redirect('admin/posts.php');

$post = $conn->query("SELECT * FROM posts WHERE id = $id")->fetch_assoc();
if (!$post) die('Post not found.');

$page_title = 'Edit Post';
$page_css = ['admin.css'];

$categories = $conn->query("SELECT * FROM categories ORDER BY name")->fetch_all(MYSQLI_ASSOC);

$error = '';
if (is_post()) {
    $title = trim($_POST['title'] ?? '');
    $content = trim($_POST['content'] ?? '');
    $category_id = (int)($_POST['category_id'] ?? 0) ?: null;
    $status = $_POST['status'] ?? 'draft';

    if (!$title || !$content) {
        $error = 'Title and content are required.';
    } else {
        $slug = slugify($title);
        // Ensure unique slug except this post
        $base_slug = $slug;
        $counter = 1;
        while ($conn->query("SELECT id FROM posts WHERE slug = '$slug' AND id != $id")->num_rows > 0) {
            $slug = $base_slug . '-' . $counter++;
        }

        $cover_image = $post['cover_image'];

        // Handle image upload
        if (!empty($_FILES['cover']['name'])) {
            $ext = strtolower(pathinfo($_FILES['cover']['name'], PATHINFO_EXTENSION));
            $allowed = ['jpg', 'jpeg', 'png', 'webp'];
            if (in_array($ext, $allowed)) {
                $filename = time() . '_' . bin2hex(random_bytes(4)) . '.' . $ext;
                $dest = UPLOAD_DIR . $filename;
                if (!is_dir(UPLOAD_DIR)) mkdir(UPLOAD_DIR, 0777, true);
                if (move_uploaded_file($_FILES['cover']['tmp_name'], $dest)) {
                    // Delete old image if exists
                    if ($post['cover_image'] && file_exists(UPLOAD_DIR . $post['cover_image'])) {
                        unlink(UPLOAD_DIR . $post['cover_image']);
                    }
                    $cover_image = $filename;
                } else {
                    $error = 'Failed to upload image. Check folder permissions.';
                }
            } else {
                $error = 'Invalid image type. Allowed: jpg, jpeg, png, webp.';
            }
        }

        if (!$error) {
            $stmt = $conn->prepare("UPDATE posts SET category_id=?, title=?, slug=?, content=?, cover_image=?, status=? WHERE id=?");
            $stmt->bind_param('isssssi', $category_id, $title, $slug, $content, $cover_image, $status, $id);
            $stmt->execute();
            $_SESSION['flash'] = ['type' => 'success', 'message' => 'Post updated successfully!'];
            redirect('admin/posts.php');
        }
    }
}

include __DIR__ . '/../includes/header.php';
?>

<div class="row justify-content-center">
    <div class="col-lg-10">
        <div class="card">
            <div class="card-body">
                <h2 class="h4 mb-4">Edit Post</h2>
                <?php if ($error): ?>
                    <div class="alert alert-danger"><?= e($error) ?></div>
                <?php endif; ?>
                <form method="post" enctype="multipart/form-data">
                    <div class="mb-3">
                        <label for="title" class="form-label">Title</label>
                        <input type="text" class="form-control" id="title" name="title" value="<?= e($post['title']) ?>" required>
                    </div>
                    <div class="mb-3">
                        <label for="category_id" class="form-label">Category</label>
                        <select class="form-select" id="category_id" name="category_id">
                            <option value="0">No category</option>
                            <?php foreach ($categories as $cat): ?>
                                <option value="<?= $cat['id'] ?>" <?= ($post['category_id'] == $cat['id']) ? 'selected' : '' ?>>
                                    <?= e($cat['name']) ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="status" class="form-label">Status</label>
                        <select class="form-select" id="status" name="status">
                            <option value="draft" <?= $post['status'] == 'draft' ? 'selected' : '' ?>>Draft</option>
                            <option value="published" <?= $post['status'] == 'published' ? 'selected' : '' ?>>Published</option>
                        </select>
                    </div>
                    <div class="mb-3">
                        <label for="cover" class="form-label">Cover Image</label>
                        <input type="file" class="form-control" id="cover" name="cover" accept=".jpg,.jpeg,.png,.webp">
                        <?php if ($post['cover_image']): ?>
                            <div class="mt-2">
                                <img src="<?= UPLOAD_URL . e($post['cover_image']) ?>" style="max-height: 100px;" alt="Current cover">
                                <p class="text-muted small">Current: <?= e($post['cover_image']) ?></p>
                            </div>
                        <?php endif; ?>
                    </div>
                    <div class="mb-3">
                        <label for="content" class="form-label">Content</label>
                        <textarea class="form-control" id="content" name="content" rows="12" required><?= e($post['content']) ?></textarea>
                    </div>
                    <button type="submit" class="btn btn-primary">Update Post</button>
                    <a href="<?= base_url('admin/posts.php') ?>" class="btn btn-secondary">Cancel</a>
                </form>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/../includes/footer.php'; ?>