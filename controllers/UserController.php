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
    public function createUser($name, $email, $role, $department = null, $password = null, $avatar = null) {
        if (empty($name) || empty($email) || empty($role) || empty($password)) {
            return ['success' => false, 'message' => 'Name, email, role, and password are required'];
        }

        if (strlen($password) < 6) {
            return ['success' => false, 'message' => 'Password must be at least 6 characters'];
        }

        try {
            $passwordHash = password_hash($password, PASSWORD_DEFAULT);
            $id = $this->userModel->create($name, $email, $role, $department, $passwordHash, $avatar);
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
    public function updateUser($id, $name, $email, $role, $department = null) {
        if (empty($name) || empty($email) || empty($role)) {
            return ['success' => false, 'message' => 'Name, email, and role are required'];
        }

        try {
            $this->userModel->update($id, $name, $email, $role, $department);
            return ['success' => true, 'message' => 'User updated successfully'];
        } catch (Exception $e) {
            return ['success' => false, 'message' => 'Error: ' . $e->getMessage()];
        }
    }

    /**
     * Update user password
     */
    public function updatePassword($id, $currentPassword, $newPassword) {
        if (empty($currentPassword) || empty($newPassword)) {
            return ['success' => false, 'message' => 'Current and new password are required'];
        }

        if (strlen($newPassword) < 6) {
            return ['success' => false, 'message' => 'New password must be at least 6 characters'];
        }

        $user = $this->userModel->getUserById($id);
        if (!$user || empty($user['password'])) {
            return ['success' => false, 'message' => 'Password not set. Contact an administrator'];
        }

        if (!password_verify($currentPassword, $user['password'])) {
            return ['success' => false, 'message' => 'Current password is incorrect'];
        }

        $passwordHash = password_hash($newPassword, PASSWORD_DEFAULT);

        try {
            $this->userModel->updatePassword($id, $passwordHash);
            return ['success' => true, 'message' => 'Password updated successfully'];
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
