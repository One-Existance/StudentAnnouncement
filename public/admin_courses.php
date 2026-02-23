<?php
/**
 * Manage Courses
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/CourseController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';

$authController = new AuthController($pdo);
$authController->requireRole('admin');

$user = $authController->getCurrentUser();
$courseController = new CourseController($pdo);
$departmentController = new DepartmentController($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'update_course':
            $result = $courseController->updateCourse(
                $_POST['course_id'],
                $_POST['course_name'],
                $_POST['course_code'],
                $_POST['dept_id']
            );
            break;

        case 'delete_course':
            if (isset($_POST['course_id'])) {
                $result = $courseController->deleteCourse($_POST['course_id']);
            }
            break;
    }

    if (isset($result)) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
    }
}

$courses = $courseController->getAllCourses();
$departments = $departmentController->getAllDepartments();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Courses - Student Announcement System</title>
    <link rel="stylesheet" href="css/admin_dashboard.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Manage Courses</h1>
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
            <h3>Courses</h3>
            <?php if (empty($courses)): ?>
                <div class="empty-state">No courses found</div>
            <?php else: ?>
                <div class="table-wrap">
                    <table class="manage-table">
                        <thead>
                            <tr>
                                <th>Course Name</th>
                                <th>Course Code</th>
                                <th>Department</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php foreach ($courses as $course): ?>
                                <tr>
                                    <form method="POST">
                                        <input type="hidden" name="course_id" value="<?php echo htmlspecialchars($course['id']); ?>">
                                        <td>
                                            <input type="text" name="course_name" value="<?php echo htmlspecialchars($course['course_name']); ?>" required>
                                        </td>
                                        <td>
                                            <input type="text" name="course_code" value="<?php echo htmlspecialchars($course['course_code']); ?>" required>
                                        </td>
                                        <td>
                                            <select name="dept_id" required>
                                                <option value="">Select Department</option>
                                                <?php foreach ($departments as $dept): ?>
                                                    <option value="<?php echo htmlspecialchars($dept['id']); ?>" <?php echo $course['department_id'] === $dept['id'] ? 'selected' : ''; ?>>
                                                        <?php echo htmlspecialchars($dept['name']); ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </td>
                                        <td>
                                            <div class="manage-actions">
                                                <button type="submit" name="action" value="update_course" class="btn-submit">Update</button>
                                                <button type="submit" name="action" value="delete_course" class="btn-delete" onclick="return confirm('Are you sure you want to delete this course?');">Delete</button>
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
