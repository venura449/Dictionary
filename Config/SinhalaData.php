<?php
require_once 'dbconn.php';

class SinhalaData {
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
                              OR `උද්ධෘතය` LIKE '%$search%' 
                              OR `කෙටි_නාමය` LIKE '%$search%' 
                              OR `පිටුව_හා_පේළිය` LIKE '%$search%' 
                              OR `සංස්කාරක` LIKE '%$search%'";
        }
        
        // Validate sort column
        $allowedSortColumns = ['id', 'Timestamp', 'වචනය', 'උද්ධෘතය', 'කෙටි_නාමය', 'පිටුව_හා_පේළිය', 'සංස්කාරක'];
        if (!in_array($sortBy, $allowedSortColumns)) {
            $sortBy = 'id';
        }
        
        // Validate sort order
        $sortOrder = strtoupper($sortOrder) === 'DESC' ? 'DESC' : 'ASC';
        
        // Get total count
        $countQuery = "SELECT COUNT(*) as total FROM `sinhala_data` $searchCondition";
        $countResult = $this->conn->query($countQuery);
        $totalRecords = $countResult->fetch_assoc()['total'];
        
        // Get paginated data
        $dataQuery = "SELECT * FROM `sinhala_data` 
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
        $query = "SELECT * FROM `sinhala_data` WHERE `id` = $id";
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
        $quote = $this->conn->real_escape_string($data['quote']);
        $shortName = $this->conn->real_escape_string($data['shortName']);
        $pageLine = $this->conn->real_escape_string($data['pageLine']);
        $editor = $this->conn->real_escape_string($data['editor']);
        
        $query = "INSERT INTO `sinhala_data` (`වචනය`, `උද්ධෘතය`, `කෙටි_නාමය`, `පිටුව_හා_පේළිය`, `සංස්කාරක`) 
                  VALUES ('$word', '$quote', '$shortName', '$pageLine', '$editor')";
        
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
        $quote = $this->conn->real_escape_string($data['quote']);
        $shortName = $this->conn->real_escape_string($data['shortName']);
        $pageLine = $this->conn->real_escape_string($data['pageLine']);
        $editor = $this->conn->real_escape_string($data['editor']);
        
        $query = "UPDATE `sinhala_data` 
                  SET `වචනය` = '$word', 
                      `උද්ධෘතය` = '$quote', 
                      `කෙටි_නාමය` = '$shortName', 
                      `පිටුව_හා_පේළිය` = '$pageLine', 
                      `සංස්කාරක` = '$editor' 
                  WHERE `id` = $id";
        
        return $this->conn->query($query);
    }
    
    /**
     * Delete record
     */
    public function delete($id) {
        $id = (int)$id;
        $query = "DELETE FROM `sinhala_data` WHERE `id` = $id";
        return $this->conn->query($query);
    }
    
    /**
     * Get statistics
     */
    public function getStats() {
        $stats = [];
        
        // Total records
        $result = $this->conn->query("SELECT COUNT(*) as total FROM `sinhala_data`");
        $stats['total'] = $result->fetch_assoc()['total'];
        
        // Records edited today
        $result = $this->conn->query("SELECT COUNT(*) as edited_today FROM `sinhala_data` WHERE DATE(`Timestamp`) = CURDATE()");
        $stats['edited_today'] = $result->fetch_assoc()['edited_today'];
        
        // Records added this week
        $result = $this->conn->query("SELECT COUNT(*) as new_this_week FROM `sinhala_data` WHERE `Timestamp` >= DATE_SUB(NOW(), INTERVAL 7 DAY)");
        $stats['new_this_week'] = $result->fetch_assoc()['new_this_week'];
        
        return $stats;
    }
    
    /**
     * Search suggestions for autocomplete
     */
    public function getSearchSuggestions($term, $limit = 10) {
        $term = $this->conn->real_escape_string($term);
        $query = "SELECT DISTINCT `වචනය` FROM `sinhala_data` 
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
}
?>
