<?php
require_once __DIR__ . '/config/config.php';
require_once __DIR__ . '/includes/functions.php';

$page_title = 'About';
include __DIR__ . '/includes/header.php';
?>

<div class="row">
    <div class="col-lg-8 mx-auto">
        <div class="card">
            <div class="card-body p-5">
                <h1 class="display-4 mb-4">About This Blog</h1>
                <p class="lead">Welcome to my personal blog! This is a place where I share my thoughts, experiences, and ideas about various topics.</p>
                <p>Here you'll find articles on technology, travel, food, lifestyle, and more. Feel free to explore and leave comments!</p>
                <p>If you have any questions or just want to say hello, head over to the <a href="<?= base_url('contact.php') ?>">contact page</a>.</p>
            </div>
        </div>
    </div>
</div>

<?php include __DIR__ . '/includes/footer.php'; ?>