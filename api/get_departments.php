<?php
/**
 * API Endpoint for Getting Departments (JSON)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$departmentController = new DepartmentController($pdo);
$id = $_GET['id'] ?? null;

if ($id) {
    $result = $departmentController->getDepartmentById($id);
} else {
    $result = $departmentController->getAllDepartments();
}

echo json_encode($result);
?>
