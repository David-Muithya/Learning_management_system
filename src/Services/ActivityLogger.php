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
     * Log user activity
     */
    public function log($userId, $action, $entityType = null, $entityId = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO activity_logs (user_id, action, entity_type, entity_id) 
            VALUES (?, ?, ?, ?)
        ");
        return $stmt->execute([$userId, $action, $entityType, $entityId]);
    }
}