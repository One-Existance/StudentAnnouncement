<?php
/**
 * API Endpoint for Getting Users (JSON)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/UserController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$userController = new UserController($pdo);
$type = $_GET['type'] ?? 'all';
$id = $_GET['id'] ?? null;

switch ($type) {
    case 'all':
        $result = $userController->getAllUsers();
        break;
    case 'single':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID parameter required']);
            exit;
        }
        $result = $userController->getUserById($id);
        break;
    case 'email':
        if (!isset($_GET['email'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Email parameter required']);
            exit;
        }
        $result = $userController->getUserByEmail($_GET['email']);
        break;
    case 'role':
        if (!isset($_GET['role'])) {
            http_response_code(400);
            echo json_encode(['error' => 'Role parameter required']);
            exit;
        }
        $result = $userController->getUsersByRole($_GET['role']);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type parameter']);
        exit;
}

echo json_encode($result);
?>
