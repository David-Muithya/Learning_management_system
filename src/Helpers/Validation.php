<?php
namespace SkillMaster\Helpers;

class Validation
{
    /**
     * Validate email
     */
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }
    
    /**
     * Validate required fields
     */
    public static function required($data, $fields)
    {
        $errors = [];
        foreach ($fields as $field) {
            if (empty($data[$field])) {
                $errors[] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }
        return $errors;
    }
    
    /**
     * Validate string length
     */
    public static function length($value, $min, $max = null)
    {
        $length = strlen($value);
        if ($length < $min) {
            return false;
        }
        if ($max !== null && $length > $max) {
            return false;
        }
        return true;
    }
    
    /**
     * Validate password strength
     */
    public static function password($password)
    {
        if (strlen($password) < PASSWORD_MIN_LENGTH) {
            return 'Password must be at least ' . PASSWORD_MIN_LENGTH . ' characters';
        }
        
        if (!preg_match('/[A-Z]/', $password)) {
            return 'Password must contain at least one uppercase letter';
        }
        
        if (!preg_match('/[a-z]/', $password)) {
            return 'Password must contain at least one lowercase letter';
        }
        
        if (!preg_match('/[0-9]/', $password)) {
            return 'Password must contain at least one number';
        }
        
        return true;
    }
    
    /**
     * Validate phone number
     */
    public static function phone($phone)
    {
        return preg_match('/^[0-9+\-\s()]{10,15}$/', $phone);
    }
    
    /**
     * Validate URL
     */
    public static function url($url)
    {
        return filter_var($url, FILTER_VALIDATE_URL) !== false;
    }
    
    /**
     * Sanitize input
     */
    public static function sanitize($input)
    {
        return htmlspecialchars(strip_tags(trim($input)), ENT_QUOTES, 'UTF-8');
    }
    
    /**
     * Sanitize array of inputs
     */
    public static function sanitizeArray($data)
    {
        $sanitized = [];
        foreach ($data as $key => $value) {
            $sanitized[$key] = self::sanitize($value);
        }
        return $sanitized;
    }
    
    /**
     * Validate file upload
     */
    public static function file($file, $allowedTypes = null, $maxSize = null)
    {
        if (!isset($file['error']) || $file['error'] !== UPLOAD_ERR_OK) {
            return false;
        }
        
        $allowedTypes = $allowedTypes ?: explode(',', ALLOWED_EXTENSIONS);
        $maxSize = $maxSize ?: MAX_FILE_SIZE;
        
        $fileType = strtolower(pathinfo($file['name'], PATHINFO_EXTENSION));
        
        if (!in_array($fileType, $allowedTypes)) {
            return false;
        }
        
        if ($file['size'] > $maxSize) {
            return false;
        }
        
        return true;
    }
    
    /**
     * Validate date format
     */
    public static function date($date, $format = 'Y-m-d')
    {
        $d = \DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }
    
    /**
     * Validate numeric value
     */
    public static function numeric($value, $min = null, $max = null)
    {
        if (!is_numeric($value)) {
            return false;
        }
        
        if ($min !== null && $value < $min) {
            return false;
        }
        
        if ($max !== null && $value > $max) {
            return false;
        }
        
        return true;
    }
}