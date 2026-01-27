<?php
/**
 * Department Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/Department.php';

class DepartmentController {
    private $departmentModel;

    public function __construct($pdo) {
        $this->departmentModel = new Department($pdo);
    }

    /**
     * Create a new department
     */
    public function createDepartment($name, $description = null) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Department name is required'];
        }

        try {
            $id = $this->departmentModel->create($name, $description);
            return ['success' => true, 'message' => 'Department created successfully', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get department by ID
     */
    public function getDepartmentById($id) {
        return $this->departmentModel->getDepartmentById($id);
    }

    /**
     * Get all departments
     */
    public function getAllDepartments() {
        return $this->departmentModel->getAllDepartments();
    }

    /**
     * Update department
     */
    public function updateDepartment($id, $name, $description = null) {
        if (empty($name)) {
            return ['success' => false, 'message' => 'Department name is required'];
        }

        try {
            $this->departmentModel->update($id, $name, $description);
            return ['success' => true, 'message' => 'Department updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete department
     */
    public function deleteDepartment($id) {
        try {
            $this->departmentModel->delete($id);
            return ['success' => true, 'message' => 'Department deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>
