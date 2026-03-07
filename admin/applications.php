<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Applications';

// Filter by status
$status_filter = isset($_GET['status']) ? $_GET['status'] : 'all';

// Build query
$query = "SELECT a.*, u.email, up.first_name, up.last_name, up.phone, c.name as course_name, col.name as college_name 
    FROM applications a 
    JOIN users u ON a.user_id = u.id 
    JOIN user_profiles up ON u.id = up.user_id
    JOIN courses c ON a.course_id = c.id 
    JOIN colleges col ON c.college_id = col.id";

if ($status_filter !== 'all') {
    $query .= " WHERE a.status = '$status_filter'";
}

$query .= " ORDER BY a.created_at DESC";

$applications = $conn->query($query);

// Handle application actions
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_status'])) {
        $app_id = $_POST['app_id'];
        $new_status = $_POST['new_status'];
        $remarks = trim($_POST['remarks']);
        
        $stmt = $conn->prepare("UPDATE applications SET status = ?, reviewed_by = ?, reviewed_at = NOW(), remarks = ? WHERE id = ?");
        $stmt->bind_param("sisi", $new_status, $_SESSION['user_id'], $remarks, $app_id);
        
        if ($stmt->execute()) {
            // Add timeline entry
            $timeline_stmt = $conn->prepare("INSERT INTO application_timeline (application_id, status, description, created_by) VALUES (?, ?, ?, ?)");
            $timeline_stmt->bind_param("issi", $app_id, $new_status, $remarks, $_SESSION['user_id']);
            $timeline_stmt->execute();
            
            // Get user_id for notification
            $app = $conn->query("SELECT user_id, application_number FROM applications WHERE id = $app_id")->fetch_assoc();
            
            // Send notification
            $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, ?)");
            $status_msg = ucfirst($new_status);
            $title = "Application $status_msg";
            $message = "Your application " . $app['application_number'] . " has been $new_status.";
            $notif_type = ($new_status === 'approved') ? 'success' : (($new_status === 'rejected') ? 'error' : 'info');
            $notif_stmt->bind_param("isss", $app['user_id'], $title, $message, $notif_type);
            $notif_stmt->execute();
            
            header('Location: applications.php?status=' . $status_filter);
            exit;
        }
    }
}

// Get stats
$total = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
$submitted = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'submitted'")->fetch_assoc()['count'];
$under_review = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'under_review'")->fetch_assoc()['count'];
$approved = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'approved'")->fetch_assoc()['count'];
$rejected = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'rejected'")->fetch_assoc()['count'];
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Applications</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Applications</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Stats -->
            <div class="row g-3 mb-4">
                <div class="col-6 col-md-2">
                    <a href="?status=all" class="text-decoration-none">
                        <div class="stat-card primary p-3">
                            <div class="stat-number"><?php echo $total; ?></div>
                            <div class="stat-label">Total</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="?status=submitted" class="text-decoration-none">
                        <div class="stat-card warning p-3">
                            <div class="stat-number"><?php echo $submitted; ?></div>
                            <div class="stat-label">Submitted</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="?status=under_review" class="text-decoration-none">
                        <div class="stat-card info p-3">
                            <div class="stat-number"><?php echo $under_review; ?></div>
                            <div class="stat-label">Under Review</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="?status=approved" class="text-decoration-none">
                        <div class="stat-card success p-3">
                            <div class="stat-number"><?php echo $approved; ?></div>
                            <div class="stat-label">Approved</div>
                        </div>
                    </a>
                </div>
                <div class="col-6 col-md-2">
                    <a href="?status=rejected" class="text-decoration-none">
                        <div class="stat-card danger p-3">
                            <div class="stat-number"><?php echo $rejected; ?></div>
                            <div class="stat-label">Rejected</div>
                        </div>
                    </a>
                </div>
            </div>
            
            <!-- Applications Table -->
            <div class="card">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-file-alt me-2"></i>
                        <?php echo $status_filter === 'all' ? 'All Applications' : ucfirst(str_replace('_', ' ', $status_filter)) . ' Applications'; ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="applicationsTable">
                            <thead>
                                <tr>
                                    <th>App No.</th>
                                    <th>Student</th>
                                    <th>Course</th>
                                    <th>College</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($applications && $applications->num_rows > 0): ?>
                                    <?php while ($app = $applications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['application_number']); ?></td>
                                        <td>
                                            <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?><br>
                                            <small class="text-muted"><?php echo htmlspecialchars($app['email']); ?></small>
                                        </td>
                                        <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['college_name']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $app['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($app['created_at'])); ?></td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#viewModal<?php echo $app['id']; ?>">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No applications found</td>
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

<?php 
// Create view/update modals
$applications = $conn->query($query);
while ($app = $applications->fetch_assoc()): 
    // Get documents
    $documents = $conn->query("SELECT * FROM documents WHERE application_id = " . $app['id']);
    // Get timeline
    $timeline = $conn->query("SELECT * FROM application_timeline WHERE application_id = " . $app['id'] . " ORDER BY created_at DESC");
?>
<!-- View/Action Modal -->
<div class="modal fade" id="viewModal<?php echo $app['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Application: <?php echo htmlspecialchars($app['application_number']); ?></h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <div class="modal-body">
                <!-- Applicant Info -->
                <div class="mb-4">
                    <h6>Applicant Details</h6>
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Name:</strong> <?php echo htmlspecialchars($app['first_name'] . ' ' . $app['last_name']); ?></p>
                            <p><strong>Email:</strong> <?php echo htmlspecialchars($app['email']); ?></p>
                            <p><strong>Phone:</strong> <?php echo htmlspecialchars($app['phone'] ?? 'N/A'); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Course:</strong> <?php echo htmlspecialchars($app['course_name']); ?></p>
                            <p><strong>College:</strong> <?php echo htmlspecialchars($app['college_name']); ?></p>
                            <p><strong>Applied On:</strong> <?php echo date('d M Y, h:i A', strtotime($app['created_at'])); ?></p>
                        </div>
                    </div>
                </div>
                
                <!-- Personal Statement -->
                <?php if (!empty($app['personal_statement'])): ?>
                <div class="mb-4">
                    <h6>Personal Statement</h6>
                    <p><?php echo nl2br(htmlspecialchars($app['personal_statement'])); ?></p>
                </div>
                <?php endif; ?>
                
                <!-- Documents -->
                <div class="mb-4">
                    <h6>Uploaded Documents</h6>
                    <?php if ($documents && $documents->num_rows > 0): ?>
                    <ul class="list-group">
                        <?php while ($doc = $documents->fetch_assoc()): ?>
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-file-<?php echo strpos($doc['mime_type'], 'pdf') ? 'pdf' : 'image'; ?> me-2"></i>
                                <?php echo htmlspecialchars($doc['document_type']); ?>
                            </span>
                            <span class="badge bg-<?php echo $doc['status'] === 'verified' ? 'success' : ($doc['status'] === 'rejected' ? 'danger' : 'warning'); ?>">
                                <?php echo ucfirst($doc['status']); ?>
                            </span>
                        </li>
                        <?php endwhile; ?>
                    </ul>
                    <?php else: ?>
                    <p class="text-muted">No documents uploaded yet.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Timeline -->
                <div class="mb-4">
                    <h6>Application Timeline</h6>
                    <?php if ($timeline && $timeline->num_rows > 0): ?>
                    <div class="timeline">
                        <?php while ($t = $timeline->fetch_assoc()): ?>
                        <div class="timeline-item">
                            <strong><?php echo htmlspecialchars($t['status']); ?></strong>
                            <p class="mb-0 text-muted"><?php echo htmlspecialchars($t['description']); ?></p>
                            <small class="text-muted"><?php echo date('d M Y, h:i A', strtotime($t['created_at'])); ?></small>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No timeline entries.</p>
                    <?php endif; ?>
                </div>
                
                <!-- Update Status Form -->
                <?php if ($app['status'] !== 'approved' && $app['status'] !== 'rejected'): ?>
                <form method="POST" class="mt-4">
                    <input type="hidden" name="app_id" value="<?php echo $app['id']; ?>">
                    <h6>Update Status</h6>
                    <div class="row">
                        <div class="col-md-5">
                            <select class="form-select" name="new_status">
                                <option value="under_review" <?php echo $app['status'] === 'submitted' ? 'selected' : ''; ?>>Under Review</option>
                                <option value="approved">Approve</option>
                                <option value="rejected">Reject</option>
                            </select>
                        </div>
                        <div class="col-md-5">
                            <input type="text" class="form-control" name="remarks" placeholder="Remarks (optional)">
                        </div>
                        <div class="col-md-2">
                            <button type="submit" name="update_status" class="btn btn-primary w-100">Update</button>
                        </div>
                    </div>
                </form>
                <?php endif; ?>
                
                <?php if (!empty($app['remarks'])): ?>
                <div class="mt-3 alert alert-info">
                    <strong>Remarks:</strong> <?php echo htmlspecialchars($app['remarks']); ?>
                </div>
                <?php endif; ?>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>

