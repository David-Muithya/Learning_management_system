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
     * Get courses by instructor
     */
    public function getByInstructor($instructorId)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} 
            WHERE instructor_id = ? AND deleted_at IS NULL
            ORDER BY created_at DESC
        ");
        $stmt->execute([$instructorId]);
        return $stmt->fetchAll();
    }
    
    /**
     * Create a new course
     */
    public function createCourse($data, $instructorId)
    {
        // Generate slug from title
        $slug = $this->generateSlug($data['title']);
        
        // Generate course code
        $code = $this->generateCourseCode($data['title'], $instructorId);
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (title, slug, code, description, short_description, category_id, instructor_id, 
             price, thumbnail, credits, start_date, end_date, status, max_students, 
             syllabus, created_by, created_at)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $data['title'],
            $slug,
            $code,
            $data['description'] ?? null,
            $data['short_description'] ?? null,
            $data['category_id'] ?? null,
            $instructorId,
            $data['price'] ?? 0,
            $data['thumbnail'] ?? null,
            $data['credits'] ?? 3,
            $data['start_date'] ?? null,
            $data['end_date'] ?? null,
            'draft',
            $data['max_students'] ?? 50,
            $data['syllabus'] ?? null,
            $instructorId
        ]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        return false;
    }
    
    /**
     * Update course
     */
    public function updateCourse($id, $data)
    {
        $fields = [];
        $params = [];
        
        $allowedFields = ['title', 'description', 'short_description', 'category_id', 'price', 
                          'credits', 'start_date', 'end_date', 'max_students', 'syllabus', 'thumbnail'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $fields[] = "$field = ?";
                $params[] = $data[$field];
            }
        }
        
        if (isset($data['title'])) {
            $fields[] = "slug = ?";
            $params[] = $this->generateSlug($data['title']);
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
     * Submit course for approval
     */
    public function submitForApproval($id)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET status = 'pending_approval', updated_at = NOW() 
            WHERE id = ? AND status = 'draft'
        ");
        return $stmt->execute([$id]);
    }
    
    /**
     * Generate slug from title
     */
    private function generateSlug($title)
    {
        $slug = strtolower(trim(preg_replace('/[^A-Za-z0-9-]+/', '-', $title)));
        return $slug . '-' . uniqid();
    }
    
    /**
     * Generate course code
     */
    private function generateCourseCode($title, $instructorId)
    {
        $prefix = strtoupper(substr(preg_replace('/[^A-Za-z]/', '', $title), 0, 3));
        return $prefix . '-' . str_pad($instructorId, 3, '0', STR_PAD_LEFT) . '-' . rand(100, 999);
    }
    
    /**
     * Get categories for dropdown
     */
    public function getCategories()
    {
        $stmt = $this->db->query("SELECT id, name FROM course_categories ORDER BY name");
        return $stmt->fetchAll();
    }
    
    /**
     * Get course by ID (without status check)
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table} WHERE id = ? AND deleted_at IS NULL
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
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
     * Get pending courses count
     */
    public function getPendingCount()
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE status = 'pending_approval' AND deleted_at IS NULL
        ");
        $stmt->execute();
        return $stmt->fetch()['count'];
    }
    
    /**
     * Get recent courses for admin dashboard
     */
    public function getRecentCourses($limit = 5)
    {
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as instructor_name
            FROM {$this->table} c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.deleted_at IS NULL
            ORDER BY c.created_at DESC
            LIMIT ?
        ");
        $stmt->execute([$limit]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get course statistics
     */
    public function getStats()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'published' THEN 1 ELSE 0 END) as published,
                SUM(CASE WHEN status = 'pending_approval' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'draft' THEN 1 ELSE 0 END) as draft,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected
            FROM {$this->table}
            WHERE deleted_at IS NULL
        ");
        return $stmt->fetch();
    }
    
    /**
     * Get pending courses for admin review
     */
    public function getPendingCourses($page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT c.*, CONCAT(u.first_name, ' ', u.last_name) as instructor_name,
                   u.email as instructor_email
            FROM {$this->table} c
            LEFT JOIN users u ON c.instructor_id = u.id
            WHERE c.status = 'pending_approval' AND c.deleted_at IS NULL
            ORDER BY c.created_at ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $courses = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->query("
            SELECT COUNT(*) as total FROM {$this->table} 
            WHERE status = 'pending_approval' AND deleted_at IS NULL
        ");
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
     * Approve a course
     */
    public function approve($courseId, $adminId)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET status = 'published', updated_at = NOW() 
            WHERE id = ? AND status = 'pending_approval'
        ");
        return $stmt->execute([$courseId]);
    }
    
    /**
     * Reject a course with reason
     */
    public function reject($courseId, $adminId, $reason = null)
    {
        $stmt = $this->db->prepare("
            UPDATE {$this->table} SET status = 'rejected', updated_at = NOW() 
            WHERE id = ? AND status = 'pending_approval'
        ");
        return $stmt->execute([$courseId]);
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