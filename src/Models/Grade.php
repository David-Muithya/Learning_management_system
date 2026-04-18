<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class Grade
{
    private $db;
    private $table = 'grades';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Create grade record
     */
    public function create($enrollmentId, $assignmentId, $gradeValue, $gradedBy)
    {
        $letterGrade = $this->calculateLetterGrade($gradeValue);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (enrollment_id, assignment_id, grade_value, letter_grade, graded_by, created_at)
            VALUES (?, ?, ?, ?, ?, NOW())
        ");
        
        return $stmt->execute([$enrollmentId, $assignmentId, $gradeValue, $letterGrade, $gradedBy]);
    }
    
    /**
     * Calculate letter grade based on score
     */
    public function calculateLetterGrade($score, $maxPoints = 100)
    {
        $percentage = ($score / $maxPoints) * 100;
        
        if ($percentage >= GRADE_A) return 'A';
        if ($percentage >= GRADE_B) return 'B';
        if ($percentage >= GRADE_C) return 'C';
        if ($percentage >= GRADE_D) return 'D';
        return 'F';
    }
    
    /**
     * Get grades by enrollment
     */
    public function getByEnrollment($enrollmentId)
    {
        $stmt = $this->db->prepare("
            SELECT g.*, a.title as assignment_title, a.max_points
            FROM {$this->table} g
            LEFT JOIN assignments a ON g.assignment_id = a.id
            WHERE g.enrollment_id = ?
            ORDER BY a.due_date ASC
        ");
        $stmt->execute([$enrollmentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get grades by student with feedback from submissions
     */
    public function getByStudent($studentId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                g.*, 
                a.title as assignment_title, 
                a.max_points, 
                a.id as assignment_id,
                c.title as course_title, 
                c.id as course_id,
                s.feedback as feedback
            FROM {$this->table} g
            LEFT JOIN assignments a ON g.assignment_id = a.id
            LEFT JOIN enrollments e ON g.enrollment_id = e.id
            LEFT JOIN courses c ON a.course_id = c.id
            LEFT JOIN submissions s ON s.assignment_id = a.id AND s.student_id = ?
            WHERE e.student_id = ?
            ORDER BY c.title ASC, a.due_date ASC
        ");
        $stmt->execute([$studentId, $studentId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get course average grade
     */
    public function getCourseAverage($courseId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                AVG(g.grade_value) as avg_grade,
                COUNT(DISTINCT g.enrollment_id) as total_students_graded,
                COUNT(g.id) as total_grades
            FROM {$this->table} g
            LEFT JOIN assignments a ON g.assignment_id = a.id
            WHERE a.course_id = ?
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetch();
    }
    
    /**
     * Get student's course grade summary
     */
    public function getStudentCourseSummary($studentId, $courseId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.id as enrollment_id,
                e.final_grade,
                COUNT(g.id) as assignments_graded,
                AVG(g.grade_value) as average_score,
                SUM(g.grade_value) as total_score,
                SUM(a.max_points) as total_possible
            FROM enrollments e
            LEFT JOIN assignments a ON a.course_id = e.course_id AND a.deleted_at IS NULL
            LEFT JOIN grades g ON g.assignment_id = a.id AND g.enrollment_id = e.id
            WHERE e.student_id = ? AND e.course_id = ?
            GROUP BY e.id
        ");
        $stmt->execute([$studentId, $courseId]);
        return $stmt->fetch();
    }
    
    /**
     * Calculate and update final grade for enrollment
     */
    public function calculateFinalGrade($enrollmentId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                SUM(g.grade_value) as total_earned,
                SUM(a.max_points) as total_possible
            FROM grades g
            LEFT JOIN assignments a ON g.assignment_id = a.id
            WHERE g.enrollment_id = ?
        ");
        $stmt->execute([$enrollmentId]);
        $result = $stmt->fetch();
        
        if ($result && $result['total_possible'] > 0) {
            $percentage = ($result['total_earned'] / $result['total_possible']) * 100;
            $finalGrade = $this->calculateLetterGrade($percentage, 100);
            
            $stmt = $this->db->prepare("
                UPDATE enrollments SET final_grade = ? WHERE id = ?
            ");
            $stmt->execute([$finalGrade, $enrollmentId]);
            
            return $finalGrade;
        }
        
        return null;
    }
    
    /**
     * Get grade distribution for a course
     */
    public function getGradeDistribution($courseId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                e.final_grade,
                COUNT(*) as count
            FROM enrollments e
            WHERE e.course_id = ? AND e.final_grade IS NOT NULL
            GROUP BY e.final_grade
            ORDER BY FIELD(e.final_grade, 'A', 'B', 'C', 'D', 'F')
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get student transcript
     */
    public function getTranscript($studentId)
    {
        $stmt = $this->db->prepare("
            SELECT 
                c.id as course_id,
                c.title as course_title,
                c.code as course_code,
                c.credits,
                e.final_grade,
                e.completed_at,
                e.status
            FROM enrollments e
            LEFT JOIN courses c ON e.course_id = c.id
            WHERE e.student_id = ? AND e.status IN ('completed', 'active')
            ORDER BY e.completed_at DESC
        ");
        $stmt->execute([$studentId]);
        $courses = $stmt->fetchAll();
        
        // Calculate GPA
        $totalPoints = 0;
        $totalCredits = 0;
        
        $gradePoints = ['A' => 4.0, 'B' => 3.0, 'C' => 2.0, 'D' => 1.0, 'F' => 0.0];
        
        foreach ($courses as $course) {
            if ($course['final_grade'] && $course['status'] === 'completed') {
                $points = $gradePoints[$course['final_grade']] ?? 0;
                $totalPoints += $points * $course['credits'];
                $totalCredits += $course['credits'];
            }
        }
        
        $gpa = $totalCredits > 0 ? round($totalPoints / $totalCredits, 2) : 0;
        
        return [
            'courses' => $courses,
            'gpa' => $gpa,
            'total_credits' => $totalCredits,
            'total_points' => $totalPoints
        ];
    }
}