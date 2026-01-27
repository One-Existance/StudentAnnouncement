<?php
/**
 * Home Page - Redirects to appropriate dashboard or login
 */

require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../controllers/AuthController.php';

$authController = new AuthController($pdo);

if ($authController->isLoggedIn()) {
    $user = $authController->getCurrentUser();
    switch ($user['role']) {
        case 'admin':
            header('Location: admin_dashboard.php');
            break;
        case 'lecturer':
            header('Location: lecturer_dashboard.php');
            break;
        case 'student':
            header('Location: student_dashboard.php');
            break;
        default:
            header('Location: login.php');
    }
    exit;
} else {
    header('Location: login.php');
    exit;
}
?>
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
        
        .btn-edit, .btn-delete {
            margin-top: 10px;
            margin-right: 5px;
            padding: 5px 10px;
            font-size: 12px;
        }
        
        .btn-delete {
            background-color: #e74c3c;
        }
        
        .btn-delete:hover {
            background-color: #c0392b;
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
                <a href="index.php">Home</a>
                <a href="announcements.php">Announcements</a>
                <a href="create_announcement.php">Post Announcement</a>
                <a href="departments.php">Departments</a>
            </nav>
        </div>
    </header>
    
    <div class="container">
        <h2>Welcome to Student Announcement System</h2>
        <p>This system allows lecturers and administrators to post announcements for students and departments.</p>
        <p>Use the navigation menu above to access different features of the system.</p>
    </div>
</body>
</html>
