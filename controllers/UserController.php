<?php
/**
 * User Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class UserController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
    }

    /**
     * Create a new user
     */
    public function createUser($name, $email, $role, $department = null, $avatar = null) {
        if (empty($name) || empty($email) || empty($role)) {
            return ['success' => false, 'message' => 'Name, email, and role are required'];
        }

        try {
            $id = $this->userModel->create($name, $email, $role, $department, $avatar);
            return ['success' => true, 'message' => 'User created successfully', 'id' => $id];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Get user by ID
     */
    public function getUserById($id) {
        return $this->userModel->getUserById($id);
    }

    /**
     * Get user by email
     */
    public function getUserByEmail($email) {
        return $this->userModel->getUserByEmail($email);
    }

    /**
     * Get all users
     */
    public function getAllUsers() {
        return $this->userModel->getAllUsers();
    }

    /**
     * Get users by role
     */
    public function getUsersByRole($role) {
        return $this->userModel->getUsersByRole($role);
    }

    /**
     * Update user
     */
    public function updateUser($id, $name, $email, $department = null, $avatar = null) {
        if (empty($name) || empty($email)) {
            return ['success' => false, 'message' => 'Name and email are required'];
        }

        try {
            $this->userModel->update($id, $name, $email, $department, $avatar);
            return ['success' => true, 'message' => 'User updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        try {
            $this->userModel->delete($id);
            return ['success' => true, 'message' => 'User deleted successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }
}
?>
