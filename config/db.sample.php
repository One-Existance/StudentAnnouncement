<?php
/**
 * Database Configuration - SAMPLE FILE
 *
 * Copy this file to config/db.php and update with your actual database credentials
 * DO NOT commit the actual config/db.php file to version control
 */

// Database configuration
$host = 'localhost';      // Your database host
$db = 'STUDENT_ANNOUNCEMENT';  // Your database name
$user = 'root';           // Your database username
$pass = '';               // Your database password

// Create PDO connection
try {
    $pdo = new PDO("mysql:host=$host;dbname=$db;charset=utf8", $user, $pass);
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
    $pdo->setAttribute(PDO::ATTR_DEFAULT_FETCH_MODE, PDO::FETCH_ASSOC);
} catch (PDOException $e) {
    die("Database connection failed: " . $e->getMessage());
}