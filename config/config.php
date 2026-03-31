<?php
// Application Configuration
// Developer: DAVID MUITHYA
// Final Year Project - SkillMaster LMS

// =============================================
// SECURE SESSION SETTINGS
// =============================================
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);
ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name('lms_session');
    session_start();
}

// =============================================
// LOAD CORE CONFIGURATION
// =============================================

// Load constants first (they define BASE_PATH and other constants)
require_once __DIR__ . '/constants.php';

// Load database connection
require_once BASE_PATH . '/config/database.php';

// =============================================
// HELPER FUNCTIONS
// =============================================

/**
 * Ensure all required upload directories exist
 * Creates directories with proper permissions if they don't exist
 */
function ensureUploadDirectories() {
    $directories = [
        UPLOAD_PATH,
        PROFILE_UPLOAD_PATH,
        ASSIGNMENT_UPLOAD_PATH,
        COURSE_UPLOAD_PATH,
        MATERIAL_UPLOAD_PATH
    ];
    
    foreach ($directories as $dir) {
        if (!file_exists($dir)) {
            if (!mkdir($dir, 0755, true)) {
                if (DEBUG_MODE) {
                    die("Failed to create directory: " . $dir);
                } else {
                    error_log("Failed to create directory: " . $dir);
                }
            }
        }
    }
}

// Create upload directories
ensureUploadDirectories();

// =============================================
// INCLUDE ADDITIONAL HELPERS (if they exist)
// =============================================

// Include helper functions if the file exists
$helpers_file = BASE_PATH . '/app/helpers/functions.php';
if (file_exists($helpers_file)) {
    require_once $helpers_file;
}

// Include email helper if the file exists
$email_helper = BASE_PATH . '/app/helpers/email.php';
if (file_exists($email_helper)) {
    require_once $email_helper;
}

// =============================================
// MAINTENANCE MODE CHECK
// =============================================
if (MAINTENANCE_MODE && (!isset($_SESSION['maintenance_bypass']) || $_SESSION['maintenance_bypass'] !== true)) {
    $maintenance_file = BASE_PATH . '/public/maintenance.php';
    if (file_exists($maintenance_file)) {
        include $maintenance_file;
        exit;
    }
}

// =============================================
// ERROR REPORTING (Based on DEBUG_MODE)
// =============================================
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// =============================================
// TIMEZONE (Ensure consistency)
// =============================================
date_default_timezone_set('Africa/Nairobi');

// =============================================
// APPLICATION READY
// =============================================
// Configuration loaded successfully