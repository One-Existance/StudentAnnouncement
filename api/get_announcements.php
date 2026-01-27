<?php
/**
 * API Endpoint for Getting Announcements (JSON)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'GET') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$announcementController = new AnnouncementController($pdo);

$type = $_GET['type'] ?? 'all';
$id = $_GET['id'] ?? null;

switch ($type) {
    case 'all':
        $result = $announcementController->getAllAnnouncements();
        break;
    case 'active':
        $result = $announcementController->getActiveAnnouncements();
        break;
    case 'single':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'ID parameter required']);
            exit;
        }
        $result = $announcementController->getAnnouncementById($id);
        break;
    case 'department':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Department ID parameter required']);
            exit;
        }
        $result = $announcementController->getAnnouncementsByDepartment($id);
        break;
    case 'course':
        if (!$id) {
            http_response_code(400);
            echo json_encode(['error' => 'Course ID parameter required']);
            exit;
        }
        $result = $announcementController->getAnnouncementsByCourse($id);
        break;
    default:
        http_response_code(400);
        echo json_encode(['error' => 'Invalid type parameter']);
        exit;
}

echo json_encode($result);
?>
