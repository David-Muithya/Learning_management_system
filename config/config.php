<?php
// Application Configuration
// Developer: DAVID MUITHYA
// Final Year Project - LMS

// Secure session settings
ini_set('session.cookie_httponly', 1);
ini_set('session.use_only_cookies', 1);

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_name('lms_session');
    session_start();
}

// Include constants
require_once __DIR__ . '/constants.php';

// Include database connection
require_once BASE_PATH . '/config/database.php';

// Include helper functions
require_once BASE_PATH . '/app/helpers/functions.php';

// Include email helper
require_once BASE_PATH . '/app/helpers/email.php';

require_once __DIR__ . "/constants.php";

function ensureUploadDirectories() {
    $paths = [
        PROFILE_UPLOAD_PATH,
        ASSIGNMENT_UPLOAD_PATH,
        COURSE_UPLOAD_PATH,
        MATERIAL_UPLOAD_PATH
    ];

    foreach ($paths as $path) {
        if (!is_dir($path)) {
            mkdir($path, 0777, true);
        }
    }
}
ensureUploadDirectories();


// Ensure upload directories exist
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
                die("Failed to create directory: " . $dir);
            }
        }
    }
}

// Create upload directories
ensureUploadDirectories();

// Maintenance mode check
if (MAINTENANCE_MODE && (!isset($_SESSION['maintenance_bypass']) || $_SESSION['maintenance_bypass'] !== true)) {
    $maintenance_file = BASE_PATH . '/maintenance.php';
    if (file_exists($maintenance_file)) {
        include $maintenance_file;
        exit;
    }
}