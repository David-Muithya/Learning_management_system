<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Assignment
{
    private $db;
    private $table = 'assignments';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Create a new assignment
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (course_id, instructor_id, title, description, due_date, max_points, created_at)
            VALUES (?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $data['course_id'],
            $data['instructor_id'],
            $data['title'],
            $data['description'] ?? null,
            $data['due_date'],
            $data['max_points'] ?? 100
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Get assignment by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.title as course_title, c.code as course_code,
                   CONCAT(u.first_name, ' ', u.last_name) as instructor_name
            FROM {$this->table} a
            LEFT JOIN courses c ON a.course_id = c.id
            LEFT JOIN users u ON a.instructor_id = u.id
            WHERE a.id = ? AND a.deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get assignments by course
     */
    public function getByCourse($courseId, $includeDeleted = false)
    {
        $sql = "SELECT * FROM {$this->table} WHERE course_id = ?";
        if (!$includeDeleted) {
            $sql .= " AND deleted_at IS NULL";
        }
        $sql .= " ORDER BY due_date ASC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get assignment with submission stats for instructor
     */
    public function getAssignmentWithStats($assignmentId, $instructorId)
    {
        $stmt = $this->db->prepare("
            SELECT a.*, c.title as course_title,
                   (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id) as total_submissions,
                   (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id AND status = 'graded') as graded_count,
                   (SELECT AVG(grade) FROM submissions WHERE assignment_id = a.id AND grade IS NOT NULL) as avg_grade
            FROM {$this->table} a
            LEFT JOIN courses c ON a.course_id = c.id
            WHERE a.id = ? AND a.instructor_id = ? AND a.deleted_at IS NULL
        ");
        $stmt->execute([$assignmentId, $instructorId]);
        return $stmt->fetch();
    }
    /**
     * Get assignments by instructor
     */
    public function getByInstructor($instructorId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT a.*, c.title as course_title, c.code as course_code,
                   (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id) as submission_count,
                   (SELECT COUNT(*) FROM submissions WHERE assignment_id = a.id AND status = 'graded') as graded_count,
                   (SELECT COUNT(*) FROM enrollments WHERE course_id = a.course_id AND status = 'active') as total_students
            FROM {$this->table} a
            LEFT JOIN courses c ON a.course_id = c.id
            WHERE a.instructor_id = ? AND a.deleted_at IS NULL
            ORDER BY a.due_date ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$instructorId, $perPage, $offset]);
        $assignments = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table} 
            WHERE instructor_id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$instructorId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'assignments' => $assignments,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get assignments for student (by course enrollment)
     */
    public function getForStudent($studentId, $status = null, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $params = [$studentId];
        
        $sql = "
            SELECT a.*, c.title as course_title, c.code as course_code,
                   s.id as submission_id, s.submitted_at, s.grade, s.status as submission_status,
                   CASE 
                       WHEN s.id IS NOT NULL THEN 'submitted'
                       WHEN a.due_date < NOW() THEN 'overdue'
                       ELSE 'pending'
                   END as assignment_status
            FROM {$this->table} a
            INNER JOIN courses c ON a.course_id = c.id
            INNER JOIN enrollments e ON c.id = e.course_id
            LEFT JOIN submissions s ON a.id = s.assignment_id AND s.student_id = ?
            WHERE e.student_id = ? AND e.status = 'active'
            AND a.deleted_at IS NULL
        ";
        $params[] = $studentId;
        
        if ($status) {
            if ($status === 'submitted') {
                $sql .= " AND s.id IS NOT NULL";
            } elseif ($status === 'pending') {
                $sql .= " AND s.id IS NULL AND a.due_date >= NOW()";
            } elseif ($status === 'overdue') {
                $sql .= " AND s.id IS NULL AND a.due_date < NOW()";
            }
        }
        
        $sql .= " ORDER BY a.due_date ASC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $assignments = $stmt->fetchAll();
        
        // Get total count (simplified for pagination)
        $countSql = "
            SELECT COUNT(*) as total
            FROM {$this->table} a
            INNER JOIN courses c ON a.course_id = c.id
            INNER JOIN enrollments e ON c.id = e.course_id
            WHERE e.student_id = ? AND e.status = 'active' AND a.deleted_at IS NULL
        ";
        $stmt = $this->db->prepare($countSql);
        $stmt->execute([$studentId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'assignments' => $assignments,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Update assignment
     */
    public function update($id, $data)
    {
        $fields = [];
        $params = [];
        
        $allowedFields = ['title', 'description', 'due_date', 'max_points'];
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (empty($fields)) {
            return false;
        }
        
        $params[] = $id;
        $sql = "UPDATE {$this->table} SET " . implode(', ', $fields) . " WHERE id = ?";
        
        $stmt = $this->db->prepare($sql);
        return $stmt->execute($params);
    }
    
    /**
     * Soft delete assignment
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET deleted_at = NOW() WHERE id = ?
        ");
        return $stmt->execute([$id]);
    }
    
    /**
     * Permanently delete assignment
     */
    public function forceDelete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Get upcoming assignments count for instructor
     */
    public function getUpcomingCount($instructorId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE instructor_id = ? AND due_date > NOW() AND deleted_at IS NULL
        ");
        $stmt->execute([$instructorId]);
        return $stmt->fetch()['count'];
    }
    
    /**
     * Get assignment statistics
     */
    public function getStats($courseId = null)
    {
        $sql = "
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN due_date < NOW() THEN 1 ELSE 0 END) as overdue,
                SUM(CASE WHEN due_date >= NOW() THEN 1 ELSE 0 END) as upcoming,
                AVG(max_points) as avg_max_points
            FROM {$this->table}
            WHERE deleted_at IS NULL
        ";
        
        if ($courseId) {
            $sql .= " AND course_id = ?";
            $stmt = $this->db->prepare($sql);
            $stmt->execute([$courseId]);
        } else {
            $stmt = $this->db->query($sql);
        }
        
        return $stmt->fetch();
    }
}