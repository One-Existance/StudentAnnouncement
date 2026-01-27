<?php
/**
 * API Endpoint for Creating Announcements (JSON)
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

header('Content-Type: application/json');

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['error' => 'Method not allowed']);
    exit;
}

$data = json_decode(file_get_contents('php://input'), true);

if (!$data) {
    http_response_code(400);
    echo json_encode(['error' => 'Invalid JSON']);
    exit;
}

$announcementController = new AnnouncementController($pdo);
$result = $announcementController->createAnnouncement(
    $data['title'] ?? '',
    $data['message'] ?? '',
    $data['posted_by'] ?? '',
    $data['department_id'] ?? '',
    $data['course_id'] ?? null,
    $data['attachment'] ?? null,
    $data['expiry_date'] ?? null
);

if ($result['success']) {
    http_response_code(201);
    echo json_encode($result);
} else {
    http_response_code(400);
    echo json_encode($result);
}
?>
