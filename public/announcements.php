<?php
/**
 * Announcements List Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

$authController = new AuthController($pdo);
$authController->requireLogin();

$user = $authController->getCurrentUser();
$announcementController = new AnnouncementController($pdo);
$announcements = $announcementController->getActiveAnnouncements();
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
                        <button class="btn-edit" onclick="editAnnouncement('<?php echo htmlspecialchars($announcement['id']); ?>')">Edit</button>
                        <button class="btn-delete" onclick="deleteAnnouncement('<?php echo htmlspecialchars($announcement['id']); ?>')">Delete</button>
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
