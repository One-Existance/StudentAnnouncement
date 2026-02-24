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

        $description = $this->normalizeNullableValue($description);

        return $this->runAction(
            function () use ($name, $description) {
                return $this->departmentModel->create($name, $description);
            },
            'Department created successfully',
            true
        );
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

        $description = $this->normalizeNullableValue($description);

        return $this->runAction(
            function () use ($id, $name, $description) {
                $this->departmentModel->update($id, $name, $description);
            },
            'Department updated successfully'
        );
    }

    /**
     * Delete department
     */
    public function deleteDepartment($id) {
        return $this->runAction(
            function () use ($id) {
                $this->departmentModel->delete($id);
            },
            'Department deleted successfully'
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

    private function normalizeNullableValue($value) {
        if ($value === null) {
            return null;
        }

        if (is_string($value) && trim($value) === '') {
            return null;
        }

        return $value;
    }
}
?>
