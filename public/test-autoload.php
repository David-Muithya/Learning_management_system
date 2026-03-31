<?php
// Test autoloader
echo "<h1>Autoloader Test</h1>";

// Define BASE_PATH if not defined
if (!defined('BASE_PATH')) {
    define('BASE_PATH', dirname(__DIR__));
    echo "<p>BASE_PATH defined: " . BASE_PATH . "</p>";
}

// Check if vendor/autoload.php exists
$autoloadPath = BASE_PATH . '/vendor/autoload.php';
echo "<p>Looking for autoloader at: " . $autoloadPath . "</p>";

if (file_exists($autoloadPath)) {
    echo "<p style='color:green'>✓ autoload.php found!</p>";
    require_once $autoloadPath;
    echo "<p style='color:green'>✓ autoloader loaded successfully!</p>";
    
    // Test if classes can be loaded
    if (class_exists('SkillMaster\Database\Connection')) {
        echo "<p style='color:green'>✓ SkillMaster\Database\Connection class found!</p>";
    } else {
        echo "<p style='color:orange'>⚠ SkillMaster\Database\Connection class not found - check namespace mapping</p>";
    }
    
} else {
    echo "<p style='color:red'>✗ autoload.php NOT found!</p>";
    echo "<p>Please run: composer install</p>";
}

// List contents of vendor folder
if (is_dir(BASE_PATH . '/vendor')) {
    echo "<h2>Vendor folder contents:</h2>";
    $files = scandir(BASE_PATH . '/vendor');
    echo "<ul>";
    foreach ($files as $file) {
        if ($file != '.' && $file != '..') {
            echo "<li>" . $file . "</li>";
        }
    }
    echo "</ul>";
} else {
    echo "<p>vendor folder does not exist!</p>";
}