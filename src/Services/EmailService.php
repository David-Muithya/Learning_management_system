<?php
namespace SkillMaster\Services;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\SMTP;
use PHPMailer\PHPMailer\Exception;

class EmailService
{
    private $mail;
    private $isConfigured;
    
    public function __construct()
    {
        $this->mail = new PHPMailer(true);
        $this->isConfigured = $this->checkConfiguration();
        
        if ($this->isConfigured) {
            $this->configure();
        }
    }
    
    /**
     * Check if email configuration is set
     */
    private function checkConfiguration()
    {
        return !empty(SMTP_USERNAME) && !empty(SMTP_PASSWORD);
    }
    
    /**
     * Configure PHPMailer
     */
    private function configure()
    {
        try {
            // Server settings
            $this->mail->isSMTP();
            $this->mail->Host = SMTP_HOST;
            $this->mail->SMTPAuth = true;
            $this->mail->Username = SMTP_USERNAME;
            $this->mail->Password = SMTP_PASSWORD;
            $this->mail->SMTPSecure = SMTP_ENCRYPTION;
            $this->mail->Port = SMTP_PORT;
            
            // Sender
            $this->mail->setFrom(SMTP_FROM_EMAIL, SMTP_FROM_NAME);
            
            // Content
            $this->mail->isHTML(true);
            $this->mail->CharSet = 'UTF-8';
            
        } catch (Exception $e) {
            error_log("Email configuration error: " . $e->getMessage());
        }
    }
	public function isConfigured(): bool
    {
        return $this->isConfigured;
    }
    
    /**
     * Send email
     */
    public function send($to, $subject, $body, $altBody = null, $replyToEmail = null, $replyToName = null)
    {
        if (!$this->isConfigured) {
            error_log("Email not sent: SMTP not configured");
            return false;
        }
        
        try {
            $this->mail->clearAddresses();
            $this->mail->addAddress($to);

            if (method_exists($this->mail, 'clearReplyTos')) {
                $this->mail->clearReplyTos();
            }

            if (!empty($replyToEmail)) {
                $this->mail->addReplyTo($replyToEmail, $replyToName ?? $replyToEmail);
            } else {
                $this->mail->addReplyTo(SMTP_REPLY_TO, SMTP_FROM_NAME);
            }

            $this->mail->Subject = $subject;
            $this->mail->Body = $body;
            
            if ($altBody) {
                $this->mail->AltBody = strip_tags($altBody);
            } else {
                $this->mail->AltBody = strip_tags($body);
            }
            
            return $this->mail->send();
            
        } catch (Exception $e) {
            error_log("Email sending failed: " . $this->mail->ErrorInfo);
            return false;
        }
    }
    
    /**
     * Send welcome email to student
     */
    public function sendWelcomeStudent($to, $name)
    {
        $subject = "Welcome to " . APP_NAME . "!";
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #06BBCC; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Welcome to " . APP_NAME . "!</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>Thank you for joining " . APP_NAME . "! We're excited to have you on board.</p>
                        <p>You can now start exploring our courses and begin your learning journey.</p>
                        <p><a href='" . LOGIN_URL . "' class='button'>Login to Your Account</a></p>
                        <p>If you have any questions, feel free to contact our support team.</p>
                        <p>Happy Learning!<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send welcome email to instructor
     */
    public function sendWelcomeInstructor($to, $name, $password = null)
    {
        $subject = "Welcome to " . APP_NAME . " - Instructor Account";
        
        $passwordMessage = $password ? "<p><strong>Temporary Password:</strong> " . htmlspecialchars($password) . "</p>
                                        <p>Please change your password after first login.</p>" : "";
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #06BBCC; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Welcome to the Instructor Team!</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>Congratulations! Your instructor application has been approved.</p>
                        $passwordMessage
                        <p>You can now log in and start creating your courses.</p>
                        <p><a href='" . LOGIN_URL . "' class='button'>Login to Your Account</a></p>
                        <p>We look forward to having you share your expertise with our students!</p>
                        <p>Best regards,<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send password reset email
     */
    public function sendPasswordReset($to, $name, $token)
    {
        $resetLink = BASE_URL . "/reset-password.php?token=" . $token;
        
        $subject = "Password Reset Request - " . APP_NAME;
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #06BBCC; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Password Reset Request</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>We received a request to reset your password. Click the button below to create a new password:</p>
                        <p style='text-align: center;'><a href='" . $resetLink . "' class='button'>Reset Password</a></p>
                        <p>If you didn't request this, please ignore this email. The link will expire in 1 hour.</p>
                        <p>Alternatively, copy and paste this link into your browser:<br>
                        <small>" . $resetLink . "</small></p>
                        <p>Thank you,<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send instructor application received email
     */
    public function sendApplicationReceived($to, $name)
    {
        $subject = "Application Received - " . APP_NAME;
        
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
                        <h2>Application Received</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>Thank you for applying to become an instructor at " . APP_NAME . ".</p>
                        <p>We have received your application and our team will review it shortly. You will receive an email notification once a decision has been made.</p>
                        <p>This process typically takes 3-5 business days.</p>
                        <p>Thank you for your interest in joining our instructor team!</p>
                        <p>Best regards,<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send application approval email
     */
    public function sendApplicationApproved($to, $name)
    {
        $subject = "Instructor Application Approved - " . APP_NAME;
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #06BBCC; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Congratulations!</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($name) . "</strong>,</p>
                        <p>We are pleased to inform you that your instructor application has been <strong style='color: green;'>APPROVED</strong>!</p>
                        <p>You can now log in to your instructor dashboard and start creating your courses.</p>
                        <p><a href='" . LOGIN_URL . "' class='button'>Login to Your Account</a></p>
                        <p>We look forward to seeing your courses!</p>
                        <p>Welcome to the team,<br>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send contact message notification to admin
     */
    public function sendContactNotification($to, $data)
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
                        <p><strong>Subject:</strong> " . htmlspecialchars($data['subject']) . "</p>
                        <p><strong>Message:</strong></p>
                        <p>" . nl2br(htmlspecialchars($data['message'])) . "</p>
                        <hr>
                        <p>You can reply directly to this email to respond to the sender.</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
    
    /**
     * Send assignment graded notification to student
     */
    public function sendAssignmentGraded($to, $studentName, $assignmentTitle, $grade, $feedback)
    {
        $subject = "Assignment Graded - " . $assignmentTitle;
        
        $body = "
            <html>
            <head>
                <style>
                    body { font-family: Arial, sans-serif; line-height: 1.6; color: #333; }
                    .container { max-width: 600px; margin: 0 auto; padding: 20px; }
                    .header { background-color: #06BBCC; color: white; padding: 20px; text-align: center; }
                    .content { padding: 20px; background-color: #f9f9f9; }
                    .button { display: inline-block; padding: 10px 20px; background-color: #06BBCC; color: white; text-decoration: none; border-radius: 5px; }
                    .footer { text-align: center; padding: 20px; font-size: 12px; color: #777; }
                </style>
            </head>
            <body>
                <div class='container'>
                    <div class='header'>
                        <h2>Assignment Graded</h2>
                    </div>
                    <div class='content'>
                        <p>Hello <strong>" . htmlspecialchars($studentName) . "</strong>,</p>
                        <p>Your assignment <strong>" . htmlspecialchars($assignmentTitle) . "</strong> has been graded.</p>
                        <p><strong>Grade:</strong> " . $grade . "/100</p>
                        <p><strong>Feedback:</strong><br>" . nl2br(htmlspecialchars($feedback)) . "</p>
                        <p><a href='" . BASE_URL . "/student/grades.php' class='button'>View Your Grades</a></p>
                        <p>Keep up the great work!</p>
                        <p>The " . APP_NAME . " Team</p>
                    </div>
                    <div class='footer'>
                        <p>&copy; " . date('Y') . " " . APP_NAME . ". All rights reserved.</p>
                    </div>
                </div>
            </body>
            </html>
        ";
        
        return $this->send($to, $subject, $body);
    }
}
