// Create test file: public/test-helpers.php
<?php
require_once __DIR__ . '/../config/config.php';

use SkillMaster\Helpers\Security;
use SkillMaster\Helpers\Validation;
use SkillMaster\Services\EmailService;

echo "<h1>Testing Helper Classes</h1>";

// Test CSRF
$token = Security::generateCsrfToken();
echo "<p>CSRF Token: " . $token . "</p>";

// Test validation
$email = "test@example.com";
echo "<p>Email validation: " . (Validation::email($email) ? "✅ Valid" : "❌ Invalid") . "</p>";

// Test password
$password = "Test1234";
$result = Validation::password($password);
echo "<p>Password validation: " . ($result === true ? "✅ Valid" : "❌ " . $result) . "</p>";

// Test email service (if configured)
$emailService = new EmailService();
echo "<p>Email service configured: " . ($emailService->isConfigured() ? "✅ Yes" : "❌ No") . "</p>";