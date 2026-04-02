<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;
use SkillMaster\Services\EmailService;

class ContactMessage
{
    private $db;
    private $table = 'contact_messages';
    private $emailService;
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
        $this->emailService = new EmailService();
    }
    
    /**
     * Create a new contact message
     */
    public function create($data)
    {
        // Validate data
        if (empty($data['name']) || empty($data['email']) || empty($data['message'])) {
            return ['success' => false, 'message' => 'All fields are required'];
        }
        
        if (!filter_var($data['email'], FILTER_VALIDATE_EMAIL)) {
            return ['success' => false, 'message' => 'Invalid email address'];
        }
        
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (name, email, subject, message, created_at)
            VALUES (?, ?, ?, ?, NOW())
        ");
        
        $result = $stmt->execute([
            $data['name'],
            $data['email'],
            $data['subject'] ?? null,
            $data['message']
        ]);
        
        if ($result) {
            $id = $this->db->lastInsertId();
            
            // Send notification email to admin
            $this->notifyAdmin($data);
            
            return ['success' => true, 'id' => $id];
        }
        
        return ['success' => false, 'message' => 'Failed to send message'];
    }
    
    /**
     * Get message by ID
     */
    public function getById($id)
    {
        $stmt = $this->db->prepare("SELECT * FROM {$this->table} WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch();
    }
    
    /**
     * Get all messages
     */
    public function getAll($page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$perPage, $offset]);
        $messages = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->query("SELECT COUNT(*) as total FROM {$this->table}");
        $total = $stmt->fetch()['total'];
        
        return [
            'messages' => $messages,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
    
    /**
     * Get unread messages count
     */
    public function getUnreadCount()
    {
        // Since you don't have an 'is_read' field in contact_messages,
        // we'll count based on date or you can add is_read column
        $stmt = $this->db->query("
            SELECT COUNT(*) as count FROM {$this->table} 
            WHERE created_at >= DATE_SUB(NOW(), INTERVAL 7 DAY)
        ");
        $result = $stmt->fetch();
        return $result['count'];
    }
    
    /**
     * Delete message
     */
    public function delete($id)
    {
        $stmt = $this->db->prepare("DELETE FROM {$this->table} WHERE id = ?");
        return $stmt->execute([$id]);
    }
    
    /**
     * Notify admin about new contact message
     */
    private function notifyAdmin($data)
    {
        $subject = "New Contact Message - " . APP_NAME;
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>New Contact Message</h2>
                    </div>
                    <div class='content'>
                        <p><strong>Name:</strong> " . htmlspecialchars($data['name']) . "</p>
                        <p><strong>Email:</strong> " . htmlspecialchars($data['email']) . "</p>
                        <p><strong>Subject:</strong> " . htmlspecialchars($data['subject'] ?? 'No subject') . "</p>
                        <p><strong>Message:</strong></p>
                        <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                        <hr>
                        <p><strong>Reply to:</strong> <a href='mailto:" . htmlspecialchars($data['email']) . "'>" . htmlspecialchars($data['email']) . "</a></p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        // Send to admin email
        if (defined('ADMIN_EMAIL') && !empty(ADMIN_EMAIL)) {
            $this->emailService->send(ADMIN_EMAIL, $subject, $body);
        }
    }
    
    /**
     * Send auto-reply to user
     */
    public function sendAutoReply($to, $name)
    {
        $subject = "Thank you for contacting " . APP_NAME;
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Thank You for Contacting Us</h2>
                    </div>
                    <div class='content'>
                        <p>Dear <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>Thank you for reaching out to " . APP_NAME . ". We have received your message and our team will get back to you within 24-48 hours.</p>
                        <p>If you have any urgent questions, please feel free to call us or reply directly to this email.</p>
                        <p>Best regards,<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->emailService->send($to, $subject, $body);
    }
    
    /**
     * Get message statistics
     */
    public function getStats()
    {
        $stmt = $this->db->query("
            SELECT 
                COUNT(*) as total,
                COUNT(DISTINCT email) as unique_senders,
                DATE(created_at) as date
            FROM {$this->table}
            GROUP BY DATE(created_at)
            ORDER BY date DESC
            LIMIT 30
        ");
        return $stmt->fetchAll();
    }
    
    /**
     * Search messages
     */
    public function search($keyword, $page = 1, $perPage = 20)
    {
        $offset = ($page - 1) * $perPage;
        $searchTerm = "%{$keyword}%";
        
        $stmt = $this->db->prepare("
            SELECT * FROM {$this->table}
            WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?
            ORDER BY created_at DESC
            LIMIT ? OFFSET ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm, $perPage, $offset]);
        $messages = $stmt->fetchAll();
        
        // Get total count
        $stmt = $this->db->prepare("
            SELECT COUNT(*) as total FROM {$this->table}
            WHERE name LIKE ? OR email LIKE ? OR subject LIKE ? OR message LIKE ?
        ");
        $stmt->execute([$searchTerm, $searchTerm, $searchTerm, $searchTerm]);
        $total = $stmt->fetch()['total'];
        
        return [
            'messages' => $messages,
            'total' => $total,
            'current_page' => $page,
            'per_page' => $perPage,
            'total_pages' => ceil($total / $perPage)
        ];
    }
}