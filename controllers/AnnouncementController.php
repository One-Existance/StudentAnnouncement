<?php
/**
 * Announcement Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../models/AnnouncementView.php';

class AnnouncementController {
    private $pdo;
    private $announcementModel;
    private $announcementViewModel;

    public function __construct($pdo) {
        $this->pdo = $pdo;
        $this->announcementModel = new Announcement($pdo);
        $this->announcementViewModel = new AnnouncementView($pdo);
    }

    /**
     * Create a new announcement
     */
    public function createAnnouncement($title, $message, $posted_by, $department_id, $course_id = null, $attachment = null, $expiry_date = null) {
        if (empty($title) || empty($message) || empty($posted_by) || empty($department_id)) {
            return ['success' => false, 'message' => 'Title, message, posted by, and department ID are required'];
        }

        [$course_id, $attachment, $expiry_date] = $this->normalizeNullableValues([$course_id, $attachment, $expiry_date]);

        $authCheck = $this->validateLecturerAnnouncementScope($posted_by, $department_id, $course_id);
        if (!$authCheck['success']) {
            return $authCheck;
        }

        return $this->runAction(
            function () use ($title, $message, $posted_by, $department_id, $course_id, $attachment, $expiry_date) {
                return $this->announcementModel->create($title, $message, $posted_by, $department_id, $course_id, $attachment, $expiry_date);
            },
            'Announcement created successfully',
            true
        );
    }

    /**
     * Get announcement by ID
     */
    public function getAnnouncementById($id) {
        return $this->announcementModel->getAnnouncementById($id);
    }

    /**
     * Get all announcements
     */
    public function getAllAnnouncements() {
        return $this->announcementModel->getAllAnnouncements();
    }

    /**
     * Get announcements by department
     */
    public function getAnnouncementsByDepartment($department_id) {
        return $this->announcementModel->getAnnouncementsByDepartment($department_id);
    }

    /**
     * Get announcements by course
     */
    public function getAnnouncementsByCourse($course_id) {
        return $this->announcementModel->getAnnouncementsByCourse($course_id);
    }

    /**
     * Get announcements posted by a user
     */
    public function getAnnouncementsByUser($posted_by) {
        return $this->announcementModel->getAnnouncementsByUser($posted_by);
    }

    /**
     * Get active announcements
     */
    public function getActiveAnnouncements() {
        return $this->announcementModel->getActiveAnnouncements();
    }

    /**
     * Get active announcements by department
     */
    public function getActiveAnnouncementsByDepartment($department_id) {
        return $this->announcementModel->getActiveAnnouncementsByDepartment($department_id);
    }

    /**
     * Update announcement
     */
    public function updateAnnouncement($id, $title, $message, $department_id, $course_id = null, $attachment = null, $expiry_date = null, $updated_by = null) {
        if (empty($title) || empty($message) || empty($department_id)) {
            return ['success' => false, 'message' => 'Title, message, and department ID are required'];
        }

        $existing = $this->announcementModel->getAnnouncementById($id);
        if (!$existing) {
            return ['success' => false, 'message' => 'Announcement not found'];
        }

        [$course_id, $attachment, $expiry_date] = $this->normalizeNullableValues([$course_id, $attachment, $expiry_date]);

        $actorId = $this->normalizeNullableValue($updated_by) ?? $existing['posted_by'];
        $authCheck = $this->validateLecturerAnnouncementScope($actorId, $department_id, $course_id);
        if (!$authCheck['success']) {
            return $authCheck;
        }

        return $this->runAction(
            function () use ($id, $title, $message, $department_id, $course_id, $attachment, $expiry_date) {
                $this->announcementModel->update($id, $title, $message, $department_id, $course_id, $attachment, $expiry_date);
            },
            'Announcement updated successfully'
        );
    }

    /**
     * Archive announcement
     */
    public function archiveAnnouncement($id) {
        return $this->runAction(
            function () use ($id) {
                $this->announcementModel->archive($id);
            },
            'Announcement archived successfully'
        );
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement($id) {
        return $this->runAction(
            function () use ($id) {
                $this->announcementModel->delete($id);
            },
            'Announcement deleted successfully'
        );
    }

    /**
     * Record announcement view
     */
    public function recordAnnouncementView($announcement_id, $student_id) {
        return $this->runAction(
            function () use ($announcement_id, $student_id) {
                $this->announcementViewModel->recordView($announcement_id, $student_id);
            },
            'View recorded successfully'
        );
    }

    private function runAction($action, $successMessage, $includeId = false) {
        try {
            $result = $action();
            $response = ['success' => true, 'message' => $successMessage];

            if ($includeId) {
                $response['id'] = $result;
            }

            return $response;
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get view count for announcement
     */
    public function getViewCount($announcement_id) {
        return $this->announcementViewModel->getViewCount($announcement_id);
    }

    /**
     * Get all views for announcement
     */
    public function getViewsByAnnouncement($announcement_id) {
        return $this->announcementViewModel->getViewsByAnnouncement($announcement_id);
    }

    private function normalizeNullableValue($value) {
        if ($value === null) {
            return null;
        }

        if (is_string($value) && trim($value) === '') {
            return null;
        }

        return $value;
    }

    private function normalizeNullableValues($values) {
        return array_map(function ($value) {
            return $this->normalizeNullableValue($value);
        }, $values);
    }

    private function validateLecturerAnnouncementScope($userId, $department_id, $course_id) {
        $stmt = $this->pdo->prepare("SELECT role, department FROM users WHERE id = ?");
        $stmt->execute([$userId]);
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if (!$user) {
            return ['success' => false, 'message' => 'Invalid user context'];
        }

        if ($user['role'] !== 'lecturer') {
            return ['success' => true];
        }

        if (empty($course_id)) {
            return ['success' => false, 'message' => 'Lecturers can only post announcements for their courses'];
        }

        if ($user['department'] !== $department_id) {
            return ['success' => false, 'message' => 'Lecturers can only post to their own department'];
        }

        $courseStmt = $this->pdo->prepare("SELECT id, department_id, lecturer_id FROM courses WHERE id = ?");
        $courseStmt->execute([$course_id]);
        $course = $courseStmt->fetch(PDO::FETCH_ASSOC);

        if (!$course) {
            return ['success' => false, 'message' => 'Selected course does not exist'];
        }

        if ($course['department_id'] !== $department_id) {
            return ['success' => false, 'message' => 'Selected course is not in the selected department'];
        }

        if (!empty($course['lecturer_id']) && $course['lecturer_id'] !== $userId) {
            return ['success' => false, 'message' => 'You are not assigned to the selected course'];
        }

        return ['success' => true];
    }
}
?>
