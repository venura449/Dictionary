<?php
session_start();

$ADMIN_EMAILS = [
    'admin@example.com',
];

function auth_current_user() {
    return isset($_SESSION['user']) ? $_SESSION['user'] : null;
}

function auth_is_logged_in() {
    return auth_current_user() !== null;
}

function auth_is_admin() {
    global $ADMIN_EMAILS;
    $user = auth_current_user();
    if (!$user) return false;
    $email = isset($user['email']) ? $user['email'] : '';
    return in_array(strtolower($email), array_map('strtolower', $ADMIN_EMAILS), true);
}

function auth_require_login_page() {
    if (!auth_is_logged_in()) {
        header('Location: login.php');
        exit();
    }
}

function auth_require_admin_api() {
    if (!auth_is_admin()) {
        http_response_code(403);
        header('Content-Type: application/json; charset=utf-8');
        echo json_encode(['success' => false, 'message' => 'Forbidden']);
        exit();
    }
}

?>


