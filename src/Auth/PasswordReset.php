<?php
namespace SkillMaster\Auth;

use SkillMaster\Database\Connection;
use SkillMaster\Services\EmailService;
use SkillMaster\Helpers\Security;

class PasswordReset
{
    private $db;
    private $emailService;
    private $table = 'password_reset_tokens';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->emailService = new EmailService();
    }
    
    /**
     * Send password reset email
     */
    public function sendResetLink($email)
    {
        // Check if user exists
        $stmt = $this->db->prepare("SELECT id, first_name, last_name, email FROM users WHERE email = ? AND is_active = 1");
        $stmt->execute([$email]);
        $user = $stmt->fetch();
        
        if (!$user) {
            return ['success' => false, 'message' => 'No account found with that email address.'];
        }
        
        // Generate unique token
        $token = Security::generateToken(32);
        $expiresAt = date('Y-m-d H:i:s', strtotime('+1 hour'));
        
        // Delete any existing tokens for this user
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE user_id = ?");
        $stmt->execute([$user['id']]);
        
        // Create new token
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (user_id, token, expires_at, created_at)
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$user['id'], $token, $expiresAt]);
        
        // Send email
        $name = $user['first_name'] . ' ' . $user['last_name'];
        $emailSent = $this->emailService->sendPasswordReset($user['email'], $name, $token);
        
        if ($emailSent) {
            return ['success' => true, 'message' => 'Password reset link has been sent to your email.'];
        }
        
        return ['success' => false, 'message' => 'Failed to send reset email. Please try again later.'];
    }
    
    /**
     * Validate reset token
     */
    public function validateToken($token)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE token = ? AND expires_at > NOW() AND used_at IS NULL
        ");
        $stmt->execute([$token]);
        $resetToken = $stmt->fetch();
        
        if (!$resetToken) {
            return ['valid' => false, 'message' => 'Invalid or expired reset token.'];
        }
        
        // Get user info
        $stmt = $this->db->prepare("SELECT id, email, first_name, last_name FROM users WHERE id = ?");
        $stmt->execute([$resetToken['user_id']]);
        $user = $stmt->fetch();
        
        return [
            'valid' => true,
            'user' => $user,
            'token' => $resetToken
        ];
    }
    
    /**
     * Reset password
     */
    public function resetPassword($token, $newPassword, $confirmPassword)
    {
        // Validate token
        $validation = $this->validateToken($token);
        if (!$validation['valid']) {
            return $validation;
        }
        
        // Validate password
        if (strlen($newPassword) < PASSWORD_MIN_LENGTH) {
            return ['success' => false, 'message' => 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters.'];
        }
        
        if ($newPassword !== $confirmPassword) {
            return ['success' => false, 'message' => 'Passwords do not match.'];
        }
        
        // Hash new password
        $hashedPassword = Security::hashPassword($newPassword);
        
        $this->db->beginTransaction();
        
        try {
            // Update user password
            $stmt = $this->db->prepare("
                UPDATE users SET password = ?, must_change_password = 0, updated_at = NOW() 
                WHERE id = ?
            ");
            $stmt->execute([$hashedPassword, $validation['user']['id']]);
            
            // Mark token as used
            $stmt = $this->db->prepare("
                UPDATE {$this->table} SET used_at = NOW() WHERE token = ?
            ");
            $stmt->execute([$token]);
            
            // Delete any other tokens for this user
            $stmt = $this->db->prepare("
                DELETE FROM {$this->table} WHERE user_id = ? AND token != ?
            ");
            $stmt->execute([$validation['user']['id'], $token]);
            
            $this->db->commit();
            
            // Log activity
            $this->logPasswordReset($validation['user']['id']);
            
            return ['success' => true, 'message' => 'Password has been reset successfully. You can now login with your new password.'];
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Password reset failed: " . $e->getMessage());
            return ['success' => false, 'message' => 'Failed to reset password. Please try again.'];
        }
    }
    
    /**
     * Log password reset activity
     */
    private function logPasswordReset($userId)
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id, created_at)
            VALUES (?, 'password_reset', 'user', ?, NOW())
        ");
        $stmt->execute([$userId, $userId]);
    }
    
    /**
     * Check if token is valid and not expired
     */
    public function isTokenValid($token)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table} 
            WHERE token = ? AND expires_at > NOW() AND used_at IS NULL
        ");
        $stmt->execute([$token]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Clean up expired tokens
     */
    public function cleanExpiredTokens()
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} WHERE expires_at < NOW()
        ");
        return $stmt->execute();
    }
}