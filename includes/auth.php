<?php
session_start();

// Check if user is logged in
function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

// Get current user role
function getUserRole() {
    return $_SESSION['role'] ?? null;
}

// Require login - redirect if not logged in
function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: /Gymmanagement/login.php");
        exit();
    }
}

// Require admin role
function requireAdmin() {
    requireLogin();
    if (getUserRole() !== 'admin') {
        header("Location: /Gymmanagement/login.php");
        exit();
    }
}

// Require member role
function requireMember() {
    requireLogin();
    if (getUserRole() !== 'member') {
        header("Location: /Gymmanagement/login.php");
        exit();
    }
}

// Check remember me cookie on page load
function checkRememberMe() {
    if (!isLoggedIn() && isset($_COOKIE['remember_user'])) {
        require_once __DIR__ . '/../config/db.php';
        $stmt = $conn->prepare("SELECT user_id, username, role FROM users WHERE username = ?");
        $stmt->execute([$_COOKIE['remember_user']]);
        $user = $stmt->fetch();
        if ($user) {
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
        }
    }
}

checkRememberMe();
?>
