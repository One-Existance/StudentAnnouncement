<?php
/**
 * Announcement Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Announcement.php';
require_once __DIR__ . '/../models/AnnouncementView.php';

class AnnouncementController {
    private $announcementModel;
    private $announcementViewModel;

    public function __construct($pdo) {
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

        try {
            $id = $this->announcementModel->create($title, $message, $posted_by, $department_id, $course_id, $attachment, $expiry_date);
            return ['success' => true, 'message' => 'Announcement created successfully', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
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
     * Update announcement
     */
    public function updateAnnouncement($id, $title, $message, $department_id, $course_id = null, $attachment = null, $expiry_date = null) {
        if (empty($title) || empty($message) || empty($department_id)) {
            return ['success' => false, 'message' => 'Title, message, and department ID are required'];
        }

        try {
            $this->announcementModel->update($id, $title, $message, $department_id, $course_id, $attachment, $expiry_date);
            return ['success' => true, 'message' => 'Announcement updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete announcement
     */
    public function deleteAnnouncement($id) {
        try {
            $this->announcementModel->delete($id);
            return ['success' => true, 'message' => 'Announcement deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Record announcement view
     */
    public function recordAnnouncementView($announcement_id, $student_id) {
        try {
            $this->announcementViewModel->recordView($announcement_id, $student_id);
            return ['success' => true, 'message' => 'View recorded successfully'];
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
}
?>
