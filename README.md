# College Admission System

A web-based college admission management system built with PHP and MySQL.

## Features

- **Student Portal**
  - User registration and login
  - Course browsing
  - Application form submission
  - Document upload
  - Application tracking
  - Payment processing
  - Profile management

- **Admin Portal**
  - Dashboard with statistics
  - Application management
  - Document verification
  - User management
  - College and course management
  - Reports generation
  - Notifications

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

