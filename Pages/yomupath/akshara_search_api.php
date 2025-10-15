<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../Config/AksharaVinyasaData.php';

$aksharaData = new AksharaVinyasaData();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'search_words':
            $term = $_GET['term'] ?? '';
            if (!empty($term)) {
                $results = $aksharaData->searchWords($term);
                echo json_encode(['success' => true, 'data' => $results]);
            } else {
                echo json_encode(['success' => false, 'message' => 'No search term provided']);
            }
            break;
            
        case 'get_suggestions':
            $term = $_GET['term'] ?? '';
            if (!empty($term)) {
                $suggestions = $aksharaData->getSearchSuggestions($term);
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

