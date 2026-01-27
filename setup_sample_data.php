<?php
/**
 * Database Setup Script - Creates sample data
 */

require_once __DIR__ . '/config/db.php';
require_once __DIR__ . '/controllers/UserController.php';
require_once __DIR__ . '/controllers/DepartmentController.php';
require_once __DIR__ . '/controllers/CourseController.php';
require_once __DIR__ . '/controllers/AnnouncementController.php';

$userController = new UserController($pdo);
$departmentController = new DepartmentController($pdo);
$courseController = new CourseController($pdo);
$announcementController = new AnnouncementController($pdo);

// Test database connection
try {
    $stmt = $pdo->query("SELECT 1");
    echo "Database connection successful!\n";
    
    // Check if tables exist
    $tables = $pdo->query("SHOW TABLES LIKE 'departments'")->fetchAll();
    if (empty($tables)) {
        echo "Warning: 'departments' table does not exist. Please run the database schema first.\n";
    } else {
        echo "Tables exist, proceeding with setup...\n";
    }
} catch (Exception $e) {
    echo "Database connection failed: " . $e->getMessage() . "\n";
    exit(1);
}

echo "Setting up sample data...\n";

// Clear existing data first (optional - comment out if you want to keep existing data)
echo "Clearing existing data...\n";
try {
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 0");
    $pdo->exec("TRUNCATE TABLE announcement_views");
    $pdo->exec("TRUNCATE TABLE announcements");
    $pdo->exec("TRUNCATE TABLE users");
    $pdo->exec("TRUNCATE TABLE courses");
    $pdo->exec("TRUNCATE TABLE departments");
    $pdo->exec("SET FOREIGN_KEY_CHECKS = 1");
    echo "Existing data cleared.\n";
} catch (Exception $e) {
    echo "Warning: Could not clear existing data: " . $e->getMessage() . "\n";
}

// Create departments
echo "Creating departments...\n";
$csDeptResult = $departmentController->createDepartment('Computer Science', 'Department of Computer Science and Information Technology');
echo "CS Dept Result: " . print_r($csDeptResult, true) . "\n";
$mathDeptResult = $departmentController->createDepartment('Mathematics', 'Department of Mathematics and Statistics');
$physicsDeptResult = $departmentController->createDepartment('Physics', 'Department of Physics and Astronomy');

$csDept = $csDeptResult['success'] ? $csDeptResult['id'] : null;
$mathDept = $mathDeptResult['success'] ? $mathDeptResult['id'] : null;
$physicsDept = $physicsDeptResult['success'] ? $physicsDeptResult['id'] : null;

echo "Departments created: CS={$csDept}, Math={$mathDept}, Physics={$physicsDept}\n";

// Create courses
echo "Creating courses...\n";
$cs101Result = $courseController->createCourse('Introduction to Programming', 'CS101', $csDept);
$cs201Result = $courseController->createCourse('Data Structures', 'CS201', $csDept);
$math101Result = $courseController->createCourse('Calculus I', 'MATH101', $mathDept);

$cs101 = $cs101Result['success'] ? $cs101Result['id'] : null;
$cs201 = $cs201Result['success'] ? $cs201Result['id'] : null;
$math101 = $math101Result['success'] ? $math101Result['id'] : null;

echo "Courses created: CS101={$cs101}, CS201={$cs201}, MATH101={$math101}\n";

// Create users
echo "Creating users...\n";

// Admin user
$adminResult = $userController->createUser('System Administrator', 'admin@university.edu', 'admin', null, null);
echo "Admin Result: " . print_r($adminResult, true) . "\n";
$admin = $adminResult['success'] ? $adminResult['id'] : null;

// Lecturers
$lecturer1Result = $userController->createUser('Dr. John Smith', 'lecturer@university.edu', 'lecturer', $csDept, null);
echo "Lecturer1 Result: " . print_r($lecturer1Result, true) . "\n";
$lecturer2Result = $userController->createUser('Prof. Jane Doe', 'lecturer2@university.edu', 'lecturer', $mathDept, null);
echo "Lecturer2 Result: " . print_r($lecturer2Result, true) . "\n";

$lecturer1 = $lecturer1Result['success'] ? $lecturer1Result['id'] : null;
$lecturer2 = $lecturer2Result['success'] ? $lecturer2Result['id'] : null;

// Students
$student1Result = $userController->createUser('Alice Johnson', 'student@university.edu', 'student', $csDept, null);
echo "Student1 Result: " . print_r($student1Result, true) . "\n";
$student2Result = $userController->createUser('Bob Wilson', 'student2@university.edu', 'student', $mathDept, null);
echo "Student2 Result: " . print_r($student2Result, true) . "\n";
$student3Result = $userController->createUser('Charlie Brown', 'student3@university.edu', 'student', $physicsDept, null);
echo "Student3 Result: " . print_r($student3Result, true) . "\n";

$student1 = $student1Result['success'] ? $student1Result['id'] : null;
$student2 = $student2Result['success'] ? $student2Result['id'] : null;
$student3 = $student3Result['success'] ? $student3Result['id'] : null;

echo "Users created: Admin={$admin}, Lecturer1={$lecturer1}, Lecturer2={$lecturer2}, Student1={$student1}, Student2={$student2}, Student3={$student3}\n";

// Create sample announcements
echo "Creating sample announcements...\n";

$result1 = $announcementController->createAnnouncement(
    'Welcome to the New Semester',
    'Welcome back students! We hope you had a great break. Please check your course schedules and be prepared for the first day of classes. Remember to bring your student ID cards.',
    $lecturer1,
    $csDept
);
echo "Announcement 1: " . ($result1['success'] ? 'Success' : 'Failed - ' . $result1['message']) . "\n";

$result2 = $announcementController->createAnnouncement(
    'CS101 Lab Schedule Change',
    'Due to maintenance work in the computer lab, CS101 lab sessions for this week have been moved to Room 205. Please arrive 15 minutes early for the first session.',
    $lecturer1,
    $csDept,
    $cs101
);
echo "Announcement 2: " . ($result2['success'] ? 'Success' : 'Failed - ' . $result2['message']) . "\n";

$result3 = $announcementController->createAnnouncement(
    'Mathematics Department Meeting',
    'All mathematics students are required to attend the department orientation meeting on Friday at 2 PM in the main auditorium. Important information about course registration will be discussed.',
    $lecturer2,
    $mathDept,
    $math101
);
echo "Announcement 3: " . ($result3['success'] ? 'Success' : 'Failed - ' . $result3['message']) . "\n";

$result4 = $announcementController->createAnnouncement(
    'Library Hours Extended',
    'The university library will be open 24/7 during exam week starting next Monday. Additional study spaces and resources will be available.',
    $admin,
    $csDept
);
echo "Announcement 4: " . ($result4['success'] ? 'Success' : 'Failed - ' . $result4['message']) . "\n";

echo "Sample announcements created!\n";

// Verify data was created
echo "\nVerifying data...\n";
$users = $userController->getAllUsers();
$departments = $departmentController->getAllDepartments();
$announcements = $announcementController->getAllAnnouncements();

echo "Total users: " . count($users) . "\n";
echo "Total departments: " . count($departments) . "\n";
echo "Total announcements: " . count($announcements) . "\n";

echo "\nSetup complete! You can now login with:\n";
echo "- Admin: admin@university.edu / password\n";
echo "- Lecturer: lecturer@university.edu / password\n";
echo "- Student: student@university.edu / password\n";
?>