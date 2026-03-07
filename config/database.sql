-- College Admission System Database Schema
-- Run this SQL in your MySQL database

CREATE DATABASE IF NOT EXISTS college_admission;
USE college_admission;

-- Users Table
CREATE TABLE IF NOT EXISTS users (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) UNIQUE NOT NULL,
    password VARCHAR(255) NOT NULL,
    role ENUM('admin', 'staff', 'student') DEFAULT 'student',
    is_verified TINYINT(1) DEFAULT 0,
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- User Profiles Table
CREATE TABLE IF NOT EXISTS user_profiles (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    first_name VARCHAR(100) NOT NULL,
    last_name VARCHAR(100),
    gender ENUM('male', 'female', 'other'),
    date_of_birth DATE,
    phone VARCHAR(20),
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(20),
    nationality VARCHAR(50),
    aadhar_number VARCHAR(20),
    profile_image VARCHAR(255),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Academic Details Table
CREATE TABLE IF NOT EXISTS academic_details (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    ssc_school VARCHAR(255),
    ssc_percentage DECIMAL(5,2),
    ssc_year INT,
    hsc_school VARCHAR(255),
    hsc_percentage DECIMAL(5,2),
    hsc_year INT,
    entrance_exam_score INT,
    entrance_exam_name VARCHAR(100),
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Colleges Table
CREATE TABLE IF NOT EXISTS colleges (
    id INT PRIMARY KEY AUTO_INCREMENT,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20) UNIQUE,
    address TEXT,
    city VARCHAR(100),
    state VARCHAR(100),
    pincode VARCHAR(20),
    phone VARCHAR(20),
    email VARCHAR(255),
    website VARCHAR(255),
    establishment_year INT,
    description TEXT,
    logo VARCHAR(255),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Courses Table
CREATE TABLE IF NOT EXISTS courses (
    id INT PRIMARY KEY AUTO_INCREMENT,
    college_id INT NOT NULL,
    name VARCHAR(255) NOT NULL,
    code VARCHAR(20),
    department VARCHAR(100),
    description TEXT,
    eligibility_criteria TEXT,
    duration_years INT,
    seats_available INT,
    fees DECIMAL(10,2),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (college_id) REFERENCES colleges(id) ON DELETE CASCADE
);

-- Applications Table
CREATE TABLE IF NOT EXISTS applications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    course_id INT NOT NULL,
    application_number VARCHAR(50) UNIQUE,
    status ENUM('draft', 'submitted', 'under_review', 'approved', 'rejected') DEFAULT 'draft',
    personal_statement TEXT,
    is_payment_done TINYINT(1) DEFAULT 0,
    payment_amount DECIMAL(10,2),
    payment_transaction_id VARCHAR(100),
    submitted_at TIMESTAMP NULL,
    reviewed_by INT,
    reviewed_at TIMESTAMP NULL,
    remarks TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (course_id) REFERENCES courses(id),
    FOREIGN KEY (reviewed_by) REFERENCES users(id)
);

-- Documents Table
CREATE TABLE IF NOT EXISTS documents (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    document_type VARCHAR(50) NOT NULL,
    file_name VARCHAR(255) NOT NULL,
    file_path VARCHAR(255) NOT NULL,
    file_size INT,
    mime_type VARCHAR(100),
    status ENUM('pending', 'verified', 'rejected') DEFAULT 'pending',
    verified_by INT,
    verified_at TIMESTAMP NULL,
    rejection_reason TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE,
    FOREIGN KEY (verified_by) REFERENCES users(id)
);

-- Payments Table
CREATE TABLE IF NOT EXISTS payments (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    user_id INT NOT NULL,
    amount DECIMAL(10,2) NOT NULL,
    payment_method VARCHAR(50),
    transaction_id VARCHAR(100) UNIQUE,
    payment_status ENUM('pending', 'completed', 'failed', 'refunded') DEFAULT 'pending',
    payment_gateway_response TEXT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Notifications Table
CREATE TABLE IF NOT EXISTS notifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    title VARCHAR(255) NOT NULL,
    message TEXT,
    type ENUM('info', 'success', 'warning', 'error') DEFAULT 'info',
    is_read TINYINT(1) DEFAULT 0,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- FAQ Table
CREATE TABLE IF NOT EXISTS faqs (
    id INT PRIMARY KEY AUTO_INCREMENT,
    question TEXT NOT NULL,
    answer TEXT NOT NULL,
    category VARCHAR(100),
    is_active TINYINT(1) DEFAULT 1,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
);

-- Application Timeline Table
CREATE TABLE IF NOT EXISTS application_timeline (
    id INT PRIMARY KEY AUTO_INCREMENT,
    application_id INT NOT NULL,
    status VARCHAR(50) NOT NULL,
    description TEXT,
    created_by INT,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (application_id) REFERENCES applications(id) ON DELETE CASCADE,
    FOREIGN KEY (created_by) REFERENCES users(id)
);

-- Password Reset Table
CREATE TABLE IF NOT EXISTS password_resets (
    id INT PRIMARY KEY AUTO_INCREMENT,
    email VARCHAR(255) NOT NULL,
    token VARCHAR(255) NOT NULL,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
);

-- OTP Verification Table
CREATE TABLE IF NOT EXISTS otp_verifications (
    id INT PRIMARY KEY AUTO_INCREMENT,
    user_id INT NOT NULL,
    otp_code VARCHAR(10) NOT NULL,
    purpose ENUM('email_verification', 'password_reset', 'login') DEFAULT 'email_verification',
    is_used TINYINT(1) DEFAULT 0,
    expires_at TIMESTAMP NOT NULL,
    created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
    FOREIGN KEY (user_id) REFERENCES users(id) ON DELETE CASCADE
);

-- Insert Default Admin (Password: admin123)
INSERT INTO users (email, password, role, is_verified, is_active) 
VALUES ('admin@college.edu', 'admin123', 'admin', 1, 1);

INSERT INTO user_profiles (user_id, first_name, last_name, phone)
VALUES (1, 'System', 'Administrator', '1234567890');

-- Insert 5 Demo Student Users (Password: password123)
INSERT INTO users (email, password, role, is_verified, is_active) 
VALUES ('student1@college.edu', 'password123', 'student', 1, 1);

INSERT INTO user_profiles (user_id, first_name, last_name, phone)
VALUES (2, 'John', 'Smith', '9876543210');

INSERT INTO academic_details (user_id) VALUES (2);

INSERT INTO users (email, password, role, is_verified, is_active) 
VALUES ('student2@college.edu', 'password123', 'student', 1, 1);

INSERT INTO user_profiles (user_id, first_name, last_name, phone)
VALUES (3, 'Sarah', 'Johnson', '9876543211');

INSERT INTO academic_details (user_id) VALUES (3);

INSERT INTO users (email, password, role, is_verified, is_active) 
VALUES ('student3@college.edu', 'password123', 'student', 1, 1);

INSERT INTO user_profiles (user_id, first_name, last_name, phone)
VALUES (4, 'Michael', 'Brown', '9876543212');

INSERT INTO academic_details (user_id) VALUES (4);

INSERT INTO users (email, password, role, is_verified, is_active) 
VALUES ('student4@college.edu', 'password123', 'student', 1, 1);

INSERT INTO user_profiles (user_id, first_name, last_name, phone)
VALUES (5, 'Emily', 'Davis', '9876543213');

INSERT INTO academic_details (user_id) VALUES (5);

INSERT INTO users (email, password, role, is_verified, is_active) 
VALUES ('student5@college.edu', 'password123', 'student', 1, 1);

INSERT INTO user_profiles (user_id, first_name, last_name, phone)
VALUES (6, 'David', 'Wilson', '9876543214');

INSERT INTO academic_details (user_id) VALUES (6);

-- Insert Sample Colleges
INSERT INTO colleges (name, code, address, city, state, pincode, phone, email, website, establishment_year, description, is_active) 
VALUES 
('Indian Institute of Technology', 'IIT', 'IIT Campus', 'Mumbai', 'Maharashtra', '400076', '022-25723456', 'admissions@iit.edu', 'https://iit.edu', 1958, 'Premier engineering institute of India', 1),
('National Institute of Technology', 'NIT', 'NIT Campus', 'Trichy', 'Tamil Nadu', '620015', '0431-2501234', 'admissions@nit.edu', 'https://nit.edu', 1964, 'National Institute of Technology', 1),
('Birla Institute of Technology', 'BITS', 'Pilani Campus', 'Pilani', 'Rajasthan', '333031', '01596-242909', 'admissions@bits.edu', 'https://bits.edu', 1964, 'Deemed University', 1),
('Vellore Institute of Technology', 'VIT', 'Vellore Campus', 'Vellore', 'Tamil Nadu', '632014', '0416-2202020', 'admissions@vit.edu', 'https://vit.edu', 1984, 'Deemed to be University', 1),
('Manipal Academy of Higher Education', 'MAHE', 'Manipal Campus', 'Manipal', 'Karnataka', '576104', '0820-2925101', 'admissions@manipal.edu', 'https://manipal.edu', 1953, 'Deemed University', 1);

-- Insert Sample Courses
INSERT INTO courses (college_id, name, code, department, description, eligibility_criteria, duration_years, seats_available, fees, is_active) VALUES
(1, 'Bachelor of Technology in Computer Science', 'CSE', 'Engineering', '4-year undergraduate program in Computer Science', '12th pass with PCM and minimum 60%', 4, 120, 150000, 1),
(1, 'Bachelor of Technology in Electrical Engineering', 'EEE', 'Engineering', '4-year undergraduate program in Electrical Engineering', '12th pass with PCM and minimum 60%', 4, 60, 150000, 1),
(1, 'Bachelor of Technology in Mechanical Engineering', 'ME', 'Engineering', '4-year undergraduate program in Mechanical Engineering', '12th pass with PCM and minimum 60%', 4, 60, 150000, 1),
(1, 'Master of Technology in Computer Science', 'MT-CSE', 'Engineering', '2-year postgraduate program in Computer Science', 'B.E./B.Tech in relevant branch with minimum 60%', 2, 30, 200000, 1),
(2, 'Bachelor of Technology in Civil Engineering', 'CE', 'Engineering', '4-year undergraduate program in Civil Engineering', '12th pass with PCM and minimum 60%', 4, 60, 125000, 1),
(2, 'Bachelor of Technology in Electronics and Communication', 'ECE', 'Engineering', '4-year undergraduate program in Electronics', '12th pass with PCM and minimum 60%', 4, 60, 125000, 1),
(2, 'Bachelor of Technology in Information Technology', 'IT', 'Engineering', '4-year undergraduate program in IT', '12th pass with PCM and minimum 60%', 4, 60, 125000, 1),
(3, 'Bachelor of Technology in Chemical Engineering', 'CH', 'Engineering', '4-year undergraduate program in Chemical Engineering', '12th pass with PCM and minimum 60%', 4, 60, 180000, 1),
(3, 'Bachelor of Technology in Biotechnology', 'BT', 'Biotechnology', '4-year undergraduate program in Biotechnology', '12th pass with PCB/PCM and minimum 60%', 4, 40, 180000, 1),
(4, 'Bachelor of Technology in Software Engineering', 'SE', 'Engineering', '4-year undergraduate program in Software Engineering', '12th pass with PCM and minimum 60%', 4, 180, 175000, 1),
(4, 'Bachelor of Technology in Data Science', 'DS', 'Data Science', '4-year undergraduate program in Data Science', '12th pass with PCM and minimum 60%', 4, 60, 175000, 1),
(4, 'Master of Business Administration', 'MBA', 'Management', '2-year postgraduate program in Business Administration', 'Bachelor degree with minimum 50%', 2, 120, 350000, 1),
(5, 'Bachelor of Medicine and Surgery', 'MBBS', 'Medical', '5.5-year undergraduate medical program', '12th pass with PCB and minimum 60%', 5, 150, 1200000, 1),
(5, 'Bachelor of Dental Surgery', 'BDS', 'Dental', '5-year undergraduate dental program', '12th pass with PCB and minimum 60%', 5, 100, 500000, 1),
(5, 'Bachelor of Pharmacy', 'B.Pharm', 'Pharmacy', '4-year undergraduate pharmacy program', '12th pass with PCB/PCM and minimum 50%', 4, 60, 250000, 1);

