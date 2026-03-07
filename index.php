<?php
require_once 'config/db.php';
require_once 'config/constants.php';

$page_title = 'Home';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>College Admission System - Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="css/style.css" rel="stylesheet">
    <style>
        .hero-section {
            background: linear-gradient(135deg, rgba(44, 62, 80, 0.9), rgba(26, 37, 47, 0.9)),
                        url('https://images.unsplash.com/photo-1562774053-701939374585?ixlib=rb-1.2.1&auto=format&fit=crop&w=1920&q=80');
            background-size: cover;
            background-position: center;
            padding: 100px 0;
            color: white;
        }
        .feature-box {
            background: white;
            border-radius: 15px;
            padding: 30px;
            text-align: center;
            box-shadow: 0 5px 25px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        .feature-box:hover {
            transform: translateY(-10px);
            box-shadow: 0 10px 35px rgba(0, 0, 0, 0.15);
        }
        .feature-icon {
            width: 70px;
            height: 70px;
            border-radius: 50%;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 1.8rem;
            margin: 0 auto 20px;
        }
        .course-card {
            background: white;
            border-radius: 12px;
            overflow: hidden;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
            transition: all 0.3s ease;
            height: 100%;
        }
        .course-card:hover {
            transform: translateY(-5px);
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }
        .course-header {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 20px;
            color: white;
        }
        .stats-section {
            background: linear-gradient(135deg, #2c3e50, #1a252f);
            padding: 60px 0;
            color: white;
        }
        .stat-item {
            text-align: center;
        }
        .stat-number {
            font-size: 3rem;
            font-weight: 700;
            color: #3498db;
        }
        .stat-label {
            font-size: 1.1rem;
            opacity: 0.9;
        }
        .testimonial-card {
            background: white;
            border-radius: 15px;
            padding: 30px;
            box-shadow: 0 5px 20px rgba(0, 0, 0, 0.1);
        }
        .testimonial-img {
            width: 80px;
            height: 80px;
            border-radius: 50%;
            object-fit: cover;
            margin-bottom: 15px;
        }
        .cta-section {
            background: linear-gradient(135deg, #3498db, #2980b9);
            padding: 60px 0;
            color: white;
        }
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(44, 62, 80, 0.95);">
        <div class="container">
            <a class="navbar-brand" href="index.php">
                <i class="fas fa-graduation-cap me-2"></i>
                College Admission
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link active" href="index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="#about">About</a></li>
                    <li class="nav-item"><a class="nav-link" href="#courses">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="#contact">Contact</a></li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="auth/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light" href="auth/register.php">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Hero Section -->
    <section class="hero-section">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-7">
                    <h1 class="display-3 fw-bold mb-4">Start Your Journey to Higher Education</h1>
                    <p class="lead mb-4">Apply to top colleges and universities through our streamlined online admission portal. Simple, fast, and secure.</p>
                    <div class="d-flex gap-3">
                        <a href="auth/register.php" class="btn btn-primary btn-lg">
                            <i class="fas fa-user-plus me-2"></i> Apply Now
                        </a>
                        <a href="#courses" class="btn btn-outline-light btn-lg">
                            <i class="fas fa-book me-2"></i> View Courses
                        </a>
                    </div>
                </div>
                <div class="col-lg-5 text-center mt-5 mt-lg-0">
                    <div class="card p-4" style="background: rgba(255,255,255,0.1); backdrop-filter: blur(10px);">
                        <h3 class="mb-3">Quick Stats</h3>
                        <div class="row text-center">
                            <div class="col-6 mb-3">
                                <i class="fas fa-university fa-2x mb-2"></i>
                                <h4>50+</h4>
                                <small>Colleges</small>
                            </div>
                            <div class="col-6 mb-3">
                                <i class="fas fa-book-open fa-2x mb-2"></i>
                                <h4>200+</h4>
                                <small>Courses</small>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-users fa-2x mb-2"></i>
                                <h4>10K+</h4>
                                <small>Students</small>
                            </div>
                            <div class="col-6">
                                <i class="fas fa-check-circle fa-2x mb-2"></i>
                                <h4>95%</h4>
                                <small>Placements</small>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Features Section -->
    <section id="features" class="py-5">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Why Choose Our Platform?</h2>
                <p class="text-muted">Everything you need for a smooth admission process</p>
            </div>
            <div class="row g-4">
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-file-alt"></i>
                        </div>
                        <h4>Easy Application</h4>
                        <p class="text-muted">Fill out your application form online with our user-friendly interface. Save drafts and submit when ready.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-cloud-upload-alt"></i>
                        </div>
                        <h4>Document Upload</h4>
                        <p class="text-muted">Upload your marksheets, certificates, and other documents securely. Track verification status in real-time.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-search-location"></i>
                        </div>
                        <h4>Track Status</h4>
                        <p class="text-muted">Stay updated with your application status. Get notifications at every step of the admission process.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-credit-card"></i>
                        </div>
                        <h4>Secure Payments</h4>
                        <p class="text-muted">Pay application fees securely through our integrated payment gateway with instant confirmation.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-bell"></i>
                        </div>
                        <h4>Instant Notifications</h4>
                        <p class="text-muted">Receive email and SMS alerts for important updates, deadlines, and admission decisions.</p>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="feature-box">
                        <div class="feature-icon">
                            <i class="fas fa-headset"></i>
                        </div>
                        <h4>24/7 Support</h4>
                        <p class="text-muted">Get help whenever you need it with our dedicated support team available round the clock.</p>
                    </div>
                </div>
            </div>
        </div>
    </section>

    <!-- Courses Section -->
    <section id="courses" class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">Popular Courses</h2>
                <p class="text-muted">Explore our diverse range of programs</p>
            </div>
            <div class="row g-4">
                <?php
                // Get courses from database
                $courses_stmt = $conn->query("SELECT c.*, col.name as college_name FROM courses c 
                    JOIN colleges col ON c.college_id = col.id 
                    WHERE c.is_active = 1 
                    ORDER BY c.seats_available DESC 
                    LIMIT 6");
                
                if ($courses_stmt && $courses_stmt->num_rows > 0):
                    while ($course = $courses_stmt->fetch_assoc()):
                ?>
                <div class="col-md-4">
                    <div class="course-card">
                        <div class="course-header">
                            <h5 class="mb-1"><?php echo htmlspecialchars($course['name']); ?></h5>
                            <small><?php echo htmlspecialchars($course['college_name']); ?></small>
                        </div>
                        <div class="course-body">
                            <p class="text-muted mb-2">
                                <i class="fas fa-layer-group me-2"></i><?php echo htmlspecialchars($course['department']); ?>
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock me-2"></i><?php echo $course['duration_years']; ?> Years
                            </p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-chair me-2"></i><?php echo $course['seats_available']; ?> Seats
                            </p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹<?php echo number_format($course['fees']); ?>/-</span>
                                <a href="auth/register.php" class="btn btn-sm btn-outline-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php 
                    endwhile;
                else:
                ?>
                <div class="col-md-4">
                    <div class="course-card">
                        <div class="course-header">
                            <h5 class="mb-1">Bachelor of Technology</h5>
                            <small>Engineering College</small>
                        </div>
                        <div class="course-body">
                            <p class="text-muted mb-2"><i class="fas fa-layer-group me-2"></i>Engineering</p>
                            <p class="text-muted mb-2"><i class="fas fa-clock me-2"></i>4 Years</p>
                            <p class="text-muted mb-3"><i class="fas fa-chair me-2"></i>120 Seats</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹1,50,000/-</span>
                                <a href="auth/register.php" class="btn btn-sm btn-outline-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="course-card">
                        <div class="course-header">
                            <h5 class="mb-1">Master of Business Administration</h5>
                            <small>Business School</small>
                        </div>
                        <div class="course-body">
                            <p class="text-muted mb-2"><i class="fas fa-layer-group me-2"></i>Management</p>
                            <p class="text-muted mb-2"><i class="fas fa-clock me-2"></i>2 Years</p>
                            <p class="text-muted mb-3"><i class="fas fa-chair me-2"></i>60 Seats</span></p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹2,00,000/-</span>
                                <a href="auth/register.php" class="btn btn-sm btn-outline-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-4">
                    <div class="course-card">
                        <div class="course-header">
                            <h5 class="mb-1">Bachelor of Medicine & Surgery</h5>
                            <small>Medical College</small>
                        </div>
                        <div class="course-body">
                            <p class="text-muted mb-2"><i class="fas fa-layer-group me-2"></i>Medical</p>
                            <p class="text-muted mb-2"><i class="fas fa-clock me-2"></i>5.5 Years</p>
                            <p class="text-muted mb-3"><i class="fas fa-chair me-2"></i>100 Seats</p>
                            <div class="d-flex justify-content-between align-items-center">
                                <span class="fw-bold text-primary">₹5,00,000/-</span>
                                <a href="auth/register.php" class="btn btn-sm btn-outline-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endif; ?>
            </div>
            <div class="text-center mt-5">
                <a href="pages/courses.php" class="btn btn-primary btn-lg">View All Courses</a>
            </div>
        </div>
    </section>

    <!-- About Section -->
    <section id="about" class="py-5">
        <div class="container">
            <div class="row align-items-center">
                <div class="col-lg-6">
                    <h2 class="fw-bold mb-4">About Our Admission System</h2>
                    <p class="text-muted mb-4">We are dedicated to simplifying the college admission process for students across the country. Our platform provides a seamless experience for applying to colleges, uploading documents, and tracking application status.</p>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-primary bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-check text-primary"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Streamlined Process</h5>
                            <small class="text-muted">Apply to multiple colleges with a single application</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center mb-3">
                        <div class="bg-success bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-shield-alt text-success"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Secure & Private</h5>
                            <small class="text-muted">Your data is protected with enterprise-grade security</small>
                        </div>
                    </div>
                    
                    <div class="d-flex align-items-center">
                        <div class="bg-warning bg-opacity-10 p-3 rounded-circle me-3">
                            <i class="fas fa-clock text-warning"></i>
                        </div>
                        <div>
                            <h5 class="mb-0">Save Time</h5>
                            <small class="text-muted">No more standing in queues - apply online from anywhere</small>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6 mt-5 mt-lg-0">
                    <img src="https://images.unsplash.com/photo-1523050854058-8df90110c9f1?ixlib=rb-1.2.1&auto=format&fit=crop&w=800&q=80" 
                         alt="Students" class="img-fluid rounded-3 shadow">
                </div>
            </div>
        </div>
    </section>

    <!-- CTA Section -->
    <section class="cta-section">
        <div class="container text-center">
            <h2 class="fw-bold mb-3">Ready to Start Your Application?</h2>
            <p class="mb-4">Join thousands of students who have successfully applied through our platform</p>
            <a href="auth/register.php" class="btn btn-light btn-lg me-2">
                <i class="fas fa-user-plus me-2"></i> Create Account
            </a>
            <a href="#contact" class="btn btn-outline-light btn-lg">
                <i class="fas fa-envelope me-2"></i> Contact Us
            </a>
        </div>
    </section>

    <!-- Footer -->
    <footer class="footer pt-5 pb-3">
        <div class="container">
            <div class="row">
                <div class="col-md-4 mb-4">
                    <h5 class="text-white mb-3">
                        <i class="fas fa-graduation-cap me-2"></i>College Admission
                    </h5>
                    <p class="text-white-50">Your trusted partner in higher education admissions. We make applying to college simple and stress-free.</p>
                    <div class="d-flex gap-3">
                        <a href="#" class="text-white-50"><i class="fab fa-facebook fa-lg"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-twitter fa-lg"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-instagram fa-lg"></i></a>
                        <a href="#" class="text-white-50"><i class="fab fa-linkedin fa-lg"></i></a>
                    </div>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="#" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="#about" class="text-white-50 text-decoration-none">About</a></li>
                        <li><a href="#courses" class="text-white-50 text-decoration-none">Courses</a></li>
                        <li><a href="#contact" class="text-white-50 text-decoration-none">Contact</a></li>
                    </ul>
                </div>
                <div class="col-md-2 mb-4">
                    <h6 class="text-white mb-3">Support</h6>
                    <ul class="list-unstyled">
                        <li><a href="pages/faq.php" class="text-white-50 text-decoration-none">FAQ</a></li>
                        <li><a href="pages/help.php" class="text-white-50 text-decoration-none">Help</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Privacy</a></li>
                        <li><a href="#" class="text-white-50 text-decoration-none">Terms</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4" id="contact">
                    <h6 class="text-white mb-3">Contact Us</h6>
                    <ul class="list-unstyled text-white-50">
                        <li class="mb-2"><i class="fas fa-map-marker-alt me-2"></i>123 Education Lane, City, State</li>
                        <li class="mb-2"><i class="fas fa-phone me-2"></i>+91 1234567890</li>
                        <li class="mb-2"><i class="fas fa-envelope me-2"></i>admissions@college.edu</li>
                    </ul>
                </div>
            </div>
            <hr style="border-color: rgba(255,255,255,0.2);">
            <div class="text-center text-white-50">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> College Admission System. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

