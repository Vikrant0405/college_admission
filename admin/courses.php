<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Manage Courses';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_course'])) {
        $college_id = $_POST['college_id'];
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);
        $department = trim($_POST['department']);
        $description = trim($_POST['description']);
        $eligibility_criteria = trim($_POST['eligibility_criteria']);
        $duration_years = $_POST['duration_years'];
        $seats_available = $_POST['seats_available'];
        $fees = $_POST['fees'];
        
        $stmt = $conn->prepare("INSERT INTO courses (college_id, name, code, department, description, eligibility_criteria, duration_years, seats_available, fees) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?)");
        $stmt->bind_param("isssssiid", $college_id, $name, $code, $department, $description, $eligibility_criteria, $duration_years, $seats_available, $fees);
        
        if ($stmt->execute()) {
            $success = 'Course added successfully!';
        } else {
            $error = 'Error adding course. Please try again.';
        }
    }
    
    if (isset($_POST['update_course'])) {
        $id = $_POST['id'];
        $college_id = $_POST['college_id'];
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);
        $department = trim($_POST['department']);
        $description = trim($_POST['description']);
        $eligibility_criteria = trim($_POST['eligibility_criteria']);
        $duration_years = $_POST['duration_years'];
        $seats_available = $_POST['seats_available'];
        $fees = $_POST['fees'];
        
        $stmt = $conn->prepare("UPDATE courses SET college_id=?, name=?, code=?, department=?, description=?, eligibility_criteria=?, duration_years=?, seats_available=?, fees=? WHERE id=?");
        $stmt->bind_param("isssssiidi", $college_id, $name, $code, $department, $description, $eligibility_criteria, $duration_years, $seats_available, $fees, $id);
        
        if ($stmt->execute()) {
            $success = 'Course updated successfully!';
        } else {
            $error = 'Error updating course. Please try again.';
        }
    }
    
    if (isset($_POST['delete_course'])) {
        $id = $_POST['id'];
        
        // Check if course has applications
        $check = $conn->query("SELECT COUNT(*) as count FROM applications WHERE course_id = $id");
        $count = $check->fetch_assoc()['count'];
        
        if ($count > 0) {
            $error = 'Cannot delete course. It has associated applications.';
        } else {
            $stmt = $conn->prepare("DELETE FROM courses WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $success = 'Course deleted successfully!';
            } else {
                $error = 'Error deleting course.';
            }
        }
    }
}

// Get all courses
$courses = $conn->query("SELECT c.*, col.name as college_name FROM courses c 
    JOIN colleges col ON c.college_id = col.id 
    ORDER BY col.name, c.name");

// Get colleges for dropdown
$colleges = $conn->query("SELECT * FROM colleges WHERE is_active = 1 ORDER BY name");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Manage Courses</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Courses</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCourseModal">
                        <i class="fas fa-plus me-2"></i>Add Course
                    </button>
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
            
            <!-- Courses Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="coursesTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Course Name</th>
                                    <th>College</th>
                                    <th>Department</th>
                                    <th>Duration</th>
                                    <th>Seats</th>
                                    <th>Fees</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($courses && $courses->num_rows > 0): ?>
                                    <?php while ($course = $courses->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($course['code']); ?></td>
                                        <td><?php echo htmlspecialchars($course['name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['college_name']); ?></td>
                                        <td><?php echo htmlspecialchars($course['department']); ?></td>
                                        <td><?php echo $course['duration_years']; ?> Years</td>
                                        <td><?php echo $course['seats_available']; ?></td>
                                        <td>₹<?php echo number_format($course['fees']); ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $course['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $course['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCourseModal<?php echo $course['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                                                <button type="submit" name="delete_course" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No courses found</td>
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

<!-- Add Course Modal -->
<div class="modal fade" id="addCourseModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">College *</label>
                            <select class="form-select" name="college_id" required>
                                <option value="">-- Select College --</option>
                                <?php while ($college = $colleges->fetch_assoc()): ?>
                                <option value="<?php echo $college['id']; ?>"><?php echo htmlspecialchars($college['name']); ?></option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" class="form-control" name="code" placeholder="e.g., BTech">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" placeholder="e.g., Computer Science">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Eligibility Criteria</label>
                        <textarea class="form-control" name="eligibility_criteria" rows="2" placeholder="Minimum qualifications required"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Duration (Years)</label>
                            <input type="number" class="form-control" name="duration_years" value="4" min="1" max="6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Seats Available</label>
                            <input type="number" class="form-control" name="seats_available" value="60" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fees (₹)</label>
                            <input type="number" class="form-control" name="fees" value="100000" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_course" class="btn btn-primary">Add Course</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Reset and create edit modals
$courses = $conn->query("SELECT c.*, col.name as college_name FROM courses c 
    JOIN colleges col ON c.college_id = col.id 
    ORDER BY col.name, c.name");
$colleges = $conn->query("SELECT * FROM colleges WHERE is_active = 1 ORDER BY name");

while ($course = $courses->fetch_assoc()): 
?>
<!-- Edit Course Modal -->
<div class="modal fade" id="editCourseModal<?php echo $course['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit Course</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $course['id']; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">College *</label>
                            <select class="form-select" name="college_id" required>
                                <?php 
                                $colleges->data_seek(0);
                                while ($college = $colleges->fetch_assoc()): 
                                ?>
                                <option value="<?php echo $college['id']; ?>" <?php echo $college['id'] == $course['college_id'] ? 'selected' : ''; ?>>
                                    <?php echo htmlspecialchars($college['name']); ?>
                                </option>
                                <?php endwhile; ?>
                            </select>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Name *</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($course['name']); ?>" required>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Course Code</label>
                            <input type="text" class="form-control" name="code" value="<?php echo htmlspecialchars($course['code']); ?>">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Department</label>
                            <input type="text" class="form-control" name="department" value="<?php echo htmlspecialchars($course['department']); ?>">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($course['description']); ?></textarea>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Eligibility Criteria</label>
                        <textarea class="form-control" name="eligibility_criteria" rows="2"><?php echo htmlspecialchars($course['eligibility_criteria']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Duration (Years)</label>
                            <input type="number" class="form-control" name="duration_years" value="<?php echo $course['duration_years']; ?>" min="1" max="6">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Seats Available</label>
                            <input type="number" class="form-control" name="seats_available" value="<?php echo $course['seats_available']; ?>" min="1">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Fees (₹)</label>
                            <input type="number" class="form-control" name="fees" value="<?php echo $course['fees']; ?>" min="0">
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_course" class="btn btn-primary">Update Course</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>

