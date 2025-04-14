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

// Get post_id and is_like from the request
$post_id = isset($_POST['post_id']) ? intval($_POST['post_id']) : 0;
$is_like = isset($_POST['is_like']) ? filter_var($_POST['is_like'], FILTER_VALIDATE_BOOLEAN) : false;

if ($is_like) {
    // Insert a new like
    $stmt = $conn->prepare("INSERT INTO likes (user_id, post_id) VALUES (?, ?)");
    $stmt->bind_param("ii", $_SESSION['user_id'], $post_id);
    $stmt->execute();
} else {
    // Remove the like
    $stmt = $conn->prepare("DELETE FROM likes WHERE user_id = ? AND post_id = ?");
    $stmt->bind_param("ii", $_SESSION['user_id'], $post_id);
    $stmt->execute();
}

// Get the updated like count
$result = $conn->query("SELECT COUNT(*) as like_count FROM likes WHERE post_id = $post_id");
$like_count = $result->fetch_assoc()['like_count'];

// Return the updated like count as JSON
header('Content-Type: application/json');
echo json_encode(['like_count' => $like_count]);

// Close the