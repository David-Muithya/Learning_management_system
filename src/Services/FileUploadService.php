<?php
namespace SkillMaster\Services;

use SkillMaster\Helpers\Validation;

class FileUploadService
{
    private $uploadPath;
    private $allowedTypes;
    private $maxSize;
    private $errors = [];
    
    public function __construct($uploadPath = null, $allowedTypes = null, $maxSize = null)
    {
        $this->uploadPath = $uploadPath ?: UPLOAD_PATH;
        $this->allowedTypes = $allowedTypes ?: explode(',', ALLOWED_EXTENSIONS);
        $this->maxSize = $maxSize ?: MAX_FILE_SIZE;
        
        // Ensure upload directory exists
        $this->ensureDirectoryExists();
    }
    
    /**
     * Ensure upload directory exists
     */
    private function ensureDirectoryExists()
    {
        if (!is_dir($this->uploadPath)) {
            mkdir($this->uploadPath, 0755, true);
        }
    }
    
    /**
     * Upload a single file
     */
    public function upload($file, $subDirectory = '', $customName = null)
    {
        $this->errors = [];
        
        // Validate file
        if (!Validation::file($file, $this->allowedTypes, $this->maxSize)) {
            $this->errors[] = 'Invalid file type or size';
            return false;
        }
        
        // Prepare upload path
        $uploadDir = $this->uploadPath . ($subDirectory ? trim($subDirectory, '/') . '/' : '');
        if (!is_dir($uploadDir)) {
            mkdir($uploadDir, 0755, true);
        }
        
        // Generate filename
        if ($customName) {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = $customName . '.' . $extension;
        } else {
            $extension = pathinfo($file['name'], PATHINFO_EXTENSION);
            $filename = uniqid() . '_' . time() . '.' . $extension;
        }
        
        $filePath = $uploadDir . $filename;
        
        // Move uploaded file
        if (move_uploaded_file($file['tmp_name'], $filePath)) {
            return [
                'success' => true,
                'filename' => $filename,
                'path' => $filePath,
                'url' => $this->getUrl($subDirectory, $filename),
                'size' => $file['size'],
                'type' => $file['type']
            ];
        }
        
        $this->errors[] = 'Failed to move uploaded file';
        return false;
    }
    
    /**
     * Upload multiple files
     */
    public function uploadMultiple($files, $subDirectory = '')
    {
        $uploaded = [];
        $this->errors = [];
        
        foreach ($files as $key => $file) {
            if (is_array($file['name'])) {
                // Handle multiple files with same name (array structure)
                for ($i = 0; $i < count($file['name']); $i++) {
                    $singleFile = [
                        'name' => $file['name'][$i],
                        'type' => $file['type'][$i],
                        'tmp_name' => $file['tmp_name'][$i],
                        'error' => $file['error'][$i],
                        'size' => $file['size'][$i]
                    ];
                    
                    $result = $this->upload($singleFile, $subDirectory);
                    if ($result) {
                        $uploaded[] = $result;
                    }
                }
            } else {
                // Handle single file
                $result = $this->upload($file, $subDirectory);
                if ($result) {
                    $uploaded[] = $result;
                }
            }
        }
        
        return $uploaded;
    }
    
    /**
     * Delete a file
     */
    public function delete($filePath)
    {
        if (file_exists($filePath) && is_file($filePath)) {
            return unlink($filePath);
        }
        return false;
    }
    
    /**
     * Get file URL
     */
    private function getUrl($subDirectory, $filename)
    {
        $baseUrl = rtrim(BASE_URL, '/');
        $subPath = $subDirectory ? trim($subDirectory, '/') . '/' : '';
        return $baseUrl . '/uploads/' . $subPath . $filename;
    }
    
    /**
     * Get upload errors
     */
    public function getErrors()
    {
        return $this->errors;
    }
    
    /**
     * Validate file before upload
     */
    public function validate($file)
    {
        return Validation::file($file, $this->allowedTypes, $this->maxSize);
    }
    
    /**
     * Set allowed file types
     */
    public function setAllowedTypes($types)
    {
        $this->allowedTypes = is_array($types) ? $types : explode(',', $types);
        return $this;
    }
    
    /**
     * Set max file size
     */
    public function setMaxSize($size)
    {
        $this->maxSize = $size;
        return $this;
    }
    
    /**
     * Get file info
     */
    public function getFileInfo($filePath)
    {
        if (!file_exists($filePath)) {
            return null;
        }
        
        return [
            'name' => basename($filePath),
            'size' => filesize($filePath),
            'type' => mime_content_type($filePath),
            'extension' => pathinfo($filePath, PATHINFO_EXTENSION),
            'modified' => date('Y-m-d H:i:s', filemtime($filePath))
        ];
    }
}