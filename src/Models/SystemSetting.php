<?php
namespace SkillMaster\Models;

use SkillMaster\Database\Connection;

class SystemSetting
{
    private $db;
    private $table = 'system_settings';
    
    public function __construct()
    {
        $this->db = Connection::getInstance()->getConnection();
    }
    
    /**
     * Get all settings as key-value array
     */
    public function getAll()
    {
        $stmt = $this->db->query("SELECT setting_key, setting_value FROM {$this->table}");
        $settings = [];
        while ($row = $stmt->fetch()) {
            $settings[$row['setting_key']] = $row['setting_value'];
        }
        return $settings;
    }
    
    /**
     * Get a specific setting
     */
    public function get($key, $default = null)
    {
        $stmt = $this->db->prepare("SELECT setting_value FROM {$this->table} WHERE setting_key = ?");
        $stmt->execute([$key]);
        $result = $stmt->fetch();
        return $result ? $result['setting_value'] : $default;
    }
    
    /**
     * Update a setting
     */
    public function update($key, $value)
    {
        $stmt = $this->db->prepare("
            INSERT INTO {$this->table} (setting_key, setting_value, updated_at) 
            VALUES (?, ?, NOW())
            ON DUPLICATE KEY UPDATE setting_value = ?, updated_at = NOW()
        ");
        return $stmt->execute([$key, $value, $value]);
    }
    
    /**
     * Set multiple settings at once
     */
    public function setMultiple($settings)
    {
        $success = true;
        foreach ($settings as $key => $value) {
            if (!$this->update($key, $value)) {
                $success = false;
            }
        }
        return $success;
    }
}