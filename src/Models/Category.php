<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Category
{
    private $db;
    private $table = 'course_categories';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Get database connection
     */
    public function getDB()
    {
        return $this->db;
    }
    
    /**
     * Get all categories
     */
    public function getAll($limit = null, $offset = 0)
    {
        $sql = "
            SELECT c.*, 
                   (SELECT COUNT(*) FROM courses WHERE category_id = c.id AND status = 'published') as course_count,
                   p.name as parent_name
            FROM {$this->table} c
            LEFT JOIN {$this->table} p ON c.parent_id = p.id
            ORDER BY c.name ASC
        ";
        
        if ($limit) {
            $sql .= " LIMIT ? OFFSET ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$limit, $offset]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute();
        }
        
        return $stmt->fetchAll();
    }
    
    /**
     * Get category by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get category by slug
     */
    public function getBySlug($slug)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch();
    }
    
    /**
     * Create new category
     */
    public function create($name, $slug, $description = null, $parentId = null)
    {
        // Generate slug if not provided
        if (empty($slug)) {
            $slug = $this->generateSlug($name);
        }
        
        // Ensure slug is unique
        $slug = $this->makeSlugUnique($slug);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, slug, description, parent_id, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$name, $slug, $description, $parentId ?: null]);
    }
    
    /**
     * Update category
     */
    public function update($id, $name, $slug, $description = null, $parentId = null)
    {
        // Generate slug if not provided
        if (empty($slug)) {
            $slug = $this->generateSlug($name);
        }
        
        // Get current category to check slug uniqueness
        $current = $this->getById($id);
        if ($current['slug'] !== $slug) {
            $slug = $this->makeSlugUnique($slug);
        }
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET name = ?, slug = ?, description = ?, parent_id = ?
            WHERE id = ?
        ");
        return $stmt->execute([$name, $slug, $description, $parentId ?: null, $id]);
    }
    
    /**
     * Delete category
     */
    public function delete($id)
    {
        // First, update courses to remove category reference
        $stmt = $this->db->prepare("UPDATE courses SET category_id = NULL WHERE category_id = ?");
        $stmt->execute([$id]);
        
        // Then delete the category
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get total count
     */
    public function getCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        return $stmt->fetch()['total'];
    }
    
    /**
     * Generate slug from name
     */
    private function generateSlug($name)
    {
        $slug = strtolower(trim($name));
        $slug = preg_replace('/[^a-z0-9-]/', '-', $slug);
        $slug = preg_replace('/-+/', '-', $slug);
        return trim($slug, '-');
    }
    
    /**
     * Make slug unique
     */
    private function makeSlugUnique($slug)
    {
        $originalSlug = $slug;
        $counter = 1;
        
        while ($this->slugExists($slug)) {
            $slug = $originalSlug . '-' . $counter;
            $counter++;
        }
        
        return $slug;
    }
    
    /**
     * Check if slug exists
     */
    private function slugExists($slug)
    {
        $stmt = $this->db->prepare("SELECT id FROM {$this->table} WHERE slug = ?");
        $stmt->execute([$slug]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get categories for dropdown
     */
    public function getForDropdown($excludeId = null)
    {
        $sql = "SELECT id, name FROM {$this->table} ORDER BY name ASC";
        if ($excludeId) {
            $sql .= " WHERE id != ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$excludeId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        return $stmt->fetchAll();
    }
}