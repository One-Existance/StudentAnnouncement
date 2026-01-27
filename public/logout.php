<?php
/**
 * Logout Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController($pdo);
$result = $authController->logout();

header('Location: login.php');
exit;
?>
