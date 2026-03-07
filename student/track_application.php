<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Track Application - Student';

$user_id = $_SESSION['user_id'];
$application = null;

// Get application by ID or application number
if (isset($_GET['id'])) {
    $app_id = (int)$_GET['id'];
    $application = $conn->query("SELECT a.*, c.name as course_name, c.fees, col.name as college_name, col.address as college_address 
        FROM applications a 
        JOIN courses c ON a.course_id = c.id 
        JOIN colleges col ON c.college_id = col.id 
        WHERE a.id = $app_id AND a.user_id = $user_id")->fetch_assoc();
} elseif (isset($_GET['app_no'])) {
    $app_no = $_GET['app_no'];
    $application = $conn->query("SELECT a.*, c.name as course_name, c.fees, col.name as college_name, col.address as college_address 
        FROM applications a 
        JOIN courses c ON a.course_id = c.id 
        JOIN colleges col ON c.college_id = col.id 
        WHERE a.application_number = '$app_no' AND a.user_id = $user_id")->fetch_assoc();
}

// Get application timeline
$timeline = null;
if ($application) {
    $timeline = $conn->query("SELECT * FROM application_timeline WHERE application_id = " . $application['id'] . " ORDER BY created_at DESC");
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Track Application</h1>
            </div>
            
            <!-- Search Form -->
            <div class="card mb-4">
                <div class="card-body">
                    <form method="GET" class="row g-3">
                        <div class="col-md-8">
                            <input type="text" name="app_no" class="form-control" placeholder="Enter Application Number" 
                                   value="<?php echo isset($_GET['app_no']) ? htmlspecialchars($_GET['app_no']) : ''; ?>">
                        </div>
                        <div class="col-md-4">
                            <button type="submit" class="btn btn-primary w-100">
                                <i class="fas fa-search"></i> Track
                            </button>
                        </div>
                    </form>
                </div>
            </div>
            
            <?php if ($application): ?>
            <!-- Application Details -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span>Application Details</span>
                    <span class="badge bg-<?php 
                        echo $application['status'] === 'approved' ? 'success' : ($application['status'] === 'rejected' ? 'danger' : ($application['status'] === 'submitted' ? 'primary' : 'warning')); 
                    ?>">
                        <?php echo ucfirst(str_replace('_', ' ', $application['status'])); ?>
                    </span>
                </div>
                <div class="card-body">
                    <div class="row">
                        <div class="col-md-6">
                            <p><strong>Application Number:</strong> <?php echo $application['application_number']; ?></p>
                            <p><strong>College:</strong> <?php echo htmlspecialchars($application['college_name']); ?></p>
                            <p><strong>Course:</strong> <?php echo htmlspecialchars($application['course_name']); ?></p>
                            <p><strong>Fees:</strong> ₹<?php echo number_format($application['fees'], 2); ?></p>
                        </div>
                        <div class="col-md-6">
                            <p><strong>Applied On:</strong> <?php echo date('d M Y, g:i A', strtotime($application['created_at'])); ?></p>
                            <?php if ($application['submitted_at']): ?>
                            <p><strong>Submitted On:</strong> <?php echo date('d M Y, g:i A', strtotime($application['submitted_at'])); ?></p>
                            <?php endif; ?>
                            <p><strong>Payment Status:</strong> 
                                <?php if ($application['is_payment_done']): ?>
                                <span class="badge bg-success">Paid</span>
                                <?php else: ?>
                                <span class="badge bg-warning">Pending</span>
                                <?php endif; ?>
                            </p>
                        </div>
                    </div>
                    
                    <?php if ($application['remarks']): ?>
                    <div class="alert alert-info mt-3">
                        <strong>Remarks:</strong> <?php echo htmlspecialchars($application['remarks']); ?>
                    </div>
                    <?php endif; ?>
                </div>
            </div>
            
            <!-- Timeline -->
            <div class="card">
                <div class="card-header">Application Timeline</div>
                <div class="card-body">
                    <?php if ($timeline && $timeline->num_rows > 0): ?>
                    <div class="timeline">
                        <?php while ($t = $timeline->fetch_assoc()): ?>
                        <div class="timeline-item mb-3">
                            <div class="d-flex">
                                <div class="timeline-marker"></div>
                                <div class="timeline-content">
                                    <h6 class="mb-1"><?php echo ucfirst($t['status']); ?></h6>
                                    <p class="mb-0 text-muted"><?php echo htmlspecialchars($t['description'] ?? ''); ?></p>
                                    <small class="text-muted"><?php echo date('d M Y, g:i A', strtotime($t['created_at'])); ?></small>
                                </div>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                    <?php else: ?>
                    <p class="text-muted">No timeline updates yet.</p>
                    <?php endif; ?>
                </div>
            </div>
            <?php elseif (isset($_GET['app_no']) || isset($_GET['id'])): ?>
            <div class="alert alert-warning">Application not found.</div>
            <?php endif; ?>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

