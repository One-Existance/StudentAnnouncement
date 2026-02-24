<?php
/**
 * Authentication Controller
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../models/User.php';

class AuthController {
    private $userModel;

    public function __construct($pdo) {
        $this->userModel = new User($pdo);
        $this->startSession();
    }

    /**
     * Start session if not already started
     */
    private function startSession() {
        if (session_status() == PHP_SESSION_NONE) {
            session_start();
        }
    }

    /**
     * Login user
     */
    public function login($email, $password) {
        if (empty($email) || empty($password)) {
            return $this->failure('Email and password are required');
        }

        $user = $this->userModel->getUserByEmail($email);

        if (!$user) {
            return $this->failure('Invalid email or password');
        }

        if (empty($user['password'])) {
            return $this->failure('Password not set. Contact an administrator');
        }

        if (!password_verify($password, $user['password'])) {
            return $this->failure('Invalid email or password');
        }

        $this->setSessionUser($user);

        return ['success' => true, 'message' => 'Login successful', 'user' => $user];
    }

    /**
     * Logout user
     */
    public function logout() {
        session_destroy();
        return ['success' => true, 'message' => 'Logout successful'];
    }

    /**
     * Check if user is logged in
     */
    public function isLoggedIn() {
        return isset($_SESSION['user_id']);
    }

    /**
     * Get current user
     */
    public function getCurrentUser() {
        if (!$this->isLoggedIn()) {
            return null;
        }

        return [
            'id' => $_SESSION['user_id'],
            'email' => $_SESSION['user_email'],
            'name' => $_SESSION['user_name'],
            'role' => $_SESSION['user_role'],
            'department' => $_SESSION['user_department']
        ];
    }

    /**
     * Check if current user has role
     */
    public function hasRole($role) {
        $user = $this->getCurrentUser();
        return $user && $user['role'] === $role;
    }

    /**
     * Check if current user can create announcements
     */
    public function canCreateAnnouncements() {
        return $this->hasRole('admin') || $this->hasRole('lecturer');
    }

    /**
     * Check if current user can manage users
     */
    public function canManageUsers() {
        return $this->hasRole('admin');
    }

    /**
     * Check if current user can manage departments
     */
    public function canManageDepartments() {
        return $this->hasRole('admin');
    }

    /**
     * Require login - redirect if not logged in
     */
    public function requireLogin() {
        if (!$this->isLoggedIn()) {
            header('Location: login.php');
            exit;
        }
    }

    /**
     * Require specific role - redirect if not authorized
     */
    public function requireRole($role) {
        $this->requireLogin();

        if (!$this->hasRole($role)) {
            header('Location: unauthorized.php');
            exit;
        }
    }

    private function setSessionUser($user) {
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['user_email'] = $user['email'];
        $_SESSION['user_name'] = $user['name'];
        $_SESSION['user_role'] = $user['role'];
        $_SESSION['user_department'] = $user['department'];
    }

    private function failure($message) {
        return ['success' => false, 'message' => $message];
    }
}
?>
