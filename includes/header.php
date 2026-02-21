<?php
if (session_status() === PHP_SESSION_NONE) session_start();
require_once __DIR__ . '/../config/config.php';
require_once __DIR__ . '/functions.php';
require_once __DIR__ . '/auth.php';
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= isset($page_title) ? e($page_title) . ' - ' : '' ?><?= SITE_NAME ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.1/css/all.min.css">
    <link rel="stylesheet" href="<?= base_url('assets/css/style.css') ?>">
    <?php if (isset($page_css)): foreach ($page_css as $css): ?>
        <link rel="stylesheet" href="<?= base_url('assets/css/' . $css) ?>">
    <?php endforeach; endif; ?>
</head>
<body>
<!-- Navigation -->
<nav class="navbar navbar-expand-lg navbar-dark bg-dark">
    <div class="container">
        <a class="navbar-brand" href="<?= base_url('index.php') ?>">
            <i class="fas fa-blog me-2"></i><?= SITE_NAME ?>
        </a>
        <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
            <span class="navbar-toggler-icon"></span>
        </button>
        <div class="collapse navbar-collapse" id="navbarNav">
            <ul class="navbar-nav me-auto">
                <li class="nav-item"><a class="nav-link" href="<?= base_url('index.php') ?>">Home</a></li>
         
                <li class="nav-item"><a class="nav-link" href="<?= base_url('about.php') ?>">About</a></li>
                <li class="nav-item"><a class="nav-link" href="<?= base_url('contact.php') ?>">Contact</a></li>
            </ul>
            <form class="d-flex me-3" action="<?= base_url('search.php') ?>" method="get">
                <input class="form-control me-2" type="search" name="q" placeholder="Search posts..." value="<?= e($_GET['q'] ?? '') ?>">
                <button class="btn btn-outline-light" type="submit"><i class="fas fa-search"></i></button>
            </form>
            <ul class="navbar-nav">
                <?php if (current_user()): ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('admin/index.php') ?>">Dashboard</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('logout.php') ?>">Logout</a></li>
                <?php else: ?>
                    <li class="nav-item"><a class="nav-link" href="<?= base_url('login.php') ?>"></a></li>
                <?php endif; ?>
            </ul>
        </div>
    </div>
</nav>

<!-- Main Content -->
<main class="container my-4">
    <?php if (isset($_SESSION['flash'])): ?>
        <div class="alert alert-<?= $_SESSION['flash']['type'] ?> alert-dismissible fade show">
            <?= e($_SESSION['flash']['message']) ?>
            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
        </div>
        <?php unset($_SESSION['flash']); ?>
    <?php endif; ?>