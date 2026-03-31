<?php
// Application Constants
// Developer: DAVID MUITHYA
// Final Year Project - SKILLMASTER LMS

// =============================================
// APPLICATION INFORMATION
// =============================================
define('APP_NAME', 'SkillMaster LMS');
define('APP_VERSION', '1.0.0');
define('APP_DEVELOPER', 'David Muithya');

// =============================================
// URL & PATH CONFIGURATION
// =============================================
define('BASE_URL', 'http://localhost/Learning_management_system');
define('BASE_PATH', dirname(__DIR__));
define('ADMIN_URL', BASE_URL . '/admin');

// =============================================
// UPLOAD DIRECTORIES
// =============================================
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('PROFILE_UPLOAD_PATH', UPLOAD_PATH . 'profiles/');
define('ASSIGNMENT_UPLOAD_PATH', UPLOAD_PATH . 'assignments/');
define('COURSE_UPLOAD_PATH', UPLOAD_PATH . 'courses/');

// =============================================
// SESSION CONFIGURATION
// =============================================
define('SESSION_TIMEOUT', 7200);
define('ADMIN_SESSION_TIMEOUT', 1800);

// =============================================
// SECURITY CONFIGURATION
// =============================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600);
define('PASSWORD_BCRYPT_COST', 12);

// =============================================
// PAGINATION
// =============================================
define('ITEMS_PER_PAGE', 10);

// =============================================
// DATE & TIME
// =============================================
date_default_timezone_set('Africa/Nairobi');
define('DATE_FORMAT', 'F j, Y');
define('DATETIME_FORMAT', 'F j, Y g:i A');

// =============================================
// DATABASE CONFIGURATION
// =============================================
define('DB_HOST', 'localhost');
define('DB_NAME', 'lms_db');
define('DB_USER', 'root');
define('DB_PASS', '');
define('DB_CHARSET', 'utf8mb4');

// =============================================
// EMAIL CONFIGURATION (for password reset, notifications, contact form)
// =============================================
// Using Gmail App Password
define('SMTP_HOST', 'smtp.gmail.com');
define('SMTP_PORT', 587);
define('SMTP_ENCRYPTION', 'tls');  // or 'ssl' for port 465
define('SMTP_AUTH', true);
define('SMTP_USERNAME', 'musyimimuithya3@gmail.com');
define('SMTP_PASSWORD', 'olup ygdn dyva tyqc');  // Gmail App Password
define('SMTP_FROM_EMAIL', 'musyimimuithya3@gmail.com');
define('SMTP_FROM_NAME', 'SkillMaster LMS');
define('SMTP_REPLY_TO', 'support@skillmaster.com');

// Email settings for contact form and notifications
define('ADMIN_EMAIL', 'musyimimuithya3@gmail.com');
define('SYSTEM_EMAIL', 'noreply@skillmaster.com');

// =============================================
// SYSTEM SETTINGS
// =============================================
define('DEBUG_MODE', true);
define('MAINTENANCE_MODE', false);

// =============================================
// ERROR REPORTING
// =============================================
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// =============================================
// CURRENCY
// =============================================
define('CURRENCY_SYMBOL', 'Ksh');
define('CURRENCY_CODE', 'KES');

// =============================================
// USER ROLES
// =============================================
define('ROLE_ADMIN', 'admin');
define('ROLE_INSTRUCTOR', 'instructor');
define('ROLE_STUDENT', 'student');

// =============================================
// COURSE STATUS
// =============================================
define('COURSE_DRAFT', 'draft');
define('COURSE_PUBLISHED', 'published');
define('COURSE_ARCHIVED', 'archived');

// =============================================
// ENROLLMENT STATUS
// =============================================
define('ENROLLMENT_ACTIVE', 'active');
define('ENROLLMENT_PENDING', 'pending');
define('ENROLLMENT_COMPLETED', 'completed');
define('ENROLLMENT_DROPPED', 'dropped');

// =============================================
// NOTIFICATION TYPES
// =============================================
define('NOTIFICATION_INFO', 'info');
define('NOTIFICATION_SUCCESS', 'success');
define('NOTIFICATION_WARNING', 'warning');
define('NOTIFICATION_ERROR', 'danger');



// BASE UPLOAD DIRECTORY (relative to public/)
define("UPLOAD_BASE_PATH", __DIR__ . "/../public/uploads/");

// Specific upload directories
define("PROFILE_UPLOAD_PATH", UPLOAD_BASE_PATH . "profiles/");
define("ASSIGNMENT_UPLOAD_PATH", UPLOAD_BASE_PATH . "assignments/");
define("COURSE_UPLOAD_PATH", UPLOAD_BASE_PATH . "courses/");
define("MATERIAL_UPLOAD_PATH", UPLOAD_BASE_PATH . "materials/");


// =============================================
// ACTIVITY LOG ACTIONS
// =============================================
define('ACTION_LOGIN', 'login');
define('ACTION_LOGOUT', 'logout');
define('ACTION_REGISTER', 'register');
define('ACTION_CREATE_COURSE', 'create_course');
define('ACTION_UPDATE_COURSE', 'update_course');
define('ACTION_DELETE_COURSE', 'delete_course');
define('ACTION_ENROLL', 'enroll');
define('ACTION_SUBMIT_ASSIGNMENT', 'submit_assignment');
define('ACTION_GRADE', 'grade');
define('ACTION_CHANGE_PASSWORD', 'change_password');
define('ACTION_PASSWORD_RESET', 'password_reset');
?>