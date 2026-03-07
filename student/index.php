<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Student Dashboard';

// Get student stats
$user_id = $_SESSION['user_id'];

$my_applications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE user_id = $user_id")->fetch_assoc()['count'];
$pending_applications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE user_id = $user_id AND status = 'submitted'")->fetch_assoc()['count'];
$approved_applications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE user_id = $user_id AND status = 'approved'")->fetch_assoc()['count'];
$my_documents = $conn->query("SELECT COUNT(*) as count FROM documents WHERE user_id = $user_id")->fetch_assoc()['count'];

// Get user's recent applications
$recent_apps = $conn->query("SELECT a.*, c.name as course_name, col.name as college_name 
    FROM applications a 
    JOIN courses c ON a.course_id = c.id 
    JOIN colleges col ON c.college_id = col.id 
    WHERE a.user_id = $user_id 
    ORDER BY a.created_at DESC LIMIT 5");

// Get unread notifications
$notifications = $conn->query("SELECT * FROM notifications WHERE user_id = $user_id AND is_read = 0 ORDER BY created_at DESC LIMIT 5");

// Check profile completion
$profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id")->fetch_assoc();
$profile_complete = !empty($profile['first_name']) && !empty($profile['date_of_birth']) && !empty($profile['phone']);
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Welcome Banner -->
            <div class="alert alert-info mb-4 mt-3" role="alert">
                <div class="d-flex align-items-center">
                    <i class="fas fa-user-circle fa-2x me-3"></i>
                    <div>
                        <h4 class="alert-heading mb-1">Welcome back, <?php echo isset($_SESSION['user_name']) ? $_SESSION['user_name'] : 'Student'; ?>!</h4>
                        <p class="mb-0">
                            <?php if (!$profile_complete): ?>
                            <i class="fas fa-exclamation-triangle text-warning me-1"></i>
                            Please complete your profile to apply for courses.
                            <a href="profile.php" class="alert-link">Complete Profile</a>
                            <?php else: ?>
                            Your profile is complete. You can now apply for courses!
                            <?php endif; ?>
                        </p>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card primary">
                        <i class="fas fa-file-alt stat-icon"></i>
                        <div class="stat-number"><?php echo $my_applications; ?></div>
                        <div class="stat-label">My Applications</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card warning">
                        <i class="fas fa-clock stat-icon"></i>
                        <div class="stat-number"><?php echo $pending_applications; ?></div>
                        <div class="stat-label">Pending Review</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card success">
                        <i class="fas fa-check-circle stat-icon"></i>
                        <div class="stat-number"><?php echo $approved_applications; ?></div>
                        <div class="stat-label">Approved</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card info">
                        <i class="fas fa-file-upload stat-icon"></i>
                        <div class="stat-number"><?php echo $my_documents; ?></div>
                        <div class="stat-label">Documents Uploaded</div>
                    </div>
                </div>
            </div>
            
            <!-- Quick Actions -->
            <div class="row g-4 mb-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6 col-md-3">
                                    <a href="application_form.php" class="btn btn-primary w-100 p-3">
                                        <i class="fas fa-file-plus fa-2x d-block mb-2"></i>
                                        New Application
                                    </a>
                                </div>
                                <div class="col-6 col-md-3">
                                    <a href="my_applications.php" class="btn btn-success w-100 p-3">
                                        <i class="fas fa-list-alt fa-2x d-block mb-2"></i>
                                        My Applications
                                    </a>
                                </div>
                                <div class="col-6 col-md-3">
                                    <a href="upload_documents.php" class="btn btn-info w-100 p-3">
                                        <i class="fas fa-cloud-upload-alt fa-2x d-block mb-2"></i>
                                        Upload Documents
                                    </a>
                                </div>
                                <div class="col-6 col-md-3">
                                    <a href="track_application.php" class="btn btn-warning w-100 p-3">
                                        <i class="fas fa-search-location fa-2x d-block mb-2"></i>
                                        Track Status
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <div class="row g-4">
                <!-- Recent Applications -->
                <div class="col-md-8">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-history me-2"></i>Recent Applications</span>
                            <a href="my_applications.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if ($recent_apps && $recent_apps->num_rows > 0): ?>
                            <div class="table-responsive">
                                <table class="table table-hover">
                                    <thead>
                                        <tr>
                                            <th>App No.</th>
                                            <th>Course</th>
                                            <th>College</th>
                                            <th>Status</th>
                                            <th>Action</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php while ($app = $recent_apps->fetch_assoc()): ?>
                                        <tr>
                                            <td><?php echo htmlspecialchars($app['application_number']); ?></td>
                                            <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                                            <td><?php echo htmlspecialchars($app['college_name']); ?></td>
                                            <td>
                                                <span class="badge badge-<?php echo $app['status']; ?>">
                                                    <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                                </span>
                                            </td>
                                            <td>
                                                <a href="track_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-info">
                                                    <i class="fas fa-eye"></i>
                                                </a>
                                            </td>
                                        </tr>
                                        <?php endwhile; ?>
                                    </tbody>
                                </table>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4">
                                <i class="fas fa-file-alt fa-3x text-muted mb-3"></i>
                                <p class="text-muted">No applications yet</p>
                                <a href="application_form.php" class="btn btn-primary">Apply Now</a>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
                
                <!-- Notifications -->
                <div class="col-md-4">
                    <div class="card">
                        <div class="card-header d-flex justify-content-between align-items-center">
                            <span><i class="fas fa-bell me-2"></i>Notifications</span>
                            <span class="badge bg-primary"><?php echo $notifications->num_rows; ?></span>
                        </div>
                        <div class="card-body p-0">
                            <?php if ($notifications && $notifications->num_rows > 0): ?>
                            <div class="list-group list-group-flush">
                                <?php while ($notif = $notifications->fetch_assoc()): ?>
                                <div class="list-group-item">
                                    <div class="d-flex w-100 justify-content-between">
                                        <h6 class="mb-1"><?php echo htmlspecialchars($notif['title']); ?></h6>
                                        <small><?php echo date('d M', strtotime($notif['created_at'])); ?></small>
                                    </div>
                                    <p class="mb-1 small"><?php echo htmlspecialchars($notif['message']); ?></p>
                                </div>
                                <?php endwhile; ?>
                            </div>
                            <div class="card-footer text-center">
                                <a href="notifications.php" class="btn btn-sm btn-outline-primary">View All</a>
                            </div>
                            <?php else: ?>
                            <div class="text-center py-4 text-muted">
                                <i class="fas fa-bell-slash fa-2x mb-2"></i>
                                <p class="mb-0">No new notifications</p>
                            </div>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

