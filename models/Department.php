<?php
/**
 * Department Model Class
 */

class Department {
    private $pdo;

    public function __construct($pdo) {
        $this->pdo = $pdo;
    }

    /**
     * Create a new department
     */
    public function create($name, $description = null) {
        $id = $this->generateUUID();
        $stmt = $this->pdo->prepare("INSERT INTO departments (id, name, description, created_at) VALUES (?, ?, ?, NOW())");
        $stmt->execute([$id, $name, $description]);
        return $id;
    }

    /**
     * Get department by ID
     */
    public function getDepartmentById($id) {
        $stmt = $this->pdo->prepare("SELECT * FROM departments WHERE id = ?");
        $stmt->execute([$id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    /**
     * Get all departments
     */
    public function getAllDepartments() {
        $stmt = $this->pdo->query("SELECT * FROM departments ORDER BY name ASC");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    /**
     * Update department
     */
    public function update($id, $name, $description = null) {
        $stmt = $this->pdo->prepare("UPDATE departments SET name = ?, description = ? WHERE id = ?");
        return $stmt->execute([$name, $description, $id]);
    }

    /**
     * Delete department
     */
    public function delete($id) {
        $stmt = $this->pdo->prepare("DELETE FROM departments WHERE id = ?");
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
