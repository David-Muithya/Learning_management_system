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
define('BASE_URL', 'http://localhost/SkillMaster-LMS/public');
define('BASE_PATH', dirname(__DIR__));
define('ADMIN_URL', BASE_URL . '/admin');
define('INSTRUCTOR_URL', BASE_URL . '/instructor');
define('STUDENT_URL', BASE_URL . '/student');

// =============================================
// UPLOAD DIRECTORIES
// =============================================
define('UPLOAD_PATH', BASE_PATH . '/public/uploads/');
define('PROFILE_UPLOAD_PATH', UPLOAD_PATH . 'profiles/');
define('ASSIGNMENT_UPLOAD_PATH', UPLOAD_PATH . 'assignments/');
define('COURSE_UPLOAD_PATH', UPLOAD_PATH . 'courses/');
define('MATERIAL_UPLOAD_PATH', UPLOAD_PATH . 'materials/');

// =============================================
// SESSION CONFIGURATION
// =============================================
define('SESSION_TIMEOUT', 7200);
define('ADMIN_SESSION_TIMEOUT', 1800);
define('SESSION_REGENERATE_INTERVAL', 1800);

// =============================================
// SECURITY CONFIGURATION
// =============================================
define('CSRF_TOKEN_NAME', 'csrf_token');
define('CSRF_TOKEN_LIFETIME', 3600);
define('PASSWORD_BCRYPT_COST', 12);
define('MAX_LOGIN_ATTEMPTS', 5);
define('LOGIN_LOCKOUT_TIME', 15);
define('PASSWORD_MIN_LENGTH', 8);

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
// DATABASE CONFIGURATION (Load from .env or defaults)
// =============================================
define('DB_HOST', getenv('DB_HOST') ?: 'localhost');
define('DB_NAME', getenv('DB_NAME') ?: 'lms_db');
define('DB_USER', getenv('DB_USER') ?: 'root');
define('DB_PASS', getenv('DB_PASS') ?: '');
define('DB_CHARSET', getenv('DB_CHARSET') ?: 'utf8mb4');

// =============================================
// EMAIL CONFIGURATION (Load from .env)
// =============================================
define('SMTP_HOST', getenv('SMTP_HOST') ?: 'smtp.gmail.com');
define('SMTP_PORT', getenv('SMTP_PORT') ?: 587);
define('SMTP_ENCRYPTION', getenv('SMTP_ENCRYPTION') ?: 'tls');
define('SMTP_AUTH', true);
define('SMTP_USERNAME', getenv('SMTP_USERNAME') ?: '');
define('SMTP_PASSWORD', getenv('SMTP_PASSWORD') ?: '');
define('SMTP_FROM_EMAIL', getenv('SMTP_FROM_EMAIL') ?: '');
define('SMTP_FROM_NAME', getenv('SMTP_FROM_NAME') ?: 'SkillMaster LMS');
define('SMTP_REPLY_TO', getenv('SMTP_REPLY_TO') ?: 'support@skillmaster.com');
define('ADMIN_EMAIL', getenv('ADMIN_EMAIL') ?: '');
define('SYSTEM_EMAIL', getenv('SYSTEM_EMAIL') ?: 'noreply@skillmaster.com');

// =============================================
// SYSTEM SETTINGS
// =============================================
define('DEBUG_MODE', getenv('APP_DEBUG') ?: true);
define('MAINTENANCE_MODE', getenv('MAINTENANCE_MODE') ?: false);

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
// COURSE STATUS (COMPLETE)
// =============================================
define('COURSE_DRAFT', 'draft');
define('COURSE_PENDING_APPROVAL', 'pending_approval');
define('COURSE_PUBLISHED', 'published');
define('COURSE_ARCHIVED', 'archived');
define('COURSE_REJECTED', 'rejected');

// =============================================
// ENROLLMENT STATUS (COMPLETE)
// =============================================
define('ENROLLMENT_PENDING_PAYMENT', 'pending_payment');
define('ENROLLMENT_PENDING_VERIFICATION', 'pending_verification');
define('ENROLLMENT_ACTIVE', 'active');
define('ENROLLMENT_COMPLETED', 'completed');
define('ENROLLMENT_DROPPED', 'dropped');
define('ENROLLMENT_REJECTED', 'rejected');

// =============================================
// MOCK PAYMENT STATUS
// =============================================
define('PAYMENT_PENDING', 'pending');
define('PAYMENT_COMPLETED', 'completed');
define('PAYMENT_VERIFIED', 'verified');
define('PAYMENT_REJECTED', 'rejected');

// =============================================
// INSTRUCTOR APPLICATION STATUS
// =============================================
define('APPLICATION_PENDING', 'pending');
define('APPLICATION_APPROVED', 'approved');
define('APPLICATION_REJECTED', 'rejected');

// =============================================
// SUBMISSION STATUS
// =============================================
define('SUBMISSION_SUBMITTED', 'submitted');
define('SUBMISSION_GRADED', 'graded');
define('SUBMISSION_LATE', 'late');
define('SUBMISSION_RESUBMITTED', 'resubmitted');

// =============================================
// QUIZ ATTEMPT STATUS
// =============================================
define('QUIZ_IN_PROGRESS', 'in_progress');
define('QUIZ_SUBMITTED', 'submitted');
define('QUIZ_GRADED', 'graded');

// =============================================
// ATTENDANCE STATUS
// =============================================
define('ATTENDANCE_PRESENT', 'present');
define('ATTENDANCE_ABSENT', 'absent');
define('ATTENDANCE_LATE', 'late');
define('ATTENDANCE_EXCUSED', 'excused');

// =============================================
// ANNOUNCEMENT PRIORITY
// =============================================
define('PRIORITY_LOW', 'low');
define('PRIORITY_NORMAL', 'normal');
define('PRIORITY_HIGH', 'high');
define('PRIORITY_URGENT', 'urgent');

// =============================================
// MATERIAL TYPE
// =============================================
define('MATERIAL_DOCUMENT', 'document');
define('MATERIAL_VIDEO', 'video');
define('MATERIAL_AUDIO', 'audio');
define('MATERIAL_IMAGE', 'image');
define('MATERIAL_LINK', 'link');
define('MATERIAL_OTHER', 'other');

// =============================================
// FILE UPLOAD CONSTANTS
// =============================================
define('MAX_FILE_SIZE', getenv('MAX_FILE_SIZE') ?: 10 * 1024 * 1024);
define('ALLOWED_EXTENSIONS', getenv('ALLOWED_EXTENSIONS') ?: 'pdf,doc,docx,jpg,jpeg,png,mp4,mp3');
define('MAX_PROFILE_SIZE', 2 * 1024 * 1024);

// =============================================
// GRADE LETTER MAPPING
// =============================================
define('GRADE_A', 80);
define('GRADE_B', 70);
define('GRADE_C', 60);
define('GRADE_D', 50);
define('GRADE_F', 0);

// =============================================
// CACHE CONFIGURATION
// =============================================
define('CACHE_ENABLED', getenv('CACHE_ENABLED') ?: true);
define('CACHE_DRIVER', getenv('CACHE_DRIVER') ?: 'file');
define('CACHE_EXPIRATION', getenv('CACHE_EXPIRATION') ?: 3600);
define('CACHE_PATH', BASE_PATH . '/cache/');

// =============================================
// EMAIL TEMPLATES
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