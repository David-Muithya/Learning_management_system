<?php
namespace SkillMaster\Services;

use SkillMaster\Database\Connection;

class ActivityLogger
{
    private $db;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Check if a user exists
     */
    private function userExists($userId)
    {
        if (empty($userId) || !is_numeric($userId)) {
            return false;
        }

        $stmt = $this->db->prepare("SELECT 1 FROM users WHERE id = ? LIMIT 1");
        $stmt->execute([$userId]);
        return (bool) $stmt->fetchColumn();
    }

    /**
     * Log user activity
     */
    public function log($userId, $action, $entityType = null, $entityId = null)
    {
        if (!$this->userExists($userId)) {
            $userId = null;
        }

        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id)
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $action, $entityType, $entityId]);
    }
}