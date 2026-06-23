<?php
/**
 * JejakHijau - User/Admin Logout
 * GET: logout link
 */

require_once 'session-check.php';

// Determine redirect based on who was logged in
$is_admin = isset($_SESSION['admin_id']);

session_destroy();

// Redirect accordingly
if ($is_admin) {
    header("Location: admin-login.php");
} else {
    header("Location: index.php");
}
exit();

?>