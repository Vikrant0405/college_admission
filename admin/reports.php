<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Reports & Analytics - Admin';

// Get statistics
$total_students = $conn->query("SELECT COUNT(*) as c FROM users WHERE role = 'student'")->fetch_assoc()['c'];
$total_applications = $conn->query("SELECT COUNT(*) as c FROM applications")->fetch_assoc()['c'];
$approved = $conn->query("SELECT COUNT(*) as c FROM applications WHERE status = 'approved'")->fetch_assoc()['c'];
$rejected = $conn->query("SELECT COUNT(*) as c FROM applications WHERE status = 'rejected'")->fetch_assoc()['c'];
$pending = $conn->query("SELECT COUNT(*) as c FROM applications WHERE status IN ('submitted', 'under_review')")->fetch_assoc()['c'];

// Applications by course
$courses_stats = $conn->query("SELECT c.name, COUNT(a.id) as count 
    FROM courses c 
    LEFT JOIN applications a ON c.id = a.course_id 
    GROUP BY c.id 
    ORDER BY count DESC 
    LIMIT 10");

// Applications by college
$college_stats = $conn->query("SELECT col.name, COUNT(a.id) as count 
    FROM colleges col 
    LEFT JOIN courses c ON col.id = c.college_id 
    LEFT JOIN applications a ON c.id = a.course_id 
    GROUP BY col.id 
    ORDER BY count DESC");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Reports & Analytics</h1>
                <button class="btn btn-primary" onclick="window.print()">
                    <i class="fas fa-print"></i> Print Report
                </button>
            </div>
            
            <!-- Summary Stats -->
            <div class="row g-4 mb-4">
                <div class="col-md-3">
                    <div class="card bg-primary text-white">
                        <div class="card-body">
                            <h5>Total Students</h5>
                            <h2><?php echo $total_students; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-info text-white">
                        <div class="card-body">
                            <h5>Total Applications</h5>
                            <h2><?php echo $total_applications; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-success text-white">
                        <div class="card-body">
                            <h5>Approved</h5>
                            <h2><?php echo $approved; ?></h2>
                        </div>
                    </div>
                </div>
                <div class="col-md-3">
                    <div class="card bg-warning text-white">
                        <div class="card-body">
                            <h5>Pending</h5>
                            <h2><?php echo $pending; ?></h2>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Charts Row -->
            <div class="row g-4 mb-4">
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Applications by Course</div>
                        <div class="card-body">
                            <canvas id="courseChart"></canvas>
                        </div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header">Applications by College</div>
                        <div class="card-body">
                            <canvas id="collegeChart"></canvas>
                        </div>
                    </div>
                </div>
            </div>
            
            <!-- Applications by Status -->
            <div class="row g-4">
                <div class="col-md-12">
                    <div class="card">
                        <div class="card-header">Application Status Overview</div>
                        <div class="card-body">
                            <div class="table-responsive">
                                <table class="table">
                                    <thead>
                                        <tr>
                                            <th>Status</th>
                                            <th>Count</th>
                                            <th>Percentage</th>
                                        </tr>
                                    </thead>
                                    <tbody>
                                        <?php 
                                        $statuses = ['draft', 'submitted', 'under_review', 'approved', 'rejected'];
                                        foreach ($statuses as $status):
                                            $count = $conn->query("SELECT COUNT(*) as c FROM applications WHERE status = '$status'")->fetch_assoc()['c'];
                                            $pct = $total_applications > 0 ? round(($count / $total_applications) * 100, 1) : 0;
                                        ?>
                                        <tr>
                                            <td><?php echo ucfirst(str_replace('_', ' ', $status)); ?></td>
                                            <td><?php echo $count; ?></td>
                                            <td>
                                                <div class="progress">
                                                    <div class="progress-bar" style="width: <?php echo $pct; ?>%"><?php echo $pct; ?>%</div>
                                                </div>
                                            </td>
                                        </tr>
                                        <?php endforeach; ?>
                                    </tbody>
                                </table>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    // Course Chart
    const courseCtx = document.getElementById('courseChart').getContext('2d');
    new Chart(courseCtx, {
        type: 'bar',
        data: {
            labels: [<?php 
                $labels = [];
                $data = [];
                while($row = $courses_stats->fetch_assoc()) {
                    $labels[] = "'" . addslashes($row['name']) . "'";
                    $data[] = $row['count'];
                }
                echo implode(',', $labels);
            ?>],
            datasets: [{
                label: 'Applications',
                data: [<?php echo implode(',', $data); ?>],
                backgroundColor: '#3498db'
            }]
        }
    });
    
    // College Chart
    const collegeCtx = document.getElementById('collegeChart').getContext('2d');
    new Chart(collegeCtx, {
        type: 'pie',
        data: {
            labels: [<?php 
                $clabels = [];
                $cdata = [];
                while($crow = $college_stats->fetch_assoc()) {
                    $clabels[] = "'" . addslashes($crow['name']) . "'";
                    $cdata[] = $crow['count'];
                }
                echo implode(',', $clabels);
            ?>],
            datasets: [{
                data: [<?php echo implode(',', $cdata); ?>],
                backgroundColor: ['#3498db', '#2ecc71', '#e74c3c', '#f39c12', '#9b59b6']
            }]
        }
    });
</script>

<?php include '../includes/footer.php'; ?>

