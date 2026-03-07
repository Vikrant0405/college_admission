<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Help & Support';

$success = '';
$error = '';

// Handle contact form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);
    
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error = 'Please fill in all fields.';
    } else {
        // Insert into database (you can create a support_tickets table)
        $success = 'Your message has been submitted. Our support team will contact you shortly.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?> - College Admission System</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="../css/style.css" rel="stylesheet">
    <style>
        body {
            background: #f5f7fa;
        }
        .help-header {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            padding: 60px 0;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(44, 62, 80, 0.95);">
        <div class="container">
            <a class="navbar-brand" href="../index.php">
                <i class="fas fa-graduation-cap me-2"></i>College Admission
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="../index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="faq.php">FAQ</a></li>
                    <li class="nav-item"><a class="nav-link active" href="help.php">Help</a></li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Header -->
    <div class="help-header text-center">
        <div class="container">
            <h1 class="display-4 fw-bold">Help & Support</h1>
            <p class="lead">We're here to help you with any questions</p>
        </div>
    </div>

    <div class="container py-5">
        <div class="row">
            <!-- Contact Form -->
            <div class="col-lg-8 mb-4">
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-envelope me-2"></i>Contact Us
                    </div>
                    <div class="card-body">
                        <?php if ($error): ?>
                        <div class="alert alert-danger"><?php echo $error; ?></div>
                        <?php endif; ?>
                        
                        <?php if ($success): ?>
                        <div class="alert alert-success"><?php echo $success; ?></div>
                        <?php endif; ?>
                        
                        <form method="POST">
                            <div class="row">
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Name</label>
                                    <input type="text" class="form-control" name="name" required>
                                </div>
                                <div class="col-md-6 mb-3">
                                    <label class="form-label">Email</label>
                                    <input type="email" class="form-control" name="email" required>
                                </div>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Subject</label>
                                <select class="form-select" name="subject">
                                    <option value="general">General Inquiry</option>
                                    <option value="application">Application Related</option>
                                    <option value="payment">Payment Issue</option>
                                    <option value="document">Document Upload</option>
                                    <option value="technical">Technical Support</option>
                                    <option value="other">Other</option>
                                </select>
                            </div>
                            <div class="mb-3">
                                <label class="form-label">Message</label>
                                <textarea class="form-control" name="message" rows="5" required></textarea>
                            </div>
                            <button type="submit" class="btn btn-primary">
                                <i class="fas fa-paper-plane me-2"></i>Send Message
                            </button>
                        </form>
                    </div>
                </div>
            </div>
            
            <!-- Contact Info -->
            <div class="col-lg-4">
                <div class="card mb-4">
                    <div class="card-header">
                        <i class="fas fa-info-circle me-2"></i>Contact Information
                    </div>
                    <div class="card-body">
                        <div class="mb-3">
                            <i class="fas fa-map-marker-alt text-primary me-2"></i>
                            <span>123 Education Lane, City, State</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-phone text-primary me-2"></i>
                            <span>+91 1234567890</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-envelope text-primary me-2"></i>
                            <span>support@college.edu</span>
                        </div>
                        <div class="mb-3">
                            <i class="fas fa-clock text-primary me-2"></i>
                            <span>Mon - Fri: 9AM - 6PM</span>
                        </div>
                    </div>
                </div>
                
                <div class="card">
                    <div class="card-header">
                        <i class="fas fa-question-circle me-2"></i>Quick Help
                    </div>
                    <div class="card-body">
                        <p>Before contacting us, you might find the answer in our <a href="faq.php">FAQ section</a>.</p>
                        <hr>
                        <h6>Support Channels</h6>
                        <div class="d-flex gap-2">
                            <a href="#" class="btn btn-outline-primary"><i class="fab fa-facebook"></i></a>
                            <a href="#" class="btn btn-outline-info"><i class="fab fa-twitter"></i></a>
                            <a href="#" class="btn btn-outline-success"><i class="fab fa-whatsapp"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Footer -->
    <footer class="footer text-center py-3">
        <div class="container">
            <p class="mb-0">&copy; <?php echo date('Y'); ?> College Admission System. All rights reserved.</p>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

