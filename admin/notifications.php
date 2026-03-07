<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Notifications - Admin';

// Mark as read
if (isset($_GET['mark_read'])) {
    $notif_id = (int)$_GET['mark_read'];
    $conn->query("UPDATE notifications SET is_read = 1 WHERE id = $notif_id");
    header('Location: notifications.php');
    exit;
}

// Mark all as read
if (isset($_GET['mark_all_read'])) {
    $conn->query("UPDATE notifications SET is_read = 1 WHERE user_id = " . $_SESSION['user_id']);
    header('Location: notifications.php');
    exit;
}

// Get all notifications
$notifications = $conn->query("SELECT * FROM notifications ORDER BY created_at DESC LIMIT 50");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Notifications</h1>
                <a href="?mark_all_read=1" class="btn btn-sm btn-outline-primary">Mark All as Read</a>
            </div>
            
            <div class="card">
                <div class="card-body p-0">
                    <div class="list-group list-group-flush">
                        <?php if ($notifications && $notifications->num_rows > 0): ?>
                            <?php while ($notif = $notifications->fetch_assoc()): ?>
                            <div class="list-group-item <?php echo $notif['is_read'] ? '' : 'bg-light'; ?>">
                                <div class="d-flex justify-content-between align-items-center">
                                    <div>
                                        <h6 class="mb-1">
                                            <?php if (!$notif['is_read']): ?><span class="badge bg-primary">New</span> <?php endif; ?>
                                            <?php echo htmlspecialchars($notif['title']); ?>
                                        </h6>
                                        <p class="mb-1 text-muted"><?php echo htmlspecialchars($notif['message']); ?></p>
                                        <small><?php echo date('d M Y, g:i A', strtotime($notif['created_at'])); ?></small>
                                    </div>
                                    <?php if (!$notif['is_read']): ?>
                                    <a href="?mark_read=<?php echo $notif['id']; ?>" class="btn btn-sm btn-outline-secondary">Mark Read</a>
                                    <?php endif; ?>
                                </div>
                            </div>
                            <?php endwhile; ?>
                        <?php else: ?>
                            <div class="list-group-item text-center text-muted py-4">
                                No notifications
                            </div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

