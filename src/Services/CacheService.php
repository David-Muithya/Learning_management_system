<?php
namespace SkillMaster\Services;

class CacheService
{
    private $cachePath;
    private $enabled;
    private $defaultExpiration;
    
    public function __construct()
    {
        $this->cachePath = defined('CACHE_PATH') ? CACHE_PATH : dirname(__DIR__, 2) . '/cache/';
        $this->enabled = defined('CACHE_ENABLED') ? CACHE_ENABLED : true;
        $this->defaultExpiration = defined('CACHE_EXPIRATION') ? CACHE_EXPIRATION : 3600;
        
        $this->ensureCacheDirectory();
    }
    
    /**
     * Ensure cache directory exists
     */
    private function ensureCacheDirectory()
    {
        if (!is_dir($this->cachePath)) {
            mkdir($this->cachePath, 0755, true);
        }
    }
    
    /**
     * Get cache key
     */
    private function getCacheKey($key)
    {
        return md5($key);
    }
    
    /**
     * Get cache file path
     */
    private function getCacheFilePath($key)
    {
        return $this->cachePath . $this->getCacheKey($key) . '.cache';
    }
    
    /**
     * Store data in cache
     */
    public function set($key, $data, $expiration = null)
    {
        if (!$this->enabled) {
            return false;
        }
        
        $expiration = $expiration ?: $this->defaultExpiration;
        $cacheFile = $this->getCacheFilePath($key);
        
        $cacheData = [
            'data' => $data,
            'expires_at' => time() + $expiration,
            'created_at' => time()
        ];
        
        $content = serialize($cacheData);
        return file_put_contents($cacheFile, $content, LOCK_EX) !== false;
    }
    
    /**
     * Get data from cache
     */
    public function get($key, $default = null)
    {
        if (!$this->enabled) {
            return $default;
        }
        
        $cacheFile = $this->getCacheFilePath($key);
        
        if (!file_exists($cacheFile)) {
            return $default;
        }
        
        $content = file_get_contents($cacheFile);
        $cacheData = unserialize($content);
        
        // Check if cache has expired
        if ($cacheData['expires_at'] < time()) {
            $this->delete($key);
            return $default;
        }
        
        return $cacheData['data'];
    }
    
    /**
     * Check if cache exists and is valid
     */
    public function has($key)
    {
        if (!$this->enabled) {
            return false;
        }
        
        $cacheFile = $this->getCacheFilePath($key);
        
        if (!file_exists($cacheFile)) {
            return false;
        }
        
        $content = file_get_contents($cacheFile);
        $cacheData = unserialize($content);
        
        return $cacheData['expires_at'] >= time();
    }
    
    /**
     * Delete a cache item
     */
    public function delete($key)
    {
        $cacheFile = $this->getCacheFilePath($key);
        
        if (file_exists($cacheFile)) {
            return unlink($cacheFile);
        }
        
        return false;
    }
    
    /**
     * Clear all cache
     */
    public function clear()
    {
        $files = glob($this->cachePath . '*.cache');
        
        foreach ($files as $file) {
            unlink($file);
        }
        
        return true;
    }
    
    /**
     * Clear expired cache
     */
    public function clearExpired()
    {
        $files = glob($this->cachePath . '*.cache');
        $cleared = 0;
        
        foreach ($files as $file) {
            $content = file_get_contents($file);
            $cacheData = unserialize($content);
            
            if ($cacheData['expires_at'] < time()) {
                unlink($file);
                $cleared++;
            }
        }
        
        return $cleared;
    }
    
    /**
     * Remember data - get from cache or store callback result
     */
    public function remember($key, $callback, $expiration = null)
    {
        if ($this->has($key)) {
            return $this->get($key);
        }
        
        $data = $callback();
        $this->set($key, $data, $expiration);
        
        return $data;
    }
    
    /**
     * Increment a counter in cache
     */
    public function increment($key, $step = 1)
    {
        $value = $this->get($key, 0);
        $value += $step;
        $this->set($key, $value);
        return $value;
    }
    
    /**
     * Decrement a counter in cache
     */
    public function decrement($key, $step = 1)
    {
        $value = $this->get($key, 0);
        $value -= $step;
        $this->set($key, $value);
        return $value;
    }
    
    /**
     * Get cache stats
     */
    public function getStats()
    {
        $files = glob($this->cachePath . '*.cache');
        $totalSize = 0;
        $valid = 0;
        $expired = 0;
        
        foreach ($files as $file) {
            $totalSize += filesize($file);
            $content = file_get_contents($file);
            $cacheData = unserialize($content);
            
            if ($cacheData['expires_at'] >= time()) {
                $valid++;
            } else {
                $expired++;
            }
        }
        
        return [
            'total_files' => count($files),
            'valid_files' => $valid,
            'expired_files' => $expired,
            'total_size' => $this->formatBytes($totalSize),
            'cache_path' => $this->cachePath,
            'enabled' => $this->enabled
        ];
    }
    
    /**
     * Format bytes to human readable
     */
    private function formatBytes($bytes, $precision = 2)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB'];
        
        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);
        
        $bytes /= pow(1024, $pow);
        
        return round($bytes, $precision) . ' ' . $units[$pow];
    }
}