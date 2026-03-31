<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use PDO;

class Course
{
    private $db;
    private $table = 'courses';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Get featured/popular courses
     */
    public function getFeaturedCourses($limit = 3)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as instructor_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.status = 'published'
            ORDER BY c.enrollment_count DESC, c.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get all published courses with pagination
     */
    public function getPublishedCourses($category = null, $search = null, $page = 1, $perPage = 9)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
                   cat.name as category_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.instructor_id = u.id
            LEFT JOIN course_categories cat ON c.category_id = cat.id
            WHERE c.status = 'published'
        ";
        
        if ($category) {
            $sql .= " AND cat.slug = ?";
            $params[] = $category;
        }
        
        if ($search) {
            $sql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $searchTerm = "%{$search}%";
            $params[] = $searchTerm;
            $params[] = $searchTerm;
        }
        
        $sql .= " ORDER BY c.created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $courses = $stmt->fetchAll();
        
        // Get total count for pagination
        $countSql = "
            SELECT COUNT(*) as total
            FROM {$this->table} c
            LEFT JOIN course_categories cat ON c.category_id = cat.id
            WHERE c.status = 'published'
        ";
        
        $countParams = [];
        if ($category) {
            $countSql .= " AND cat.slug = ?";
            $countParams[] = $category;
        }
        
        if ($search) {
            $countSql .= " AND (c.title LIKE ? OR c.description LIKE ?)";
            $countParams[] = $searchTerm;
            $countParams[] = $searchTerm;
        }
        
        $stmt = $this->db->prepare($countSql);
        $stmt->execute($countParams);
        $total = $stmt->fetch()['total'];
        
        return [
            'courses' => $courses,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get single course by ID or slug
     */
    public function getCourse($identifier)
    {
        $field = is_numeric($identifier) ? 'c.id' : 'c.slug';
        $stmt = $this->db->prepare("
            SELECT c.*, 
                   CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
                   u.bio as instructor_bio,
                   u.profile_pic as instructor_pic,
                   u.facebook_link, u.twitter_link, u.linkedin_link,
                   cat.name as category_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.instructor_id = u.id
            LEFT JOIN course_categories cat ON c.category_id = cat.id
            WHERE {$field} = ? AND c.status = 'published'
        ");
        $stmt->execute([$identifier]);
        return $stmt->fetch();
    }
    
    /**
     * Get course categories with course counts
     */
    public function getCategoriesWithCounts()
    {
        $stmt = $this->db->query("
            SELECT cat.*, COUNT(c.id) as course_count
            FROM course_categories cat
            LEFT JOIN courses c ON cat.id = c.category_id AND c.status = 'published'
            GROUP BY cat.id
            ORDER BY cat.name
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Get related courses (same category)
     */
    public function getRelatedCourses($courseId, $categoryId, $limit = 3)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as instructor_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.category_id = ? AND c.id != ? AND c.status = 'published'
            ORDER BY c.enrollment_count DESC
            LIMIT ?
        ");
        $stmt->execute([$categoryId, $courseId, $limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get course modules and materials
     */
    public function getCourseContent($courseId)
    {
        $stmt = $this->db->prepare("
            SELECT m.*, 
                   (SELECT COUNT(*) FROM materials WHERE module_id = m.id) as material_count
            FROM modules m
            WHERE m.course_id = ?
            ORDER BY m.order_index ASC
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get course materials by module
     */
    public function getMaterialsByModule($moduleId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM materials 
            WHERE module_id = ? 
            ORDER BY order_index ASC
        ");
        $stmt->execute([$moduleId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Check if student is enrolled in course
     */
    public function isEnrolled($courseId, $studentId)
    {
        $stmt = $this->db->prepare("
            SELECT id FROM enrollments 
            WHERE course_id = ? AND student_id = ? AND status IN ('active', 'completed')
        ");
        $stmt->execute([$courseId, $studentId]);
        return $stmt->fetch() !== false;
    }
    
    /**
     * Get enrollment count for a course
     */
    public function getEnrollmentCount($courseId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM enrollments 
            WHERE course_id = ? AND status = 'active'
        ");
        $stmt->execute([$courseId]);
        return $stmt->fetch()['count'];
    }
}