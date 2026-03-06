<?php
// Database configuration
define('DB_HOST', 'localhost');
define('DB_USER', 'rsoa_rsoa276_76');
define('DB_PASS', '123456');
define('DB_NAME', 'rsoa_rsoa276_76');
 
// Create connection
$conn = new mysqli(DB_HOST, DB_USER, DB_PASS, DB_NAME);
 
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}
 
// Set charset
$conn->set_charset("utf8mb4");
 
// Base URL (change this according to your setup)
define('BASE_URL', 'http://localhost/cnn-clone/');
 
// Site name
define('SITE_NAME', 'CNN News Clone');
 
// Start session if not started
if (session_status() == PHP_SESSION_NONE) {
    session_start();
}
?>
