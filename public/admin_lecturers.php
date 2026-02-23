<?php
/**
 * Manage Lecturers
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';

$authController = new AuthController($pdo);
$authController->requireRole('admin');

$user = $authController->getCurrentUser();
$userController = new UserController($pdo);
$departmentController = new DepartmentController($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_lecturer':
            $department = empty($_POST['department']) ? null : $_POST['department'];
            $result = $userController->updateUser(
                $_POST['user_id'],
                $_POST['name'],
                $_POST['email'],
                'lecturer',
                $department
            );
            break;

        case 'delete_lecturer':
            if (isset($_POST['user_id'])) {
                $result = $userController->deleteUser($_POST['user_id']);
            }
            break;
    }

    if (isset($result)) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
    }
}

$lecturers = $userController->getUsersByRole('lecturer');
$departments = $departmentController->getAllDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Lecturers - Student Announcement System</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Manage Lecturers</h1>
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
            <h3>Lecturers</h3>
            <?php if (empty($lecturers)): ?>
                <div class="empty-state">No lecturers found</div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="manage-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Email</th>
                                <th>Department</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($lecturers as $lecturer): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="user_id" value="<?php echo htmlspecialchars($lecturer['id']); ?>">
                                        <td>
                                            <input type="text" name="name" value="<?php echo htmlspecialchars($lecturer['name']); ?>" required>
                                        </td>
                                        <td>
                                            <input type="email" name="email" value="<?php echo htmlspecialchars($lecturer['email']); ?>" required>
                                        </td>
                                        <td>
                                            <select name="department">
                                                <option value="">No Department</option>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo htmlspecialchars($dept['id']); ?>" <?php echo $lecturer['department'] === $dept['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($dept['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="manage-actions">
                                                <button type="submit" name="action" value="update_lecturer" class="btn-submit">Update</button>
                                                <button type="submit" name="action" value="delete_lecturer" class="btn-delete" onclick="return confirm('Are you sure you want to delete this lecturer?');">Delete</button>
                                            </div>
                                        </td>
                                    </form>
                                </tr>
                            <?php endforeach; ?>
                        </tbody>
                    </table>
                </div>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
