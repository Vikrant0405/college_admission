<?php
require_once '../config/db.php';

if (!isset($_SESSION)) {
    session_start();
}

if (!isset($_SESSION['user_id'])) {
    echo 0;
    exit;
}

$user_id = $_SESSION['user_id'];
$result = $conn->query("SELECT COUNT(*) as c FROM notifications WHERE user_id = $user_id AND is_read = 0");
$row = $result->fetch_assoc();
echo $row['c'];

