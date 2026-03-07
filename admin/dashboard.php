<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

// Get admin stats
$total_students = $conn->query("SELECT COUNT(*) as count FROM users WHERE role = 'student'")->fetch_assoc()['count'];
$total_applications = $conn->query("SELECT COUNT(*) as count FROM applications")->fetch_assoc()['count'];
$pending_applications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'submitted'")->fetch_assoc()['count'];
$approved_applications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'approved'")->fetch_assoc()['count'];
$rejected_applications = $conn->query("SELECT COUNT(*) as count FROM applications WHERE status = 'rejected'")->fetch_assoc()['count'];
$total_colleges = $conn->query("SELECT COUNT(*) as count FROM colleges WHERE is_active = 1")->fetch_assoc()['count'];
$total_courses = $conn->query("SELECT COUNT(*) as count FROM courses WHERE is_active = 1")->fetch_assoc()['count'];
$pending_documents = $conn->query("SELECT COUNT(*) as count FROM documents WHERE status = 'pending'")->fetch_assoc()['count'];

// Get recent applications
$recent_applications = $conn->query("SELECT a.*, u.email, c.name as course_name, col.name as college_name 
    FROM applications a 
    JOIN users u ON a.user_id = u.id 
    JOIN courses c ON a.course_id = c.id 
    JOIN colleges col ON c.college_id = col.id 
    ORDER BY a.created_at DESC LIMIT 10");

// Get application stats by status
$status_stats = $conn->query("SELECT status, COUNT(*) as count FROM applications GROUP BY status");
$status_data = [];
while ($row = $status_stats->fetch_assoc()) {
    $status_data[$row['status']] = $row['count'];
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Page Header -->
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Dashboard</h1>
                <div class="btn-toolbar mb-2 mb-md-0">
                    <div class="btn-group me-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="window.print()">
                            <i class="fas fa-print"></i> Print
                        </button>
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="exportToExcel('applicationsTable', 'applications')">
                            <i class="fas fa-file-excel"></i> Export
                        </button>
                    </div>
                </div>
            </div>
            
            <!-- Stats Cards -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card primary">
                        <i class="fas fa-users stat-icon"></i>
                        <div class="stat-number"><?php echo $total_students; ?></div>
                        <div class="stat-label">Total Students</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card success">
                        <i class="fas fa-file-alt stat-icon"></i>
                        <div class="stat-number"><?php echo $total_applications; ?></div>
                        <div class="stat-label">Total Applications</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card warning">
                        <i class="fas fa-clock stat-icon"></i>
                        <div class="stat-number"><?php echo $pending_applications; ?></div>
                        <div class="stat-label">Pending Applications</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-xl-3">
                    <div class="stat-card danger">
                        <i class="fas fa-check-circle stat-icon"></i>
                        <div class="stat-number"><?php echo $approved_applications; ?></div>
                        <div class="stat-label">Approved Applications</div>
                    </div>
                </div>
            </div>
            
            <!-- Second Row Stats -->
            <div class="row g-4 mb-4">
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="stat-card primary">
                        <i class="fas fa-university stat-icon"></i>
                        <div class="stat-number"><?php echo $total_colleges; ?></div>
                        <div class="stat-label">Active Colleges</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="stat-card success">
                        <i class="fas fa-book stat-icon"></i>
                        <div class="stat-number"><?php echo $total_courses; ?></div>
                        <div class="stat-label">Active Courses</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="stat-card warning">
                        <i class="fas fa-file-upload stat-icon"></i>
                        <div class="stat-number"><?php echo $pending_documents; ?></div>
                        <div class="stat-label">Pending Documents</div>
                    </div>
                </div>
                
                <div class="col-12 col-sm-6 col-md-3">
                    <div class="stat-card danger">
                        <i class="fas fa-times-circle stat-icon"></i>
                        <div class="stat-number"><?php echo $rejected_applications; ?></div>
                        <div class="stat-label">Rejected Applications</div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <!-- Application Status Chart -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-chart-pie me-2"></i>Application Status Distribution
                        </div>
                        <div class="card-body">
                            <div class="chart-container" style="height: 250px;">
                                <canvas id="statusChart"></canvas>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Quick Actions -->
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">
                            <i class="fas fa-bolt me-2"></i>Quick Actions
                        </div>
                        <div class="card-body">
                            <div class="row g-3">
                                <div class="col-6">
                                    <a href="applications.php?status=submitted" class="btn btn-outline-primary w-100 p-3">
                                        <i class="fas fa-tasks fa-2x d-block mb-2"></i>
                                        Review Applications
                                        <span class="badge bg-danger"><?php echo $pending_applications; ?></span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="verify_documents.php" class="btn btn-outline-success w-100 p-3">
                                        <i class="fas fa-file-check fa-2x d-block mb-2"></i>
                                        Verify Documents
                                        <span class="badge bg-warning"><?php echo $pending_documents; ?></span>
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="colleges.php" class="btn btn-outline-info w-100 p-3">
                                        <i class="fas fa-university fa-2x d-block mb-2"></i>
                                        Manage Colleges
                                    </a>
                                </div>
                                <div class="col-6">
                                    <a href="courses.php" class="btn btn-outline-warning w-100 p-3">
                                        <i class="fas fa-book fa-2x d-block mb-2"></i>
                                        Manage Courses
                                    </a>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Recent Applications -->
            <div class="card mb-4">
                <div class="card-header d-flex justify-content-between align-items-center">
                    <span><i class="fas fa-list me-2"></i>Recent Applications</span>
                    <a href="applications.php" class="btn btn-sm btn-primary">View All</a>
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
                                <?php if ($recent_applications && $recent_applications->num_rows > 0): ?>
                                    <?php while ($app = $recent_applications->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($app['application_number']); ?></td>
                                        <td><?php echo htmlspecialchars($app['email']); ?></td>
                                        <td><?php echo htmlspecialchars($app['course_name']); ?></td>
                                        <td><?php echo htmlspecialchars($app['college_name']); ?></td>
                                        <td>
                                            <span class="badge badge-<?php echo $app['status']; ?>">
                                                <?php echo ucfirst(str_replace('_', ' ', $app['status'])); ?>
                                            </span>
                                        </td>
                                        <td><?php echo date('d M Y', strtotime($app['created_at'])); ?></td>
                                        <td>
                                            <a href="view_application.php?id=<?php echo $app['id']; ?>" class="btn btn-sm btn-info">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="7" class="text-center text-muted">No applications yet</td>
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

<!-- Chart.js -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Status Chart
    const ctx = document.getElementById('statusChart').getContext('2d');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: ['Draft', 'Submitted', 'Under Review', 'Approved', 'Rejected'],
            datasets: [{
                data: [
                    <?php echo isset($status_data['draft']) ? $status_data['draft'] : 0; ?>,
                    <?php echo isset($status_data['submitted']) ? $status_data['submitted'] : 0; ?>,
                    <?php echo isset($status_data['under_review']) ? $status_data['under_review'] : 0; ?>,
                    <?php echo isset($status_data['approved']) ? $status_data['approved'] : 0; ?>,
                    <?php echo isset($status_data['rejected']) ? $status_data['rejected'] : 0; ?>
                ],
                backgroundColor: [
                    '#95a5a6',
                    '#3498db',
                    '#f39c12',
                    '#27ae60',
                    '#e74c3c'
                ]
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'right'
                }
            }
        }
    });
</script>

