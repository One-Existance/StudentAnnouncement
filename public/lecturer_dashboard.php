<?php
/**
 * Lecturer Dashboard
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/Course.php';

$authController = new AuthController($pdo);
$authController->requireRole('lecturer');

$user = $authController->getCurrentUser();
$announcementController = new AnnouncementController($pdo);
$departmentModel = new Department($pdo);
$courseModel = new Course($pdo);

// Handle announcement creation
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_announcement':
            $result = $announcementController->createAnnouncement(
                $_POST['title'],
                $_POST['message'],
                $user['id'],
                $_POST['department_id'],
                $_POST['course_id'] ?? null,
                $_POST['attachment'] ?? null,
                $_POST['expiry_date'] ?? null
            );

            $message = $result['message'];
            $messageType = $result['success'] ? 'success' : 'error';
            break;

        case 'delete_announcement':
            if (isset($_POST['announcement_id'])) {
                $announcement = $announcementController->getAnnouncementById($_POST['announcement_id']);
                if ($announcement && $announcement['posted_by'] === $user['id']) {
                    $result = $announcementController->deleteAnnouncement($_POST['announcement_id']);
                } else {
                    $result = ['success' => false, 'message' => 'You can only delete your own announcements'];
                }

                $message = $result['message'];
                $messageType = $result['success'] ? 'success' : 'error';
            }
            break;
    }
}

// Get lecturer's announcements
$myAnnouncements = $announcementController->getAnnouncementsByUser($user['id']);
$allAnnouncements = $announcementController->getActiveAnnouncements();

$departments = $departmentModel->getAllDepartments();
$courses = $courseModel->getAllCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lecturer Dashboard - Student Announcement System</title>
    <style>
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }

        body {
            font-family: Arial, sans-serif;
            background-color: #f4f4f4;
            color: #333;
        }

        .header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
        }

        .header-content {
            max-width: 1200px;
            margin: 0 auto;
            padding: 0 20px;
            display: flex;
            justify-content: space-between;
            align-items: center;
        }

        .header h1 {
            margin: 0;
        }

        .user-info {
            display: flex;
            align-items: center;
            gap: 20px;
        }

        .user-info span {
            font-size: 14px;
        }

        .btn-logout {
            background-color: #e74c3c;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-logout:hover {
            background-color: #c0392b;
        }

        .btn-secondary {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 4px;
            cursor: pointer;
            text-decoration: none;
            font-size: 14px;
        }

        .btn-secondary:hover {
            background-color: #2980b9;
        }

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 30px;
        }

        .dashboard-card {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .dashboard-card h3 {
            color: #2c3e50;
            margin-bottom: 15px;
        }

        .stats {
            display: flex;
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: #27ae60;
            color: white;
            padding: 15px;
            border-radius: 5px;
            flex: 1;
            text-align: center;
        }

        .stat-card h4 {
            margin: 0 0 5px 0;
            font-size: 20px;
        }

        .stat-card p {
            margin: 0;
            font-size: 12px;
        }

        .form-group {
            margin-bottom: 15px;
        }

        label {
            display: block;
            margin-bottom: 5px;
            font-weight: bold;
        }

        input, textarea, select {
            width: 100%;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 100px;
        }

        .btn-submit {
            background-color: #27ae60;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #229954;
        }

        .message {
            padding: 10px;
            margin-bottom: 15px;
            border-radius: 3px;
        }

        .success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

        .error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .announcement-item {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #27ae60;
            border-radius: 3px;
        }

        .announcement-item h4 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }

        .announcement-meta {
            font-size: 12px;
            color: #7f8c8d;
            margin-bottom: 10px;
        }

        .announcement-item p {
            line-height: 1.6;
            color: #555;
        }

        .btn-view {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            text-decoration: none;
            margin-right: 5px;
        }

        .btn-view:hover {
            background-color: #2980b9;
        }

        .no-announcements {
            padding: 20px;
            text-align: center;
            color: #7f8c8d;
        }

        @media (max-width: 768px) {
            .dashboard-grid {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
    <div class="header">
        <div class="header-content">
            <h1>Student Announcement System</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['name']); ?> (Lecturer)</span>
                <a href="lecturer_password.php" class="btn-secondary">Update Password</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-grid">
            <!-- Create Announcement Section -->
            <div class="dashboard-card">
                <h3>Create New Announcement</h3>

                <?php if ($message): ?>
                    <div class="message <?php echo $messageType; ?>">
                        <?php echo htmlspecialchars($message); ?>
                    </div>
                <?php endif; ?>

                <form method="POST">
                    <input type="hidden" name="action" value="create_announcement">

                    <div class="form-group">
                        <label for="title">Title:</label>
                        <input type="text" id="title" name="title" required>
                    </div>

                    <div class="form-group">
                        <label for="message">Message:</label>
                        <textarea id="message" name="message" required></textarea>
                    </div>

                    <div class="form-group">
                        <label for="department_id">Department:</label>
                        <select id="department_id" name="department_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="course_id">Course (Required):</label>
                        <select id="course_id" name="course_id">
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course['id']); ?>" data-department-id="<?php echo htmlspecialchars($course['department_id']); ?>">
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="attachment">Attachment (Optional):</label>
                        <input type="text" id="attachment" name="attachment" placeholder="File path or URL">
                    </div>

                    <div class="form-group">
                        <label for="expiry_date">Expiry Date (Optional):</label>
                        <input type="date" id="expiry_date" name="expiry_date">
                    </div>

                    <button type="submit" class="btn-submit">Post Announcement</button>
                </form>
            </div>

            <!-- My Announcements Section -->
            <div class="dashboard-card">
                <h3>My Announcements</h3>

                <div class="stats">
                    <div class="stat-card">
                        <h4><?php echo count($myAnnouncements); ?></h4>
                        <p>My Posts</p>
                    </div>
                    <div class="stat-card">
                        <h4><?php echo count($allAnnouncements); ?></h4>
                        <p>Total Active</p>
                    </div>
                </div>

                <?php if (empty($myAnnouncements)): ?>
                    <div class="no-announcements">
                        <p>You haven't posted any announcements yet.</p>
                    </div>
                <?php else: ?>
                    <?php foreach (array_slice($myAnnouncements, 0, 5) as $announcement): ?>
                        <div class="announcement-item">
                            <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                            <div class="announcement-meta">
                                <strong>Posted:</strong> <?php echo htmlspecialchars(date('M d, Y H:i', strtotime($announcement['created_at']))); ?>
                                <?php if ($announcement['expiry_date']): ?>
                                    | <strong>Expires:</strong> <?php echo htmlspecialchars(date('M d, Y', strtotime($announcement['expiry_date']))); ?>
                                <?php endif; ?>
                            </div>
                            <p><?php echo htmlspecialchars(substr($announcement['message'], 0, 150) . (strlen($announcement['message']) > 150 ? '...' : '')); ?></p>
                            <a href="view_announcement.php?id=<?php echo htmlspecialchars($announcement['id']); ?>" class="btn-view">View</a>
                            <a href="edit_announcement.php?id=<?php echo htmlspecialchars($announcement['id']); ?>" class="btn-view">Edit</a>
                            <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                <input type="hidden" name="action" value="delete_announcement">
                                <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['id']); ?>">
                                <button type="submit" class="btn-view" style="background-color: #e74c3c;">Delete</button>
                            </form>
                        </div>
                    <?php endforeach; ?>
                <?php endif; ?>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const departmentSelect = document.getElementById('department_id');
            const courseSelect = document.getElementById('course_id');
            if (!departmentSelect || !courseSelect) return;

            function filterCoursesByDepartment() {
                const selectedDepartment = departmentSelect.value;
                const options = courseSelect.querySelectorAll('option[data-department-id]');

                courseSelect.value = '';

                options.forEach(function (option) {
                    option.hidden = selectedDepartment !== '' && option.dataset.departmentId !== selectedDepartment;
                });
            }

            departmentSelect.addEventListener('change', filterCoursesByDepartment);
            filterCoursesByDepartment();
        })();
    </script>
</body>
</html>
