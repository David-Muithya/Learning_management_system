<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use SkillMaster\Services\NotificationService;

class Enrollment
{
    private $db;
    private $table = 'enrollments';
    private $notificationService;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->notificationService = new NotificationService();
    }
     /**
     * Get database connection for use in other methods
     */
    public function getDB()
    {
        return $this->db;
    }
    /**
     * Create enrollment record
     */
    public function create($studentId, $courseId, $enrolledBy = null)
    {
        // Check if already enrolled
        if ($this->isEnrolled($studentId, $courseId)) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (student_id, course_id, enrolled_by, enrollment_date, status)
            VALUES (?, ?, ?, CURDATE(), 'pending_payment')
        ");
        
        $result = $stmt->execute([$studentId, $courseId, $enrolledBy ?: $studentId]);
        
        if ($result) {
            $enrollmentId = $this->db->lastInsertId();
            return $enrollmentId;
        }
        
        return false;
    }
    
    /**
     * Check if student is enrolled
     */
        /**
     * Get students by course for instructor
     */
    public function getStudentsByCourse($courseId, $instructorId, $page = 1, $perPage = 20)
    {
        // Verify instructor owns the course
        $stmt = $this->db->prepare("
            SELECT id FROM courses WHERE id = ? AND instructor_id = ?
        ");
        $stmt->execute([$courseId, $instructorId]);
        if (!$stmt->fetch()) {
            return ['students' => [], 'total' => 0];
        }
        
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT e.*, u.first_name, u.last_name, u.email, u.profile_pic,
                   u.phone_number, u.last_login
            FROM {$this->table} e
            LEFT JOIN users u ON e.student_id = u.id
            WHERE e.course_id = ? AND e.status = 'active'
            ORDER BY e.enrolled_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$courseId, $perPage, $offset]);
        $students = $stmt->fetchAll();
        
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table}
            WHERE course_id = ? AND status = 'active'
        ");
        $stmt->execute([$courseId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'students' => $students,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    public function isEnrolled($studentId, $courseId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table} 
            WHERE student_id = ? AND course_id = ? 
            AND status NOT IN ('dropped', 'rejected')
        ");
        $stmt->execute([$studentId, $courseId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get enrollment by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, c.title as course_title, c.price, 
                   CONCAT(u.first_name, ' ', u.last_name) as student_name
            FROM {$this->table} e
            LEFT JOIN courses c ON e.course_id = c.id
            LEFT JOIN users u ON e.student_id = u.id
            WHERE e.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get enrollments by student
     */
    public function getByStudent($studentId, $status = null, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        $params = [$studentId];
        
        $sql = "
            SELECT e.*, c.title as course_title, c.thumbnail, c.credits,
                   CONCAT(u.first_name, ' ', u.last_name) as instructor_name
            FROM {$this->table} e
            LEFT JOIN courses c ON e.course_id = c.id
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE e.student_id = ?
        ";
        
        if ($status) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY e.enrolled_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $enrollments = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table} WHERE student_id = ?";
        $countParams = [$studentId];
        
        if ($status) {
            $countSql .= " AND status = ?";
            $countParams[] = $status;
        }
        
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($countParams);
        $total = $stmt->fetch()['total'];
        
        return [
            'enrollments' => $enrollments,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get enrollments by course
     */
    public function getByCourse($courseId, $status = null)
    {
        $params = [$courseId];
        
        $sql = "
            SELECT e.*, CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email
            FROM {$this->table} e
            LEFT JOIN users u ON e.student_id = u.id
            WHERE e.course_id = ?
        ";
        
        if ($status) {
            $sql .= " AND e.status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY e.enrolled_at DESC";
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        return $stmt->fetchAll();
    }
    
    /**
     * Update enrollment status
     */
    public function updateStatus($id, $status)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET status = ? WHERE id = ?
        ");
        return $stmt->execute([$status, $id]);
    }
    
    /**
     * Verify enrollment after payment
     */
    public function verifyEnrollment($id, $mockPaymentId = null)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = 'active', mock_payment_id = ?, mock_payment_status = 'verified'
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$mockPaymentId, $id]);
        
        if ($result) {
            // Update course enrollment count
            $enrollment = $this->getById($id);
            if ($enrollment) {
                $this->incrementCourseEnrollmentCount($enrollment['course_id']);
            }
        }
        
        return $result;
    }
    
    /**
     * Increment course enrollment count
     */
    private function incrementCourseEnrollmentCount($courseId)
    {
        $stmt = $this->db->prepare("
            UPDATE courses SET enrollment_count = enrollment_count + 1 
            WHERE id = ?
        ");
        $stmt->execute([$courseId]);
    }
    
    /**
     * Complete enrollment (course finished)
     */
    public function complete($id, $finalGrade = null)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = 'completed', completed_at = NOW(), completion_date = CURDATE(), final_grade = ?
            WHERE id = ?
        ");
        return $stmt->execute([$finalGrade, $id]);
    }
    
    /**
     * Drop enrollment
     */
    public function drop($id)
    {
        $enrollment = $this->getById($id);
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET status = 'dropped' WHERE id = ?
        ");
        $result = $stmt->execute([$id]);
        
        if ($result && $enrollment) {
            // Decrement course enrollment count
            $stmt = $this->db->prepare("
                UPDATE courses SET enrollment_count = GREATEST(enrollment_count - 1, 0) 
                WHERE id = ?
            ");
            $stmt->execute([$enrollment['course_id']]);
        }
        
        return $result;
    }
    
    /**
     * Get pending enrollments for admin verification
     */
    public function getPendingVerification($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT e.*, c.title as course_title, c.price,
                   CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email
            FROM {$this->table} e
            LEFT JOIN courses c ON e.course_id = c.id
            LEFT JOIN users u ON e.student_id = u.id
            WHERE e.status = 'pending_verification'
            ORDER BY e.enrolled_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $enrollments = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'pending_verification'");
        $total = $stmt->fetch()['total'];
        
        return [
            'enrollments' => $enrollments,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
	    /**
     * Get pending enrollments count
     */
    public function getPendingCount()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE status = 'pending_verification'
        ");
        $stmt->execute();
        return $stmt->fetch()['count'];
    }
    
    /**
     * Get recent enrollments for admin dashboard
     */
    public function getRecentEnrollments($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT e.*, c.title as course_title, 
                   CONCAT(u.first_name, ' ', u.last_name) as student_name
            FROM {$this->table} e
            LEFT JOIN courses c ON e.course_id = c.id
            LEFT JOIN users u ON e.student_id = u.id
            ORDER BY e.enrolled_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get enrollment statistics
     */
    public function getStats()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'active' THEN 1 ELSE 0 END) as active,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'pending_verification' THEN 1 ELSE 0 END) as pending
            FROM {$this->table}
        ");
        return $stmt->fetch();
    }
}