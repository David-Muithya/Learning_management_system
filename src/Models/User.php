<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use PDO;

class User
{
    private $db;
    private $table = 'users';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Find user by email
     */

        /**
     * Get database connection
     */
    public function getDB()
    {
        return $this->db;
    }
    public function findByEmail($email)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE email = ? AND deleted_at IS NULL");
        $stmt->execute([$email]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by username
     */
    public function findByUsername($username)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE username = ? AND deleted_at IS NULL");
        $stmt->execute([$username]);
        return $stmt->fetch();
    }
    
    /**
     * Find user by ID
     */
    public function findById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Create a new student user
     */
    public function createStudent($data)
    {
        $username = strtolower($data['first_name'] . '.' . $data['last_name']);
        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_COST]);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (username, email, password, role, first_name, last_name, phone_number, is_active, created_by)
            VALUES (?, ?, ?, 'student', ?, ?, ?, 1, NULL)
        ");
        
        return $stmt->execute([
            $username,
            $data['email'],
            $password,
            $data['first_name'],
            $data['last_name'],
            $data['phone_number'] ?? null
        ]);
    }
    
    /**
     * Create instructor from approved application
     */
    public function createInstructor($data)
    {
        $username = strtolower($data['first_name'] . '.' . $data['last_name']);
        $password = password_hash($data['password'], PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_COST]);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (username, email, password, role, first_name, last_name, bio, phone_number, is_active, created_by)
            VALUES (?, ?, ?, 'instructor', ?, ?, ?, ?, 1, ?)
        ");
        
        return $stmt->execute([
            $username,
            $data['email'],
            $password,
            $data['first_name'],
            $data['last_name'],
            $data['bio'] ?? null,
            $data['phone_number'] ?? null,
            $data['created_by'] ?? null
        ]);
    }
    
    /**
     * Update last login timestamp
     */
    public function updateLastLogin($userId)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET last_login = NOW() WHERE id = ?");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Update user password
     */
    public function updatePassword($userId, $newPassword)
    {
        $password = password_hash($newPassword, PASSWORD_BCRYPT, ['cost' => PASSWORD_BCRYPT_COST]);
        $stmt = $this->db->prepare("UPDATE {$this->table} SET password = ?, must_change_password = 0 WHERE id = ?");
        return $stmt->execute([$password, $userId]);
    }
    
    /**
     * Update user profile
     */
    public function updateProfile($userId, $data)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET 
                first_name = ?, 
                last_name = ?, 
                phone_number = ?, 
                address = ?, 
                bio = ?, 
                facebook_link = ?, 
                twitter_link = ?, 
                linkedin_link = ?,
                updated_at = NOW()
            WHERE id = ?
        ");
        
        return $stmt->execute([
            $data['first_name'] ?? null,
            $data['last_name'] ?? null,
            $data['phone_number'] ?? null,
            $data['address'] ?? null,
            $data['bio'] ?? null,
            $data['facebook_link'] ?? null,
            $data['twitter_link'] ?? null,
            $data['linkedin_link'] ?? null,
            $userId
        ]);
    }
    
    /**
     * Update user profile picture
     */
    public function updateProfilePicture($userId, $filename)
    {
        $stmt = $this->db->prepare("UPDATE {$this->table} SET profile_pic = ?, updated_at = NOW() WHERE id = ?");
        return $stmt->execute([$filename, $userId]);
    }
    
    /**
     * Check if email already exists
     */
    public function emailExists($email)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE email = ?");
        $stmt->execute([$email]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Check if username already exists
     */
    public function usernameExists($username)
    {
        $stmt = $this->db->prepare("SELECT COUNT(*) FROM {$this->table} WHERE username = ?");
        $stmt->execute([$username]);
        return $stmt->fetchColumn() > 0;
    }
    
    /**
     * Get recent users for admin dashboard
     */
    public function getRecentUsers($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT id, username, email, first_name, last_name, role, created_at 
            FROM {$this->table} 
            WHERE deleted_at IS NULL
            ORDER BY created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all active instructors
     */
    public function getActiveInstructors($limit = null)
    {
        $sql = "SELECT * FROM {$this->table} WHERE role = 'instructor' AND is_active = 1 AND deleted_at IS NULL ORDER BY created_at DESC";
        if ($limit) {
            $sql .= " LIMIT " . intval($limit);
        }
        $stmt = $this->db->query($sql);
        return $stmt->fetchAll();
    }
    
    /**
     * Get system statistics
     */
    public function getStats()
    {
        $stmt = $this->db->query("
            SELECT 
                (SELECT COUNT(*) FROM {$this->table} WHERE role = 'student' AND is_active = 1) as total_students,
                (SELECT COUNT(*) FROM {$this->table} WHERE role = 'instructor' AND is_active = 1) as total_instructors,
                (SELECT COUNT(*) FROM courses WHERE status = 'published') as total_courses,
                (SELECT COUNT(*) FROM enrollments WHERE status = 'active') as total_enrollments
        ");
        return $stmt->fetch();
    }
}