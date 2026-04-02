<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use SkillMaster\Services\FileUploadService;
use SkillMaster\Services\NotificationService;

class Submission
{
    private $db;
    private $table = 'submissions';
    private $fileUpload;
    private $notificationService;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->fileUpload = new FileUploadService(ASSIGNMENT_UPLOAD_PATH);
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Submit assignment with file upload
     */
    public function submit($assignmentId, $studentId, $submissionText = null, $file = null)
    {
        // Check if already submitted
        if ($this->hasSubmitted($assignmentId, $studentId)) {
            return ['success' => false, 'message' => 'You have already submitted this assignment'];
        }
        
        // Check if assignment exists and not overdue
        $assignment = (new Assignment())->getById($assignmentId);
        if (!$assignment) {
            return ['success' => false, 'message' => 'Assignment not found'];
        }
        
        // Handle file upload
        $filePath = null;
        if ($file && $file['error'] !== UPLOAD_ERR_NO_FILE) {
            $uploadResult = $this->fileUpload->upload($file, '', 'submission_' . $assignmentId . '_' . $studentId);
            if (!$uploadResult) {
                return ['success' => false, 'message' => 'File upload failed: ' . implode(', ', $this->fileUpload->getErrors())];
            }
            $filePath = $uploadResult['path'];
        }
        
        // Determine if submission is late
        $isLate = strtotime($assignment['due_date']) < time();
        $status = $isLate ? 'late' : 'submitted';
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (assignment_id, student_id, submission_text, submitted_at, is_late, status)
            VALUES (?, ?, ?, NOW(), ?, ?)
        ");
        
        $result = $stmt->execute([
            $assignmentId,
            $studentId,
            $submissionText,
            $isLate ? 1 : 0,
            $status
        ]);
        
        if ($result) {
            $submissionId = $this->db->lastInsertId();
            
            // Notify instructor about submission
            $this->notifyInstructor($assignment['instructor_id'], $studentId, $assignment['title']);
            
            return ['success' => true, 'submission_id' => $submissionId];
        }
        
        return ['success' => false, 'message' => 'Failed to submit assignment'];
    }
    
    /**
     * Check if student has submitted
     */

        /**
     * Get submissions by assignment for grading
     */
    public function getForGrading($assignmentId, $instructorId, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email, u.profile_pic
            FROM {$this->table} s
            LEFT JOIN users u ON s.student_id = u.id
            LEFT JOIN assignments a ON s.assignment_id = a.id
            WHERE s.assignment_id = ? AND a.instructor_id = ?
            ORDER BY s.submitted_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$assignmentId, $instructorId, $perPage, $offset]);
        $submissions = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table} s
            LEFT JOIN assignments a ON s.assignment_id = a.id
            WHERE s.assignment_id = ? AND a.instructor_id = ?
        ");
        $stmt->execute([$assignmentId, $instructorId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'submissions' => $submissions,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get single submission for grading
     */
    public function getForGradingById($submissionId, $instructorId)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, a.title as assignment_title, a.max_points, a.due_date,
                   c.title as course_title,
                   CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email
            FROM {$this->table} s
            LEFT JOIN assignments a ON s.assignment_id = a.id
            LEFT JOIN courses c ON a.course_id = c.id
            LEFT JOIN users u ON s.student_id = u.id
            WHERE s.id = ? AND a.instructor_id = ?
        ");
        $stmt->execute([$submissionId, $instructorId]);
        return $stmt->fetch();
    }
    public function hasSubmitted($assignmentId, $studentId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM {$this->table} 
            WHERE assignment_id = ? AND student_id = ? 
            AND status NOT IN ('graded')
        ");
        $stmt->execute([$assignmentId, $studentId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get submission by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT s.*, a.title as assignment_title, a.max_points, a.due_date,
                   c.title as course_title, c.id as course_id,
                   CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email,
                   CONCAT(g.first_name, ' ', g.last_name) as grader_name
            FROM {$this->table} s
            LEFT JOIN assignments a ON s.assignment_id = a.id
            LEFT JOIN courses c ON a.course_id = c.id
            LEFT JOIN users u ON s.student_id = u.id
            LEFT JOIN users g ON s.graded_by = g.id
            WHERE s.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get submissions by assignment
     */
    public function getByAssignment($assignmentId, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT s.*, CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email
            FROM {$this->table} s
            LEFT JOIN users u ON s.student_id = u.id
            WHERE s.assignment_id = ?
            ORDER BY s.submitted_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$assignmentId, $perPage, $offset]);
        $submissions = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table} WHERE assignment_id = ?
        ");
        $stmt->execute([$assignmentId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'submissions' => $submissions,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get submissions by student
     */
    public function getByStudent($studentId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT s.*, a.title as assignment_title, a.max_points, a.due_date,
                   c.title as course_title, c.id as course_id
            FROM {$this->table} s
            LEFT JOIN assignments a ON s.assignment_id = a.id
            LEFT JOIN courses c ON a.course_id = c.id
            WHERE s.student_id = ?
            ORDER BY s.submitted_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$studentId, $perPage, $offset]);
        $submissions = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table} WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'submissions' => $submissions,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Grade a submission
     */
    public function grade($submissionId, $grade, $feedback, $gradedBy)
    {
        $submission = $this->getById($submissionId);
        if (!$submission) {
            return false;
        }
        
        $assignment = (new Assignment())->getById($submission['assignment_id']);
        $maxPoints = $assignment['max_points'];
        
        // Validate grade
        if ($grade < 0 || $grade > $maxPoints) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET grade = ?, feedback = ?, graded_by = ?, graded_at = NOW(), status = 'graded'
            WHERE id = ?
        ");
        
        $result = $stmt->execute([$grade, $feedback, $gradedBy, $submissionId]);
        
        if ($result) {
            // Create grade record
            $this->createGradeRecord($submission['student_id'], $submission['assignment_id'], $grade, $gradedBy);
            
            // Notify student
            $this->notificationService->notifyAssignmentGraded(
                $submission['student_id'],
                $assignment['title'],
                $grade,
                $feedback
            );
        }
        
        return $result;
    }
    
    /**
     * Create grade record
     */
    private function createGradeRecord($studentId, $assignmentId, $grade, $gradedBy)
    {
        // Get enrollment ID
        $stmt = $this->db->prepare("
            SELECT id FROM enrollments 
            WHERE student_id = ? AND course_id = 
                (SELECT course_id FROM assignments WHERE id = ?)
            AND status = 'active'
        ");
        $stmt->execute([$studentId, $assignmentId]);
        $enrollment = $stmt->fetch();
        
        if ($enrollment) {
            $gradeModel = new Grade();
            $gradeModel->create($enrollment['id'], $assignmentId, $grade, $gradedBy);
        }
    }
    
    /**
     * Get submission statistics for assignment
     */
    public function getStats($assignmentId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                COUNT(*) as total_submissions,
                AVG(grade) as avg_grade,
                MIN(grade) as min_grade,
                MAX(grade) as max_grade,
                SUM(CASE WHEN is_late = 1 THEN 1 ELSE 0 END) as late_submissions,
                SUM(CASE WHEN grade IS NOT NULL THEN 1 ELSE 0 END) as graded_count
            FROM {$this->table}
            WHERE assignment_id = ?
        ");
        $stmt->execute([$assignmentId]);
        return $stmt->fetch();
    }
    
    /**
     * Notify instructor about new submission
     */
    private function notifyInstructor($instructorId, $studentId, $assignmentTitle)
    {
        $stmt = $this->db->prepare("
            SELECT CONCAT(first_name, ' ', last_name) as name FROM users WHERE id = ?
        ");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        $title = 'New Assignment Submission';
        $message = "{$student['name']} has submitted the assignment '{$assignmentTitle}'.";
        
        $this->notificationService->create($instructorId, $title, $message, 'info');
    }
    
    /**
     * Get pending grading count for instructor
     */
    public function getPendingGradingCount($instructorId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count
            FROM {$this->table} s
            LEFT JOIN assignments a ON s.assignment_id = a.id
            WHERE a.instructor_id = ? AND s.status != 'graded' AND s.status != 'late'
        ");
        $stmt->execute([$instructorId]);
        return $stmt->fetch()['count'];
    }
}