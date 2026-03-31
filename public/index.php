<?php
// Front Controller
require_once __DIR__ . '/../config/config.php';

// Simple test to verify setup
if (DEBUG_MODE) {
    echo "<h1>SkillMaster LMS - Setup Successful!</h1>";
    echo "<p>Database: " . DB_NAME . " @ " . DB_HOST . "</p>";
    echo "<p>Environment: " . (getenv('APP_ENV') ?: 'development') . "</p>";
    echo "<p>Debug Mode: " . (DEBUG_MODE ? 'ON' : 'OFF') . "</p>";
    echo "<hr>";
    echo "<p><strong>Next Steps:</strong> Configure .env file and create upload directories.</p>";
    
    // Test database connection
    try {
        $conn = getDBConnection();
        echo "<p style='color: green'>✓ Database connection successful!</p>";
    } catch (Exception $e) {
        echo "<p style='color: red'>✗ Database connection failed: " . $e->getMessage() . "</p>";
    }
} else {
    // Production - show homepage
    header('Location: /home');
    exit;
}