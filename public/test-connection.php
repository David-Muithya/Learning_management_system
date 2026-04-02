<?php
require_once __DIR__ . '/../config/config.php';

echo "<h1>SkillMaster LMS - Connection Test</h1>";

// Test 1: Constants
echo "<h2>1. Constants Test</h2>";
echo "<ul>";
echo "<li>APP_NAME: " . (defined('APP_NAME') ? APP_NAME : 'NOT DEFINED') . "</li>";
echo "<li>BASE_PATH: " . (defined('BASE_PATH') ? BASE_PATH : 'NOT DEFINED') . "</li>";
echo "<li>DB_HOST: " . (defined('DB_HOST') ? DB_HOST : 'NOT DEFINED') . "</li>";
echo "<li>DEBUG_MODE: " . (defined('DEBUG_MODE') ? (DEBUG_MODE ? 'ON' : 'OFF') : 'NOT DEFINED') . "</li>";
echo "</ul>";

// Test 2: Database Connection
echo "<h2>2. Database Connection Test</h2>";
try {
    $conn = getDBConnection();
    echo "<p style='color:green'>✅ Database connected successfully!</p>";
    
    // Count tables
    $stmt = $conn->query("SHOW TABLES");
    $tables = $stmt->fetchAll();
    echo "<p>📊 Tables found: " . count($tables) . "</p>";
    
} catch (Exception $e) {
    echo "<p style='color:red'>❌ Database error: " . $e->getMessage() . "</p>";
}

// Test 3: Session
echo "<h2>3. Session Test</h2>";
echo "<p>Session ID: " . session_id() . "</p>";
echo "<p>Session Status: " . session_status() . "</p>";

// Test 4: Environment
echo "<h2>4. Environment Test</h2>";
echo "<ul>";
echo "<li>PHP Version: " . phpversion() . "</li>";
echo "<li>Server Software: " . ($_SERVER['SERVER_SOFTWARE'] ?? 'Unknown') . "</li>";
echo "<li>Document Root: " . ($_SERVER['DOCUMENT_ROOT'] ?? 'Unknown') . "</li>";
echo "<li>Request URI: " . ($_SERVER['REQUEST_URI'] ?? 'Unknown') . "</li>";
echo "</ul>";