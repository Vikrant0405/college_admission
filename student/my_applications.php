<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'My Applications - Student';

$user_id = $_SESSION['user_id'];

// Get user's applications
$applications = $conn->query("SELECT a.*, c.name as course_name, col.name as college_name 
    FROM applications a 
    JOIN courses c ON a.course_id = c.id 
    JOIN colleges col ON c.college_id = col.id 
    WHERE a.user_id = $user_id 
    ORDER BY a.created_at DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">My Applications</h1>
                <a href="application_form.php" class="btn btn-primary">
                    <i class="fas fa-plus"></i> New Application
                </a>
            </div>
            
            <div class="card mb-4">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="applicationsTable">
                            <thead>
                                <tr>
                                    <th>App No.</th>
                                    <th>College</th>
                                    <th>Course</th>
                                    <th>Status</th>
                                    <th>Payment</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($applications && $applications->num_rows > 0): ?>
                                    <?php while ($app = $applications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['application_number']); ?></td>
                                        <td><?php echo htmlspecialchars($app['college_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                                        <td>
                                            <?php 
                                            $status_class = [
                                                'draft' => 'secondary',
                                                'submitted' => 'primary',
                                                'under_review' => 'warning',
                                                'approved' => 'success',
                                                'rejected' => 'danger'
                                            ];
                                            ?>
                                            <span class="badge bg-<?php echo $status_class[$app['status']] ?? 'secondary'; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                            </span>
                                        </td>
                                        <td>
                                            <?php if ($app['is_payment_done']): ?>
                                            <span class="badge bg-success">Paid</span>
                                            <?php else: ?>
                                            <span class="badge bg-warning">Pending</span>
                                            <?php endif; ?>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($app['created_at'])); ?></td>
                                        <td>
                                            <a href="track_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i> View
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted py-4">
                                            No applications yet. <a href="application_form.php">Apply now</a>
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            </tbody>
                        </table>
                    </div>
            </div>
        </main>
    </div>

<?php include '../includes/footer.php'; ?>
