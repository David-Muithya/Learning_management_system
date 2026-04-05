<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Attendance
{
    private $db;
    private $table = 'attendance';
    
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
     * Mark attendance for a student
     */
    public function markAttendance($courseId, $studentId, $date, $status, $markedBy)
    {
        // Check if attendance already exists for this date
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table} 
            WHERE course_id = ? AND student_id = ? AND date = ?
        ");
        $stmt->execute([$courseId, $studentId, $date]);
        
        if ($stmt->fetch()) {
            // Update existing record
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = ?, marked_by = ?, created_at = NOW()
                WHERE course_id = ? AND student_id = ? AND date = ?
            ");
            return $stmt->execute([$status, $markedBy, $courseId, $studentId, $date]);
        } else {
            // Insert new record
            $stmt = $this->db->prepare("
                INSERT INTO {$this->table} (course_id, student_id, date, status, marked_by, created_at)
                VALUES (?, ?, ?, ?, ?, NOW())
            ");
            return $stmt->execute([$courseId, $studentId, $date, $status, $markedBy]);
        }
    }
    
    /**
     * Get attendance records for a student in a course
     */
    public function getByStudentAndCourse($studentId, $courseId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE student_id = ? AND course_id = ?
            ORDER BY date DESC
        ");
        $stmt->execute([$studentId, $courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get attendance statistics
     */
    public function getStats($courseId, $studentId = null)
    {
        $sql = "
            SELECT 
                SUM(CASE WHEN status = 'present' THEN 1 ELSE 0 END) as present,
                SUM(CASE WHEN status = 'absent' THEN 1 ELSE 0 END) as absent,
                SUM(CASE WHEN status = 'late' THEN 1 ELSE 0 END) as late,
                SUM(CASE WHEN status = 'excused' THEN 1 ELSE 0 END) as excused,
                COUNT(*) as total
            FROM {$this->table}
            WHERE course_id = ?
        ";
        
        if ($studentId) {
            $sql .= " AND student_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId, $studentId]);
        } else {
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
        }
        
        $result = $stmt->fetch();
        return [
            'present' => $result['present'] ?? 0,
            'absent' => $result['absent'] ?? 0,
            'late' => $result['late'] ?? 0,
            'excused' => $result['excused'] ?? 0,
            'total' => $result['total'] ?? 0,
            'percentage' => ($result['total'] ?? 0) > 0 ? round((($result['present'] ?? 0) / ($result['total'] ?? 0)) * 100) : 0
        ];
    }
    
    /**
     * Delete attendance record
     */
    public function delete($id, $courseId, $studentId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM {$this->table} 
            WHERE id = ? AND course_id = ? AND student_id = ?
        ");
        return $stmt->execute([$id, $courseId, $studentId]);
    }
}