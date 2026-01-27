<?php
/**
 * Admin Dashboard
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/UserController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';
require_once __DIR__ . '/../controllers/CourseController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

$authController = new AuthController($pdo);
$authController->requireRole('admin');

$user = $authController->getCurrentUser();
$userController = new UserController($pdo);
$departmentController = new DepartmentController($pdo);
$courseController = new CourseController($pdo);
$announcementController = new AnnouncementController($pdo);

// Handle form submissions
$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    switch ($_POST['action']) {
        case 'create_user':
            $result = $userController->createUser(
                $_POST['name'],
                $_POST['email'],
                $_POST['role'],
                $_POST['department'] ?? null,
                $_POST['avatar'] ?? null
            );
            break;

        case 'create_department':
            $result = $departmentController->createDepartment(
                $_POST['dept_name'],
                $_POST['dept_description'] ?? null
            );
            break;

        case 'create_course':
            $result = $courseController->createCourse(
                $_POST['course_name'],
                $_POST['course_code'],
                $_POST['dept_id'],
                $_POST['lecturer_id'] ?? null
            );
            break;

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
            break;
    }

    if (isset($result)) {
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
    }
}

// Get data for display
$users = $userController->getAllUsers();
$departments = $departmentController->getAllDepartments();
$courses = $courseController->getAllCourses();
$announcements = $announcementController->getAllAnnouncements();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - Student Announcement System</title>
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

        .container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        .dashboard-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
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
            border-bottom: 2px solid #3498db;
            padding-bottom: 10px;
        }

        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(150px, 1fr));
            gap: 15px;
            margin-bottom: 20px;
        }

        .stat-card {
            background-color: #3498db;
            color: white;
            padding: 15px;
            border-radius: 5px;
            text-align: center;
        }

        .stat-card.admin { background-color: #9b59b6; }
        .stat-card.lecturer { background-color: #27ae60; }
        .stat-card.student { background-color: #e67e22; }

        .stat-card h4 {
            margin: 0 0 5px 0;
            font-size: 24px;
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
            font-size: 14px;
        }

        input, textarea, select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 3px;
            font-size: 14px;
        }

        textarea {
            resize: vertical;
            min-height: 80px;
        }

        .btn-submit {
            background-color: #3498db;
            color: white;
            padding: 8px 16px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
            width: 100%;
        }

        .btn-submit:hover {
            background-color: #2980b9;
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

        .data-list {
            max-height: 300px;
            overflow-y: auto;
        }

        .data-item {
            background-color: #f9f9f9;
            padding: 10px;
            margin-bottom: 8px;
            border-radius: 3px;
            border-left: 3px solid #3498db;
        }

        .data-item.admin { border-left-color: #9b59b6; }
        .data-item.lecturer { border-left-color: #27ae60; }
        .data-item.student { border-left-color: #e67e22; }

        .data-item h5 {
            margin: 0 0 5px 0;
            font-size: 14px;
        }

        .data-item p {
            margin: 0;
            font-size: 12px;
            color: #666;
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
            <h1>Admin Dashboard - Student Announcement System</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['name']); ?> (Administrator)</span>
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

        <!-- Statistics -->
        <div class="dashboard-card">
            <h3>System Overview</h3>
            <div class="stats-grid">
                <div class="stat-card">
                    <h4><?php echo count($users); ?></h4>
                    <p>Total Users</p>
                </div>
                <div class="stat-card admin">
                    <h4><?php echo count(array_filter($users, function($u) { return $u['role'] === 'admin'; })); ?></h4>
                    <p>Administrators</p>
                </div>
                <div class="stat-card lecturer">
                    <h4><?php echo count(array_filter($users, function($u) { return $u['role'] === 'lecturer'; })); ?></h4>
                    <p>Lecturers</p>
                </div>
                <div class="stat-card student">
                    <h4><?php echo count(array_filter($users, function($u) { return $u['role'] === 'student'; })); ?></h4>
                    <p>Students</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo count($departments); ?></h4>
                    <p>Departments</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo count($courses); ?></h4>
                    <p>Courses</p>
                </div>
                <div class="stat-card">
                    <h4><?php echo count($announcements); ?></h4>
                    <p>Announcements</p>
                </div>
            </div>
        </div>

        <div class="dashboard-grid">
            <!-- Create User -->
            <div class="dashboard-card">
                <h3>Create User</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_user">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="name" required>
                    </div>
                    <div class="form-group">
                        <label>Email:</label>
                        <input type="email" name="email" required>
                    </div>
                    <div class="form-group">
                        <label>Role:</label>
                        <select name="role" required>
                            <option value="student">Student</option>
                            <option value="lecturer">Lecturer</option>
                            <option value="admin">Administrator</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Department:</label>
                        <input type="text" name="department">
                    </div>
                    <button type="submit" class="btn-submit">Create User</button>
                </form>
            </div>

            <!-- Create Department -->
            <div class="dashboard-card">
                <h3>Create Department</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_department">
                    <div class="form-group">
                        <label>Name:</label>
                        <input type="text" name="dept_name" required>
                    </div>
                    <div class="form-group">
                        <label>Description:</label>
                        <textarea name="dept_description"></textarea>
                    </div>
                    <button type="submit" class="btn-submit">Create Department</button>
                </form>
            </div>

            <!-- Create Course -->
            <div class="dashboard-card">
                <h3>Create Course</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_course">
                    <div class="form-group">
                        <label>Course Name:</label>
                        <input type="text" name="course_name" required>
                    </div>
                    <div class="form-group">
                        <label>Course Code:</label>
                        <input type="text" name="course_code" required>
                    </div>
                    <div class="form-group">
                        <label>Department:</label>
                        <select name="dept_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Lecturer:</label>
                        <select name="lecturer_id">
                            <option value="">Select Lecturer</option>
                            <?php foreach (array_filter($users, function($u) { return $u['role'] === 'lecturer'; }) as $lecturer): ?>
                                <option value="<?php echo htmlspecialchars($lecturer['id']); ?>">
                                    <?php echo htmlspecialchars($lecturer['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit">Create Course</button>
                </form>
            </div>

            <!-- Create Announcement -->
            <div class="dashboard-card">
                <h3>Post Announcement</h3>
                <form method="POST">
                    <input type="hidden" name="action" value="create_announcement">
                    <div class="form-group">
                        <label>Title:</label>
                        <input type="text" name="title" required>
                    </div>
                    <div class="form-group">
                        <label>Message:</label>
                        <textarea name="message" required></textarea>
                    </div>
                    <div class="form-group">
                        <label>Department:</label>
                        <select name="department_id" required>
                            <option value="">Select Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Course:</label>
                        <select name="course_id">
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course['id']); ?>">
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <button type="submit" class="btn-submit">Post Announcement</button>
                </form>
            </div>
        </div>

        <!-- Recent Data -->
        <div class="dashboard-grid">
            <div class="dashboard-card">
                <h3>Recent Users</h3>
                <div class="data-list">
                    <?php foreach (array_slice(array_reverse($users), 0, 5) as $u): ?>
                        <div class="data-item <?php echo htmlspecialchars($u['role']); ?>">
                            <h5><?php echo htmlspecialchars($u['name']); ?></h5>
                            <p><?php echo htmlspecialchars($u['email']); ?> - <?php echo htmlspecialchars($u['role']); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <div class="dashboard-card">
                <h3>Recent Announcements</h3>
                <div class="data-list">
                    <?php foreach (array_slice(array_reverse($announcements), 0, 5) as $a): ?>
                        <div class="data-item">
                            <h5><?php echo htmlspecialchars($a['title']); ?></h5>
                            <p><?php echo htmlspecialchars(date('M d, Y', strtotime($a['created_at']))); ?></p>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>
        </div>
    </div>
</body>
</html>
