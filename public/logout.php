<?php
// Logout Handler
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Auth\Authenticator;

$auth = new Authenticator();
$auth->logout();

// Redirect to home page
header('Location: index.php');
exit;