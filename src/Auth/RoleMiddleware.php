<?php
namespace SkillMaster\Auth;

class RoleMiddleware
{
    /**
     * Resolve a redirect target URL to the proper public route.
     */
    private static function resolveRedirectUrl($path)
    {
        if (stripos($path, 'http://') === 0 || stripos($path, 'https://') === 0) {
            return $path;
        }

        if (strpos($path, PUBLIC_URL) === 0) {
            return $path;
        }

        if (strpos($path, '/') === 0) {
            return rtrim(PUBLIC_URL, '/') . $path;
        }

        return rtrim(PUBLIC_URL, '/') . '/' . $path;
    }

    /**
     * Build the proper login redirect for each role.
     */
    private static function redirectToLogin($requiredRole)
    {
        $target = $requiredRole === 'admin' ? ADMIN_URL . '/login.php' : self::resolveRedirectUrl('/login.php');
        header('Location: ' . $target);
        exit;
    }

    /**
     * Determine the active session timeout for the requested role.
     */
    private static function getSessionTimeout($requiredRole)
    {
        if ($requiredRole === 'admin') {
            return defined('ADMIN_SESSION_TIMEOUT') ? ADMIN_SESSION_TIMEOUT : 1800;
        }

        return defined('SESSION_TIMEOUT') ? SESSION_TIMEOUT : 7200;
    }

    /**
     * Check if we have a valid authenticated session.
     */
    private static function hasValidSession($requiredRole)
    {
        if (session_status() !== PHP_SESSION_ACTIVE) {
            return false;
        }

        if (empty($_SESSION['user_id']) || empty($_SESSION['user_role']) || empty($_SESSION['login_time'])) {
            return false;
        }

        if ($requiredRole === 'admin' && empty($_SESSION['is_admin_authenticated'])) {
            return false;
        }

        $timeout = self::getSessionTimeout($requiredRole);
        if (time() - $_SESSION['login_time'] > $timeout) {
            return false;
        }

        return true;
    }

    /**
     * Detect if the current request is already targeting the login route.
     */
    private static function isLoginRoute($requiredRole)
    {
        if ($requiredRole !== 'admin') {
            return false;
        }

        $uri = $_SERVER['REQUEST_URI'] ?? '';
        return stripos($uri, '/admin/login.php') !== false;
    }

    /**
     * Check if user has required role
     */
    public static function check($requiredRole)
    {
        if (!self::hasValidSession($requiredRole)) {
            if (self::isLoginRoute($requiredRole)) {
                return;
            }

            self::redirectToLogin($requiredRole);
        }
        
        if ($_SESSION['user_role'] !== $requiredRole && $requiredRole !== 'any') {
            // Redirect to appropriate dashboard
            switch ($_SESSION['user_role']) {
                case 'admin':
                    header('Location: ' . ADMIN_URL . '/index.php');
                    break;
                case 'instructor':
                    header('Location: ' . INSTRUCTOR_URL . '/index.php');
                    break;
                case 'student':
                    header('Location: ' . STUDENT_URL . '/index.php');
                    break;
                default:
                    header('Location: ' . self::resolveRedirectUrl('/login.php'));
            }
            exit;
        }
    }
    
    /**
     * Check if user is admin
     */
    public static function isAdmin()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
    }
    
    /**
     * Check if user is instructor
     */
    public static function isInstructor()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'instructor';
    }
    
    /**
     * Check if user is student
     */
    public static function isStudent()
    {
        return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'student';
    }
    
    /**
     * Redirect if not logged in
     */
    public static function requireLogin()
    {
        if (!isset($_SESSION['user_id'])) {
            $_SESSION['redirect_after_login'] = $_SERVER['REQUEST_URI'];
            header('Location: ' . self::resolveRedirectUrl('/login.php'));
            exit;
        }
    }
    
    /**
     * Redirect if logged in (for login/register pages)
     */
    public static function requireGuest()
    {
        if (isset($_SESSION['user_id'])) {
            $redirect = (new Authenticator())->getRoleRedirect($_SESSION['user_role']);
            header('Location: ' . $redirect);
            exit;
        }
    }
}
