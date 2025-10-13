<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../Config/SinhalaWords.php';

$sinhalaWords = new SinhalaWords();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_data':
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 50);
            $search = $_GET['search'] ?? '';
            $sortBy = $_GET['sortBy'] ?? 'id';
            $sortOrder = $_GET['sortOrder'] ?? 'ASC';
            
            $result = $sinhalaWords->getPaginatedData($page, $limit, $search, $sortBy, $sortOrder);
            echo json_encode($result);
            break;
            
        case 'get_record':
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $record = $sinhalaWords->getById($id);
                echo json_encode(['success' => true, 'data' => $record]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            }
            break;
            
        case 'save_record':
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['id']) && $data['id'] > 0) {
                // Update existing record
                $success = $sinhalaWords->update($data['id'], $data);
                $message = $success ? 'Word updated successfully' : 'Failed to update word';
            } else {
                // Insert new record
                $id = $sinhalaWords->insert($data);
                $success = $id !== false;
                $message = $success ? 'Word added successfully' : 'Failed to add word';
            }
            
            echo json_encode(['success' => $success, 'message' => $message]);
            break;
            
        case 'delete_record':
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $success = $sinhalaWords->delete($id);
                $message = $success ? 'Word deleted successfully' : 'Failed to delete word';
                echo json_encode(['success' => $success, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            }
            break;
            
        case 'get_stats':
            $stats = $sinhalaWords->getStats();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'get_suggestions':
            $term = $_GET['term'] ?? '';
            if (!empty($term)) {
                $suggestions = $sinhalaWords->getSearchSuggestions($term);
                echo json_encode(['success' => true, 'data' => $suggestions]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No search term provided']);
            }
            break;
            
        case 'get_categories':
            $categories = $sinhalaWords->getCategories();
            echo json_encode(['success' => true, 'data' => $categories]);
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
