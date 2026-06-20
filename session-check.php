<?php
// session-check.php
// Middleware untuk mengecek session user yang sudah login

session_start();

// Fungsi untuk check apakah user sudah login
function checkUserLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

// Fungsi untuk check apakah admin sudah login
function checkAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin-login.php");
        exit();
    }
}

// Fungsi untuk logout
function logout() {
    session_destroy();
    header("Location: index.php");
    exit();
}

// Fungsi untuk redirect jika sudah login
function redirectIfLoggedIn($user_type = 'user') {
    if ($user_type === 'user' && isset($_SESSION['user_id'])) {
        header("Location: profile.php");
        exit();
    } elseif ($user_type === 'admin' && isset($_SESSION['admin_id'])) {
        header("Location: admin-dashboard.php");
        exit();
    }
}
?>
