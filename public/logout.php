<?php
// Logout Handler
require_once __DIR__ . '/../config/config.php';
use SkillMaster\Auth\Authenticator;

$publicSession = defined('PUBLIC_SESSION_NAME') ? PUBLIC_SESSION_NAME : 'skillmaster_public_session';
$adminSession = defined('ADMIN_SESSION_NAME') ? ADMIN_SESSION_NAME : 'skillmaster_admin_session';

$auth = new Authenticator();

/**
 * Destroy the named session if the corresponding cookie exists.
 */
function logoutNamedSession(Authenticator $auth, string $sessionName): void
{
    if (empty($_COOKIE[$sessionName])) {
        return;
    }

    if (session_status() === PHP_SESSION_ACTIVE) {
        session_write_close();
    }

    session_name($sessionName);
    session_start();
    $auth->logout();
}

logoutNamedSession($auth, $adminSession);
logoutNamedSession($auth, $publicSession);

// Redirect to home page
header('Location: index.php');
exit;
