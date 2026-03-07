<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Forgot Password';
$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    
    if (empty($email)) {
        $error = 'Please enter your email address.';
    } else {
        // Check if email exists
        $stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            // Generate reset token
            $token = bin2hex(random_bytes(32));
            
            // Store token in database
            $insert_stmt = $conn->prepare("INSERT INTO password_resets (email, token, expires_at) VALUES (?, ?, DATE_ADD(NOW(), INTERVAL 1 HOUR))");
            $insert_stmt->bind_param("ss", $email, $token);
            $insert_stmt->execute();
            
            // In production, send email with reset link
            $reset_link = BASE_URL . 'auth/reset_password.php?token=' . $token;
            
            $success = 'Password reset link has been sent to your email.<br>
                       <strong>Demo Link:</strong> <a href="' . $reset_link . '">Click here to reset password</a>';
        } else {
            $error = 'No account found with this email address.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Forgot Password - College Admission System</title>
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
        .forgot-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        .forgot-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .forgot-body {
            padding: 40px;
        }
    </style>
</head>
<body>
    <div class="forgot-card">
        <div class="forgot-header">
            <i class="fas fa-key fa-3x mb-3"></i>
            <h2>Reset Password</h2>
            <p>Enter your email to receive reset instructions</p>
        </div>
        
        <div class="forgot-body">
            <?php if ($error): ?>
            <div class="alert alert-danger alert-dismissible fade show" role="alert">
                <i class="fas fa-exclamation-circle me-2"></i>
                <?php echo $error; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <?php if ($success): ?>
            <div class="alert alert-success alert-dismissible fade show" role="alert">
                <i class="fas fa-check-circle me-2"></i>
                <?php echo $success; ?>
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
            <?php endif; ?>
            
            <form method="POST" action="">
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required>
                    </div>
                </div>
                
                <button type="submit" class="btn btn-primary w-100">
                    <i class="fas fa-paper-plane me-2"></i> Send Reset Link
                </button>
            </form>
            
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

