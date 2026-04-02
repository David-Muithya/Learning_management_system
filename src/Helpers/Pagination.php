<?php
namespace SkillMaster\Helpers;

class Pagination
{
    private $totalItems;
    private $itemsPerPage;
    private $currentPage;
    private $totalPages;
    private $url;
    
    public function __construct($totalItems, $itemsPerPage = ITEMS_PER_PAGE, $currentPage = 1)
    {
        $this->totalItems = $totalItems;
        $this->itemsPerPage = $itemsPerPage;
        $this->currentPage = max(1, $currentPage);
        $this->totalPages = ceil($totalItems / $itemsPerPage);
        $this->url = $this->getCurrentUrl();
    }
    
    /**
     * Get offset for database query
     */
    public function getOffset()
    {
        return ($this->currentPage - 1) * $this->itemsPerPage;
    }
    
    /**
     * Get items per page
     */
    public function getLimit()
    {
        return $this->itemsPerPage;
    }
    
    /**
     * Check if there are more pages
     */
    public function hasMore()
    {
        return $this->currentPage < $this->totalPages;
    }
    
    /**
     * Check if there are previous pages
     */
    public function hasPrevious()
    {
        return $this->currentPage > 1;
    }
    
    /**
     * Get previous page number
     */
    public function getPreviousPage()
    {
        return $this->hasPrevious() ? $this->currentPage - 1 : 1;
    }
    
    /**
     * Get next page number
     */
    public function getNextPage()
    {
        return $this->hasMore() ? $this->currentPage + 1 : $this->totalPages;
    }
    
    /**
     * Get current page number
     */
    public function getCurrentPage()
    {
        return $this->currentPage;
    }
    
    /**
     * Get total pages
     */
    public function getTotalPages()
    {
        return $this->totalPages;
    }
    
    /**
     * Get total items
     */
    public function getTotalItems()
    {
        return $this->totalItems;
    }
    
    /**
     * Get current URL with query parameters
     */
    private function getCurrentUrl()
    {
        $url = $_SERVER['REQUEST_URI'];
        $parts = parse_url($url);
        $path = $parts['path'];
        
        if (isset($parts['query'])) {
            parse_str($parts['query'], $query);
            unset($query['page']);
            $queryString = http_build_query($query);
            return $path . ($queryString ? '?' . $queryString : '');
        }
        
        return $path;
    }
    
    /**
     * Generate page URL
     */
    public function getPageUrl($page)
    {
        $separator = strpos($this->url, '?') === false ? '?' : '&';
        return $this->url . $separator . 'page=' . $page;
    }
    
    /**
     * Render pagination HTML
     */
    public function render()
    {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        
        // Previous button
        if ($this->hasPrevious()) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="' . $this->getPageUrl($this->getPreviousPage()) . '" aria-label="Previous">
                            <span aria-hidden="true">&laquo;</span>
                        </a>
                      </li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&laquo;</span></li>';
        }
        
        // Page numbers
        $start = max(1, $this->currentPage - 2);
        $end = min($this->totalPages, $this->currentPage + 2);
        
        if ($start > 1) {
            $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPageUrl(1) . '">1</a></li>';
            if ($start > 2) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
        }
        
        for ($i = $start; $i <= $end; $i++) {
            if ($i == $this->currentPage) {
                $html .= '<li class="page-item active"><span class="page-link">' . $i . '</span></li>';
            } else {
                $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPageUrl($i) . '">' . $i . '</a></li>';
            }
        }
        
        if ($end < $this->totalPages) {
            if ($end < $this->totalPages - 1) {
                $html .= '<li class="page-item disabled"><span class="page-link">...</span></li>';
            }
            $html .= '<li class="page-item"><a class="page-link" href="' . $this->getPageUrl($this->totalPages) . '">' . $this->totalPages . '</a></li>';
        }
        
        // Next button
        if ($this->hasMore()) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="' . $this->getPageUrl($this->getNextPage()) . '" aria-label="Next">
                            <span aria-hidden="true">&raquo;</span>
                        </a>
                      </li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">&raquo;</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
    
    /**
     * Render simple pagination (prev/next only)
     */
    public function renderSimple()
    {
        if ($this->totalPages <= 1) {
            return '';
        }
        
        $html = '<nav aria-label="Page navigation"><ul class="pagination justify-content-center">';
        
        if ($this->hasPrevious()) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="' . $this->getPageUrl($this->getPreviousPage()) . '">Previous</a>
                      </li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Previous</span></li>';
        }
        
        if ($this->hasMore()) {
            $html .= '<li class="page-item">
                        <a class="page-link" href="' . $this->getPageUrl($this->getNextPage()) . '">Next</a>
                      </li>';
        } else {
            $html .= '<li class="page-item disabled"><span class="page-link">Next</span></li>';
        }
        
        $html .= '</ul></nav>';
        
        return $html;
    }
}