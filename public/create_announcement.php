<?php
/**
 * Create Announcement Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';
require_once __DIR__ . '/../models/Department.php';
require_once __DIR__ . '/../models/Course.php';

$authController = new AuthController($pdo);
$authController->requireLogin();

if (!$authController->canCreateAnnouncements()) {
    header('Location: unauthorized.php');
    exit;
}

$user = $authController->getCurrentUser();
$announcementController = new AnnouncementController($pdo);
$departmentModel = new Department($pdo);
$courseModel = new Course($pdo);

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
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
}

$departments = $departmentModel->getAllDepartments();
$courses = $courseModel->getAllCourses();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create Announcement</title>
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
        
        .form-group {
            margin-bottom: 20px;
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
            min-height: 150px;
        }
        
        button {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
            font-size: 14px;
        }
        
        button:hover {
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
            <h2>Create New Announcement</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <form method="POST">
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
                    <label for="course_id">Course (Optional):</label>
                    <select id="course_id" name="course_id">
                        <option value="">Select Course</option>
                        <?php foreach ($courses as $course): ?>
                            <option value="<?php echo htmlspecialchars($course['id']); ?>">
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
                
                <button type="submit">Post Announcement</button>
            </form>
        </div>
    </div>
</body>
</html>
