<?php
// update_admin_password.php
// ✅ NO COMPOSER REQUIRED - Uses built-in PHP functions only
// 🔒 Run this ONCE via browser, then DELETE the file!

// --- CONFIGURATION ---
$target_email = 'admin@skillmaster.co.ke';
$new_password = 'Nairobi@2026!Skill'; // ← CHANGE THIS TO YOUR DESIRED PASSWORD
$db_host     = 'localhost';
$db_name     = 'lms';
$db_user     = 'root';
$db_pass     = ''; // Default XAMPP MySQL password is usually empty
// ---------------------

// Generate bcrypt hash (cost=12) using built-in PHP function
$hash = password_hash($new_password, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Update password
    $stmt = $pdo->prepare("
        UPDATE users 
        SET password = :hash, 
            updated_at = NOW(),
            must_change_password = 0
        WHERE email = :email AND role = 'admin'
        LIMIT 1
    ");
    $stmt->execute(['hash' => $hash, 'email' => $target_email]);

    if ($stmt->rowCount() > 0) {
        echo "<div style='font-family: sans-serif; padding: 20px; background: #e8f5e9; border: 1px solid #4caf50; border-radius: 8px;'>";
        echo "<h2 style='color: #2e7d32; margin-top: 0;'>✅ SUCCESS!</h2>";
        echo "<p>Password updated for <strong>{$target_email}</strong></p>";
        echo "<p>Your new password is: <code style='background: #fff; padding: 4px 8px; border-radius: 4px;'>{$new_password}</code></p>";
        echo "<p><strong>⚠️ IMPORTANT: Delete this file immediately after use!</strong></p>";
        echo "</div>";
    } else {
        echo "<div style='font-family: sans-serif; padding: 20px; background: #fff3e0; border: 1px solid #ff9800; border-radius: 8px;'>";
        echo "<h2 style='color: #e65100; margin-top: 0;'>⚠️ No admin found with that email</h2>";
        echo "<p>Check the email address or verify the <code>users</code> table in phpMyAdmin.</p>";
        echo "</div>";
    }
} catch (PDOException $e) {
    echo "<div style='font-family: sans-serif; padding: 20px; background: #ffebee; border: 1px solid #f44336; border-radius: 8px;'>";
    echo "<h2 style='color: #c62828; margin-top: 0;'>❌ DATABASE ERROR</h2>";
    echo "<p><strong>Message:</strong> " . htmlspecialchars($e->getMessage()) . "</p>";
    echo "<p>Check your database credentials and ensure <code>lms_db</code> exists and is accessible.</p>";
    echo "</div>";
}
?>