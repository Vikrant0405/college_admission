# TODO: Remove OTP Verification and Password Hashing - COMPLETED

## Plan Overview
Remove OTP verification (auto-verify users on registration) and remove password hashing (store plain passwords).

## Tasks Completed
- [x] 1. Modify auth/register.php - Auto-verify users and remove password hashing
- [x] 2. Modify auth/login.php - Compare plain passwords (remove password_verify)
- [x] 3. Modify auth/reset_password.php - Store plain password (remove password_hash)
- [x] 4. Create missing admin files (verify_documents, manage_users, reports, notifications, settings)
- [x] 5. Create missing student files (my_applications, upload_documents, track_application, payment, notifications, settings)
- [x] 6. Create ajax/get_notifications.php

## Demo Users (Plain Passwords - No Hashing)
- Admin: admin@college.edu / admin123
- Student1: student1@college.edu / password123
- Student2: student2@college.edu / password123
- Student3: student3@college.edu / password123
- Student4: student4@college.edu / password123
- Student5: student5@college.edu / password123


