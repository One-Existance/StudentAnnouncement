<?php
/**
 * View Announcement Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

$authController = new AuthController($pdo);
$authController->requireLogin();

$user = $authController->getCurrentUser();
$announcementController = new AnnouncementController($pdo);

if (!isset($_GET['id'])) {
    header('Location: announcements.php');
    exit;
}

$announcement = $announcementController->getAnnouncementById($_GET['id']);

if (!$announcement) {
    echo "Announcement not found.";
    exit;
}

if ($user['role'] === 'student' && $announcement['department_id'] !== $user['department']) {
    header('Location: unauthorized.php');
    exit;
}

// Record view if student_id is provided or use current user
$studentId = $_GET['student_id'] ?? $user['id'];
$announcementController->recordAnnouncementView($_GET['id'], $studentId);

$viewCount = $announcementController->getViewCount($_GET['id']);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($announcement['title']); ?></title>
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
        
        .announcement-header {
            border-bottom: 2px solid #3498db;
            padding-bottom: 15px;
            margin-bottom: 20px;
        }
        
        .announcement-header h2 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .announcement-meta {
            font-size: 14px;
            color: #7f8c8d;
        }
        
        .announcement-body {
            line-height: 1.8;
            color: #555;
            margin-bottom: 20px;
        }
        
        .announcement-footer {
            border-top: 1px solid #ddd;
            padding-top: 15px;
            font-size: 12px;
            color: #7f8c8d;
        }
        
        .btn-back {
            background-color: #3498db;
            color: white;
            padding: 10px 20px;
            border: none;
            border-radius: 3px;
            cursor: pointer;
        }
        
        .btn-back:hover {
            background-color: #2980b9;
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
            <div class="announcement-header">
                <h2><?php echo htmlspecialchars($announcement['title']); ?></h2>
                <div class="announcement-meta">
                    <p><strong>Posted:</strong> <?php echo htmlspecialchars($announcement['created_at']); ?></p>
                    <p><strong>Posted By:</strong> <?php echo htmlspecialchars($announcement['posted_by']); ?></p>
                    <p><strong>Views:</strong> <?php echo $viewCount; ?></p>
                    <?php if ($announcement['expiry_date']): ?>
                        <p><strong>Expires:</strong> <?php echo htmlspecialchars($announcement['expiry_date']); ?></p>
                    <?php endif; ?>
                    <?php if ($announcement['attachment']): ?>
                        <p><strong>Attachment:</strong> <a href="<?php echo htmlspecialchars($announcement['attachment']); ?>">Download</a></p>
                    <?php endif; ?>
                </div>
            </div>
            
            <div class="announcement-body">
                <?php echo nl2br(htmlspecialchars($announcement['message'])); ?>
            </div>
            
            <div class="announcement-footer">
                <p>ID: <?php echo htmlspecialchars($announcement['id']); ?></p>
            </div>
            
            <button class="btn-back" onclick="window.history.back()">Back to Announcements</button>
        </div>
    </div>
</body>
</html>
