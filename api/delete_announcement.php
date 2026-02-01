<?php
/**
 * Delete Announcement API
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

header('Content-Type: application/json');

$authController = new AuthController($pdo);

// Verify admin role
if (!$authController->isLoggedIn()) {
    http_response_code(401);
    echo json_encode(['success' => false, 'message' => 'Unauthorized']);
    exit;
}

$user = $authController->getCurrentUser();
if ($user['role'] !== 'admin') {
    http_response_code(403);
    echo json_encode(['success' => false, 'message' => 'Forbidden']);
    exit;
}

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    http_response_code(405);
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);
    exit;
}

$announcementController = new AnnouncementController($pdo);

$data = json_decode(file_get_contents('php://input'), true);
$announcementId = $data['announcement_id'] ?? null;

if (!$announcementId) {
    http_response_code(400);
    echo json_encode(['success' => false, 'message' => 'Announcement ID is required']);
    exit;
}

// Verify announcement exists
$announcement = $announcementController->getAnnouncementById($announcementId);
if (!$announcement) {
    http_response_code(404);
    echo json_encode(['success' => false, 'message' => 'Announcement not found']);
    exit;
}

// Delete announcement
$result = $announcementController->deleteAnnouncement($announcementId);

if ($result['success']) {
    http_response_code(200);
    echo json_encode($result);
} else {
    http_response_code(500);
    echo json_encode($result);
}
