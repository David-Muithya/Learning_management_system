<?php
// Application Constants
// Developer: DAVID MUITHYA
// Final Year Project - SKILLMASTER LMS

// Prevent multiple definitions
if (defined('APP_NAME')) {
    return;
}

// =============================================
// APPLICATION INFORMATION
// =============================================
define('APP_NAME', 'SkillMaster LMS');
define('APP_VERSION', '1.0.0');
define('APP_DEVELOPER', 'David Muithya');

// =============================================
// URL & PATH CONFIGURATION
// =============================================
define('BASE_URL', 'http://localhost:8080/Learning_management_system');

define('PUBLIC_URL', BASE_URL . '/public');
define('ADMIN_URL', PUBLIC_URL . '/admin');
define('INSTRUCTOR_URL', PUBLIC_URL . '/instructor');
define('STUDENT_URL', PUBLIC_URL . '/student');

define('LOGIN_URL', PUBLIC_URL . '/login.php');

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
define('DB_NAME', 'lms');
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
// COURSE STATUS (Original + New)
// =============================================
define('COURSE_DRAFT', 'draft');
define('COURSE_PENDING_APPROVAL', 'pending_approval');  // NEW
define('COURSE_PUBLISHED', 'published');
define('COURSE_ARCHIVED', 'archived');
define('COURSE_REJECTED', 'rejected');  // NEW

// =============================================
// ENROLLMENT STATUS (Original + New)
// =============================================
define('ENROLLMENT_ACTIVE', 'active');
define('ENROLLMENT_PENDING', 'pending');
define('ENROLLMENT_COMPLETED', 'completed');
define('ENROLLMENT_DROPPED', 'dropped');
define('ENROLLMENT_PENDING_PAYMENT', 'pending_payment');  // NEW
define('ENROLLMENT_PENDING_VERIFICATION', 'pending_verification');  // NEW
define('ENROLLMENT_REJECTED', 'rejected');  // NEW

// =============================================
// MOCK PAYMENT STATUS (NEW)
// =============================================
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_VERIFIED', 'verified');
define('PAYMENT_REJECTED', 'rejected');

// =============================================
// INSTRUCTOR APPLICATION STATUS (NEW)
// =============================================
define('APPLICATION_PENDING', 'pending');
define('APPLICATION_APPROVED', 'approved');
define('APPLICATION_REJECTED', 'rejected');

// =============================================
// SUBMISSION STATUS (NEW)
// =============================================
define('SUBMISSION_SUBMITTED', 'submitted');
define('SUBMISSION_GRADED', 'graded');
define('SUBMISSION_LATE', 'late');
define('SUBMISSION_RESUBMITTED', 'resubmitted');

// =============================================
// QUIZ ATTEMPT STATUS (NEW)
// =============================================
define('QUIZ_IN_PROGRESS', 'in_progress');
define('QUIZ_SUBMITTED', 'submitted');
define('QUIZ_GRADED', 'graded');

// =============================================
// ATTENDANCE STATUS (NEW)
// =============================================
define('ATTENDANCE_PRESENT', 'present');
define('ATTENDANCE_ABSENT', 'absent');
define('ATTENDANCE_LATE', 'late');
define('ATTENDANCE_EXCUSED', 'excused');

// =============================================
// NOTIFICATION TYPES (Original)
// =============================================
define('NOTIFICATION_INFO', 'info');
define('NOTIFICATION_SUCCESS', 'success');
define('NOTIFICATION_WARNING', 'warning');
define('NOTIFICATION_ERROR', 'danger');

// =============================================
// ANNOUNCEMENT PRIORITY (NEW)
// =============================================
define('PRIORITY_LOW', 'low');
define('PRIORITY_NORMAL', 'normal');
define('PRIORITY_HIGH', 'high');
define('PRIORITY_URGENT', 'urgent');

// =============================================
// MATERIAL TYPE (NEW)
// =============================================
define('MATERIAL_DOCUMENT', 'document');
define('MATERIAL_VIDEO', 'video');
define('MATERIAL_AUDIO', 'audio');
define('MATERIAL_IMAGE', 'image');
define('MATERIAL_LINK', 'link');
define('MATERIAL_OTHER', 'other');

// =============================================
// FILE UPLOAD CONSTANTS (NEW)
// =============================================
define('MAX_FILE_SIZE', 10 * 1024 * 1024);  // 10MB in bytes
define('ALLOWED_EXTENSIONS', 'pdf,doc,docx,jpg,jpeg,png,mp4,mp3');
define('MAX_PROFILE_SIZE', 2 * 1024 * 1024);  // 2MB for profile pictures

// =============================================
// GRADE LETTER MAPPING (NEW)
// =============================================
define('GRADE_A', 80);
define('GRADE_B', 70);
define('GRADE_C', 60);
define('GRADE_D', 50);
define('GRADE_F', 0);

// =============================================
// CACHE CONFIGURATION (NEW)
// =============================================
define('CACHE_ENABLED', true);
define('CACHE_DRIVER', 'file');  // file, redis, memcached
define('CACHE_EXPIRATION', 3600);  // 1 hour default
define('CACHE_PATH', BASE_PATH . '/cache/');

// =============================================
// SECURITY ENHANCEMENTS (NEW)
// =============================================
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15);  // minutes
define('PASSWORD_MIN_LENGTH', 8);
define('SESSION_REGENERATE_INTERVAL', 1800);  // 30 minutes
define('SESSION_LIFETIME', 120);  // minutes
define('SESSION_SECURE_COOKIE', false);  // Set to true in production with HTTPS

// =============================================
// EMAIL TEMPLATES (NEW)
// =============================================
define('EMAIL_WELCOME_STUDENT', 'welcome_student');
define('EMAIL_WELCOME_INSTRUCTOR', 'welcome_instructor');
define('EMAIL_PASSWORD_RESET', 'password_reset');
define('EMAIL_APPLICATION_RECEIVED', 'application_received');
define('EMAIL_APPLICATION_APPROVED', 'application_approved');
define('EMAIL_APPLICATION_REJECTED', 'application_rejected');
define('EMAIL_COURSE_APPROVED', 'course_approved');
define('EMAIL_COURSE_REJECTED', 'course_rejected');
define('EMAIL_ENROLLMENT_VERIFIED', 'enrollment_verified');
define('EMAIL_ASSIGNMENT_GRADED', 'assignment_graded');

// =============================================
// ADDITIONAL PATH CONSTANTS (for bootstrap)
// =============================================
define('LOG_PATH', BASE_PATH . '/logs');
define('UPLOAD_BASE_PATH', BASE_PATH . "/public/uploads/");

// Specific upload directories (ensure these are defined)
if (!defined('MATERIAL_UPLOAD_PATH')) {
    define('MATERIAL_UPLOAD_PATH', UPLOAD_BASE_PATH . "materials/");
}

// =============================================
// ACTIVITY LOG ACTIONS (Original)
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