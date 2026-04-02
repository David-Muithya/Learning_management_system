<?php
namespace SkillMaster\Services;

use SkillMaster\Database\Connection;
use SkillMaster\Helpers\Session;

class NotificationService
{
    private $db;
    private $emailService;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->emailService = new EmailService();
    }
    
    /**
     * Create a notification for a user
     */
    public function create($userId, $title, $message, $type = 'info', $link = null)
    {
        $stmt = $this->db->prepare("
            INSERT INTO notifications (user_id, title, message, is_read, created_at)
            VALUES (?, ?, ?, 0, NOW())
        ");
        
        return $stmt->execute([$userId, $title, $message]);
    }
    
    /**
     * Create notification for multiple users
     */
    public function createForUsers($userIds, $title, $message, $type = 'info')
    {
        $success = true;
        
        foreach ($userIds as $userId) {
            if (!$this->create($userId, $title, $message, $type)) {
                $success = false;
            }
        }
        
        return $success;
    }
    
    /**
     * Create notification for all students in a course
     */
    public function createForCourseStudents($courseId, $title, $message, $type = 'info')
    {
        $stmt = $this->db->prepare("
            SELECT DISTINCT student_id FROM enrollments 
            WHERE course_id = ? AND status = 'active'
        ");
        $stmt->execute([$courseId]);
        $students = $stmt->fetchAll();
        
        $userIds = array_column($students, 'student_id');
        
        return $this->createForUsers($userIds, $title, $message, $type);
    }
    
    /**
     * Create notification for all instructors
     */
    public function createForAllInstructors($title, $message, $type = 'info')
    {
        $stmt = $this->db->prepare("
            SELECT id FROM users WHERE role = 'instructor' AND is_active = 1
        ");
        $stmt->execute();
        $instructors = $stmt->fetchAll();
        
        $userIds = array_column($instructors, 'id');
        
        return $this->createForUsers($userIds, $title, $message, $type);
    }
    
    /**
     * Create notification for all students
     */
    public function createForAllStudents($title, $message, $type = 'info')
    {
        $stmt = $this->db->prepare("
            SELECT id FROM users WHERE role = 'student' AND is_active = 1
        ");
        $stmt->execute();
        $students = $stmt->fetchAll();
        
        $userIds = array_column($students, 'id');
        
        return $this->createForUsers($userIds, $title, $message, $type);
    }
    
    /**
     * Get notifications for a user
     */
    public function getUserNotifications($userId, $limit = 10, $offset = 0)
    {
        $stmt = $this->db->prepare("
            SELECT * FROM notifications 
            WHERE user_id = ? 
            ORDER BY created_at DESC 
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$userId, $limit, $offset]);
        return $stmt->fetchAll();
    }
    
    /**
     * Get unread notification count
     */
    public function getUnreadCount($userId)
    {
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as count FROM notifications 
            WHERE user_id = ? AND is_read = 0
        ");
        $stmt->execute([$userId]);
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Mark notification as read
     */
    public function markAsRead($notificationId, $userId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications SET is_read = 1 
            WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notificationId, $userId]);
    }
    
    /**
     * Mark all notifications as read for a user
     */
    public function markAllAsRead($userId)
    {
        $stmt = $this->db->prepare("
            UPDATE notifications SET is_read = 1 
            WHERE user_id = ?
        ");
        return $stmt->execute([$userId]);
    }
    
    /**
     * Delete notification
     */
    public function delete($notificationId, $userId)
    {
        $stmt = $this->db->prepare("
            DELETE FROM notifications WHERE id = ? AND user_id = ?
        ");
        return $stmt->execute([$notificationId, $userId]);
    }
    
    /**
     * Send email notification (in addition to in-app)
     */
    public function sendEmail($to, $subject, $body)
    {
        return $this->emailService->send($to, $subject, $body);
    }
    
    /**
     * Notify user about course enrollment approval
     */
    public function notifyEnrollmentApproved($studentId, $courseTitle)
    {
        $title = 'Enrollment Approved';
        $message = "Your enrollment in course '{$courseTitle}' has been approved. You can now access the course materials.";
        
        $this->create($studentId, $title, $message, 'success');
        
        // Get student email
        $stmt = $this->db->prepare("SELECT email, first_name FROM users WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if ($student) {
            $emailSubject = "Enrollment Approved - {$courseTitle}";
            $emailBody = $this->emailService->sendEnrollmentVerified($student['email'], $student['first_name'], $courseTitle);
        }
    }
    
    /**
     * Notify student about assignment grade
     */
    public function notifyAssignmentGraded($studentId, $assignmentTitle, $grade, $feedback)
    {
        $title = 'Assignment Graded';
        $message = "Your assignment '{$assignmentTitle}' has been graded. Score: {$grade}/100";
        
        $this->create($studentId, $title, $message, 'info');
        
        // Get student email
        $stmt = $this->db->prepare("SELECT email, first_name FROM users WHERE id = ?");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        if ($student) {
            $this->emailService->sendAssignmentGraded($student['email'], $student['first_name'], $assignmentTitle, $grade, $feedback);
        }
    }
    
    /**
     * Notify instructor about new enrollment
     */
    public function notifyNewEnrollment($instructorId, $studentName, $courseTitle)
    {
        $title = 'New Student Enrolled';
        $message = "A new student ({$studentName}) has enrolled in your course '{$courseTitle}'.";
        
        $this->create($instructorId, $title, $message, 'info');
    }
    
    /**
     * Notify admin about new instructor application
     */
    public function notifyNewApplication($adminId, $applicantName)
    {
        $title = 'New Instructor Application';
        $message = "{$applicantName} has submitted an application to become an instructor.";
        
        $this->create($adminId, $title, $message, 'warning');
    }
}