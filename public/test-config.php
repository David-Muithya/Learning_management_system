<?php
// test-config.php - Use the main config file

// Load the main configuration (handles everything)
require_once __DIR__ . '/../config/config.php';

echo "<h1>'SkillMaster' - Configuration Test</h1>";

// Check if constants are loaded
echo "<h2>Loaded Constants:</h2>";
echo "<ul>";
echo "<li>APP_NAME: " . APP_NAME . "</li>";
echo "<li>APP_VERSION: " . APP_VERSION . "</li>";
echo "<li>BASE_PATH: " . BASE_PATH . "</li>";
echo "<li>DB_HOST: " . DB_HOST . "</li>";
echo "<li>DB_NAME: " . DB_NAME . "</li>";
echo "<li>DEBUG_MODE: " . (DEBUG_MODE ? 'ON' : 'OFF') . "</li>";
echo "</ul>";

// Test database connection
echo "<h2>Database Connection:</h2>";
try {
    $conn = getDBConnection();
    echo "<p style='color: green'>✅ Database connection successful!</p>";
    
    // Show some statistics
    $stmt = $conn->query("SELECT COUNT(*) as total FROM users");
    $result = $stmt->fetch();
    echo "<p>Total users: " . $result['total'] . "</p>";
    
    $stmt = $conn->query("
        SELECT 
            (SELECT COUNT(*) FROM users WHERE role = 'admin') as admins,
            (SELECT COUNT(*) FROM users WHERE role = 'instructor') as instructors,
            (SELECT COUNT(*) FROM users WHERE role = 'student') as students
    ");
    $stats = $stmt->fetch();
    
    echo "<h3>User Statistics:</h3>";
    echo "<ul>";
    echo "<li>Admins: " . $stats['admins'] . "</li>";
    echo "<li>Instructors: " . $stats['instructors'] . "</li>";
    echo "<li>Students: " . $stats['students'] . "</li>";
    echo "</ul>";
    
    $stmt = $conn->query("
        SELECT status, COUNT(*) as count 
        FROM courses 
        GROUP BY status
    ");
    $courseStats = $stmt->fetchAll();
    
    echo "<h3>Course Statistics:</h3>";
    echo "<ul>";
    foreach ($courseStats as $stat) {
        echo "<li>" . ucfirst($stat['status']) . ": " . $stat['count'] . "</li>";
    }
    echo "</ul>";
    
} catch (Exception $e) {
    echo "<p style='color: red'>❌ Error: " . $e->getMessage() . "</p>";
}

echo "<hr>";
echo "<p>✅ Configuration is working correctly!</p>";