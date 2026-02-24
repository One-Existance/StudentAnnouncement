<?php
/**
 * Announcements List Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';
require_once __DIR__ . '/../controllers/CourseController.php';

$authController = new AuthController($pdo);
$authController->requireLogin();

$user = $authController->getCurrentUser();
$announcementController = new AnnouncementController($pdo);
$departmentController = new DepartmentController($pdo);
$courseController = new CourseController($pdo);

$searchKeyword = trim($_GET['q'] ?? '');
$filterDepartment = trim($_GET['department_id'] ?? '');
$filterCourse = trim($_GET['course_id'] ?? '');
$filterYear = trim($_GET['year'] ?? '');

if ($user['role'] === 'student') {
    $announcements = $announcementController->getActiveAnnouncementsByDepartment($user['department']);
    $filterDepartment = $user['department'];
    $filterCourse = '';
} else {
    $announcements = $announcementController->getActiveAnnouncements();
}

if ($searchKeyword !== '') {
    $announcements = array_filter($announcements, function($announcement) use ($searchKeyword) {
        return stripos($announcement['title'], $searchKeyword) !== false || stripos($announcement['message'], $searchKeyword) !== false;
    });
}

if ($filterDepartment !== '') {
    $announcements = array_filter($announcements, function($announcement) use ($filterDepartment) {
        return $announcement['department_id'] === $filterDepartment;
    });
}

if ($filterCourse !== '' && $user['role'] !== 'student') {
    $announcements = array_filter($announcements, function($announcement) use ($filterCourse) {
        return $announcement['course_id'] === $filterCourse;
    });
}

if ($filterYear !== '') {
    $announcements = array_filter($announcements, function($announcement) use ($filterYear) {
        return date('Y', strtotime($announcement['created_at'])) === $filterYear;
    });
}

$announcements = array_values($announcements);
usort($announcements, function($a, $b) {
    return strtotime($b['created_at']) <=> strtotime($a['created_at']);
});

$departments = $departmentController->getAllDepartments();
$courses = $courseController->getAllCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Announcements</title>
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
        
        .container {
            max-width: 1200px;
            margin: 0 auto;
            padding: 20px;
        }
        
        header {
            background-color: #2c3e50;
            color: white;
            padding: 20px 0;
            margin-bottom: 30px;
        }
        
        header h1 {
            margin: 0;
        }
        
        nav {
            margin-top: 15px;
        }
        
        nav a {
            color: white;
            text-decoration: none;
            margin-right: 20px;
            padding: 5px 10px;
            border-radius: 3px;
        }
        
        nav a:hover {
            background-color: #34495e;
        }
        
        .user-info {
            display: flex;
            align-items: center;
            gap: 15px;
        }
        
        .user-info span {
            font-size: 14px;
        }
        
        .btn-logout {
            background-color: #e74c3c;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            text-decoration: none;
            font-size: 12px;
        }
        
        .btn-logout:hover {
            background-color: #c0392b;
        }
        
        .content {
            background-color: white;
            padding: 30px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }

        .filters {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(180px, 1fr));
            gap: 10px;
            margin: 15px 0 20px;
        }

        .filters input,
        .filters select {
            width: 100%;
            padding: 8px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .filters button,
        .filters a {
            padding: 8px 10px;
            border-radius: 4px;
            font-size: 13px;
            text-align: center;
            text-decoration: none;
            border: none;
            cursor: pointer;
        }

        .filters button {
            background-color: #3498db;
            color: white;
        }

        .filters a {
            background-color: #95a5a6;
            color: white;
        }
        
        .announcement-item {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #3498db;
            border-radius: 3px;
        }
        
        .announcement-item h3 {
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
        
        .btn-edit, .btn-delete, .btn-view {
            background-color: #3498db;
            color: white;
            padding: 5px 10px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 12px;
            margin-top: 10px;
            margin-right: 5px;
        }
        
        .btn-delete {
            background-color: #e74c3c;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
        }
        
        .btn-edit:hover, .btn-view:hover {
            background-color: #2980b9;
        }
        
        .no-announcements {
            padding: 20px;
            text-align: center;
            color: #7f8c8d;
        }
    </style>
</head>
<body>
    <header>
        <div class="container">
            <h1>Student Announcement System</h1>
            <nav>
                <?php if ($user['role'] === 'admin'): ?>
                    <a href="admin_dashboard.php">Dashboard</a>
                <?php elseif ($user['role'] === 'lecturer'): ?>
                    <a href="lecturer_dashboard.php">Dashboard</a>
                <?php else: ?>
                    <a href="student_dashboard.php">Dashboard</a>
                <?php endif; ?>
                <a href="announcements.php">Announcements</a>
                <?php if ($authController->canCreateAnnouncements()): ?>
                    <a href="create_announcement.php">Post Announcement</a>
                <?php endif; ?>
                <?php if ($authController->canManageDepartments()): ?>
                    <a href="departments.php">Departments</a>
                <?php endif; ?>
                <div class="user-info">
                    <span>Welcome, <?php echo htmlspecialchars($user['name']); ?> (<?php echo htmlspecialchars(ucfirst($user['role'])); ?>)</span>
                    <a href="logout.php" class="btn-logout">Logout</a>
                </div>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <div class="content">
            <h2>Announcements</h2>

            <form method="GET" class="filters">
                <input type="text" name="q" placeholder="Search keyword" value="<?php echo htmlspecialchars($searchKeyword); ?>">

                <select name="department_id" <?php echo $user['role'] === 'student' ? 'disabled' : ''; ?>>
                    <option value="">All Departments</option>
                    <?php foreach ($departments as $department): ?>
                        <option value="<?php echo htmlspecialchars($department['id']); ?>" <?php echo $filterDepartment === $department['id'] ? 'selected' : ''; ?>>
                            <?php echo htmlspecialchars($department['name']); ?>
                        </option>
                    <?php endforeach; ?>
                </select>
                <?php if ($user['role'] === 'student'): ?>
                    <input type="hidden" name="department_id" value="<?php echo htmlspecialchars($filterDepartment); ?>">
                <?php endif; ?>

                <?php if ($user['role'] !== 'student'): ?>
                    <select name="course_id">
                        <option value="">All Courses</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['id']); ?>" <?php echo $filterCourse === $course['id'] ? 'selected' : ''; ?>>
                                <?php echo htmlspecialchars($course['course_name']); ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                <?php endif; ?>

                <input type="number" min="2000" max="2100" name="year" placeholder="Year (e.g. 2026)" value="<?php echo htmlspecialchars($filterYear); ?>">
                <button type="submit">Apply Filters</button>
                <a href="announcements.php">Reset</a>
            </form>
            
            <?php if (empty($announcements)): ?>
                <div class="no-announcements">
                    <p>No announcements available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach ($announcements as $announcement): ?>
                    <div class="announcement-item">
                        <h3><?php echo htmlspecialchars($announcement['title']); ?></h3>
                        <div class="announcement-meta">
                            <strong>Posted:</strong> <?php echo htmlspecialchars($announcement['created_at']); ?>
                            <?php if ($announcement['expiry_date']): ?>
                                | <strong>Expires:</strong> <?php echo htmlspecialchars($announcement['expiry_date']); ?>
                            <?php endif; ?>
                        </div>
                        <p><?php echo nl2br(htmlspecialchars(substr($announcement['message'], 0, 200) . '...')); ?></p>
                        
                        <button class="btn-view" onclick="viewAnnouncement('<?php echo htmlspecialchars($announcement['id']); ?>')">View</button>
                        <?php if ($user['role'] === 'admin' || $user['role'] === 'lecturer'): ?>
                            <button class="btn-edit" onclick="editAnnouncement('<?php echo htmlspecialchars($announcement['id']); ?>')">Edit</button>
                            <button class="btn-delete" onclick="deleteAnnouncement('<?php echo htmlspecialchars($announcement['id']); ?>')">Delete</button>
                        <?php endif; ?>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
    
    <script>
        function viewAnnouncement(id) {
            window.location.href = 'view_announcement.php?id=' + id + '&student_id=<?php echo htmlspecialchars($user['id']); ?>';
        }
        
        function editAnnouncement(id) {
            window.location.href = 'edit_announcement.php?id=' + id;
        }
        
        function deleteAnnouncement(id) {
            if (confirm('Are you sure you want to delete this announcement?')) {
                window.location.href = 'delete_announcement.php?id=' + id;
            }
        }
    </script>
</body>
</html>
