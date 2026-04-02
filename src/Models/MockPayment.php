<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use SkillMaster\Services\NotificationService;

class MockPayment
{
    private $db;
    private $table = 'mock_payments';
    private $notificationService;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->notificationService = new NotificationService();
    }
    
    /**
     * Create a mock payment record
     */
    public function create($studentId, $courseId, $amount)
    {
        $transactionId = $this->generateTransactionId();
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (student_id, course_id, amount, transaction_id, status, created_at)
            VALUES (?, ?, ?, ?, 'pending', NOW())
        ");
        
        $result = $stmt->execute([$studentId, $courseId, $amount, $transactionId]);
        
        if ($result) {
            return $this->db->lastInsertId();
        }
        
        return false;
    }
    
    /**
     * Generate unique transaction ID
     */
    private function generateTransactionId()
    {
        return 'MOCK-' . strtoupper(uniqid()) . '-' . rand(1000, 9999);
    }
    
    /**
     * Complete payment (student clicks "Pay Now")
     */
    public function complete($paymentId)
    {
        $payment = $this->getById($paymentId);
        if (!$payment || $payment['status'] !== 'pending') {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Update payment status
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = 'completed', payment_date = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$paymentId]);
            
            // Create enrollment record with pending verification
            $enrollmentModel = new Enrollment();
            $enrollmentId = $enrollmentModel->create($payment['student_id'], $payment['course_id']);
            
            if ($enrollmentId) {
                // Link enrollment to payment
                $stmt = $this->db->prepare("
                    UPDATE {$this->table} SET enrollment_id = ? WHERE id = ?
                ");
                $stmt->execute([$enrollmentId, $paymentId]);
                
                // Update enrollment status
                $enrollmentModel->updateStatus($enrollmentId, 'pending_verification');
                
                $this->db->commit();
                
                // Notify admin about new enrollment
                $this->notifyAdmin($payment['student_id'], $payment['course_id']);
                
                return $enrollmentId;
            }
            
            $this->db->rollBack();
            return false;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Payment completion failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Verify payment by admin
     */
    public function verify($paymentId, $adminId, $notes = null)
    {
        $payment = $this->getById($paymentId);
        if (!$payment || $payment['status'] !== 'completed') {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Update payment status
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = 'verified', verified_by = ?, verified_at = NOW(), notes = ?
                WHERE id = ?
            ");
            $stmt->execute([$adminId, $notes, $paymentId]);
            
            // Update enrollment status to active
            if ($payment['enrollment_id']) {
                $enrollmentModel = new Enrollment();
                $enrollmentModel->verifyEnrollment($payment['enrollment_id'], $paymentId);
                
                // Notify student
                $this->notifyStudent($payment['student_id'], $payment['course_id']);
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Payment verification failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reject payment by admin
     */
    public function reject($paymentId, $adminId, $notes)
    {
        $payment = $this->getById($paymentId);
        if (!$payment) {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            // Update payment status
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = 'rejected', verified_by = ?, verified_at = NOW(), notes = ?
                WHERE id = ?
            ");
            $stmt->execute([$adminId, $notes, $paymentId]);
            
            // Update enrollment status to rejected
            if ($payment['enrollment_id']) {
                $enrollmentModel = new Enrollment();
                $enrollmentModel->updateStatus($payment['enrollment_id'], 'rejected');
            }
            
            $this->db->commit();
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            return false;
        }
    }
    
    /**
     * Get payment by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("
            SELECT p.*, 
                   c.title as course_title, c.price,
                   CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email
            FROM {$this->table} p
            LEFT JOIN courses c ON p.course_id = c.id
            LEFT JOIN users u ON p.student_id = u.id
            WHERE p.id = ?
        ");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get payments by student
     */
    public function getByStudent($studentId, $page = 1, $perPage = 10)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT p.*, c.title as course_title
            FROM {$this->table} p
            LEFT JOIN courses c ON p.course_id = c.id
            WHERE p.student_id = ?
            ORDER BY p.created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$studentId, $perPage, $offset]);
        $payments = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table} WHERE student_id = ?
        ");
        $stmt->execute([$studentId]);
        $total = $stmt->fetch()['total'];
        
        return [
            'payments' => $payments,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get pending payments for admin
     */
    public function getPendingVerification($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT p.*, c.title as course_title,
                   CONCAT(u.first_name, ' ', u.last_name) as student_name,
                   u.email as student_email
            FROM {$this->table} p
            LEFT JOIN courses c ON p.course_id = c.id
            LEFT JOIN users u ON p.student_id = u.id
            WHERE p.status = 'completed'
            ORDER BY p.payment_date ASC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $payments = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->query("
            SELECT COUNT(*) as total FROM {$this->table} WHERE status = 'completed'
        ");
        $total = $stmt->fetch()['total'];
        
        return [
            'payments' => $payments,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get payment statistics
     */
    public function getStats()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                SUM(CASE WHEN status = 'pending' THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN status = 'completed' THEN 1 ELSE 0 END) as completed,
                SUM(CASE WHEN status = 'verified' THEN 1 ELSE 0 END) as verified,
                SUM(CASE WHEN status = 'rejected' THEN 1 ELSE 0 END) as rejected,
                SUM(CASE WHEN status = 'verified' THEN amount ELSE 0 END) as total_revenue
            FROM {$this->table}
        ");
        return $stmt->fetch();
    }
    
    /**
     * Notify admin about new payment
     */
    private function notifyAdmin($studentId, $courseId)
    {
        $stmt = $this->db->prepare("
            SELECT CONCAT(first_name, ' ', last_name) as name FROM users WHERE id = ?
        ");
        $stmt->execute([$studentId]);
        $student = $stmt->fetch();
        
        $stmt = $this->db->prepare("SELECT title FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        
        // Get admin users
        $stmt = $this->db->query("SELECT id FROM users WHERE role = 'admin'");
        $admins = $stmt->fetchAll();
        
        $title = "New Enrollment Payment";
        $message = "{$student['name']} has completed payment for course '{$course['title']}'. Awaiting verification.";
        
        foreach ($admins as $admin) {
            $this->notificationService->create($admin['id'], $title, $message, 'warning');
        }
    }
    
    /**
     * Notify student about enrollment verification
     */
    private function notifyStudent($studentId, $courseId)
    {
        $stmt = $this->db->prepare("SELECT title FROM courses WHERE id = ?");
        $stmt->execute([$courseId]);
        $course = $stmt->fetch();
        
        $title = "Enrollment Approved!";
        $message = "Your enrollment for '{$course['title']}' has been verified. You can now access the course materials.";
        
        $this->notificationService->create($studentId, $title, $message, 'success');
    }
}