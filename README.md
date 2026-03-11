# College Admission System

Start Your Journey to Higher Education - A streamlined online college admission portal for applying to top colleges and universities.

## Overview

Our College Admission System is a comprehensive web-based platform that simplifies the college admission process. Built with PHP and MySQL, it provides a seamless experience for students to apply to colleges, upload documents, track application status, and make secure payments.

## Key Statistics

- **50+** Partner Colleges
- **200+** Course Options
- **10,000+** Students Enrolled
- **95%** Placement Rate

## Features

### For Students
- **Easy Application** - User-friendly interface with save draft functionality
- **Document Upload** - Secure marksheet and certificate upload with real-time verification status
- **Application Tracking** - Stay updated with your application status at every step
- **Secure Payments** - Integrated payment gateway with instant confirmation
- **Instant Notifications** - Email and SMS alerts for important updates and deadlines
- **24/7 Support** - Dedicated support team available round the clock

### For Administrators
- **Dashboard** - Comprehensive statistics and overview
- **Application Management** - Review and process student applications
- **Document Verification** - Verify student-submitted documents
- **User Management** - Manage student and staff accounts
- **College & Course Management** - Add and manage colleges and courses
- **Reports Generation** - Generate admission reports and analytics
- **Notifications** - Send updates to applicants

## Requirements

- PHP 7.4 or higher
- MySQL 5.7 or higher
- Apache/Nginx web server
- XAMPP (recommended for local development)

## Installation

1. Clone the repository to your web server's document root
2. Import the database from `config/database.sql`
3. Configure database connection in `config/db.php`
4. Update constants in `config/constants.php` if needed
5. Start Apache and MySQL in XAMPP
6. Access the application in your browser

## Default Login Credentials

### Admin
- Email: admin@college.edu
- Password: admin123

### Student
- Register a new account to login

## Project Structure

```
college_admission/
├── admin/          # Admin panel files
├── ajax/           # AJAX handlers
├── auth/           # Authentication files
├── config/         # Configuration files
├── css/            # Stylesheets
├── includes/       # Common includes (header, footer, sidebar)
├── js/             # JavaScript files
├── pages/          # Public pages
├── student/        # Student portal files
└── uploads/        # Uploaded files directory
```

## License

This project is for educational purposes.

