<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Login';
$error = '';
$success = '';

// Redirect if already logged in
if (isset($_SESSION['user_id'])) {
    if ($_SESSION['role'] === 'admin' || $_SESSION['role'] === 'staff') {
        header('Location: ' . BASE_URL . 'admin/dashboard.php');
    } else {
        header('Location: ' . BASE_URL . 'student/index.php');
    }
    exit;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    
    if (empty($email) || empty($password)) {
        $error = 'Please enter both email and password.';
    } else {
        // Check user in database
        $stmt = $conn->prepare("SELECT id, email, password, role, is_verified, is_active FROM users WHERE email = ?");
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows === 1) {
            $user = $result->fetch_assoc();
            
            if ($user['is_active'] == 0) {
                $error = 'Your account has been deactivated. Please contact administrator.';
            } elseif ($password === $user['password']) {
                // Login successful
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['email'] = $user['email'];
                $_SESSION['role'] = $user['role'];
                
                // Get user profile name
                $profile_stmt = $conn->prepare("SELECT first_name, last_name FROM user_profiles WHERE user_id = ?");
                $profile_stmt->bind_param("i", $user['id']);
                $profile_stmt->execute();
                $profile_result = $profile_stmt->get_result();
                
                if ($profile_result->num_rows === 1) {
                    $profile = $profile_result->fetch_assoc();
                    $_SESSION['user_name'] = $profile['first_name'] . ' ' . $profile['last_name'];
                } else {
                    $_SESSION['user_name'] = $user['email'];
                }
                
                // Log the login activity
                logActivity($user['id'], 'login', 'User logged in');
                
                // Redirect based on role
                if ($user['role'] === 'admin' || $user['role'] === 'staff') {
                    header('Location: ' . BASE_URL . 'admin/dashboard.php');
                } else {
                    header('Location: ' . BASE_URL . 'student/index.php');
                }
                exit;
            } else {
                $error = 'Invalid email or password.';
            }
        } else {
            $error = 'Invalid email or password.';
        }
    }
}

function logActivity($user_id, $activity, $description) {
    global $conn;
    $stmt = $conn->prepare("INSERT INTO notifications (user_id, title, message, type) VALUES (?, ?, ?, 'info')");
    $stmt->bind_param("iss", $user_id, $activity, $description);
    $stmt->execute();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - College Admission System</title>
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
        .login-card {
            background: white;
            border-radius: 15px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.3);
            overflow: hidden;
            max-width: 450px;
            width: 100%;
            margin: 20px;
        }
        .login-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 40px 30px;
            text-align: center;
            color: white;
        }
        .login-header h2 {
            margin: 0;
            font-weight: 600;
        }
        .login-header p {
            margin: 10px 0 0;
            opacity: 0.9;
        }
        .login-body {
            padding: 40px;
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
        .btn-login {
            background: linear-gradient(135deg, #3498db, #2980b9);
            border: none;
            padding: 12px;
            font-weight: 600;
            border-radius: 8px;
        }
        .btn-login:hover {
            background: linear-gradient(135deg, #2980b9, #3498db);
        }
        .social-login {
            text-align: center;
            margin-top: 20px;
        }
        .social-btn {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            display: inline-flex;
            align-items: center;
            justify-content: center;
            margin: 0 5px;
            color: white;
            text-decoration: none;
        }
    </style>
</head>
<body>
    <div class="login-card">
        <div class="login-header">
            <i class="fas fa-graduation-cap fa-3x mb-3"></i>
            <h2>Welcome Back</h2>
            <p>Login to your admission portal</p>
        </div>
        
        <div class="login-body">
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
                               placeholder="Enter your email" required 
                               value="<?php echo isset($_POST['email']) ? htmlspecialchars($_POST['email']) : ''; ?>">
                    </div>
                </div>
                
                <div class="mb-3">
                    <label for="password" class="form-label">Password</label>
                    <div class="input-group">
                        <span class="input-group-text"><i class="fas fa-lock"></i></span>
                        <input type="password" class="form-control" id="password" name="password" 
                               placeholder="Enter your password" required>
                        <button class="btn btn-outline-secondary" type="button" id="togglePassword">
                            <i class="fas fa-eye"></i>
                        </button>
                    </div>
                </div>
                
                <div class="mb-3 form-check">
                    <input type="checkbox" class="form-check-input" id="remember" name="remember">
                    <label class="form-check-label" for="remember">Remember me</label>
                </div>
                
                <button type="submit" class="btn btn-primary btn-login w-100">
                    <i class="fas fa-sign-in-alt me-2"></i> Login
                </button>
            </form>
            
            <div class="text-center mt-4">
                <p class="mb-2">
                    <a href="<?php echo BASE_URL; ?>auth/forgot_password.php" class="text-decoration-none">
                        Forgot Password?
                    </a>
                </p>
                <p class="mb-0">
                    Don't have an account? 
                    <a href="<?php echo BASE_URL; ?>auth/register.php" class="text-decoration-none fw-bold">
                        Register Now
                    </a>
                </p>
            </div>
            
            <div class="social-login">
                <p class="text-muted mb-2">Or login with</p>
                <a href="#" class="social-btn bg-primary"><i class="fab fa-facebook-f"></i></a>
<a href="#" class="social-btn bg-danger"><i class="fab fa-google"></i></a>
                <a href="#" class="social-btn bg-info"><i class="fab fa-twitter"></i></a>
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
    </script>
</body>
</html>

