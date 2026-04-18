<?php
namespace SkillMaster\Auth;

use SkillMaster\Models\User;
use SkillMaster\Services\ActivityLogger;

class Authenticator
{
    private $userModel;
    private $logger;
    
    public function __construct()
    {
        $this->userModel = new User();
        $this->logger = new ActivityLogger();
    }
    
    /**
     * Attempt to log in a user
     */
    public function login($email, $password, $remember = false)
    {
        // Clear any existing session data to prevent conflicts
        $this->clearSession();
        
        $user = $this->userModel->findByEmail($email);
        
        if (!$user) {
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        if (!$user['is_active']) {
            return ['success' => false, 'message' => 'Account inactive. Please contact the site administrator if you believe this is a mistake.'];
        }
        
        // Verify password
        if (!password_verify($password, $user['password'])) {
            $this->logger->log($user['id'], 'failed_login', 'user', $user['id']);
            return ['success' => false, 'message' => 'Invalid email or password'];
        }
        
        // Check if password needs to be changed
        if ($user['must_change_password']) {
            $_SESSION['force_password_change'] = true;
            $_SESSION['user_id'] = $user['id'];
            return ['success' => false, 'redirect' => 'reset-password.php', 'message' => 'Please change your password'];
        }
        
        // Set session variables
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_name'] = $user['first_name'] . ' ' . $user['last_name'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_avatar'] = $user['profile_pic'];
        $_SESSION['login_time'] = time();
        $_SESSION['is_admin_authenticated'] = $user['role'] === 'admin';

        // Debug logging
        error_log("Login successful for user: {$user['email']} (Role: {$user['role']})");

        // Update last login
        $this->userModel->updateLastLogin($user['id']);
        
        // Log activity
        $this->logger->log($user['id'], 'login', 'user', $user['id']);
        
        // Determine redirect based on role
        $redirect = $this->getRoleRedirect($user['role']);
        
        error_log("Login redirect to: $redirect");
        
        return ['success' => true, 'redirect' => $redirect, 'user' => $user];
    }
    
    /**
     * Clear current session data
     */
    private function clearSession()
    {
        // Clear all session variables except those we want to preserve
        $preserve = [];
        foreach ($_SESSION as $key => $value) {
            if (!in_array($key, $preserve)) {
                unset($_SESSION[$key]);
            }
        }
    }
    
    /**
     * Check if user is logged in
     */
    public function isLoggedIn()
    {
        return isset($_SESSION['user_id']) && !empty($_SESSION['user_id']);
    }
    
    /**
     * Get current logged in user
     */
    public function getCurrentUser()
    {
        if (!$this->isLoggedIn()) {
            return null;
        }
        
        return $this->userModel->findById($_SESSION['user_id']);
    }

    /**
     * Log the current user out and destroy the session.
     */
    public function logout()
    {
        if ($this->isLoggedIn()) {
            $this->logger->log($_SESSION['user_id'], 'logout', 'user', $_SESSION['user_id']);
        }

        $_SESSION = [];

        if (ini_get('session.use_cookies')) {
            $params = session_get_cookie_params();
            setcookie(session_name(), '', time() - 42000,
                $params['path'], $params['domain'],
                $params['secure'], $params['httponly']
            );
        }

        session_destroy();
    }
    
    /**
     * Get redirect URL based on user role
     */
    public function getRoleRedirect($role)
    {
        switch ($role) {
            case 'admin':
                return ADMIN_URL . '/index.php';
            case 'instructor':
                return INSTRUCTOR_URL . '/index.php';
            case 'student':
                return STUDENT_URL . '/index.php';
            default:
                return PUBLIC_URL . '/index.php';
        }
    }
    /**
     * Register a new student
     */
    public function registerStudent($data)
    {
        // Validate email uniqueness
        if ($this->userModel->emailExists($data['email'])) {
            return ['success' => false, 'message' => 'Email already registered'];
        }
        
        // Create student
        $result = $this->userModel->createStudent($data);
        
        if ($result) {
            $user = $this->userModel->findByEmail($data['email']);
            $this->logger->log($user['id'], 'register', 'user', $user['id']);
            return ['success' => true, 'message' => 'Registration successful! Please login.'];
        }
        
        return ['success' => false, 'message' => 'Registration failed. Please try again.'];
    }
}
