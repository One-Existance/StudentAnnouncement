<?php
/**
 * Course Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Course.php';

class CourseController {
    private $courseModel;

    public function __construct($pdo) {
        $this->courseModel = new Course($pdo);
    }

    /**
     * Create a new course
     */
    public function createCourse($course_name, $course_code, $department_id, $lecturer_id = null) {
        if (empty($course_name) || empty($course_code) || empty($department_id)) {
            return ['success' => false, 'message' => 'Course name, code, and department ID are required'];
        }

        try {
            $id = $this->courseModel->create($course_name, $course_code, $department_id, $lecturer_id);
            return ['success' => true, 'message' => 'Course created successfully', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get course by ID
     */
    public function getCourseById($id) {
        return $this->courseModel->getCourseById($id);
    }

    /**
     * Get all courses
     */
    public function getAllCourses() {
        return $this->courseModel->getAllCourses();
    }

    /**
     * Get courses by department
     */
    public function getCoursesByDepartment($department_id) {
        return $this->courseModel->getCoursesByDepartment($department_id);
    }

    /**
     * Get courses by lecturer
     */
    public function getCoursesByLecturer($lecturer_id) {
        return $this->courseModel->getCoursesByLecturer($lecturer_id);
    }

    /**
     * Update course
     */
    public function updateCourse($id, $course_name, $course_code, $department_id, $lecturer_id = null) {
        if (empty($course_name) || empty($course_code) || empty($department_id)) {
            return ['success' => false, 'message' => 'Course name, code, and department ID are required'];
        }

        try {
            $this->courseModel->update($id, $course_name, $course_code, $department_id, $lecturer_id);
            return ['success' => true, 'message' => 'Course updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete course
     */
    public function deleteCourse($id) {
        try {
            $this->courseModel->delete($id);
            return ['success' => true, 'message' => 'Course deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>
