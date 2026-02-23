<?php
/**
 * Admin Password Update
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';

$authController = new AuthController($pdo);
$authController->requireRole('admin');

$user = $authController->getCurrentUser();
$userController = new UserController($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'update_password') {
        if (($_POST['new_password'] ?? '') !== ($_POST['confirm_password'] ?? '')) {
            $result = ['success' => false, 'message' => 'New password and confirmation do not match'];
        } else {
            $result = $userController->updatePassword(
                $user['id'],
                $_POST['current_password'] ?? null,
                $_POST['new_password'] ?? null
            );
        }

        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Update Password - Student Announcement System</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Update Password</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['name']); ?> (Administrator)</span>
                <a href="admin_dashboard.php" class="btn-secondary">Back to Dashboard</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <?php if ($message): ?>
            <div class="message <?php echo $messageType; ?>">
                <?php echo htmlspecialchars($message); ?>
            </div>
        <?php endif; ?>

        <div class="dashboard-card">
            <h3>Change Password</h3>
            <form method="POST">
                <input type="hidden" name="action" value="update_password">
                <div class="form-group">
                    <label>Current Password:</label>
                    <input type="password" name="current_password" required>
                </div>
                <div class="form-group">
                    <label>New Password:</label>
                    <input type="password" name="new_password" required>
                </div>
                <div class="form-group">
                    <label>Confirm New Password:</label>
                    <input type="password" name="confirm_password" required>
                </div>
                <button type="submit" class="btn-submit">Update Password</button>
            </form>
        </div>
    </div>
</body>
</html>
