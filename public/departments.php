<?php
/**
 * Departments List Page
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';
require_once __DIR__ . '/../controllers/DepartmentController.php';

$authController = new AuthController($pdo);
$authController->requireLogin();

if (!$authController->canManageDepartments()) {
    header('Location: unauthorized.php');
    exit;
}

$user = $authController->getCurrentUser();
$departmentController = new DepartmentController($pdo);
$departments = $departmentController->getAllDepartments();

$message = '';
$messageType = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['action'])) {
    if ($_POST['action'] === 'create') {
        $result = $departmentController->createDepartment($_POST['name'], $_POST['description'] ?? null);
        $message = $result['message'];
        $messageType = $result['success'] ? 'success' : 'error';
        if ($result['success']) {
            $departments = $departmentController->getAllDepartments();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Departments</title>
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
        
        input, textarea {
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
        
        .department-item {
            background-color: #f9f9f9;
            padding: 15px;
            margin-bottom: 15px;
            border-left: 4px solid #27ae60;
            border-radius: 3px;
        }
        
        .department-item h3 {
            margin: 0 0 10px 0;
            color: #2c3e50;
        }
        
        .department-item p {
            line-height: 1.6;
            color: #555;
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
            <h2>Departments</h2>
            
            <?php if ($message): ?>
                <div class="message <?php echo $messageType; ?>">
                    <?php echo htmlspecialchars($message); ?>
                </div>
            <?php endif; ?>
            
            <h3 style="margin-top: 30px; margin-bottom: 20px;">Add New Department</h3>
            <form method="POST">
                <input type="hidden" name="action" value="create">
                <div class="form-group">
                    <label for="name">Department Name:</label>
                    <input type="text" id="name" name="name" required>
                </div>
                
                <div class="form-group">
                    <label for="description">Description:</label>
                    <textarea id="description" name="description"></textarea>
                </div>
                
                <button type="submit">Add Department</button>
            </form>
            
            <h3 style="margin-top: 30px; margin-bottom: 20px;">Departments List</h3>
            
            <?php if (empty($departments)): ?>
                <p>No departments available.</p>
            <?php else: ?>
                <?php foreach ($departments as $dept): ?>
                    <div class="department-item">
                        <h3><?php echo htmlspecialchars($dept['name']); ?></h3>
                        <?php if ($dept['description']): ?>
                            <p><?php echo htmlspecialchars($dept['description']); ?></p>
                        <?php endif; ?>
                        <small>ID: <?php echo htmlspecialchars($dept['id']); ?></small>
                    </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
    </div>
</body>
</html>
