<?php
// test-db.php - Database test with duplicate constant check

// Only define BASE_PATH if it's not already defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
}

// Only load constants if they're not already loaded
if (!defined('APP_NAME')) {
    require_once BASE_PATH . '/config/constants.php';
}

// Load database configuration
require_once BASE_PATH . '/config/database.php';

echo "<h1>'SkillMaster' - Database Test</h1>";

try {
    $conn = getDBConnection();
    echo "<p style='color: green'>✅ Database connection successful!</p>";
    
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "<p>Tables found: " . count($tables) . "</p>";
    
    echo "<ul>";
    foreach ($tables as $table) {
        echo "<li>" . reset($table) . "</li>";
    }
    echo "</ul>";
    
    // Show some stats
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users WHERE role = 'student'");
    $students = $stmt->fetch();
    echo "<p>Students registered: " . $students['total'] . "</p>";
    
    $stmt = $conn->query("SELECT COUNT(*) as total FROM courses WHERE status = 'published'");
    $courses = $stmt->fetch();
    echo "<p>Published courses: " . $courses['total'] . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color: red'>❌ Error: " . $e->getMessage() . "</p>";
}