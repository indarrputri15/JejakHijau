<?php
// Database Configuration
$db_host = "localhost";
$db_user = "root";
$db_pass = "";
$db_name = "database_jejakhijau";

// Create connection
$conn = new mysqli($db_host, $db_user, $db_pass, $db_name);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Set charset to UTF-8
$conn->set_charset("utf8");


// ==================== ADMIN CREDENTIALS ====================
// Email and password for admin login
// Password is hashed with password_hash()
$admin_email = "admin@jejakhijau.com";
$admin_password_hash = "$2y$10$vgeDFeX/.xWELKCvcsospevl2aAjf3ZZfi.cLpFJizFs5ahO7ZNB"; // password: admin123

// ==================== APPLICATION SETTINGS ====================
$app_name = "JejakHijau";
$app_url = "http://localhost/JejakHijau";

// File upload settings
$upload_dir_campaigns = "uploads/campaigns/";
$upload_dir_donations = "uploads/donations/";
$max_file_size = 5 * 1024 * 1024; // 5MB

// Ensure upload directories exist
if (!is_dir($upload_dir_campaigns)) {
    mkdir($upload_dir_campaigns, 0755, true);
}
if (!is_dir($upload_dir_donations)) {
    mkdir($upload_dir_donations, 0755, true);
}

// ==================== ERROR HANDLING ====================
error_reporting(E_ALL);
ini_set('display_errors', 0); // Don't display errors in production
ini_set('log_errors', 1);
ini_set('error_log', 'logs/error.log');

?>
