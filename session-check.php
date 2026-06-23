<?php
/**
 * JejakHijau - Session Check Middleware
 * Mengelola session user dan admin
 */

session_start();

/**
 * Check jika user sudah login
 * Jika belum, redirect ke login.php
 */
function checkUserLogin() {
    if (!isset($_SESSION['user_id'])) {
        header("Location: login.php");
        exit();
    }
}

/**
 * Check jika admin sudah login
 * Jika belum, redirect ke admin-login.php
 */
function checkAdminLogin() {
    if (!isset($_SESSION['admin_id'])) {
        header("Location: admin-login.php");
        exit();
    }
}

/**
 * Check apakah user sudah login
 * Return true jika login, false jika belum
 */
function isUserLoggedIn() {
    return isset($_SESSION['user_id']);
}

/**
 * Check apakah admin sudah login
 * Return true jika login, false jika belum
 */
function isAdminLoggedIn() {
    return isset($_SESSION['admin_id']);
}

/**
 * Logout user/admin
 * Destroy session dan redirect
 */
function logoutUser() {
    session_destroy();
    header("Location: index.html");
    exit();
}

/**
 * Redirect jika sudah login
 * Mencegah user login dari mengakses login page
 */
function redirectIfLoggedIn($user_type = 'user') {
    if ($user_type === 'user' && isset($_SESSION['user_id'])) {
        header("Location: index.html");
        exit();
    } elseif ($user_type === 'admin' && isset($_SESSION['admin_id'])) {
        header("Location: admin-dashboard.php");
        exit();
    }
}

/**
 * Get current user ID
 */
function getCurrentUserId() {
    return isset($_SESSION['user_id']) ? $_SESSION['user_id'] : null;
}

/**
 * Get current user name
 */
function getCurrentUserName() {
    return isset($_SESSION['user_name']) ? $_SESSION['user_name'] : null;
}

/**
 * Get current user email
 */
function getCurrentUserEmail() {
    return isset($_SESSION['user_email']) ? $_SESSION['user_email'] : null;
}

?>