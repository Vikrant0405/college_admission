<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Manage Colleges';

$error = '';
$success = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['add_college'])) {
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $pincode = trim($_POST['pincode']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $website = trim($_POST['website']);
        $establishment_year = $_POST['establishment_year'];
        $description = trim($_POST['description']);
        
        // Check if code already exists
        $check = $conn->query("SELECT id FROM colleges WHERE code = '$code'");
        if ($check->num_rows > 0) {
            $error = 'College code already exists.';
        } else {
            $stmt = $conn->prepare("INSERT INTO colleges (name, code, address, city, state, pincode, phone, email, website, establishment_year, description) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssssssissis", $name, $code, $address, $city, $state, $pincode, $phone, $email, $website, $establishment_year, $description);
            
            if ($stmt->execute()) {
                $success = 'College added successfully!';
            } else {
                $error = 'Error adding college. Please try again.';
            }
        }
    }
    
    if (isset($_POST['update_college'])) {
        $id = $_POST['id'];
        $name = trim($_POST['name']);
        $code = trim($_POST['code']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $pincode = trim($_POST['pincode']);
        $phone = trim($_POST['phone']);
        $email = trim($_POST['email']);
        $website = trim($_POST['website']);
        $establishment_year = $_POST['establishment_year'];
        $description = trim($_POST['description']);
        
        $stmt = $conn->prepare("UPDATE colleges SET name=?, code=?, address=?, city=?, state=?, pincode=?, phone=?, email=?, website=?, establishment_year=?, description=? WHERE id=?");
        $stmt->bind_param("ssssssissisi", $name, $code, $address, $city, $state, $pincode, $phone, $email, $website, $establishment_year, $description, $id);
        
        if ($stmt->execute()) {
            $success = 'College updated successfully!';
        } else {
            $error = 'Error updating college. Please try again.';
        }
    }
    
    if (isset($_POST['delete_college'])) {
        $id = $_POST['id'];
        
        // Check if college has courses
        $check = $conn->query("SELECT COUNT(*) as count FROM courses WHERE college_id = $id");
        $count = $check->fetch_assoc()['count'];
        
        if ($count > 0) {
            $error = 'Cannot delete college. It has associated courses.';
        } else {
            $stmt = $conn->prepare("DELETE FROM colleges WHERE id = ?");
            $stmt->bind_param("i", $id);
            
            if ($stmt->execute()) {
                $success = 'College deleted successfully!';
            } else {
                $error = 'Error deleting college.';
            }
        }
    }
}

// Get all colleges
$colleges = $conn->query("SELECT * FROM colleges ORDER BY name");
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="page-header">
                <div class="d-flex justify-content-between align-items-center">
                    <div>
                        <h2>Manage Colleges</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="dashboard.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Colleges</li>
                            </ol>
                        </nav>
                    </div>
                    <button class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#addCollegeModal">
                        <i class="fas fa-plus me-2"></i>Add College
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
            
            <!-- Colleges Table -->
            <div class="card">
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover" id="collegesTable">
                            <thead>
                                <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>City</th>
                                    <th>State</th>
                                    <th>Phone</th>
                                    <th>Email</th>
                                    <th>Established</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php if ($colleges && $colleges->num_rows > 0): ?>
                                    <?php while ($college = $colleges->fetch_assoc()): ?>
                                    <tr>
                                        <td><?php echo htmlspecialchars($college['code']); ?></td>
                                        <td><?php echo htmlspecialchars($college['name']); ?></td>
                                        <td><?php echo htmlspecialchars($college['city']); ?></td>
                                        <td><?php echo htmlspecialchars($college['state']); ?></td>
                                        <td><?php echo htmlspecialchars($college['phone']); ?></td>
                                        <td><?php echo htmlspecialchars($college['email']); ?></td>
                                        <td><?php echo $college['establishment_year']; ?></td>
                                        <td>
                                            <span class="badge bg-<?php echo $college['is_active'] ? 'success' : 'secondary'; ?>">
                                                <?php echo $college['is_active'] ? 'Active' : 'Inactive'; ?>
                                            </span>
                                        </td>
                                        <td>
                                            <button class="btn btn-sm btn-info" data-bs-toggle="modal" data-bs-target="#editCollegeModal<?php echo $college['id']; ?>">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure?');">
                                                <input type="hidden" name="id" value="<?php echo $college['id']; ?>">
                                                <button type="submit" name="delete_college" class="btn btn-sm btn-danger">
                                                    <i class="fas fa-trash"></i>
                                                </button>
                                            </form>
                                        </td>
                                    </tr>
                                    <?php endwhile; ?>
                                <?php else: ?>
                                    <tr>
                                        <td colspan="9" class="text-center text-muted">No colleges found</td>
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

<!-- Add College Modal -->
<div class="modal fade" id="addCollegeModal" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Add New College</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">College Name *</label>
                            <input type="text" class="form-control" name="name" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">College Code *</label>
                            <input type="text" class="form-control" name="code" required placeholder="e.g., IITM">
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" name="pincode">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" name="website" placeholder="https://">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Establishment Year</label>
                            <input type="number" class="form-control" name="establishment_year" min="1900" max="2030">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="add_college" class="btn btn-primary">Add College</button>
                </div>
            </form>
        </div>
    </div>
</div>

<?php 
// Reset pointer and create edit modals
$colleges = $conn->query("SELECT * FROM colleges ORDER BY name");
while ($college = $cols = $colleges->fetch_assoc()): 
?>
<!-- Edit College Modal -->
<div class="modal fade" id="editCollegeModal<?php echo $college['id']; ?>" tabindex="-1">
    <div class="modal-dialog modal-lg">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Edit College</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
            </div>
            <form method="POST">
                <input type="hidden" name="id" value="<?php echo $college['id']; ?>">
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">College Name *</label>
                            <input type="text" class="form-control" name="name" value="<?php echo htmlspecialchars($college['name']); ?>" required>
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">College Code *</label>
                            <input type="text" class="form-control" name="code" value="<?php echo htmlspecialchars($college['code']); ?>" required>
                        </div>
                    </div>
                    <div class="mb-3">
                        <label class="form-label">Address</label>
                        <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($college['address']); ?></textarea>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">City</label>
                            <input type="text" class="form-control" name="city" value="<?php echo htmlspecialchars($college['city']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">State</label>
                            <input type="text" class="form-control" name="state" value="<?php echo htmlspecialchars($college['state']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Pincode</label>
                            <input type="text" class="form-control" name="pincode" value="<?php echo htmlspecialchars($college['pincode']); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Phone</label>
                            <input type="text" class="form-control" name="phone" value="<?php echo htmlspecialchars($college['phone']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Email</label>
                            <input type="email" class="form-control" name="email" value="<?php echo htmlspecialchars($college['email']); ?>">
                        </div>
                        <div class="col-md-4 mb-3">
                            <label class="form-label">Website</label>
                            <input type="text" class="form-control" name="website" value="<?php echo htmlspecialchars($college['website']); ?>">
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Establishment Year</label>
                            <input type="number" class="form-control" name="establishment_year" value="<?php echo $college['establishment_year']; ?>" min="1900" max="2030">
                        </div>
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Description</label>
                            <textarea class="form-control" name="description" rows="2"><?php echo htmlspecialchars($college['description']); ?></textarea>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                    <button type="submit" name="update_college" class="btn btn-primary">Update College</button>
                </div>
            </form>
        </div>
    </div>
</div>
<?php endwhile; ?>

<?php include '../includes/footer.php'; ?>

