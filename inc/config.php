<?php
// config.php - Updated with admin configuration

// Site base URL
define('BASE_URL', 'http://localhost/Masitortechnology/');
define('ADMIN_URL', BASE_URL . 'admin/');

// Uploads folder URL
define('UPLOAD_URL', BASE_URL . 'uploads/');
define('ADMIN_UPLOAD_URL', BASE_URL . 'admin/uploads/');

// Assets folder URL
define('ASSETS_URL', BASE_URL . 'assets/');
define('ADMIN_ASSETS_URL', BASE_URL . 'assets/admin/');

// Database credentials
define('DB_HOST', 'localhost');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_NAME', 'Masitortechnology');

// Admin credentials (change these in production)
define('ADMIN_USERNAME', 'admin');
define('ADMIN_PASSWORD_HASH', password_hash('admin123', PASSWORD_DEFAULT));

// File upload settings
define('MAX_FILE_SIZE', 5 * 1024 * 1024); // 5MB
define('ALLOWED_IMAGE_TYPES', ['jpg', 'jpeg', 'png', 'gif', 'webp']);
define('ALLOWED_DOC_TYPES', ['pdf', 'doc', 'docx']);

// Start session globally
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
                       
// Error reporting (turn off in production)
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Database connection function
function getDBConnection() {
    $conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }
    return $conn;
}

// Admin authentication check
function isAdminLoggedIn() {
    return isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true;
}

// Redirect to login if not authenticated
function requireAdminAuth() {
    if (!isAdminLoggedIn()) {
        header('Location: ' . ADMIN_URL . 'index.php');
        exit;
    }
}
?>