<?php
// bootstrap.php
// Application Bootstrap - Entry point for all requests
// Developer: DAVID MUITHYA | 'SkillMaster'

// =============================================
// 1. DEFINE BASE PATH (Project Root)
// =============================================
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// =============================================
// 2. DEFINE DEFAULT PATHS (before constants)
// =============================================
// These will be overridden by constants.php later, but we need them now
define('LOG_PATH_DEFAULT', BASE_PATH . '/logs');
define('CACHE_PATH_DEFAULT', BASE_PATH . '/cache');
define('UPLOAD_PATH_DEFAULT', BASE_PATH . '/public/uploads');
define('PROFILE_UPLOAD_PATH_DEFAULT', UPLOAD_PATH_DEFAULT . '/profiles');
define('ASSIGNMENT_UPLOAD_PATH_DEFAULT', UPLOAD_PATH_DEFAULT . '/assignments');
define('COURSE_UPLOAD_PATH_DEFAULT', UPLOAD_PATH_DEFAULT . '/courses');
define('MATERIAL_UPLOAD_PATH_DEFAULT', UPLOAD_PATH_DEFAULT . '/materials');

// =============================================
// 3. LOAD COMPOSER AUTOLOADER
// =============================================
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
if (!file_exists($autoloadPath)) {
    die('Composer dependencies not installed. Run: composer install');
}
require_once $autoloadPath;

// =============================================
// 4. LOAD ENVIRONMENT VARIABLES
// =============================================
use Dotenv\Dotenv;

// Try to load .env file
$envFile = BASE_PATH . '/.env';
if (file_exists($envFile)) {
    try {
        $dotenv = Dotenv::createImmutable(BASE_PATH);
        $dotenv->load();
    } catch (Exception $e) {
        if (defined('DEBUG_MODE') && DEBUG_MODE) {
            die("Error parsing .env file: " . $e->getMessage() . 
                "<br><br>Make sure to wrap values with spaces in quotes.<br>" .
                "Example: SMTP_PASSWORD=\"your password with spaces\"");
        } else {
            error_log("Failed to parse .env file: " . $e->getMessage());
        }
    }
}

// =============================================
// 5. LOAD APPLICATION CONSTANTS
// =============================================
require_once BASE_PATH . '/config/constants.php';

// =============================================
// 6. ENSURE CRITICAL DIRECTORIES EXIST
// =============================================
// Use defined constants if available, otherwise use defaults
$criticalDirs = [
    defined('LOG_PATH') ? LOG_PATH : LOG_PATH_DEFAULT,
    defined('CACHE_PATH') ? CACHE_PATH : CACHE_PATH_DEFAULT,
    defined('UPLOAD_PATH') ? UPLOAD_PATH : UPLOAD_PATH_DEFAULT,
    defined('PROFILE_UPLOAD_PATH') ? PROFILE_UPLOAD_PATH : PROFILE_UPLOAD_PATH_DEFAULT,
    defined('ASSIGNMENT_UPLOAD_PATH') ? ASSIGNMENT_UPLOAD_PATH : ASSIGNMENT_UPLOAD_PATH_DEFAULT,
    defined('COURSE_UPLOAD_PATH') ? COURSE_UPLOAD_PATH : COURSE_UPLOAD_PATH_DEFAULT,
    defined('MATERIAL_UPLOAD_PATH') ? MATERIAL_UPLOAD_PATH : MATERIAL_UPLOAD_PATH_DEFAULT
];

foreach ($criticalDirs as $dir) {
    if (!is_dir($dir) && $dir !== null) {
        @mkdir($dir, 0755, true);
    }
}

// =============================================
// 7. CONFIGURE ERROR HANDLING
// =============================================
if (defined('DEBUG_MODE') && DEBUG_MODE) {
    error_reporting(E_ALL);
    ini_set('display_errors', 1);
    ini_set('display_startup_errors', 1);
} else {
    error_reporting(0);
    ini_set('display_errors', 0);
    ini_set('log_errors', 1);
    $logPath = defined('LOG_PATH') ? LOG_PATH : LOG_PATH_DEFAULT;
    ini_set('error_log', $logPath . '/error.log');
}

// =============================================
// 8. SET TIMEZONE
// =============================================
date_default_timezone_set('Africa/Nairobi');

// =============================================
// 9. INITIALIZE SECURE SESSIONS
// =============================================
if (session_status() === PHP_SESSION_NONE) {
    $isSecure = !empty($_SERVER['HTTPS']) && $_SERVER['HTTPS'] !== 'off';
    
    ini_set('session.cookie_httponly', 1);
    ini_set('session.use_only_cookies', 1);
    
    $lifetime = defined('SESSION_LIFETIME') ? SESSION_LIFETIME * 60 : 7200;
    session_set_cookie_params([
        'lifetime' => $lifetime,
        'path' => '/',
        'secure' => $isSecure,
        'httponly' => true,
        'samesite' => 'Lax'
    ]);
    
    // Set session name based on route: separate admin sessions from public sessions
    $requestUri = $_SERVER['REQUEST_URI'] ?? '';
    $sessionName = 'skillmaster_public_session';

    if (strpos($requestUri, '/admin/') !== false) {
        $sessionName = 'skillmaster_admin_session';
    }

    session_name($sessionName);
    session_start();

    if (!isset($_SESSION['created'])) {
        $_SESSION['created'] = time();
    } elseif (time() - $_SESSION['created'] > 1800) {
        session_regenerate_id(true);
        $_SESSION['created'] = time();
    }
}

// =============================================
// 10. MAINTENANCE MODE CHECK
// =============================================
if (defined('MAINTENANCE_MODE') && MAINTENANCE_MODE && empty($_SESSION['maintenance_bypass'])) {
    $maintenanceFile = BASE_PATH . '/public/maintenance.php';
    if (file_exists($maintenanceFile)) {
        http_response_code(503);
        header('Retry-After: 3600');
        include $maintenanceFile;
        exit;
    }
}

// =============================================
// ✅ APPLICATION READY
// =============================================