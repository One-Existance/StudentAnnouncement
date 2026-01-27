<?php
/**
 * Course Model Class
 */

class Course {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new course
     */
    public function create($course_name, $course_code, $department_id, $lecturer_id = null) {
        $id = $this->generateUUID();
        $stmt = $this->pdo->prepare("INSERT INTO courses (id, course_name, course_code, department_id, lecturer_id, created_at) VALUES (?, ?, ?, ?, ?, NOW())");
        $stmt->execute([$id, $course_name, $course_code, $department_id, $lecturer_id]);
        return $id;
    }

    /**
     * Get course by ID
     */
    public function getCourseById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all courses
     */
    public function getAllCourses() {
        $stmt = $this->pdo->query("SELECT * FROM courses ORDER BY course_name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses by department
     */
    public function getCoursesByDepartment($department_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE department_id = ? ORDER BY course_name ASC");
        $stmt->execute([$department_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Get courses by lecturer
     */
    public function getCoursesByLecturer($lecturer_id) {
        $stmt = $this->pdo->prepare("SELECT * FROM courses WHERE lecturer_id = ? ORDER BY course_name ASC");
        $stmt->execute([$lecturer_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update course
     */
    public function update($id, $course_name, $course_code, $department_id, $lecturer_id = null) {
        $stmt = $this->pdo->prepare("UPDATE courses SET course_name = ?, course_code = ?, department_id = ?, lecturer_id = ? WHERE id = ?");
        return $stmt->execute([$course_name, $course_code, $department_id, $lecturer_id, $id]);
    }

    /**
     * Delete course
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM courses WHERE id = ?");
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
