<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Register';
$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    header('Location: ' . BASE_URL . 'student/index.php');
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $phone = trim($_POST['phone']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $agree_terms = isset($_POST['agree_terms']) ? 1 : 0;
    
    // Validation
    if (empty($first_name) || empty($email) || empty($password)) {
        $error = 'Please fill in all required fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!$agree_terms) {
        $error = 'Please agree to the terms and conditions.';
    } else {
        // Check if email already exists
        $check_stmt = $conn->prepare("SELECT id FROM users WHERE email = ?");
        $check_stmt->bind_param("s", $email);
        $check_stmt->execute();
        $check_result = $check_stmt->get_result();
        
        if ($check_result->num_rows > 0) {
            $error = 'Email already registered. Please use a different email or login.';
        } else {
            // Store password as plain text (no hashing)
            $plain_password = $password;
            
            // Insert user with is_verified = 1 (auto-verify, no OTP)
            $insert_stmt = $conn->prepare("INSERT INTO users (email, password, role, is_verified) VALUES (?, ?, 'student', 1)");
            $insert_stmt->bind_param("ss", $email, $plain_password);
            
            if ($insert_stmt->execute()) {
                $user_id = $conn->insert_id;
                
                // Create user profile
                $profile_stmt = $conn->prepare("INSERT INTO user_profiles (user_id, first_name, last_name, phone) VALUES (?, ?, ?, ?)");
                $profile_stmt->bind_param("isss", $user_id, $first_name, $last_name, $phone);
                $profile_stmt->execute();
                
                // Create academic details record
                $academic_stmt = $conn->prepare("INSERT INTO academic_details (user_id) VALUES (?)");
                $academic_stmt->bind_param("i", $user_id);
                $academic_stmt->execute();
                
                // Auto-login after registration
                $_SESSION['user_id'] = $user_id;
                $_SESSION['email'] = $email;
                $_SESSION['role'] = 'student';
                $_SESSION['user_name'] = $first_name . ' ' . $last_name;
                
                // Redirect to student dashboard
                header('Location: ' . BASE_URL . 'student/index.php?registered=1');
                exit;
            } else {
                $error = 'Registration failed. Please try again.';
            }
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register - College Admission System</title>
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
            padding: 30px 0;
        }
        .register-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 550px;
            width: 100%;
            margin: 20px;
        }
        .register-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 30px;
            text-align: center;
            color: white;
        }
        .register-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .register-body {
            padding: 35px;
        }
        .form-control {
            padding: 12px 15px;
            border: 2px solid #e0e0e0;
            border-radius: 8px;
        }
        .form-control:focus {
            border-color: #3498db;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.1);
        }
        .btn-register {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }
        .password-strength {
            height: 5px;
            border-radius: 3px;
            margin-top: 5px;
            transition: all 0.3s ease;
        }
    </style>
</head>
<body>
    <div class="register-card">
        <div class="register-header">
            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
            <h2>Create Account</h2>
            <p>Join our admission portal</p>
        </div>
        
        <div class="register-body">
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
                <div class="row">
                    <div class="col-md-6 mb-3">
                        <label for="first_name" class="form-label">First Name <span class="text-danger">*</span></label>
                        <input type="text" class="form-control" id="first_name" name="first_name" 
                               placeholder="Enter first name" required
                               value="<?php echo isset($_POST['first_name']) ? htmlspecialchars($_POST['first_name']) : ''; ?>">
                    </div>
                    
                    <div class="col-md-6 mb-3">
                        <label for="last_name" class="form-label">Last Name</label>
                        <input type="text" class="form-control" id="last_name" name="last_name" 
                               placeholder="Enter last name"
                               value="<?php echo isset($_POST['last_name']) ? htmlspecialchars($_POST['last_name']) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="email" class="form-label">Email Address <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-envelope"></i></span>
                        <input type="email" class="form-control" id="email" name="email" 
                               placeholder="Enter your email" required
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="phone" class="form-label">Phone Number</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-phone"></i></span>
                        <input type="tel" class="form-control" id="phone" name="phone" 
                               placeholder="Enter phone number"
                               value="<?php echo isset($_POST['phone']) ? htmlspecialchars($_POST['phone']) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Create password" required minlength="6">
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                    <div class="password-strength bg-light" id="passwordStrength"></div>
                    <small class="text-muted">Minimum 6 characters</small>
                </div>
                
                <div class="mb-3">
                    <label for="confirm_password" class="form-label">Confirm Password <span class="text-danger">*</span></label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="confirm_password" name="confirm_password" 
                               placeholder="Confirm password" required>
                    </div>
                    <div id="passwordMatch"></div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="agree_terms" name="agree_terms">
                    <label class="form-check-label" for="agree_terms">
                        I agree to the <a href="#" data-bs-toggle="modal" data-bs-target="#termsModal">Terms and Conditions</a>
                    </label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-register w-100">
                    <i class="fas fa-user-plus me-2"></i> Register
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="mb-0">
                    Already have an account? 
                    <a href="<?php echo BASE_URL; ?>auth/login.php" class="text-decoration-none fw-bold">
                        Login Now
                    </a>
                </p>
            </div>
        </div>
    </div>

    <!-- Terms Modal -->
    <div class="modal fade" id="termsModal" tabindex="-1">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Terms and Conditions</h5>
                    <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <h6>1. Acceptance of Terms</h6>
                    <p>By accessing and using this College Admission System, you accept and agree to be bound by the terms and provision of this agreement.</p>
                    
                    <h6>2. Privacy Policy</h6>
                    <p>We are committed to protecting your privacy. Your personal information will be used only for admission purposes.</p>
                    
                    <h6>3. Application Accuracy</h6>
                    <p>You agree to provide accurate and complete information in your application form.</p>
                    
                    <h6>4. Document Verification</h6>
                    <p>All uploaded documents must be authentic. Any false documents will result in immediate rejection.</p>
                    
                    <h6>5. Payment Terms</h6>
                    <p>Application fees are non-refundable unless specified otherwise.</p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Toggle password visibility
        document.getElementById('togglePassword').addEventListener('click', function() {
            const passwordInput = document.getElementById('password');
            const icon = this.querySelector('i');
            
            if (passwordInput.type === 'password') {
                passwordInput.type = 'text';
                icon.classList.remove('fa-eye');
                icon.classList.add('fa-eye-slash');
            } else {
                passwordInput.type = 'password';
                icon.classList.remove('fa-eye-slash');
                icon.classList.add('fa-eye');
            }
        });
        
        // Password strength indicator
        document.getElementById('password').addEventListener('input', function() {
            const strength = document.getElementById('passwordStrength');
            const length = this.value.length;
            
            if (length === 0) {
                strength.className = 'password-strength bg-light';
                strength.style.width = '0%';
            } else if (length < 6) {
                strength.className = 'password-strength bg-danger';
                strength.style.width = '30%';
            } else if (length < 8) {
                strength.className = 'password-strength bg-warning';
                strength.style.width = '60%';
            } else {
                strength.className = 'password-strength bg-success';
                strength.style.width = '100%';
            }
        });
        
        // Confirm password match
        document.getElementById('confirm_password').addEventListener('input', function() {
            const password = document.getElementById('password').value;
            const matchDiv = document.getElementById('passwordMatch');
            
            if (this.value === '') {
                matchDiv.innerHTML = '';
            } else if (this.value === password) {
                matchDiv.innerHTML = '<small class="text-success"><i class="fas fa-check"></i> Passwords match</small>';
            } else {
                matchDiv.innerHTML = '<small class="text-danger"><i class="fas fa-times"></i> Passwords do not match</small>';
            }
        });
    </script>
</body>
</html>

