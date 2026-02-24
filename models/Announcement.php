<?php
/**
 * Announcement Model Class
 */

class Announcement {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new announcement
     */
    public function create($title, $message, $posted_by, $department_id, $course_id = null, $attachment = null, $expiry_date = null) {
        $id = $this->generateUUID();
        $stmt = $this->pdo->prepare("INSERT INTO announcements (id, title, message, posted_by, department_id, course_id, attachment, expiry_date, created_at) VALUES (?, ?, ?, ?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $title, $message, $posted_by, $department_id, $course_id, $attachment, $expiry_date]);
        return $id;
    }

    /**
     * Get announcement by ID
     */
    public function getAnnouncementById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcements WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all announcements
     */
    public function getAllAnnouncements() {
        $stmt = $this->pdo->query("SELECT * FROM announcements ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get announcements by department
     */
    public function getAnnouncementsByDepartment($department_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcements WHERE department_id = ? ORDER BY created_at DESC");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get active announcements by department
     */
    public function getActiveAnnouncementsByDepartment($department_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcements WHERE department_id = ? AND (expiry_date IS NULL OR expiry_date >= CURDATE()) ORDER BY created_at DESC");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get announcements by course
     */
    public function getAnnouncementsByCourse($course_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcements WHERE course_id = ? ORDER BY created_at DESC");
        $stmt->execute([$course_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get announcements posted by a user
     */
    public function getAnnouncementsByUser($posted_by) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcements WHERE posted_by = ? ORDER BY created_at DESC");
        $stmt->execute([$posted_by]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update announcement
     */
    public function update($id, $title, $message, $department_id, $course_id = null, $attachment = null, $expiry_date = null) {
        $stmt = $this->pdo->prepare("UPDATE announcements SET title = ?, message = ?, department_id = ?, course_id = ?, attachment = ?, expiry_date = ? WHERE id = ?");
        return $stmt->execute([$title, $message, $department_id, $course_id, $attachment, $expiry_date, $id]);
    }

    /**
     * Delete announcement
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM announcements WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Archive announcement by marking it expired
     */
    public function archive($id) {
        $stmt = $this->pdo->prepare("UPDATE announcements SET expiry_date = DATE_SUB(CURDATE(), INTERVAL 1 DAY) WHERE id = ?");
        return $stmt->execute([$id]);
    }

    /**
     * Get active announcements (not expired)
     */
    public function getActiveAnnouncements() {
        $stmt = $this->pdo->query("SELECT * FROM announcements WHERE expiry_date IS NULL OR expiry_date >= CURDATE() ORDER BY created_at DESC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Generate UUID
     */
    private function generateUUID() {
        return sprintf(
            '%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }
}
?>
