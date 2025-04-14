<?php
session_start();
if (!isset($_SESSION['username'])) {
    header("location: login.php");
    exit();
}

// Database connection
$servername = "localhost";
$username = "root";
$password = "Siddharth@360";
$dbname = "blogsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user_id and is_follow from the request
$user_id = isset($_POST['user_id']) ? intval($_POST['user_id']) : 0;
$is_follow = isset($_POST['is_follow']) ? filter_var($_POST['is_follow'], FILTER_VALIDATE_BOOLEAN) : false;

if ($is_follow) {
    // Insert a new follow
    $stmt = $conn->prepare("INSERT INTO follows (follower_id, followed_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $user_id);
    $stmt->execute();
} else {
    // Remove the follow
    $stmt = $conn->prepare("DELETE FROM follows WHERE follower_id = ? AND followed_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $user_id);
    $stmt->execute();
}

// Close the database connection
$conn->close();
?>