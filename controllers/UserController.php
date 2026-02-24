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

        [$department, $avatar] = $this->normalizeNullableValues([$department, $avatar]);

        return $this->runAction(
            function () use ($name, $email, $role, $department, $password, $avatar) {
                $passwordHash = password_hash($password, PASSWORD_DEFAULT);
                return $this->userModel->create($name, $email, $role, $department, $passwordHash, $avatar);
            },
            'User created successfully',
            true
        );
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

        $department = $this->normalizeNullableValue($department);

        return $this->runAction(
            function () use ($id, $name, $email, $role, $department) {
                $this->userModel->update($id, $name, $email, $role, $department);
            },
            'User updated successfully'
        );
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

        return $this->runAction(
            function () use ($id, $passwordHash) {
                $this->userModel->updatePassword($id, $passwordHash);
            },
            'Password updated successfully'
        );
    }

    /**
     * Delete user
     */
    public function deleteUser($id) {
        return $this->runAction(
            function () use ($id) {
                $this->userModel->delete($id);
            },
            'User deleted successfully'
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

    private function normalizeNullableValues($values) {
        return array_map(function ($value) {
            return $this->normalizeNullableValue($value);
        }, $values);
    }
}
?>
