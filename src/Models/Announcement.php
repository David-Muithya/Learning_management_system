<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Announcement
{
    private $db;
    private $table = 'announcements';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Create a new announcement
     */
    public function create($courseId, $postedBy, $title, $content, $priority = 'normal')
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (course_id, posted_by, title, content, priority, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        return $stmt->execute([$courseId, $postedBy, $title, $content, $priority]);
    }
    
    /**
     * Get announcement by ID
     */
    public function getById($id, $courseId = null)
    {
        $sql = "SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as posted_by_name
                FROM {$this->table} a
                LEFT JOIN users u ON a.posted_by = u.id
                WHERE a.id = ?";
        
        if ($courseId) {
            $sql .= " AND a.course_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id, $courseId]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$id]);
        }
        
        return $stmt->fetch();
    }
    
    /**
     * Get announcements by course
     */
    public function getByCourse($courseId, $limit = 20, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, CONCAT(u.first_name, ' ', u.last_name) as posted_by_name
            FROM {$this->table} a
            LEFT JOIN users u ON a.posted_by = u.id
            WHERE a.course_id = ?
            ORDER BY a.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$courseId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get announcement count by course
     */
    public function getCountByCourse($courseId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table} WHERE course_id = ?
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetch()['total'];
    }
    
    /**
     * Update announcement
     */
    public function update($id, $courseId, $title, $content, $priority)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET title = ?, content = ?, priority = ?, updated_at = NOW()
            WHERE id = ? AND course_id = ?
        ");
        return $stmt->execute([$title, $content, $priority, $id, $courseId]);
    }
    
    /**
     * Delete announcement
     */
    public function delete($id, $courseId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} WHERE id = ? AND course_id = ?
        ");
        return $stmt->execute([$id, $courseId]);
    }
    
    /**
     * Get recent announcements for dashboard
     */
    public function getRecentForInstructor($instructorId, $limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.title as course_title
            FROM {$this->table} a
            LEFT JOIN courses c ON a.course_id = c.id
            WHERE c.instructor_id = ?
            ORDER BY a.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$instructorId, $limit]);
        return $stmt->fetchAll();
    }
}