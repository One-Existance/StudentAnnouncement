<?php
/**
 * Announcement View Model Class
 */

class AnnouncementView {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Record announcement view
     */
    public function recordView($announcement_id, $student_id) {
        $id = $this->generateUUID();
        $stmt = $this->pdo->prepare("INSERT INTO announcement_views (id, announcement_id, student_id, viewed_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$id, $announcement_id, $student_id]);
        return $id;
    }

    /**
     * Get all views for an announcement
     */
    public function getViewsByAnnouncement($announcement_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcement_views WHERE announcement_id = ? ORDER BY viewed_at DESC");
        $stmt->execute([$announcement_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get view count for an announcement
     */
    public function getViewCount($announcement_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM announcement_views WHERE announcement_id = ?");
        $stmt->execute([$announcement_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'];
    }

    /**
     * Check if student has viewed announcement
     */
    public function hasViewed($announcement_id, $student_id) {
        $stmt = $this->pdo->prepare("SELECT COUNT(*) as count FROM announcement_views WHERE announcement_id = ? AND student_id = ?");
        $stmt->execute([$announcement_id, $student_id]);
        $result = $stmt->fetch(PDO::FETCH_ASSOC);
        return $result['count'] > 0;
    }

    /**
     * Get views for a student
     */
    public function getViewsByStudent($student_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM announcement_views WHERE student_id = ? ORDER BY viewed_at DESC");
        $stmt->execute([$student_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Delete view record
     */
    public function deleteView($id) {
        $stmt = $this->pdo->prepare("DELETE FROM announcement_views WHERE id = ?");
        return $stmt->execute([$id]);
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
