<?php
require_once '../config/db.php';
require_once '../config/constants.php';
require_once '../includes/header.php';

$page_title = 'Settings - Student';

$success = '';
$error = '';

// Get current user profile
$user_id = $_SESSION['user_id'];
$profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id")->fetch_assoc();

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['update_profile'])) {
        $first_name = trim($_POST['first_name']);
        $last_name = trim($_POST['last_name']);
        $phone = trim($_POST['phone']);
        $address = trim($_POST['address']);
        $city = trim($_POST['city']);
        $state = trim($_POST['state']);
        $pincode = trim($_POST['pincode']);
        
        if ($profile) {
            $stmt = $conn->prepare("UPDATE user_profiles SET first_name = ?, last_name = ?, phone = ?, address = ?, city = ?, state = ?, pincode = ? WHERE user_id = ?");
            $stmt->bind_param("sssssssi", $first_name, $last_name, $phone, $address, $city, $state, $pincode, $user_id);
        } else {
            $stmt = $conn->prepare("INSERT INTO user_profiles (user_id, first_name, last_name, phone, address, city, state, pincode) VALUES (?, ?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("issssssi", $user_id, $first_name, $last_name, $phone, $address, $city, $state, $pincode);
        }
        
        if ($stmt->execute()) {
            $_SESSION['user_name'] = $first_name . ' ' . $last_name;
            $profile = $conn->query("SELECT * FROM user_profiles WHERE user_id = $user_id")->fetch_assoc();
            $success = 'Profile updated successfully';
        } else {
            $error = 'Failed to update profile';
        }
    }
    
    if (isset($_POST['change_password'])) {
        $new_password = $_POST['new_password'];
        $confirm_password = $_POST['confirm_password'];
        
        if ($new_password !== $confirm_password) {
            $error = 'Passwords do not match';
        } elseif (strlen($new_password) < 6) {
            $error = 'Password must be at least 6 characters';
        } else {
            $stmt = $conn->prepare("UPDATE users SET password = ? WHERE id = ?");
            $stmt->bind_param("si", $new_password, $user_id);
            
            if ($stmt->execute()) {
                $success = 'Password changed successfully';
            } else {
                $error = 'Failed to change password';
            }
        }
    }
}
?>

<div class="container-fluid">
    <div class="row">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="col-md-9 ms-sm-auto col-lg-10 px-md-4">
            <div class="d-flex justify-content-between flex-wrap flex-md-nowrap align-items-center pt-3 pb-2 mb-3 border-bottom">
                <h1 class="h2">Settings</h1>
            </div>
            
            <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            
            <?php if ($error): ?>
            <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>
            
            <div class="row">
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Update Profile</div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="update_profile" value="1">
                                <div class="mb-3">
                                    <label class="form-label">First Name</label>
                                    <input type="text" name="first_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($profile['first_name'] ?? ''); ?>" required>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Last Name</label>
                                    <input type="text" name="last_name" class="form-control" 
                                           value="<?php echo htmlspecialchars($profile['last_name'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Phone</label>
                                    <input type="text" name="phone" class="form-control" 
                                           value="<?php echo htmlspecialchars($profile['phone'] ?? ''); ?>">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Address</label>
                                    <textarea name="address" class="form-control"><?php echo htmlspecialchars($profile['address'] ?? ''); ?></textarea>
                                </div>
                                <div class="row">
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">City</label>
                                        <input type="text" name="city" class="form-control" 
                                               value="<?php echo htmlspecialchars($profile['city'] ?? ''); ?>">
                                    </div>
                                    <div class="col-md-6 mb-3">
                                        <label class="form-label">State</label>
                                        <input type="text" name="state" class="form-control" 
                                               value="<?php echo htmlspecialchars($profile['state'] ?? ''); ?>">
                                    </div>
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Pincode</label>
                                    <input type="text" name="pincode" class="form-control" 
                                           value="<?php echo htmlspecialchars($profile['pincode'] ?? ''); ?>">
                                </div>
                                <button type="submit" class="btn btn-primary">Save Changes</button>
                            </form>
                        </div>
                    </div>
                </div>
                
                <div class="col-md-6">
                    <div class="card mb-4">
                        <div class="card-header">Change Password</div>
                        <div class="card-body">
                            <form method="POST">
                                <input type="hidden" name="change_password" value="1">
                                <div class="mb-3">
                                    <label class="form-label">New Password</label>
                                    <input type="password" name="new_password" class="form-control" required minlength="6">
                                </div>
                                <div class="mb-3">
                                    <label class="form-label">Confirm Password</label>
                                    <input type="password" name="confirm_password" class="form-control" required>
                                </div>
                                <button type="submit" class="btn btn-warning">Change Password</button>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<?php include '../includes/footer.php'; ?>

