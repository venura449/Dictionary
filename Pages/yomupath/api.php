<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../Config/auth.php';
require_once '../../Config/SinhalaData.php';

$sinhalaData = new SinhalaData();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'get_data':
            $page = (int)($_GET['page'] ?? 1);
            $limit = (int)($_GET['limit'] ?? 50);
            $search = $_GET['search'] ?? '';
            $sortBy = $_GET['sortBy'] ?? 'id';
            $sortOrder = $_GET['sortOrder'] ?? 'ASC';
            
            $result = $sinhalaData->getPaginatedData($page, $limit, $search, $sortBy, $sortOrder);
            echo json_encode($result);
            break;
            
        case 'get_record':
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $record = $sinhalaData->getById($id);
                echo json_encode(['success' => true, 'data' => $record]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            }
            break;
            
        case 'save_record':
            auth_require_admin_api();
            $data = json_decode(file_get_contents('php://input'), true);
            
            if (isset($data['id']) && $data['id'] > 0) {
                // Update existing record
                $success = $sinhalaData->update($data['id'], $data);
                $message = $success ? 'Record updated successfully' : 'Failed to update record';
            } else {
                // Insert new record
                $id = $sinhalaData->insert($data);
                $success = $id !== false;
                $message = $success ? 'Record added successfully' : 'Failed to add record';
            }
            
            echo json_encode(['success' => $success, 'message' => $message]);
            break;
            
        case 'delete_record':
            auth_require_admin_api();
            $id = (int)($_GET['id'] ?? 0);
            if ($id > 0) {
                $success = $sinhalaData->delete($id);
                $message = $success ? 'Record deleted successfully' : 'Failed to delete record';
                echo json_encode(['success' => $success, 'message' => $message]);
            } else {
                echo json_encode(['success' => false, 'message' => 'Invalid ID']);
            }
            break;
            
        case 'get_stats':
            $stats = $sinhalaData->getStats();
            echo json_encode(['success' => true, 'data' => $stats]);
            break;
            
        case 'get_suggestions':
            $term = $_GET['term'] ?? '';
            if (!empty($term)) {
                $suggestions = $sinhalaData->getSearchSuggestions($term);
                echo json_encode(['success' => true, 'data' => $suggestions]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No search term provided']);
            }
            break;
            
        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
            break;
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}
?>
