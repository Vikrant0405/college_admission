<?php
// Database Configuration
// Update these values according to your XAMPP/MySQL settings
define('DB_HOST', 'localhost:3309');  // Include port if not default 3306
define('DB_USER', 'root');
define('DB_PASS', '');               // Set your MySQL password here if any
define('DB_NAME', 'college_admission');

// Connect to MySQL server (without database first)
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Create database if not exists
$conn->query("CREATE DATABASE IF NOT EXISTS " . DB_NAME);

// Select the database
$conn->select_db(DB_NAME);

// Check for database selection errors
if ($conn->error) {
    die("Database error: " . $conn->error);
}

// Session configuration
session_start();

// Base URL - Auto-detect
$protocol = isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] === 'on' ? 'https' : 'http';
$host = $_SERVER['HTTP_HOST'];
$basePath = '/college_admission/';  // Adjust if folder name is different
define('BASE_URL', $protocol . '://' . $host . $basePath);

// Upload paths
define('UPLOAD_PATH', __DIR__ . '/../uploads/');
define('DOCUMENT_PATH', UPLOAD_PATH . 'documents/');
define('IMAGE_PATH', UPLOAD_PATH . 'images/');

// Allowed file extensions
define('ALLOWED_EXTENSIONS', ['jpg', 'jpeg', 'png', 'pdf']);

// Timezone
date_default_timezone_set('Asia/Kolkata');
?>

