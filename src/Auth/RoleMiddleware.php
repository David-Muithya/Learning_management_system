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
     * Check if user has required role
     */
    public static function check($requiredRole)
    {
        if (!isset($_SESSION['user_role'])) {
            header('Location: ' . self::resolveRedirectUrl('/login.php'));
            exit;
        }
        
        if ($_SESSION['user_role'] !== $requiredRole && $requiredRole !== 'any') {
            // Redirect to appropriate dashboard
            switch ($_SESSION['user_role']) {
                case 'admin':
                    header('Location: ' . self::resolveRedirectUrl('/admin/'));
                    break;
                case 'instructor':
                    header('Location: ' . self::resolveRedirectUrl('/instructor/'));
                    break;
                case 'student':
                    header('Location: ' . self::resolveRedirectUrl('/student/'));
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