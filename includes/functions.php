<?php
// Safe HTML output
function e($str) {
    return htmlspecialchars($str ?? '', ENT_QUOTES, 'UTF-8');
}

// Check if request is POST
function is_post() {
    return $_SERVER['REQUEST_METHOD'] === 'POST';
}

// Redirect using BASE_URL (if defined) or fallback
function redirect($url) {
    if (defined('BASE_URL')) {
        header('Location: ' . BASE_URL . ltrim($url, '/'));
    } else {
        header('Location: /personal_blog/' . ltrim($url, '/'));
    }
    exit;
}

// Create URL-friendly slug
function slugify($text) {
    $text = preg_replace('~[^\pL\d]+~u', '-', $text);
    $text = iconv('utf-8', 'us-ascii//TRANSLIT', $text);
    $text = preg_replace('~[^-\w]+~', '', $text);
    $text = trim($text, '-');
    $text = preg_replace('~-+~', '-', $text);
    $text = strtolower($text);
    return $text ?: 'post';
}

// Require login
function require_login() {
    if (session_status() === PHP_SESSION_NONE) session_start();
    if (empty($_SESSION['user'])) {
        redirect('login.php');
    }
}

// Helper to get base URL for paths
function base_url($path = '') {
    return (defined('BASE_URL') ? BASE_URL : '/personal_blog/') . ltrim($path, '/');
}