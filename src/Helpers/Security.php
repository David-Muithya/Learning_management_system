<?php
namespace SkillMaster\Helpers;

class Security
{
    /**
     * Generate CSRF token
     */
    public static function generateCsrfToken()
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME])) {
            $_SESSION[CSRF_TOKEN_NAME] = bin2hex(random_bytes(32));
        }
        return $_SESSION[CSRF_TOKEN_NAME];
    }
    
    /**
     * Verify CSRF token
     */
    public static function verifyCsrfToken($token)
    {
        if (!isset($_SESSION[CSRF_TOKEN_NAME]) || empty($token)) {
            return false;
        }
        return hash_equals($_SESSION[CSRF_TOKEN_NAME], $token);
    }
    
    /**
     * Output CSRF hidden field
     */
    public static function csrfField()
    {
        $token = self::generateCsrfToken();
        return '<input type="hidden" name="' . CSRF_TOKEN_NAME . '" value="' . $token . '">';
    }
    
    /**
     * Hash password
     */
    public static function hashPassword($password)
    {
        return password_hash($password, PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_COST]);
    }
    
    /**
     * Verify password
     */
    public static function verifyPassword($password, $hash)
    {
        return password_verify($password, $hash);
    }
    
    /**
     * Generate random token
     */
    public static function generateToken($length = 32)
    {
        return bin2hex(random_bytes($length));
    }
    
    /**
     * Escape output for HTML
     */
    public static function escape($string)
    {
        return htmlspecialchars($string, ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Escape array for HTML
     */
    public static function escapeArray($data)
    {
        $escaped = [];
        foreach ($data as $key => $value) {
            $escaped[$key] = self::escape($value);
        }
        return $escaped;
    }
    
    /**
     * Prevent XSS attacks
     */
    public static function sanitizeInput($input)
    {
        return strip_tags(trim($input));
    }
    
    /**
     * Check if request is AJAX
     */
    public static function isAjax()
    {
        return !empty($_SERVER['HTTP_X_REQUESTED_WITH']) && 
               strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest';
    }
    
    /**
     * Get client IP address
     */
    public static function getClientIP()
    {
        $ipHeaders = [
            'HTTP_CLIENT_IP',
            'HTTP_X_FORWARDED_FOR',
            'HTTP_X_FORWARDED',
            'HTTP_X_CLUSTER_CLIENT_IP',
            'HTTP_FORWARDED_FOR',
            'HTTP_FORWARDED',
            'REMOTE_ADDR'
        ];
        
        foreach ($ipHeaders as $header) {
            if (!empty($_SERVER[$header])) {
                $ips = explode(',', $_SERVER[$header]);
                $ip = trim($ips[0]);
                if (filter_var($ip, FILTER_VALIDATE_IP)) {
                    return $ip;
                }
            }
        }
        
        return '0.0.0.0';
    }
    
    /**
     * Rate limiting for login attempts
     */
    public static function checkLoginAttempts($email)
    {
        $key = 'login_attempts_' . md5($email);
        $attempts = isset($_SESSION[$key]) ? $_SESSION[$key] : 0;
        
        if ($attempts >= MAX_LOGIN_ATTEMPTS) {
            $lockoutTime = isset($_SESSION[$key . '_time']) ? $_SESSION[$key . '_time'] : 0;
            if (time() - $lockoutTime < LOGIN_LOCKOUT_TIME * 60) {
                return false;
            }
            // Reset after lockout
            unset($_SESSION[$key]);
            unset($_SESSION[$key . '_time']);
        }
        
        return true;
    }
    
    /**
     * Increment login attempts
     */
    public static function incrementLoginAttempts($email)
    {
        $key = 'login_attempts_' . md5($email);
        $_SESSION[$key] = ($_SESSION[$key] ?? 0) + 1;
        
        if (!isset($_SESSION[$key . '_time'])) {
            $_SESSION[$key . '_time'] = time();
        }
    }
    
    /**
     * Reset login attempts
     */
    public static function resetLoginAttempts($email)
    {
        $key = 'login_attempts_' . md5($email);
        unset($_SESSION[$key]);
        unset($_SESSION[$key . '_time']);
    }
}