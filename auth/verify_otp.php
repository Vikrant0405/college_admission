<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Verify OTP';
$error = '';
$success = '';

// Get verification type
$verification_type = isset($_GET['type']) ? $_GET['type'] : 'email_verification';

// Resend OTP handling
if (isset($_GET['resend']) && $_GET['resend'] == 1 && isset($_SESSION['user_id'])) {
    $user_id = $_SESSION['user_id'];
    
    // Generate new OTP
    $new_otp = str_pad(random_int(0, 999999), 6, '0', STR_PAD_LEFT);
    
    // Update OTP in database
    $update_stmt = $conn->prepare("UPDATE otp_verifications SET otp_code = ?, expires_at = DATE_ADD(NOW(), INTERVAL 24 HOUR) WHERE user_id = ? AND purpose = ? AND is_used = 0");
    $update_stmt->bind_param("sis", $new_otp, $user_id, $verification_type);
    $update_stmt->execute();
    
    $success = 'New OTP has been sent to your email/phone. OTP: ' . $new_otp . ' (Demo)';
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $otp = trim($_POST['otp']);
    
    if (empty($otp)) {
        $error = 'Please enter the OTP.';
    } elseif (!isset($_SESSION['user_id'])) {
        $error = 'Session expired. Please register again.';
    } else {
        $user_id = $_SESSION['user_id'];
        
        // Verify OTP
        $stmt = $conn->prepare("SELECT id, expires_at FROM otp_verifications WHERE user_id = ? AND otp_code = ? AND purpose = ? AND is_used = 0");
        $stmt->bind_param("iss", $user_id, $otp, $verification_type);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $otp_record = $result->fetch_assoc();
            
            // Check if OTP is expired
            if (strtotime($otp_record['expires_at']) < time()) {
                $error = 'OTP has expired. Please request a new one.';
            } else {
                // Mark OTP as used
                $update_stmt = $conn->prepare("UPDATE otp_verifications SET is_used = 1 WHERE id = ?");
                $update_stmt->bind_param("i", $otp_record['id']);
                $update_stmt->execute();
                
                // Update user verification status
                $user_stmt = $conn->prepare("UPDATE users SET is_verified = 1 WHERE id = ?");
                $user_stmt->bind_param("i", $user_id);
                $user_stmt->execute();
                
                $success = 'Verification successful! Redirecting...';
                
                // Redirect based on verification type
                if ($verification_type === 'email_verification') {
                    header('Location: ' . BASE_URL . 'student/index.php?verified=1');
                } else {
                    header('Location: ' . BASE_URL . 'auth/reset_password.php');
                }
                exit;
            }
        } else {
            $error = 'Invalid OTP. Please try again.';
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Verify OTP - College Admission System</title>
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
        .verify-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        .verify-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .verify-body {
            padding: 40px;
        }
        .otp-input {
            text-align: center;
            font-size: 1.5rem;
            letter-spacing: 0.5rem;
        }
    </style>
</head>
<body>
    <div class="verify-card">
        <div class="verify-header">
            <i class="fas fa-shield-alt fa-3x mb-3"></i>
            <h2>Verify Your Account</h2>
            <p>Enter the OTP sent to your email/phone</p>
        </div>
        
        <div class="verify-body">
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
                <div class="mb-4">
                    <label for="otp" class="form-label">Enter OTP</label>
                    <input type="text" class="form-control otp-input" id="otp" name="otp" 
                           placeholder="------" maxlength="6" required autocomplete="off">
                </div>
                
                <button type="submit" class="btn btn-primary w-100 mb-3">
                    <i class="fas fa-check me-2"></i> Verify
                </button>
            </form>
            
            <div class="text-center">
                <p class="text-muted mb-2">Didn't receive the OTP?</p>
                <a href="?type=<?php echo $verification_type; ?>&resend=1" class="btn btn-outline-primary">
                    <i class="fas fa-redo me-2"></i> Resend OTP
                </a>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Auto-focus and numeric only input
        document.getElementById('otp').addEventListener('input', function() {
            this.value = this.value.replace(/[^0-9]/g, '');
        });
        
        // Auto-submit when 6 digits entered
        document.getElementById('otp').addEventListener('input', function() {
            if (this.value.length === 6) {
                this.form.submit();
            }
        });
    </script>
</body>
</html>

