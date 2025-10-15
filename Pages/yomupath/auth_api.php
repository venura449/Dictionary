<?php
header('Content-Type: application/json; charset=utf-8');
require_once '../../Config/auth.php';
require_once '../../Config/User.php';

$repo = new UserRepository();
$action = $_GET['action'] ?? '';

try {
    switch ($action) {
        case 'register':
            $data = json_decode(file_get_contents('php://input'), true);
            $name = trim($data['name'] ?? '');
            $email = trim($data['email'] ?? '');
            $password = $data['password'] ?? '';

            if ($name === '' || $email === '' || $password === '') {
                echo json_encode(['success' => false, 'message' => 'All fields are required']);
                break;
            }

            if ($repo->findByEmail($email)) {
                echo json_encode(['success' => false, 'message' => 'Email already registered']);
                break;
            }

            $id = $repo->create($name, $email, $password);
            if ($id === false) {
                echo json_encode(['success' => false, 'message' => 'Failed to create user']);
                break;
            }
            $_SESSION['user'] = ['id' => $id, 'name' => $name, 'email' => $email];
            echo json_encode(['success' => true]);
            break;

        case 'login':
            $data = json_decode(file_get_contents('php://input'), true);
            $email = trim($data['email'] ?? '');
            $password = $data['password'] ?? '';

            $user = $repo->findByEmail($email);
            if (!$user || !password_verify($password, $user['password_hash'])) {
                echo json_encode(['success' => false, 'message' => 'Invalid credentials']);
                break;
            }
            $_SESSION['user'] = ['id' => (int)$user['id'], 'name' => $user['name'], 'email' => $user['email']];
            echo json_encode(['success' => true, 'isAdmin' => auth_is_admin()]);
            break;

        case 'logout':
            $_SESSION = [];
            if (ini_get('session.use_cookies')) {
                $params = session_get_cookie_params();
                setcookie(session_name(), '', time() - 42000, $params['path'], $params['domain'], $params['secure'], $params['httponly']);
            }
            session_destroy();
            echo json_encode(['success' => true]);
            break;

        case 'me':
            $user = auth_current_user();
            echo json_encode(['success' => true, 'user' => $user, 'isAdmin' => auth_is_admin()]);
            break;

        default:
            echo json_encode(['success' => false, 'message' => 'Invalid action']);
    }
} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Error: ' . $e->getMessage()]);
}

?>


