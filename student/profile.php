<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'My Profile';

$user_id = $_SESSION['user_id'];
$error = '';
$success = '';

// Get user profile data
$profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id")->fetch_assoc();
$academic = $conn->query("SELECT * FROM academic_details WHERE user_id = $user_id")->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $gender = $_POST['gender'];
        $date_of_birth = $_POST['date_of_birth'];
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $pincode = trim($_POST['pincode']);
        $nationality = trim($_POST['nationality']);
        $aadhar_number = trim($_POST['aadhar_number']);
        
        // Handle profile image upload
        $profile_image = $profile['profile_image'];
        if (isset($_FILES['profile_image']) && $_FILES['profile_image']['error'] === 0) {
            $upload_dir = '../uploads/images/';
            if (!file_exists($upload_dir)) {
                mkdir($upload_dir, 0777, true);
            }
            
            $file_ext = strtolower(pathinfo($_FILES['profile_image']['name'], PATHINFO_EXTENSION));
            $allowed_ext = ['jpg', 'jpeg', 'png', 'gif'];
            
            if (in_array($file_ext, $allowed_ext)) {
                $new_filename = 'profile_' . $user_id . '_' . time() . '.' . $file_ext;
                $target_path = $upload_dir . $new_filename;
                
                if (move_uploaded_file($_FILES['profile_image']['tmp_name'], $target_path)) {
                    // Delete old image if exists
                    if ($profile_image && file_exists($upload_dir . $profile_image)) {
                        unlink($upload_dir . $profile_image);
                    }
                    $profile_image = $new_filename;
                }
            }
        }
        
        // Update profile
        $stmt = $conn->prepare("UPDATE user_profiles SET first_name=?, last_name=?, gender=?, date_of_birth=?, phone=?, address=?, city=?, state=?, pincode=?, nationality=?, aadhar_number=?, profile_image=? WHERE user_id=?");
        $stmt->bind_param("ssssssssssssi", $first_name, $last_name, $gender, $date_of_birth, $phone, $address, $city, $state, $pincode, $nationality, $aadhar_number, $profile_image, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Profile updated successfully!';
            $profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id")->fetch_assoc();
        } else {
            $error = 'Error updating profile. Please try again.';
        }
    }
    
    if (isset($_POST['update_academic'])) {
        $ssc_school = trim($_POST['ssc_school']);
        $ssc_percentage = $_POST['ssc_percentage'];
        $ssc_year = $_POST['ssc_year'];
        $hsc_school = trim($_POST['hsc_school']);
        $hsc_percentage = $_POST['hsc_percentage'];
        $hsc_year = $_POST['hsc_year'];
        $entrance_exam_name = trim($_POST['entrance_exam_name']);
        $entrance_exam_score = $_POST['entrance_exam_score'];
        
        // Update academic details
        $stmt = $conn->prepare("UPDATE academic_details SET ssc_school=?, ssc_percentage=?, ssc_year=?, hsc_school=?, hsc_percentage=?, hsc_year=?, entrance_exam_name=?, entrance_exam_score=? WHERE user_id=?");
        $stmt->bind_param("sdisssisi", $ssc_school, $ssc_percentage, $ssc_year, $hsc_school, $hsc_percentage, $hsc_year, $entrance_exam_name, $entrance_exam_score, $user_id);
        
        if ($stmt->execute()) {
            $success = 'Academic details updated successfully!';
            $academic = $conn->query("SELECT * FROM academic_details WHERE user_id = $user_id")->fetch_assoc();
        } else {
            $error = 'Error updating academic details. Please try again.';
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
                        <h2>My Profile</h2>
                        <nav aria-label="breadcrumb">
                            <ol class="breadcrumb">
                                <li class="breadcrumb-item"><a href="index.php">Dashboard</a></li>
                                <li class="breadcrumb-item active">Profile</li>
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
            
            <div class="row">
                <!-- Profile Photo Section -->
                <div class="col-md-4 mb-4">
                    <div class="card">
                        <div class="card-body text-center">
                            <div class="profile-image-container">
                                <?php if (!empty($profile['profile_image']) && file_exists('../uploads/images/' . $profile['profile_image'])): ?>
                                <img src="../uploads/images/<?php echo $profile['profile_image']; ?>" 
                                     alt="Profile" class="profile-image" id="profileImg">
                                <?php else: ?>
                                <img src="https://via.placeholder.com/150" 
                                     alt="Profile" class="profile-image" id="profileImg">
                                <?php endif; ?>
                            </div>
                            <h4 class="mt-3"><?php echo htmlspecialchars($profile['first_name'] . ' ' . $profile['last_name']); ?></h4>
                            <p class="text-muted"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                            
                            <div class="mt-3">
                                <button class="btn btn-primary" onclick="document.getElementById('profileImageInput').click()">
                                    <i class="fas fa-camera me-2"></i>Change Photo
                                </button>
                                <input type="file" id="profileImageInput" style="display: none;" accept="image/*">
                            </div>
                        </div>
                    </div>
                    
                    <!-- Account Info -->
                    <div class="card mt-3">
                        <div class="card-header">
                            <i class="fas fa-info-circle me-2"></i>Account Information
                        </div>
                        <div class="card-body">
                            <div class="mb-2">
                                <small class="text-muted">Email</small>
                                <p class="mb-0"><?php echo htmlspecialchars($_SESSION['email']); ?></p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Account Status</small>
                                <p class="mb-0">
                                    <span class="badge bg-success">Active</span>
                                </p>
                            </div>
                            <div class="mb-2">
                                <small class="text-muted">Member Since</small>
                                <p class="mb-0"><?php echo date('d M Y', strtotime($profile['created_at'])); ?></p>
                            </div>
                        </div>
                    </div>
                </div>
                
                <!-- Profile Form -->
                <div class="col-md-8">
                    <!-- Personal Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-user me-2"></i>Personal Details
                        </div>
                        <div class="card-body">
                            <form method="POST" enctype="multipart/form-data">
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">First Name *</label>
                                        <input type="text" class="form-control" name="first_name" 
                                               value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Last Name</label>
                                        <input type="text" class="form-control" name="last_name" 
                                               value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Gender</label>
                                        <select class="form-select" name="gender">
                                            <option value="">Select Gender</option>
                                            <option value="male" <?php echo ($profile['gender'] ?? '') === 'male' ? 'selected' : ''; ?>>Male</option>
                                            <option value="female" <?php echo ($profile['gender'] ?? '') === 'female' ? 'selected' : ''; ?>>Female</option>
                                            <option value="other" <?php echo ($profile['gender'] ?? '') === 'other' ? 'selected' : ''; ?>>Other</option>
                                        </select>
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Date of Birth</label>
                                        <input type="date" class="form-control datepicker" name="date_of_birth" 
                                               value="<?php echo $profile['date_of_birth'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Phone Number</label>
                                        <input type="tel" class="form-control" name="phone" 
                                               value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Nationality</label>
                                        <input type="text" class="form-control" name="nationality" 
                                               value="<?php echo htmlspecialchars($profile['nationality'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea class="form-control" name="address" rows="2"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                                </div>
                                
                                <div class="row">
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" class="form-control" name="city" 
                                               value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" class="form-control" name="state" 
                                               value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-4 mb-3">
                                        <label class="form-label">Pincode</label>
                                        <input type="text" class="form-control" name="pincode" 
                                               value="<?php echo htmlspecialchars($profile['pincode'] ?? ''); ?>">
                                    </div>
                                </div>
                                
                                <div class="mb-3">
                                    <label class="form-label">Aadhar Number</label>
                                    <input type="text" class="form-control" name="aadhar_number" 
                                           value="<?php echo htmlspecialchars($profile['aadhar_number'] ?? ''); ?>" maxlength="12">
                                </div>
                                
                                <input type="file" name="profile_image" id="profileImageInput" style="display: none;" accept="image/*">
                                
                                <button type="submit" name="update_profile" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Changes
                                </button>
                            </form>
                        </div>
                    </div>
                    
                    <!-- Academic Details -->
                    <div class="card mb-4">
                        <div class="card-header">
                            <i class="fas fa-graduation-cap me-2"></i>Academic Details
                        </div>
                        <div class="card-body">
                            <form method="POST">
                                <h6 class="text-muted mb-3">SSC (10th Grade)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">School Name</label>
                                        <input type="text" class="form-control" name="ssc_school" 
                                               value="<?php echo htmlspecialchars($academic['ssc_school'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Percentage</label>
                                        <input type="number" step="0.01" class="form-control" name="ssc_percentage" 
                                               value="<?php echo $academic['ssc_percentage'] ?? ''; ?>" min="0" max="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Year</label>
                                        <input type="number" class="form-control" name="ssc_year" 
                                               value="<?php echo $academic['ssc_year'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <h6 class="text-muted mb-3">HSC (12th Grade)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">School/College Name</label>
                                        <input type="text" class="form-control" name="hsc_school" 
                                               value="<?php echo htmlspecialchars($academic['hsc_school'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Percentage</label>
                                        <input type="number" step="0.01" class="form-control" name="hsc_percentage" 
                                               value="<?php echo $academic['hsc_percentage'] ?? ''; ?>" min="0" max="100">
                                    </div>
                                    <div class="col-md-3 mb-3">
                                        <label class="form-label">Year</label>
                                        <input type="number" class="form-control" name="hsc_year" 
                                               value="<?php echo $academic['hsc_year'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <h6 class="text-muted mb-3">Entrance Exam (if applicable)</h6>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Exam Name</label>
                                        <input type="text" class="form-control" name="entrance_exam_name" 
                                               value="<?php echo htmlspecialchars($academic['entrance_exam_name'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">Score/Rank</label>
                                        <input type="number" class="form-control" name="entrance_exam_score" 
                                               value="<?php echo $academic['entrance_exam_score'] ?? ''; ?>">
                                    </div>
                                </div>
                                
                                <button type="submit" name="update_academic" class="btn btn-primary">
                                    <i class="fas fa-save me-2"></i>Save Academic Details
                                </button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
    // Profile image preview
    document.getElementById('profileImageInput').addEventListener('change', function(e) {
        if (this.files && this.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById('profileImg').src = e.target.result;
            }
            reader.readAsDataURL(this.files[0]);
        }
    });
</script>

<?php include '../includes/footer.php'; ?>

