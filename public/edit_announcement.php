<?php
/**
 * Edit Announcement Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';
require_once __DIR__ . '/../controllers/CourseController.php';

$authController = new AuthController($pdo);
$authController->requireLogin();

$user = $authController->getCurrentUser();
$isAdmin = $user && $user['role'] === 'admin';
$isLecturer = $user && $user['role'] === 'lecturer';

if (!$isAdmin && !$isLecturer) {
    header('Location: unauthorized.php');
    exit;
}
$announcementController = new AnnouncementController($pdo);
$departmentController = new DepartmentController($pdo);
$courseController = new CourseController($pdo);

// Check if announcement ID is provided
if (!isset($_GET['id'])) {
    header('Location: admin_dashboard.php');
    exit;
}

$announcementId = $_GET['id'];
$announcement = $announcementController->getAnnouncementById($announcementId);

if (!$announcement) {
    header('Location: ' . ($isLecturer ? 'lecturer_dashboard.php' : 'admin_dashboard.php'));
    exit;
}

if ($isLecturer && $announcement['posted_by'] !== $user['id']) {
    header('Location: unauthorized.php');
    exit;
}

$message = '';
$messageType = '';

// Handle form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action']) && $_POST['action'] === 'update_announcement') {
    $result = $announcementController->updateAnnouncement(
        $announcementId,
        $_POST['title'],
        $_POST['message'],
        $_POST['department_id'],
        $_POST['course_id'] ?? null,
        $_POST['attachment'] ?? null,
        $_POST['expiry_date'] ?? null
    );

    if ($result['success']) {
        $message = $result['message'];
        $messageType = 'success';
        // Refresh announcement data
        $announcement = $announcementController->getAnnouncementById($announcementId);
    } else {
        $message = $result['message'];
        $messageType = 'error';
    }
}

$departments = $departmentController->getAllDepartments();
$courses = $courseController->getAllCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Announcement - Student Announcement System</title>
    <link rel="stylesheet" href="css/edit_announcement.css">
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Edit Announcement</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['name']); ?> (<?php echo $isLecturer ? 'Lecturer' : 'Administrator'; ?>)</span>
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

        <div class="form-card">
            <h2>Edit Announcement</h2>

            <div class="info-section">
                <p><span class="info-label">Announcement ID:</span> <?php echo htmlspecialchars($announcement['id']); ?></p>
                <p><span class="info-label">Created:</span> <?php echo htmlspecialchars(date('M d, Y H:i', strtotime($announcement['created_at']))); ?></p>
                <p><span class="info-label">Posted By:</span> <?php echo htmlspecialchars($announcement['posted_by']); ?></p>
            </div>

            <form method="POST">
                <input type="hidden" name="action" value="update_announcement">

                <div class="form-group">
                    <label for="title">Title *</label>
                    <input type="text" id="title" name="title" value="<?php echo htmlspecialchars($announcement['title']); ?>" required>
                </div>

                <div class="form-group">
                    <label for="message">Message *</label>
                    <textarea id="message" name="message" required><?php echo htmlspecialchars($announcement['message']); ?></textarea>
                </div>

                <div class="form-group">
                    <label for="department_id">Department *</label>
                    <select id="department_id" name="department_id" required>
                        <option value="">Select Department</option>
                        <?php foreach ($departments as $dept): ?>
                            <option value="<?php echo htmlspecialchars($dept['id']); ?>" <?php echo $dept['id'] === $announcement['department_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($dept['name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="course_id">Course (Optional)</label>
                    <select id="course_id" name="course_id">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['id']); ?>" <?php echo $course['id'] === $announcement['course_id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>

                <div class="form-group">
                    <label for="attachment">Attachment (Optional)</label>
                    <input type="text" id="attachment" name="attachment" value="<?php echo htmlspecialchars($announcement['attachment'] ?? ''); ?>" placeholder="File path or URL">
                </div>

                <div class="form-group">
                    <label for="expiry_date">Expiry Date (Optional)</label>
                    <input type="date" id="expiry_date" name="expiry_date" value="<?php echo $announcement['expiry_date'] ? htmlspecialchars(substr($announcement['expiry_date'], 0, 10)) : ''; ?>">
                </div>

                <div class="button-group">
                    <button type="submit" class="btn-submit">Update Announcement</button>
                    <a href="<?php echo $isLecturer ? 'lecturer_dashboard.php' : 'admin_dashboard.php'; ?>" class="btn-cancel">Cancel</a>
                </div>
            </form>
        </div>
    </div>
</body>
</html>
