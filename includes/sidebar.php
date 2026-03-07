<?php
// Get current page name
$current_page = basename($_SERVER['PHP_SELF'], '.php');

// Get user role
$user_role = isset($_SESSION['role']) ? $_SESSION['role'] : '';
?>

<!-- Sidebar -->
<div class="col-md-3 col-lg-2 sidebar">
    <div class="d-flex flex-column">
        <?php if ($user_role === 'admin' || $user_role === 'staff'): ?>
        <!-- Admin/Staff Sidebar -->
        <nav class="nav flex-column">
            <a class="nav-link <?php echo ($current_page == 'dashboard') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/dashboard.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <?php if ($user_role === 'admin'): ?>
            <a class="nav-link <?php echo ($current_page == 'colleges') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/colleges.php">
                <i class="fas fa-university"></i> Manage Colleges
            </a>
            <a class="nav-link <?php echo ($current_page == 'courses') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/courses.php">
                <i class="fas fa-book"></i> Manage Courses
            </a>
            <a class="nav-link <?php echo ($current_page == 'manage_users') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/manage_users.php">
                <i class="fas fa-users"></i> Manage Users
            </a>
            <?php endif; ?>
            
            <a class="nav-link <?php echo ($current_page == 'applications') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/applications.php">
                <i class="fas fa-file-alt"></i> Applications
                <?php 
                $pending_count = getPendingApplicationsCount();
                if ($pending_count > 0): 
                ?>
                <span class="badge bg-danger float-end"><?php echo $pending_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'verify_documents') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/verify_documents.php">
                <i class="fas fa-file-upload"></i> Verify Documents
                <?php 
                $pending_docs = getPendingDocumentsCount();
                if ($pending_docs > 0): 
                ?>
                <span class="badge bg-warning float-end"><?php echo $pending_docs; ?></span>
                <?php endif; ?>
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'reports') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/reports.php">
                <i class="fas fa-chart-bar"></i> Reports & Analytics
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'notifications') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/notifications.php">
                <i class="fas fa-bell"></i> Notifications
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'settings') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>admin/settings.php">
                <i class="fas fa-cog"></i> Settings
            </a>
        </nav>
        
        <?php else: ?>
        <!-- Student Sidebar -->
        <nav class="nav flex-column">
            <a class="nav-link <?php echo ($current_page == 'index' || $current_page == 'dashboard') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/index.php">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'profile') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/profile.php">
                <i class="fas fa-user"></i> My Profile
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'application_form') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/application_form.php">
                <i class="fas fa-file-alt"></i> Apply Now
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'my_applications') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/my_applications.php">
                <i class="fas fa-list-alt"></i> My Applications
                <?php 
                $app_count = getStudentApplicationCount($_SESSION['user_id']);
                if ($app_count > 0): 
                ?>
                <span class="badge bg-primary float-end"><?php echo $app_count; ?></span>
                <?php endif; ?>
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'upload_documents') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/upload_documents.php">
                <i class="fas fa-cloud-upload-alt"></i> Upload Documents
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'track_application') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/track_application.php">
                <i class="fas fa-search-location"></i> Track Application
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'payment') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/payment.php">
                <i class="fas fa-credit-card"></i> Payments
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'notifications') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>student/notifications.php">
                <i class="fas fa-bell"></i> Notifications
                <?php 
                $unread = getUnreadNotificationCount($_SESSION['user_id']);
                if ($unread > 0): 
                ?>
                <span class="badge bg-danger float-end"><?php echo $unread; ?></span>
                <?php endif; ?>
            </a>
            
            <a class="nav-link <?php echo ($current_page == 'help') ? 'active' : ''; ?>" 
               href="<?php echo BASE_URL; ?>pages/help.php">
                <i class="fas fa-question-circle"></i> Help & Support
            </a>
        </nav>
        <?php endif; ?>
    </div>
</div>

<?php
// Helper functions for sidebar
function getPendingApplicationsCount() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'submitted'");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getPendingDocumentsCount() {
    global $conn;
    $result = $conn->query("SELECT COUNT(*) as count FROM documents WHERE status = 'pending'");
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getStudentApplicationCount($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM applications WHERE user_id = ?");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}

function getUnreadNotificationCount($user_id) {
    global $conn;
    $stmt = $conn->prepare("SELECT COUNT(*) as count FROM notifications WHERE user_id = ? AND is_read = 0");
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $row = $result->fetch_assoc();
    return $row['count'];
}
?>

