<?php
require_once '../config/db.php';
require_once '../config/constants.php';

$page_title = 'Courses - College Admission System';
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo $page_title; ?></title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css" rel="stylesheet">
    <link href="<?php echo BASE_URL; ?>css/style.css" rel="stylesheet">
    <style>
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
    </style>
</head>
<body>
    <!-- Navigation -->
    <nav class="navbar navbar-expand-lg navbar-dark" style="background: rgba(44, 62, 80, 0.95);">
        <div class="container">
            <a class="navbar-brand" href="<?php echo BASE_URL; ?>index.php">
                <i class="fas fa-graduation-cap me-2"></i>
                College Admission
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav ms-auto">
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>index.php">Home</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>index.php#about">About</a></li>
                    <li class="nav-item"><a class="nav-link active" href="<?php echo BASE_URL; ?>pages/courses.php">Courses</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>index.php#features">Features</a></li>
                    <li class="nav-item"><a class="nav-link" href="<?php echo BASE_URL; ?>index.php#contact">Contact</a></li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-primary" href="<?php echo BASE_URL; ?>auth/login.php">
                            <i class="fas fa-sign-in-alt me-1"></i> Login
                        </a>
                    </li>
                    <li class="nav-item ms-2">
                        <a class="btn btn-outline-light" href="<?php echo BASE_URL; ?>auth/register.php">
                            <i class="fas fa-user-plus me-1"></i> Register
                        </a>
                    </li>
                </ul>
            </div>
        </div>
    </nav>

    <!-- Courses Section -->
    <section class="py-5 bg-light">
        <div class="container">
            <div class="text-center mb-5">
                <h2 class="fw-bold">All Courses</h2>
                <p class="text-muted">Explore our diverse range of programs</p>
            </div>
            
            <?php
            // Get courses from database
            $courses_stmt = $conn->query("SELECT c.*, col.name as college_name, col.city, col.state 
                FROM courses c 
                JOIN colleges col ON c.college_id = col.id 
                WHERE c.is_active = 1 
                ORDER BY c.name ASC");
            
            if ($courses_stmt && $courses_stmt->num_rows > 0):
            ?>
            <div class="row g-4">
                <?php while ($course = $courses_stmt->fetch_assoc()): ?>
                <div class="col-md-4">
                    <div class="course-card">
                        <div class="course-header">
                            <h5 class="mb-1"><?php echo htmlspecialchars($course['name']); ?></h5>
                            <small><?php echo htmlspecialchars($course['college_name']); ?></small>
                        </div>
                        <div class="course-body p-3">
                            <p class="text-muted mb-2">
                                <i class="fas fa-layer-group me-2"></i><?php echo htmlspecialchars($course['department']); ?>
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-clock me-2"></i><?php echo $course['duration_years']; ?> Years
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-chair me-2"></i><?php echo $course['seats_available']; ?> Seats
                            </p>
                            <p class="text-muted mb-2">
                                <i class="fas fa-rupee-sign me-2"></i>₹<?php echo number_format($course['fees']); ?>/-
                            </p>
                            <p class="text-muted mb-3">
                                <i class="fas fa-map-marker-alt me-2"></i><?php echo htmlspecialchars($course['city'] . ', ' . $course['state']); ?>
                            </p>
                            <div class="d-grid">
                                <a href="<?php echo BASE_URL; ?>auth/register.php" class="btn btn-outline-primary">Apply Now</a>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>
            <?php else: ?>
            <div class="text-center py-5">
                <i class="fas fa-book-open fa-4x text-muted mb-3"></i>
                <h4>No courses available at the moment</h4>
                <p class="text-muted">Please check back later for available courses.</p>
            </div>
            <?php endif; ?>
            
            <div class="text-center mt-5">
                <a href="<?php echo BASE_URL; ?>index.php" class="btn btn-primary btn-lg">
                    <i class="fas fa-home me-2"></i> Back to Home
                </a>
            </div>
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
                    <p class="text-white-50">Your trusted partner in higher education admissions.</p>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="text-white mb-3">Quick Links</h6>
                    <ul class="list-unstyled">
                        <li><a href="<?php echo BASE_URL; ?>index.php" class="text-white-50 text-decoration-none">Home</a></li>
                        <li><a href="<?php echo BASE_URL; ?>pages/courses.php" class="text-white-50 text-decoration-none">Courses</a></li>
                        <li><a href="<?php echo BASE_URL; ?>auth/login.php" class="text-white-50 text-decoration-none">Login</a></li>
                    </ul>
                </div>
                <div class="col-md-4 mb-4">
                    <h6 class="text-white mb-3">Contact Us</h6>
                    <ul class="list-unstyled text-white-50">
                        <li><i class="fas fa-envelope me-2"></i>admissions@college.edu</li>
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

