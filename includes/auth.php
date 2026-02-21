<?php
if (session_status() === PHP_SESSION_NONE) session_start();

function current_user() {
    return $_SESSION['user'] ?? null;
}

function is_admin() {
    $u = current_user();
    return $u && ($u['role'] === 'admin');
}