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
                $_POST['password'] ?? null,
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
                $_POST['dept_id']
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

        case 'delete_announcement':
            if (isset($_POST['announcement_id'])) {
                $result = $announcementController->deleteAnnouncement($_POST['announcement_id']);
            }
            break;

        case 'archive_announcement':
            if (isset($_POST['announcement_id'])) {
                $result = $announcementController->archiveAnnouncement($_POST['announcement_id']);
            }
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
    <link rel="stylesheet" href="css/admin_dashboard.css">
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
                        <label>Password:</label>
                        <input type="password" name="password" required>
                    </div>
                    <div class="form-group">
                        <label>Department:</label>
                        <select name="department">
                            <option value="">No Department</option>
                            <?php foreach ($departments as $dept): ?>
                                <option value="<?php echo htmlspecialchars($dept['id']); ?>">
                                    <?php echo htmlspecialchars($dept['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
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
                        <select id="announcement_department_id" name="department_id" required>
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
                        <select id="announcement_course_id" name="course_id">
                            <option value="">Select Course</option>
                            <?php foreach ($courses as $course): ?>
                                <option value="<?php echo htmlspecialchars($course['id']); ?>" data-department-id="<?php echo htmlspecialchars($course['department_id']); ?>">
                                    <?php echo htmlspecialchars($course['course_name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Attachment (Optional):</label>
                        <input type="text" name="attachment" placeholder="File path or URL">
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
        </div>

        <div class="dashboard-card" style="margin-top: 30px;">
            <h3>Management Pages</h3>
            <div class="link-grid">
                <a class="link-card" href="admin_students.php">Manage Students</a>
                <a class="link-card" href="admin_lecturers.php">Manage Lecturers</a>
                <a class="link-card" href="admin_admins.php">Manage Administrators</a>
                <a class="link-card" href="admin_courses.php">Manage Courses</a>
                <a class="link-card" href="admin_departments.php">Manage Departments</a>
                <a class="link-card" href="admin_password.php">Update Password</a>
            </div>
        </div>

        <!-- Announcements Management -->
        <div class="dashboard-card" style="margin-top: 30px;">
            <h3>Manage Announcements</h3>
            <div style="overflow-x: auto;">
                <table style="width: 100%; border-collapse: collapse;">
                    <thead>
                        <tr style="background-color: #f5f5f5; border-bottom: 2px solid #ddd;">
                            <th style="padding: 12px; text-align: left; font-weight: bold;">Title</th>
                            <th style="padding: 12px; text-align: left; font-weight: bold;">Posted By</th>
                            <th style="padding: 12px; text-align: left; font-weight: bold;">Department</th>
                            <th style="padding: 12px; text-align: left; font-weight: bold;">Date</th>
                            <th style="padding: 12px; text-align: left; font-weight: bold;">Expiry</th>
                            <th style="padding: 12px; text-align: center; font-weight: bold;">Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (empty($announcements)): ?>
                            <tr>
                                <td colspan="6" style="padding: 20px; text-align: center; color: #999;">No announcements found</td>
                            </tr>
                        <?php else: ?>
                            <?php foreach ($announcements as $row): 
                                $announcement = $row;
                                $announcementId = $announcement['id'];
                                $announcementPostedBy = $announcement['posted_by'];
                                $announcementDeptId = $announcement['department_id'];
                                
                                $postedBy = array_filter($users, function($u) use ($announcementPostedBy) { return $u['id'] === $announcementPostedBy; });
                                $postedByName = !empty($postedBy) ? array_values($postedBy)[0]['name'] : 'Unknown';
                                $dept = array_filter($departments, function($d) use ($announcementDeptId) { return $d['id'] === $announcementDeptId; });
                                $deptName = !empty($dept) ? array_values($dept)[0]['name'] : 'Unknown';
                            ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td style="padding: 12px; font-weight: 500;"><?php echo htmlspecialchars(substr($announcement['title'], 0, 30)); ?><?php echo strlen($announcement['title']) > 30 ? '...' : ''; ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($postedByName); ?></td>
                                    <td style="padding: 12px;"><?php echo htmlspecialchars($deptName); ?></td>
                                    <td style="padding: 12px; font-size: 12px;"><?php echo htmlspecialchars(date('M d, Y', strtotime($announcement['created_at']))); ?></td>
                                    <td style="padding: 12px; font-size: 12px;">
                                        <?php if ($announcement['expiry_date']): ?>
                                            <?php echo htmlspecialchars(date('M d, Y', strtotime($announcement['expiry_date']))); ?>
                                        <?php else: ?>
                                            <span style="color: #999;">No expiry</span>
                                        <?php endif; ?>
                                    </td>
                                    <td style="padding: 12px; text-align: center;">
                                        <a href="view_announcement.php?id=<?php echo htmlspecialchars($announcement['id']); ?>" style="display: inline-block; padding: 6px 10px; background-color: #3498db; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; margin-right: 5px;">View</a>
                                        <a href="edit_announcement.php?id=<?php echo htmlspecialchars($announcement['id']); ?>" style="display: inline-block; padding: 6px 10px; background-color: #27ae60; color: white; text-decoration: none; border-radius: 3px; font-size: 12px; margin-right: 5px;">Edit</a>
                                        <?php if (!$announcement['expiry_date'] || strtotime($announcement['expiry_date']) >= strtotime(date('Y-m-d'))): ?>
                                            <form method="POST" style="display: inline;" onsubmit="return confirm('Archive this announcement now?');">
                                                <input type="hidden" name="action" value="archive_announcement">
                                                <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['id']); ?>">
                                                <button type="submit" style="padding: 6px 10px; background-color: #f39c12; color: white; border: none; border-radius: 3px; font-size: 12px; cursor: pointer; margin-right: 5px;">Archive</button>
                                            </form>
                                        <?php endif; ?>
                                        <form method="POST" style="display: inline;" onsubmit="return confirm('Are you sure you want to delete this announcement?');">
                                            <input type="hidden" name="action" value="delete_announcement">
                                            <input type="hidden" name="announcement_id" value="<?php echo htmlspecialchars($announcement['id']); ?>">
                                            <button type="submit" style="padding: 6px 10px; background-color: #e74c3c; color: white; border: none; border-radius: 3px; font-size: 12px; cursor: pointer;">Delete</button>
                                        </form>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <script>
        (function () {
            const departmentSelect = document.getElementById('announcement_department_id');
            const courseSelect = document.getElementById('announcement_course_id');
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
