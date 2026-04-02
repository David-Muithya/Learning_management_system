<?php
namespace SkillMaster\Helpers;

class Session
{
    /**
     * Start session if not already started
     */
    public static function start()
    {
        if (session_status() === PHP_SESSION_NONE) {
            session_start();
        }
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
     * Set flash message (deleted after retrieval)
     */
    public static function setFlash($key, $value)
    {
        $_SESSION['_flash'][$key] = $value;
    }
    
    /**
     * Get flash message and delete it
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
    
    /**
     * Regenerate session ID (prevents fixation)
     */
    public static function regenerate()
    {
        session_regenerate_id(true);
    }
    
    /**
     * Get current session ID
     */
    public static function getId()
    {
        return session_id();
    }
}