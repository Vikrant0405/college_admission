<?php
require_once '../config/db.php';
require_once '../config/constants.php';

if (!isset($_SESSION['user_id']) || ($_SESSION['role'] !== 'admin' && $_SESSION['role'] !== 'staff')) {
    header('Location: ' . BASE_URL . 'auth/login.php');
    exit;
}

$doc_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$action = isset($_GET['action']) ? $_GET['action'] : '';
$reason = isset($_GET['reason']) ? $_GET['reason'] : '';

if ($doc_id > 0 && in_array($action, ['verify', 'reject'])) {
    if ($action === 'verify') {
        $stmt = $conn->prepare("UPDATE documents SET status = 'verified', verified_by = ?, verified_at = NOW() WHERE id = ?");
        $stmt->bind_param("ii", $_SESSION['user_id'], $doc_id);
        $stmt->execute();
        $msg = 'Document verified successfully';
    } else {
        $stmt = $conn->prepare("UPDATE documents SET status = 'rejected', verified_by = ?, verified_at = NOW(), rejection_reason = ? WHERE id = ?");
        $stmt->bind_param("isi", $_SESSION['user_id'], $reason, $doc_id);
        $stmt->execute();
        $msg = 'Document rejected';
    }
    
    header('Location: verify_documents.php?msg=' . urlencode($msg));
} else {
    header('Location: verify_documents.php?error=Invalid request');
}
exit;

