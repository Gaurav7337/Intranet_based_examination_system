# Intranet_based_examination_system

# Intranet-Based Online Examination System

This is a PHP-based local intranet examination platform with fault tolerance, load balancing (via Apache/NGINX), and real-time exam tracking.

## 🔧 Technologies Used

- PHP 8.x
- Apache (XAMPP)
- MySQL (MariaDB)
- JavaScript (Vanilla)
- HTML/CSS
- NGINX (for load balancing - optional)

## 📁 Features

- Student login
- Exam selection
- Auto-save answers
- Timer with fault tolerance (session resume)
- Admin result view and CSV download

## 🚀 Setup Instructions

1. Import the `exam_system.sql` file in phpMyAdmin.
2. Place all PHP files in `htdocs/exam_project/`.
3. Access via `http://localhost:8081/login.php`
4. Use `exam123` as the login password for all students.

## 📂 File Structure

