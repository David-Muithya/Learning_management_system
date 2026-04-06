<?php
// Database connection
$conn = new mysqli("localhost", "root", "", "lms");

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Define the new password
$newPasswordPlain = "Student@2026"; // You’ll use this password to log in later

// Hash the password using bcrypt with cost 12
$options = ['cost' => 12];
$hashedPassword = password_hash($newPasswordPlain, PASSWORD_BCRYPT, $options);

// Update all instructor passwords
$sql = "UPDATE users SET password = '$hashedPassword' WHERE role = 'student'";

if ($conn->query($sql) === TRUE) {
    echo "Passwords updated successfully!";
} else {
    echo "Error updating passwords: " . $conn->error;
}

$conn->close();
?>
