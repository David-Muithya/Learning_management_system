<?php
// add_instructor.php
// ✅ Standalone - NO COMPOSER REQUIRED
// 🔒 Run ONCE via browser or CLI, then DELETE immediately!

// --- DATABASE CONFIG (XAMPP defaults) ---
$db_host = 'localhost';
$db_name = 'lms';
$db_user = 'root';
$db_pass = '';

// --- INSTRUCTOR DETAILS (EDIT THESE) ---
$username      = 'sarah.akinyi';
$email         = 'sarah.akinyi@skillmaster.co.ke';
$password      = 'Instructor@2026!KE'; // ← CHANGE TO YOUR DESIRED PASSWORD
$first_name    = 'Sarah';
$last_name     = 'Akinyi';
$phone         = '+254723456789';
$address       = 'Nairobi, Kenya';
$bio           = 'Senior Data Scientist & ML Educator';
$role          = 'instructor'; // Fixed as requested
$created_by    = 1; // Usually the superadmin ID
// ----------------------------------------

// Generate secure bcrypt hash (cost=12)
$password_hash = password_hash($password, PASSWORD_BCRYPT, ['cost' => 12]);

try {
    // Connect to database
    $pdo = new PDO("mysql:host=$db_host;dbname=$db_name;charset=utf8mb4", $db_user, $db_pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Prevent duplicate email/username
    $checkStmt = $pdo->prepare("SELECT COUNT(*) FROM users WHERE email = :email OR username = :username");
    $checkStmt->execute([':email' => $email, ':username' => $username]);
    if ($checkStmt->fetchColumn() > 0) {
        throw new Exception("❌ Email or Username already exists in the database.");
    }

    // Insert instructor
    $insertStmt = $pdo->prepare("
        INSERT INTO users (
            username, email, password, role, first_name, last_name,
            phone_number, address, bio, is_active, must_change_password, created_by
        ) VALUES (
            :username, :email, :password, :role, :first_name, :last_name,
            :phone, :address, :bio, :is_active, :must_change_password, :created_by
        )
    ");

    $insertStmt->execute([
        ':username'           => $username,
        ':email'              => $email,
        ':password'           => $password_hash,
        ':role'               => $role,
        ':first_name'         => $first_name,
        ':last_name'          => $last_name,
        ':phone'              => $phone,
        ':address'            => $address,
        ':bio'                => $bio,
        ':is_active'          => 1,
        ':must_change_password'=> 0,
        ':created_by'         => $created_by
    ]);

    $new_id = $pdo->lastInsertId();

    // Output (works in both browser & terminal)
    $is_cli = (php_sapi_name() === 'cli');
    $msg = $is_cli ? "\n✅ SUCCESS\n" : "<div style='font-family:sans-serif;padding:20px;background:#e8f5e9;border:1px solid #4caf50;border-radius:8px;max-width:600px;'>";
    $msg .= $is_cli ? "👤 Instructor Added Successfully!\n" : "<h2 style='color:#2e7d32;margin-top:0;'>✅ Instructor Added Successfully!</h2>";
    $msg .= "Name: {$first_name} {$last_name}\nUsername: {$username}\nEmail: {$email}\nPassword: {$password}\nDB ID: {$new_id}\n";
    $msg .= $is_cli ? "\n⚠️ DELETE this file immediately after use!\n" : "<p><strong>⚠️ IMPORTANT: Delete this file immediately after use!</strong></p></div>";
    
    echo $msg;

} catch (PDOException $e) {
    $msg = (php_sapi_name() === 'cli') ? "\n❌ DATABASE ERROR: " . $e->getMessage() . "\n" 
        : "<div style='font-family:sans-serif;padding:20px;background:#ffebee;border:1px solid #f44336;border-radius:8px;max-width:600px;'><h2 style='color:#c62828;margin-top:0;'>❌ Database Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p></div>";
    echo $msg;
} catch (Exception $e) {
    $msg = (php_sapi_name() === 'cli') ? "\n⚠️ ERROR: " . $e->getMessage() . "\n"
        : "<div style='font-family:sans-serif;padding:20px;background:#fff3e0;border:1px solid #ff9800;border-radius:8px;max-width:600px;'><h2 style='color:#e65100;margin-top:0;'>⚠️ Application Error</h2><p>" . htmlspecialchars($e->getMessage()) . "</p></div>";
    echo $msg;
}
?>