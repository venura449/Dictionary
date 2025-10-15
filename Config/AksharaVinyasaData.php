<?php
require_once 'dbconn.php';

class AksharaVinyasaData {
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
                              OR `ප්‍රවර්ගය` LIKE '%$search%' 
                              OR `අර්ථය` LIKE '%$search%' 
                              OR `නිරුක්තිය` LIKE '%$search%'";
        }
        
        // Validate sort column
        $allowedSortColumns = ['id', 'වචනය', 'ප්‍රවර්ගය', 'අර්ථය', 'නිරුක්තිය'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }
        
        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM `Akshara_vinyasa` $searchCondition";
        $countResult = $this->conn->query($countQuery);
        $totalRecords = $countResult->fetch_assoc()['total'];
        
        // Get paginated data
        $dataQuery = "SELECT * FROM `Akshara_vinyasa` 
                     $searchCondition 
                     ORDER BY `$sortBy` $sortOrder 
                     LIMIT $limit OFFSET $offset";
        
        $result = $this->conn->query($dataQuery);
        
        $data = [];
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $data[] = $row;
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
        $query = "SELECT * FROM `Akshara_vinyasa` WHERE `id` = $id";
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
        $category = $this->conn->real_escape_string($data['category']);
        $meaning = $this->conn->real_escape_string($data['meaning']);
        $etymology = $this->conn->real_escape_string($data['etymology']);
        
        $query = "INSERT INTO `Akshara_vinyasa` (`වචනය`, `ප්‍රවර්ගය`, `අර්ථය`, `නිරුක්තිය`) 
                  VALUES ('$word', '$category', '$meaning', '$etymology')";
        
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
        $category = $this->conn->real_escape_string($data['category']);
        $meaning = $this->conn->real_escape_string($data['meaning']);
        $etymology = $this->conn->real_escape_string($data['etymology']);
        
        $query = "UPDATE `Akshara_vinyasa` 
                  SET `වචනය` = '$word', 
                      `ප්‍රවර්ගය` = '$category', 
                      `අර්ථය` = '$meaning', 
                      `නිරුක්තිය` = '$etymology' 
                  WHERE `id` = $id";
        
        return $this->conn->query($query);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $id = (int)$id;
        $query = "DELETE FROM `Akshara_vinyasa` WHERE `id` = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        $stats = [];
        
        // Total records
        $result = $this->conn->query("SELECT COUNT(*) as total FROM `Akshara_vinyasa`");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        // Records by category
        $result = $this->conn->query("SELECT COUNT(*) as categories FROM (SELECT DISTINCT `ප්‍රවර්ගය` FROM `Akshara_vinyasa`) as cat");
        $stats['categories'] = $result->fetch_assoc()['categories'];
        
        // Recent entries (last 7 days)
        $result = $this->conn->query("SELECT COUNT(*) as recent FROM `Akshara_vinyasa` WHERE `id` > (SELECT MAX(`id`) - 100 FROM `Akshara_vinyasa`)");
        $stats['recent'] = $result->fetch_assoc()['recent'];
        
        return $stats;
    }
    
    /**
     * Search suggestions for autocomplete
     */
    public function getSearchSuggestions($term, $limit = 10) {
        $term = $this->conn->real_escape_string($term);
        $query = "SELECT DISTINCT `වචනය` FROM `Akshara_vinyasa` 
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
     * Get all categories
     */
    public function getCategories() {
        $query = "SELECT DISTINCT `ප්‍රවර්ගය` FROM `Akshara_vinyasa` WHERE `ප්‍රවර්ගය` IS NOT NULL AND `ප්‍රවර්ගය` != '' ORDER BY `ප්‍රවර්ගය`";
        $result = $this->conn->query($query);
        $categories = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $categories[] = $row['ප්‍රවර්ගය'];
            }
        }
        
        return $categories;
    }
    
    /**
     * Search words with full-text search
     */
    public function searchWords($term, $limit = 100) {
        $term = $this->conn->real_escape_string($term);
        
        $query = "SELECT * FROM `Akshara_vinyasa` 
                  WHERE `වචනය` LIKE '%$term%' 
                     OR `ප්‍රවර්ගය` LIKE '%$term%' 
                     OR `අර්ථය` LIKE '%$term%' 
                     OR `නිරුක්තිය` LIKE '%$term%'
                  ORDER BY 
                    CASE 
                        WHEN `වචනය` LIKE '$term%' THEN 1
                        WHEN `වචනය` LIKE '%$term%' THEN 2
                        ELSE 3
                    END,
                    `වචනය`
                  LIMIT $limit";
        
        $result = $this->conn->query($query);
        $words = [];
        
        if ($result && $result->num_rows > 0) {
            while ($row = $result->fetch_assoc()) {
                $words[] = $row;
            }
        }
        
        return $words;
    }
}
?>
