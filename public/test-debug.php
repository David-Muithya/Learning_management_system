<?php
echo "<h1>Debug Test</h1>";

// Define BASE_PATH manually
define('BASE_PATH', dirname(__DIR__));
echo "<p>BASE_PATH: " . BASE_PATH . "</p>";

// Check autoloader
$autoload = BASE_PATH . '/vendor/autoload.php';
echo "<p>Autoloader path: " . $autoload . "</p>";
echo "<p>Autoloader exists: " . (file_exists($autoload) ? 'YES' : 'NO') . "</p>";

if (file_exists($autoload)) {
    require_once $autoload;
    echo "<p style='color:green'>✓ Autoloader loaded</p>";
}

// Load constants
if (file_exists(BASE_PATH . '/config/constants.php')) {
    require_once BASE_PATH . '/config/constants.php';
    echo "<p>✓ Constants loaded</p>";
    echo "<p>APP_NAME: " . APP_NAME . "</p>";
    echo "<p>DB_HOST: " . DB_HOST . "</p>";
}

// Test database
if (class_exists('SkillMaster\Database\Connection')) {
    echo "<p>✓ Connection class found</p>";
    try {
        $conn = SkillMaster\Database\Connection::getInstance()->getConnection();
        echo "<p style='color:green'>✓ Database connection successful!</p>";
    } catch (Exception $e) {
        echo "<p style='color:red'>✗ Database error: " . $e->getMessage() . "</p>";
    }
} else {
    echo "<p style='color:red'>✗ Connection class not found</p>";
}