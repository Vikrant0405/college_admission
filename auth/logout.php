<?php
require_once '../config/db.php';
require_once '../config/constants.php';

// Log the logout activity
if (isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Create notification
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, 'Logged Out', 'You have successfully logged out', 'info')");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
}

// Destroy session
session_destroy();

// Redirect to login page
header('Location: ' . BASE_URL . 'auth/login.php?logged_out=1');
exit;
?>

