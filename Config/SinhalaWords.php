<?php
require_once 'dbconn.php';

class SinhalaWords {
    private $conn;
    
    public function __construct() {
        global $conn;
        $this->conn = $conn;
    }
    
    /**
     * Get paginated data with search and sorting
     */
    public function getPaginatedData($page = 1, $limit = 50, $search = '', $sortBy = 'id', $sortOrder = 'ASC') {
        $offset = ($page - 1) * $limit;
        
        // Build search conditions
        $searchCondition = '';
        if (!empty($search)) {
            $search = $this->conn->real_escape_string($search);
            $searchCondition = "WHERE `වචනය` LIKE '%$search%' 
                              OR `අක්ෂර_පරිවර්තනය` LIKE '%$search%' 
                              OR `Transliteration` LIKE '%$search%' 
                              OR `ශබ්දකෝෂ_කලාපය` LIKE '%$search%' 
                              OR `කර්තෘවරයා` LIKE '%$search%'";
        }
        
        // Validate sort column
        $allowedSortColumns = ['id', 'Timestamp', 'වචනය', 'අක්ෂර_පරිවර්තනය', 'Transliteration', 'ශබ්දකෝෂ_කලාපය', 'කර්තෘවරයා'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }
        
        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM `sinhala_words` $searchCondition";
        $countResult = $this->conn->query($countQuery);
        if ($countResult === false) {
            // Log the SQL error for debugging and fall back to zero
            error_log("[SinhalaWords::getPaginatedData] SQL Error: " . $this->conn->error . " -- Query: " . $countQuery);
            $totalRecords = 0;
        } else {
            $row = $countResult->fetch_assoc();
            $totalRecords = isset($row['total']) ? (int)$row['total'] : 0;
        }
        
        // Get paginated data
        $dataQuery = "SELECT * FROM `sinhala_words` 
                     $searchCondition 
                     ORDER BY `$sortBy` $sortOrder 
                     LIMIT $limit OFFSET $offset";
        
        $result = $this->conn->query($dataQuery);

        $data = [];
        if ($result === false) {
            error_log("[SinhalaWords::getPaginatedData] SQL Error: " . $this->conn->error . " -- Query: " . $dataQuery);
        } else {
            if ($result->num_rows > 0) {
                while ($row = $result->fetch_assoc()) {
                    $data[] = $row;
                }
            }
        }
        
        return [
            'data' => $data,
            'total' => $totalRecords,
            'page' => $page,
            'limit' => $limit,
            'totalPages' => ceil($totalRecords / $limit)
        ];
    }
    
    /**
     * Get single record by ID
     */
    public function getById($id) {
        $id = (int)$id;
        $query = "SELECT * FROM `sinhala_words` WHERE `id` = $id";
        $result = $this->conn->query($query);
        
        if ($result && $result->num_rows > 0) {
            return $result->fetch_assoc();
        }
        return null;
    }
    
    /**
     * Insert new record
     */
    public function insert($data) {
        $word = $this->conn->real_escape_string($data['word']);
        $akshara = $this->conn->real_escape_string($data['akshara']);
        $transliteration = $this->conn->real_escape_string($data['transliteration']);
        $category = $this->conn->real_escape_string($data['category']);
        $author = $this->conn->real_escape_string($data['author']);
        
        $query = "INSERT INTO `sinhala_words` (`වචනය`, `අක්ෂර_පරිවර්තනය`, `Transliteration`, `ශබ්දකෝෂ_කලාපය`, `කර්තෘවරයා`) 
                  VALUES ('$word', '$akshara', '$transliteration', '$category', '$author')";
        
        if ($this->conn->query($query)) {
            return $this->conn->insert_id;
        }
        return false;
    }
    
    /**
     * Update existing record
     */
    public function update($id, $data) {
        $id = (int)$id;
        $word = $this->conn->real_escape_string($data['word']);
        $akshara = $this->conn->real_escape_string($data['akshara']);
        $transliteration = $this->conn->real_escape_string($data['transliteration']);
        $category = $this->conn->real_escape_string($data['category']);
        $author = $this->conn->real_escape_string($data['author']);
        
        $query = "UPDATE `sinhala_words` 
                  SET `වචනය` = '$word', 
                      `අක්ෂර_පරිවර්තනය` = '$akshara', 
                      `Transliteration` = '$transliteration', 
                      `ශබ්දකෝෂ_කලාපය` = '$category', 
                      `කර්තෘවරයා` = '$author'
                  WHERE `id` = $id";
        
        return $this->conn->query($query);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $id = (int)$id;
        $query = "DELETE FROM `sinhala_words` WHERE `id` = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        $stats = [];
        
        // Total records
        $q1 = "SELECT COUNT(*) as total FROM `sinhala_words`";
        $result = $this->conn->query($q1);
        if ($result === false) {
            error_log("[SinhalaWords::getStats] SQL Error: " . $this->conn->error . " -- Query: " . $q1);
            $stats['total'] = 0;
        } else {
            $row = $result->fetch_assoc();
            $stats['total'] = isset($row['total']) ? (int)$row['total'] : 0;
        }

        // Records updated today
        $q2 = "SELECT COUNT(*) as updated_today FROM `sinhala_words` WHERE DATE(`Timestamp`) = CURDATE()";
        $result = $this->conn->query($q2);
        if ($result === false) {
            error_log("[SinhalaWords::getStats] SQL Error: " . $this->conn->error . " -- Query: " . $q2);
            $stats['updated_today'] = 0;
        } else {
            $row = $result->fetch_assoc();
            $stats['updated_today'] = isset($row['updated_today']) ? (int)$row['updated_today'] : 0;
        }

        // Records added this week
        $q3 = "SELECT COUNT(*) as new_this_week FROM `sinhala_words` WHERE `Timestamp` >= DATE_SUB(NOW(), INTERVAL 7 DAY)";
        $result = $this->conn->query($q3);
        if ($result === false) {
            error_log("[SinhalaWords::getStats] SQL Error: " . $this->conn->error . " -- Query: " . $q3);
            $stats['new_this_week'] = 0;
        } else {
            $row = $result->fetch_assoc();
            $stats['new_this_week'] = isset($row['new_this_week']) ? (int)$row['new_this_week'] : 0;
        }

        // Categories count
        $q4 = "SELECT COUNT(DISTINCT `ශබ්දකෝෂ_කලාපය`) as categories FROM `sinhala_words` WHERE `ශබ්දකෝෂ_කලාපය` IS NOT NULL AND `ශබ්දකෝෂ_කලාපය` != ''";
        $result = $this->conn->query($q4);
        if ($result === false) {
            error_log("[SinhalaWords::getStats] SQL Error: " . $this->conn->error . " -- Query: " . $q4);
            $stats['categories'] = 0;
        } else {
            $row = $result->fetch_assoc();
            $stats['categories'] = isset($row['categories']) ? (int)$row['categories'] : 0;
        }
        
        return $stats;
    }
    
    /**
     * Search suggestions for autocomplete
     */
    public function getSearchSuggestions($term, $limit = 10) {
        $term = $this->conn->real_escape_string($term);
        $query = "SELECT DISTINCT `වචනය` FROM `sinhala_words` 
                  WHERE `වචනය` LIKE '%$term%' 
                  ORDER BY `වචනය` 
                  LIMIT $limit";
        
        $result = $this->conn->query($query);
        $suggestions = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $suggestions[] = $row['වචනය'];
            }
        }
        
        return $suggestions;
    }
    
    /**
     * Get categories for dropdown
     */
    public function getCategories() {
        $query = "SELECT DISTINCT `ශබ්දකෝෂ_කලාපය` FROM `sinhala_words` 
                  WHERE `ශබ්දකෝෂ_කලාපය` IS NOT NULL AND `ශබ්දකෝෂ_කලාපය` != '' 
                  ORDER BY `ශබ්දකෝෂ_කලාපය`";
        
        $result = $this->conn->query($query);
        $categories = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['ශබ්දකෝෂ_කලාපය'];
            }
        }
        
        return $categories;
    }
}
?>
