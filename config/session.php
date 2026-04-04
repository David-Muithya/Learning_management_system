<?php
// Session Management
// Handles session security and regeneration

class Session
{
    /**
     * Start session with secure settings
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            ini_set('session.cookie_httponly', 1);
            ini_set('session.use_only_cookies', 1);
            if (isset($_SERVER['HTTPS'])) {
                ini_set('session.cookie_secure', 1);
            }
            
            session_start();
        }
    }
    
    /**
     * Regenerate session ID to prevent fixation
     */
    public static function regenerate()
    {
        session_regenerate_id(true);
    }
    
    /**
     * Set session variable
     */
    public static function set($key, $value)
    {
        $_SESSION[$key] = $value;
    }
    
    /**
     * Get session variable
     */
    public static function get($key, $default = null)
    {
        return $_SESSION[$key] ?? $default;
    }
    
    /**
     * Check if session variable exists
     */
    public static function has($key)
    {
        return isset($_SESSION[$key]);
    }
    
    /**
     * Delete session variable
     */
    public static function delete($key)
    {
        unset($_SESSION[$key]);
    }
    
    /**
     * Destroy entire session
     */
    public static function destroy()
    {
        $_SESSION = [];
        
        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params["path"], $params["domain"],
                $params["secure"], $params["httponly"]
            );
        }
        
        session_destroy();
    }
    
    /**
     * Set flash message (temporary session data)
     */
    public static function setFlash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Get and delete flash message
     */
    public static function getFlash($key, $default = null)
    {
        $value = $_SESSION['_flash'][$key] ?? $default;
        unset($_SESSION['_flash'][$key]);
        return $value;
    }
    
    /**
     * Check if flash message exists
     */
    public static function hasFlash($key)
    {
        return isset($_SESSION['_flash'][$key]);
    }
}