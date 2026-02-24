<?php
/**
 * Student Dashboard
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/AnnouncementController.php';

$authController = new AuthController($pdo);
$authController->requireRole('student');

$user = $authController->getCurrentUser();
$announcementController = new AnnouncementController($pdo);

$searchKeyword = trim($_GET['q'] ?? '');

// Get announcements relevant to the student
$relevantAnnouncements = $announcementController->getActiveAnnouncementsByDepartment($user['department']);

if ($searchKeyword !== '') {
    $relevantAnnouncements = array_values(array_filter($relevantAnnouncements, function($announcement) use ($searchKeyword) {
        return stripos($announcement['title'], $searchKeyword) !== false || stripos($announcement['message'], $searchKeyword) !== false;
    }));
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Student Dashboard - Student Announcement System</title>
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

        .dashboard-header {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
            margin-bottom: 30px;
        }

        .dashboard-header h2 {
            color: #2c3e50;
            margin-bottom: 10px;
        }

        .stats {
            display: flex;
            gap: 20px;
            margin-top: 15px;
        }

        .stat-card {
            background-color: #3498db;
            color: white;
            padding: 15px;
            border-radius: 5px;
            flex: 1;
            text-align: center;
        }

        .stat-card h3 {
            margin: 0 0 5px 0;
            font-size: 24px;
        }

        .stat-card p {
            margin: 0;
            font-size: 14px;
        }

        .announcements-section {
            background-color: white;
            padding: 20px;
            border-radius: 5px;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        }


        .announcements-section h3 {
            color: #2c3e50;
            margin-bottom: 20px;
        }

        .search-form {
            display: flex;
            gap: 10px;
            margin-bottom: 20px;
        }

        .search-form input {
            flex: 1;
            padding: 10px;
            border: 1px solid #ddd;
            border-radius: 4px;
        }

        .search-form button,
        .search-form a {
            padding: 10px 14px;
            border: none;
            border-radius: 4px;
            text-decoration: none;
            font-size: 14px;
            cursor: pointer;
        }

        .search-form button {
            background-color: #3498db;
            color: white;
        }

        .search-form a {
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
            margin-bottom: 10px;
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
        }

        .btn-view:hover {
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
    <div class="header">
        <div class="header-content">
            <h1>Student Announcement System</h1>
            <div class="user-info">
                <span>Welcome, <?php echo htmlspecialchars($user['name']); ?> (Student)</span>
                <a href="student_password.php" class="btn-secondary">Update Password</a>
                <a href="logout.php" class="btn-logout">Logout</a>
            </div>
        </div>
    </div>

    <div class="container">
        <div class="dashboard-header">
            <h2>Student Dashboard</h2>
            <p>View announcements from your lecturers and department.</p>

            <div class="stats">
                <div class="stat-card">
                    <h3><?php echo count($relevantAnnouncements); ?></h3>
                    <p>Active Announcements</p>
                </div>
                <div class="stat-card">
                    <h3><?php echo count(array_filter($relevantAnnouncements, function($a) { return strtotime($a['expiry_date']) > time() && $a['expiry_date']; })); ?></h3>
                    <p>Expiring Soon</p>
                </div>
            </div>
        </div>

        <div class="announcements-section">
            <h3>Recent Announcements</h3>

            <form method="GET" class="search-form">
                <input type="text" name="q" placeholder="Search announcements" value="<?php echo htmlspecialchars($searchKeyword); ?>">
                <button type="submit">Search</button>
                <a href="student_dashboard.php">Reset</a>
            </form>

            <?php if (empty($relevantAnnouncements)): ?>
                <div class="no-announcements">
                    <p>No announcements available at the moment.</p>
                </div>
            <?php else: ?>
                <?php foreach (array_slice($relevantAnnouncements, 0, 10) as $announcement): ?>
                    <div class="announcement-item">
                        <h4><?php echo htmlspecialchars($announcement['title']); ?></h4>
                        <div class="announcement-meta">
                            <strong>Posted:</strong> <?php echo htmlspecialchars(date('M d, Y H:i', strtotime($announcement['created_at']))); ?>
                            <?php if ($announcement['expiry_date']): ?>
                                | <strong>Expires:</strong> <?php echo htmlspecialchars(date('M d, Y', strtotime($announcement['expiry_date']))); ?>
                            <?php endif; ?>
                        </div>
                        <p><?php echo htmlspecialchars(substr($announcement['message'], 0, 200) . (strlen($announcement['message']) > 200 ? '...' : '')); ?></p>
                        <a href="view_announcement.php?id=<?php echo htmlspecialchars($announcement['id']); ?>&student_id=<?php echo htmlspecialchars($user['id']); ?>" class="btn-view">Read More</a>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
