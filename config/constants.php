<?php
// Application Constants

// User Roles
define('ROLE_ADMIN', 'admin');
define('ROLE_STAFF', 'staff');
define('ROLE_STUDENT', 'student');

// Application Status
define('APP_STATUS_DRAFT', 'draft');
define('APP_STATUS_SUBMITTED', 'submitted');
define('APP_STATUS_UNDER_REVIEW', 'under_review');
define('APP_STATUS_APPROVED', 'approved');
define('APP_STATUS_REJECTED', 'rejected');

// Document Status
define('DOC_STATUS_PENDING', 'pending');
define('DOC_STATUS_VERIFIED', 'verified');
define('DOC_STATUS_REJECTED', 'rejected');

// Payment Status
define('PAYMENT_STATUS_PENDING', 'pending');
define('PAYMENT_STATUS_COMPLETED', 'completed');
define('PAYMENT_STATUS_FAILED', 'failed');
define('PAYMENT_STATUS_REFUNDED', 'refunded');

// Pagination
define('ITEMS_PER_PAGE', 10);

// File Upload Settings
define('MAX_FILE_SIZE', 5242880); // 5MB
define('ALLOWED_IMAGE_TYPES', ['image/jpeg', 'image/png', 'image/jpg']);
define('ALLOWED_DOCUMENT_TYPES', ['application/pdf']);

// Email Settings (Configure your SMTP)
define('EMAIL_FROM', 'noreply@college.edu');
define('EMAIL_FROM_NAME', 'College Admission System');

// SMS API (Configure your SMS gateway)
define('SMS_API_KEY', '');
define('SMS_SENDER_ID', 'COLLEGE');
?>

