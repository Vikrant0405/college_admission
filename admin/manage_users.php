<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Manage Users - Admin';

// Handle user actions
if (isset($_GET['action']) && isset($_GET['id'])) {
    $user_id = (int)$_GET['id'];
    
    if ($_GET['action'] === 'delete') {
        $stmt = $conn->prepare("DELETE FROM users WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        header('Location: manage_users.php?msg=User deleted');
        exit;
    } elseif ($_GET['action'] === 'toggle_status') {
        $stmt = $conn->prepare("UPDATE users SET is_active = NOT is_active WHERE id = ? AND role != 'admin'");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        header('Location: manage_users.php?msg=Status updated');
        exit;
    }
}

// Get all users
$users = $conn->query("SELECT u.*, up.first_name, up.last_name, up.phone 
    FROM users u 
    LEFT JOIN user_profiles up ON u.id = up.user_id 
    ORDER BY u.created_at DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Manage Users</h1>
            </div>
            
            <?php if (isset($_GET['msg'])): ?>
            <div class="alert alert-success"><?php echo htmlspecialchars($_GET['msg']); ?></div>
            <?php endif; ?>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="usersTable">
                            <thead>
                                <tr>
                                    <th>ID</th>
                                    <th>Name</th>
                                    <th>Email</th>
                                    <th>Phone</th>
                                    <th>Role</th>
                                    <th>Status</th>
                                    <th>Verified</th>
                                    <th>Joined</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($users && $users->num_rows > 0): ?>
                                    <?php while ($user = $users->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo $user['id']; ?></td>
                                        <td><?php echo htmlspecialchars($user['first_name'] . ' ' . $user['last_name']); ?></td>
                                        <td><?php echo htmlspecialchars($user['email']); ?></td>
                                        <td><?php echo htmlspecialchars($user['phone'] ?? '-'); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['role'] === 'admin' ? 'danger' : ($user['role'] === 'staff' ? 'warning' : 'primary'); ?>">
                                                <?php echo ucfirst($user['role']); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="?action=toggle_status&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-<?php echo $user['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $user['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </a>
                                            <?php else: ?>
                                            <span class="badge bg-success">Active</span>
                                            <?php endif; ?>
                                        </td>
                                        <td>
                                            <span class="badge bg-<?php echo $user['is_verified'] ? 'success' : 'warning'; ?>">
                                                <?php echo $user['is_verified'] ? 'Verified' : 'Pending'; ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($user['created_at'])); ?></td>
                                        <td>
                                            <?php if ($user['role'] !== 'admin'): ?>
                                            <a href="?action=delete&id=<?php echo $user['id']; ?>" 
                                               class="btn btn-sm btn-danger" onclick="return confirm('Delete this user?')">
                                                <i class="fas fa-trash"></i>
                                            </a>
                                            <?php endif; ?>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No users found</td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

