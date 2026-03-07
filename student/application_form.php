<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Application Form';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get available courses
$courses = $conn->query("SELECT c.*, col.name as college_name FROM courses c 
    JOIN colleges col ON c.college_id = col.id 
    WHERE c.is_active = 1 AND c.seats_available > 0 
    ORDER BY col.name, c.name");

// Get user's profile for pre-fill
$profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id")->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $course_id = $_POST['course_id'];
    $personal_statement = trim($_POST['personal_statement']);
    $action = $_POST['action']; // 'save_draft' or 'submit'
    
    // Validate
    if (empty($course_id)) {
        $error = 'Please select a course.';
    } else {
        // Check if already applied for same course
        $check_stmt = $conn->prepare("SELECT id FROM applications WHERE user_id = ? AND course_id = ?");
        $check_stmt->bind_param("ii", $user_id, $course_id);
        $check_stmt->execute();
        
        if ($check_stmt->get_result()->num_rows > 0) {
            $error = 'You have already applied for this course.';
        } else {
            // Generate application number
            $app_number = 'APP' . date('Ymd') . str_pad(rand(1, 9999), 4, '0', STR_PAD_LEFT);
            
            $status = ($action === 'submit') ? 'submitted' : 'draft';
            $submitted_at = ($action === 'submit') ? date('Y-m-d H:i:s') : null;
            
            // Insert application
            $stmt = $conn->prepare("INSERT INTO applications (user_id, course_id, application_number, status, personal_statement, submitted_at) VALUES (?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("iissss", $user_id, $course_id, $app_number, $status, $personal_statement, $submitted_at);
            
            if ($stmt->execute()) {
                $application_id = $conn->insert_id;
                
                // Add timeline entry
                $timeline_stmt = $conn->prepare("INSERT INTO application_timeline (application_id, status, description) VALUES (?, ?, ?)");
                $timeline_status = ($action === 'submit') ? 'Application Submitted' : 'Draft Saved';
                $timeline_desc = ($action === 'submit') ? 'Application has been submitted successfully' : 'Application saved as draft';
                $timeline_stmt->bind_param("iss", $application_id, $timeline_status, $timeline_desc);
                $timeline_stmt->execute();
                
                // Send notification
                $notif_stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'info')");
                $notif_title = ($action === 'submit') ? 'Application Submitted' : 'Draft Saved';
                $notif_msg = ($action === 'submit') ? "Your application ($app_number) has been submitted successfully." : "Your application has been saved as draft.";
                $notif_stmt->bind_param("iss", $user_id, $notif_title, $notif_msg);
                $notif_stmt->execute();
                
                if ($action === 'submit') {
                    $success = 'Application submitted successfully! Application Number: ' . $app_number;
                } else {
                    $success = 'Application saved as draft.';
                }
            } else {
                $error = 'Error submitting application. Please try again.';
            }
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <!-- Page Header -->
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Application Form</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Apply</li>
                            </ol>
                        </nav>
                    </div>
                </div>
            </div>
            
            <!-- Alerts -->
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i><?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i><?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <!-- Step Wizard -->
            <div class="step-wizard mb-4">
                <div class="step-item active">
                    <div class="step-number">1</div>
                    <div class="step-label">Select Course</div>
                </div>
                <div class="step-item">
                    <div class="step-number">2</div>
                    <div class="step-label">Fill Details</div>
                </div>
                <div class="step-item">
                    <div class="step-number">3</div>
                    <div class="step-label">Upload Documents</div>
                </div>
                <div class="step-item">
                    <div class="step-number">4</div>
                    <div class="step-label">Submit</div>
                </div>
            </div>
            
            <!-- Application Form -->
            <div class="card">
                <div class="card-header">
                    <i class="fas fa-file-alt me-2"></i>New Application
                </div>
                <div class="card-body">
                    <form method="POST" id="applicationForm">
                        <!-- Course Selection -->
                        <div class="form-section">
                            <div class="form-section-title">Select Course</div>
                            
                            <div class="mb-3">
                                <label class="form-label">Choose Course <span class="text-danger">*</span></label>
                                <select class="form-select" name="course_id" id="courseSelect" required>
                                    <option value="">-- Select a Course --</option>
                                    <?php if ($courses && $courses->num_rows > 0): ?>
                                        <?php while ($course = $courses->fetch_assoc()): ?>
                                        <option value="<?php echo $course['id']; ?>">
                                            <?php echo htmlspecialchars($course['college_name'] . ' - ' . $course['name'] . ' (' . $course['department'] . ')'); ?>
                                            - <?php echo $course['seats_available']; ?> seats | ₹<?php echo number_format($course['fees']); ?>
                                        </option>
                                        <?php endwhile; ?>
                                    <?php endif; ?>
                                </select>
                            </div>
                            
                            <!-- Course Details (shown when selected) -->
                            <div id="courseDetails" class="alert alert-info" style="display: none;">
                                <h5 id="courseName"></h5>
                                <div class="row mt-3">
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>College:</strong> <span id="collegeName"></span></p>
                                        <p class="mb-1"><strong>Department:</strong> <span id="department"></span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Duration:</strong> <span id="duration"></span> Years</p>
                                        <p class="mb-1"><strong>Seats Available:</strong> <span id="seats"></span></p>
                                    </div>
                                    <div class="col-md-4">
                                        <p class="mb-1"><strong>Fees:</strong> ₹<span id="fees"></span></p>
                                        <p class="mb-1"><strong>Eligibility:</strong> <span id="eligibility"></span></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <!-- Personal Statement -->
                        <div class="form-section">
                            <div class="form-section-title">Personal Statement</div>
                            
                            <div class="mb-3">
                                <label class="form-label">Why do you want to join this course?</label>
                                <textarea class="form-control" name="personal_statement" rows="5" 
                                    placeholder="Write about your interest in this course and career goals..."></textarea>
                                <small class="text-muted">Minimum 50 characters</small>
                            </div>
                        </div>
                        
                        <!-- Summary -->
                        <div class="form-section">
                            <div class="form-section-title">Application Summary</div>
                            
                            <div class="alert alert-light">
                                <div class="row">
                                    <div class="col-md-6">
                                        <p><strong>Applicant Name:</strong> <?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></p>
                                        <p><strong>Email:</strong> <?php echo htmlspecialchars($_SESSION['email']); ?></p>
                                        <p><strong>Phone:</strong> <?php echo htmlspecialchars($profile['phone'] ?? 'Not provided'); ?></p>
                                    </div>
                                    <div class="col-md-6">
                                        <p><strong>Date of Birth:</strong> <?php echo $profile['date_of_birth'] ?? 'Not provided'; ?></p>
                                        <p><strong>Gender:</strong> <?php echo ucfirst($profile['gender'] ?? 'Not provided'); ?></p>
                                        <p><strong>Application Date:</strong> <?php echo date('d M Y'); ?></p>
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="d-flex justify-content-between">
                            <a href="my_applications.php" class="btn btn-secondary">Cancel</a>
                            <div>
                                <button type="submit" name="action" value="save_draft" class="btn btn-outline-primary">
                                    <i class="fas fa-save me-2"></i>Save Draft
                                </button>
                                <button type="submit" name="action" value="submit" class="btn btn-primary">
                                    <i class="fas fa-paper-plane me-2"></i>Submit Application
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Course selection - show details
    const courseSelect = document.getElementById('courseSelect');
    const courseDetails = document.getElementById('courseDetails');
    
    const coursesData = <?php 
        $courses_data = $conn->query("SELECT c.*, col.name as college_name FROM courses c 
            JOIN colleges col ON c.college_id = col.id 
            WHERE c.is_active = 1");
        $data = [];
        while ($c = $courses_data->fetch_assoc()) {
            $data[$c['id']] = $c;
        }
        echo json_encode($data);
    ?>;
    
    courseSelect.addEventListener('change', function() {
        const courseId = this.value;
        if (courseId && coursesData[courseId]) {
            const course = coursesData[courseId];
            document.getElementById('courseName').textContent = course.name;
            document.getElementById('collegeName').textContent = course.college_name;
            document.getElementById('department').textContent = course.department;
            document.getElementById('duration').textContent = course.duration_years;
            document.getElementById('seats').textContent = course.seats_available;
            document.getElementById('fees').textContent = parseInt(course.fees).toLocaleString();
            document.getElementById('eligibility').textContent = course.eligibility_criteria || 'As per college norms';
            courseDetails.style.display = 'block';
        } else {
            courseDetails.style.display = 'none';
        }
    });
</script>

<?php include '../includes/footer.php'; ?>

