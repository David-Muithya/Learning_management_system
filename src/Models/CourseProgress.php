<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class CourseProgress
{
    private $db;
    private $table = 'course_progress';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Get progress for a student in a course
     */
    public function getStudentProgress($studentId, $courseId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total_materials,
                   SUM(CASE WHEN is_completed = 1 THEN 1 ELSE 0 END) as completed_materials
            FROM {$this->table}
            WHERE student_id = ? AND course_id = ?
        ");
        $stmt->execute([$studentId, $courseId]);
        $result = $stmt->fetch();
        
        $percentage = 0;
        if ($result && $result['total_materials'] > 0) {
            $percentage = round(($result['completed_materials'] / $result['total_materials']) * 100);
        }
        
        return [
            'total_materials' => $result['total_materials'] ?? 0,
            'completed_materials' => $result['completed_materials'] ?? 0,
            'percentage' => $percentage
        ];
    }
    
    /**
     * Mark material as completed
     */
    public function markCompleted($studentId, $courseId, $materialId)
    {
        // Check if already marked
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table}
            WHERE student_id = ? AND course_id = ? AND material_id = ?
        ");
        $stmt->execute([$studentId, $courseId, $materialId]);
        
        if ($stmt->fetch()) {
            return true; // Already completed
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (student_id, course_id, material_id, is_completed, completed_at)
            VALUES (?, ?, ?, 1, NOW())
        ");
        return $stmt->execute([$studentId, $courseId, $materialId]);
    }
}