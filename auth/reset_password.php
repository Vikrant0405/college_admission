<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Reset Password';
$error = '';
$success = '';

// Verify token
$token = isset($_GET['token']) ? $_GET['token'] : '';

if (empty($token)) {
    $error = 'Invalid or missing token.';
} else {
    // Check if token is valid and not expired
    $stmt = $conn->prepare("SELECT email, expires_at FROM password_resets WHERE token = ?");
    $stmt->bind_param("s", $token);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows !== 1) {
        $error = 'Invalid token. Please request a new password reset.';
    } else {
        $reset_record = $result->fetch_assoc();
        
        if (strtotime($reset_record['expires_at']) < time()) {
            $error = 'Token has expired. Please request a new password reset.';
        }
    }
}

if ($_SERVER['REQUEST_METHOD'] === 'POST' && empty($error)) {
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    
    if (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } else {
        // Get email from token
        $email = $reset_record['email'];
        
        // Store password as plain text (no hashing)
        $plain_password = $password;
        
        // Update user password
        $update_stmt = $conn->prepare("UPDATE users SET password = ? WHERE email = ?");
        $update_stmt->bind_param("ss", $plain_password, $email);
        $update_stmt->execute();
        
        // Delete used token
        $delete_stmt = $conn->prepare("DELETE FROM password_resets WHERE token = ?");
        $delete_stmt->bind_param("s", $token);
        $delete_stmt->execute();
        
        $success = 'Password reset successful! Redirecting to login...';
        
        header('Refresh: 2; URL=' . BASE_URL . 'auth/login.php');
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reset Password - College Admission System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">
    <style>
        body {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        .reset-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        .reset-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .reset-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="reset-card">
        <div class="reset-header">
            <i class="fas fa-lock fa-3x mb-3"></i>
            <h2>Create New Password</h2>
            <p>Enter your new password below</p>
        </div>
        
        <div class="reset-body">
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <div class="text-center mt-3">
                <a href="<?php echo BASE_URL; ?>auth/forgot_password.php" class="btn btn-primary">
                    Request New Reset Link
                </a>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if (empty($error)): ?>
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="password" class="form-label">New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter new password" required minlength="6">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm New Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm new password" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-check me-2"></i> Reset Password
                </button>
            </form>
            <?php endif; ?>
            
            <div class="text-center mt-4">
                <a href="<?php echo BASE_URL; ?>auth/login.php" class="text-decoration-none">
                    <i class="fas fa-arrow-left me-1"></i> Back to Login
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

