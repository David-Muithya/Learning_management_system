<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use SkillMaster\Services\EmailService;

class InstructorApplication
{
    private $db;
    private $table = 'instructor_applications';
    private $emailService;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->emailService = new EmailService();
    }
    
    /**
     * Create new application
     */
    public function create($data)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} 
            (`email`, `first_name`, `last_name`, `phone`, `highest_qualification`, `institution`, 
             `graduation_year`, `years_experience`, `current_role`, `organization`, 
             `expertise_areas`, `teaching_philosophy`, `sample_course_idea`, 
             `portfolio_link`, `why_teach`, `status`, `created_at`)
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())
        ");
        
        $result = $stmt->execute([
            $data['email'],
            $data['first_name'],
            $data['last_name'],
            $data['phone'] ?? null,
            $data['highest_qualification'] ?? null,
            $data['institution'] ?? null,
            $data['graduation_year'] ?? null,
            $data['years_experience'] ?? null,
            $data['current_role'] ?? null,
            $data['organization'] ?? null,
            $data['expertise_areas'] ?? null,
            $data['teaching_philosophy'] ?? null,
            $data['sample_course_idea'] ?? null,
            $data['portfolio_link'] ?? null,
            $data['why_teach'] ?? null
        ]);
        
        if ($result) {
            $id = $this->db->lastInsertId();
            $this->sendApplicationReceived($data['email'], $data['first_name']);
            return $id;
        }
        
        return false;
    }
    
    /**
     * Get application by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all applications
     */
    public function getAll($status = null, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $params = [];
        
        $sql = "SELECT * FROM {$this->table}";
        
        if ($status && $status !== 'all') {
            $sql .= " WHERE status = ?";
            $params[] = $status;
        }
        
        $sql .= " ORDER BY created_at DESC LIMIT ? OFFSET ?";
        $params[] = $perPage;
        $params[] = $offset;
        
        $stmt = $this->db->prepare($sql);
        $stmt->execute($params);
        $applications = $stmt->fetchAll();
        
        // Get total count
        $countSql = "SELECT COUNT(*) as total FROM {$this->table}";
        if ($status && $status !== 'all') {
            $countSql .= " WHERE status = ?";
            $stmt = $this->db->prepare($countSql);
            $stmt->execute([$status]);
        } else {
            $stmt = $this->db->query($countSql);
        }
        $total = $stmt->fetch()['total'];
        
        return [
            'applications' => $applications,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get pending applications count
     */
    public function getPendingCount()
    {
        $stmt = $this->db->query("SELECT COUNT(*) as count FROM {$this->table} WHERE status = 'pending'");
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Approve application
     */
    public function approve($id, $reviewedBy, $notes = null)
    {
        $application = $this->getById($id);
        if (!$application || $application['status'] !== 'pending') {
            return false;
        }
        
        $this->db->beginTransaction();
        
        try {
            $stmt = $this->db->prepare("
                UPDATE {$this->table} 
                SET status = 'approved', reviewed_by = ?, review_notes = ?, reviewed_at = NOW()
                WHERE id = ?
            ");
            $stmt->execute([$reviewedBy, $notes, $id]);
            
            $userModel = new User();
            $userData = [
                'first_name' => $application['first_name'],
                'last_name' => $application['last_name'],
                'email' => $application['email'],
                'phone_number' => $application['phone'],
                'bio' => $application['expertise_areas'],
                'password' => $this->generateRandomPassword(),
                'created_by' => $reviewedBy
            ];
            
            $userCreated = $userModel->createInstructor($userData);
            
            if (!$userCreated) {
                throw new \Exception('Failed to create user account');
            }
            
            $this->db->commit();
            $this->sendApprovalEmail($application['email'], $application['first_name'], $userData['password']);
            
            return true;
            
        } catch (\Exception $e) {
            $this->db->rollBack();
            error_log("Application approval failed: " . $e->getMessage());
            return false;
        }
    }
    
    /**
     * Reject application
     */
    public function reject($id, $reviewedBy, $notes = null)
    {
        $application = $this->getById($id);
        if (!$application) {
            return false;
        }
        
        $stmt = $this->db->prepare("
            UPDATE {$this->table} 
            SET status = 'rejected', reviewed_by = ?, review_notes = ?, reviewed_at = NOW()
            WHERE id = ?
        ");
        $result = $stmt->execute([$reviewedBy, $notes, $id]);
        
        if ($result) {
            $this->sendRejectionEmail($application['email'], $application['first_name'], $notes);
        }
        
        return $result;
    }
    
    /**
     * Generate random password
     */
    private function generateRandomPassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%';
        $password = '';
        for ($i = 0; $i < $length; $i++) {
            $password .= $chars[random_int(0, strlen($chars) - 1)];
        }
        return $password;
    }
    
    /**
     * Send application received email
     */
    private function sendApplicationReceived($email, $name)
    {
        $this->emailService->sendApplicationReceived($email, $name);
    }
    
    /**
     * Send approval email
     */
    private function sendApprovalEmail($email, $name, $password)
    {
        $this->emailService->sendWelcomeInstructor($email, $name, $password);
    }
    
    /**
     * Send rejection email
     */
    private function sendRejectionEmail($email, $name, $notes = null)
    {
        $subject = "Instructor Application Update - " . APP_NAME;
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #dc3545; color: white; padding: 20px; text-align: center; border-radius: 10px 10px 0 0; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Application Status Update</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>Thank you for your interest in becoming an instructor at " . APP_NAME . ".</p>
                        <p>After careful review, we regret to inform you that your application has not been approved at this time.</p>
                        " . ($notes ? "<p><strong>Review Notes:</strong><br>" . nl2br(htmlspecialchars($notes)) . "</p>" : "") . "
                        <p>We encourage you to gain more experience and reapply in the future.</p>
                        <p>Thank you for your understanding.</p>
                        <p>Best regards,<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        $this->emailService->send($email, $subject, $body);
    }
}