<?php
/**
 * Manage Departments
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';

$authController = new AuthController($pdo);
$authController->requireRole('admin');

$user = $authController->getCurrentUser();
$departmentController = new DepartmentController($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_department':
            $result = $departmentController->updateDepartment(
                $_POST['dept_id'],
                $_POST['dept_name'],
                $_POST['dept_description'] ?? null
            );
            break;

        case 'delete_department':
            if (isset($_POST['dept_id'])) {
                $result = $departmentController->deleteDepartment($_POST['dept_id']);
            }
            break;
    }

    if (isset($result)) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
    }
}

$departments = $departmentController->getAllDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Departments - Student Announcement System</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Manage Departments</h1>
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
            <h3>Departments</h3>
            <?php if (empty($departments)): ?>
                <div class="empty-state">No departments found</div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="manage-table">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Description</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($departments as $dept): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="dept_id" value="<?php echo htmlspecialchars($dept['id']); ?>">
                                        <td>
                                            <input type="text" name="dept_name" value="<?php echo htmlspecialchars($dept['name']); ?>" required>
                                        </td>
                                        <td>
                                            <textarea name="dept_description"><?php echo htmlspecialchars($dept['description']); ?></textarea>
                                        </td>
                                        <td>
                                            <div class="manage-actions">
                                                <button type="submit" name="action" value="update_department" class="btn-submit">Update</button>
                                                <button type="submit" name="action" value="delete_department" class="btn-delete" onclick="return confirm('Are you sure you want to delete this department?');">Delete</button>
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
