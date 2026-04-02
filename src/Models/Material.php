<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Material
{
    private $db;
    private $table = 'materials';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Create a new material
     */
    public function create($moduleId, $title, $type, $filePath = null, $contentUrl = null, $description = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (module_id, title, description, type, file_path, content_url, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$moduleId, $title, $description, $type, $filePath, $contentUrl]);
    }
    
    /**
     * Get materials by module
     */
    public function getByModule($moduleId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} WHERE module_id = ? ORDER BY order_index ASC
        ");
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get material by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Delete material
     */
    public function delete($id)
    {
        $material = $this->getById($id);
        if ($material && $material['file_path'] && file_exists($material['file_path'])) {
            unlink($material['file_path']);
        }
        
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Delete all materials in a module
     */
    public function deleteByModule($moduleId)
    {
        $materials = $this->getByModule($moduleId);
        foreach ($materials as $material) {
            $this->delete($material['id']);
        }
        return true;
    }
}