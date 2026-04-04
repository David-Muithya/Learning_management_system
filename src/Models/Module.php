<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Module
{
    private $db;
    private $table = 'modules';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Create a new module
     */
    public function create($courseId, $title, $description = null, $orderIndex = null)
    {
        if ($orderIndex === null) {
            // Get next order index
            $stmt = $this->db->prepare("SELECT MAX(order_index) as max_order FROM {$this->table} WHERE course_id = ?");
            $stmt->execute([$courseId]);
            $result = $stmt->fetch();
            $orderIndex = ($result['max_order'] ?? -1) + 1;
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (course_id, title, description, order_index, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        $result = $stmt->execute([$courseId, $title, $description, $orderIndex]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Get modules by course
     */
    public function getByCourse($courseId)
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   (SELECT COUNT(*) FROM materials WHERE module_id = m.id) as material_count
            FROM {$this->table} m
            WHERE m.course_id = ?
            ORDER BY m.order_index ASC
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get module by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Update module
     */
    public function update($id, $title, $description)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET title = ?, description = ?, updated_at = NOW()
            WHERE id = ?
        ");
        return $stmt->execute([$title, $description, $id]);
    }
    
    /**
     * Delete module
     */
    public function delete($id)
    {
        // First delete associated materials
        $materialModel = new Material();
        $materialModel->deleteByModule($id);
        
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Reorder modules
     */
    public function reorder($courseId, $orderIds)
    {
        foreach ($orderIds as $index => $moduleId) {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} SET order_index = ? WHERE id = ? AND course_id = ?
            ");
            $stmt->execute([$index, $moduleId, $courseId]);
        }
        return true;
    }
}