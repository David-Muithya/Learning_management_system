<?php
// Application Bootstrap
// Loads environment variables and sets up autoloading

// Define base path if not defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Load Composer autoloader
require_once BASE_PATH . '/vendor/autoload.php';

// Load environment variables from .env file
use Dotenv\Dotenv;

try {
    if (file_exists(BASE_PATH . '/.env')) {
        $dotenv = Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();
    }
} catch (Exception $e) {
    // .env file might not exist, continue with defaults
}

// Load application constants
require_once BASE_PATH . '/config/constants.php';

// Set error reporting based on debug mode
if (DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    ini_set('error_log', BASE_PATH . '/logs/error.log');
}

// Set timezone
date_default_timezone_set('Africa/Nairobi');

// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    // Secure session settings
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    ini_set('session.cookie_secure', isset($_SERVER['HTTPS']));
    
    session_name('lms_session');
    session_start();
}

// Ensure upload directories exist
function ensureUploadDirectories() {
    $paths = [
        UPLOAD_PATH,
        PROFILE_UPLOAD_PATH,
        ASSIGNMENT_UPLOAD_PATH,
        COURSE_UPLOAD_PATH,
        MATERIAL_UPLOAD_PATH,
        CACHE_PATH
    ];
    
    foreach ($paths as $path) {
        if (!file_exists($path)) {
            if (!mkdir($path, 0755, true)) {
                if (DEBUG_MODE) {
                    error_log("Failed to create directory: " . $path);
                }
            }
        }
    }
}

// Create necessary directories
ensureUploadDirectories();

// Maintenance mode check
if (MAINTENANCE_MODE && (!isset($_SESSION['maintenance_bypass']) || $_SESSION['maintenance_bypass'] !== true)) {
    $maintenance_file = BASE_PATH . '/public/maintenance.php';
    if (file_exists($maintenance_file)) {
        include $maintenance_file;
        exit;
    }
}