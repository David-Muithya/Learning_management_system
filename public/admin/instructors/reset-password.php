<?php
// Reset Instructor Password
require_once __DIR__ . '/../../../config/config.php';
use SkillMaster\Auth\RoleMiddleware;
use SkillMaster\Models\User;
use SkillMaster\Helpers\Security;

RoleMiddleware::check('admin');

$userId = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$userModel = new User();
$user = $userModel->findById($userId);

if (!$user || $user['role'] !== 'instructor') {
    header('Location: list.php');
    exit;
}

$newPassword = 'Instructor@2026';
$hashedPassword = Security::hashPassword($newPassword);

$db = $userModel->getDB();
$stmt = $db->prepare("UPDATE users SET password = ?, must_change_password = 1 WHERE id = ?");
$result = $stmt->execute([$hashedPassword, $userId]);

if ($result) {
    $_SESSION['success'] = "Password reset to: {$newPassword}. Instructor must change it on next login.";
} else {
    $_SESSION['error'] = "Failed to reset password.";
}

header("Location: edit.php?id={$userId}");
exit;