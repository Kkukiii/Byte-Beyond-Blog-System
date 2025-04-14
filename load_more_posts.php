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

// Get the offset from the request
$offset = isset($_GET['offset']) ? intval($_GET['offset']) : 0;
$limit = 10; // Number of posts to fetch

// Fetch the next set of posts
$sql = "SELECT p.id, p.title,p.user_id, p.content, p.image_path, p.created_at, u.username, u.profile_picture
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
        LIMIT $offset, $limit";

$result = $conn->query($sql);

$posts = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $posts[] = [
            'id' => $row['id'],
            'user_id'=> $row['user_id'],
            'title' => $row['title'],
            'content' => $row['content'],
            'image_path' => $row['image_path'],
            'created_at' => $row['created_at'],
            'username' => $row['username'],
            'profile_picture' => $row['profile_picture']
        ];
    }
}

// Return the posts as a JSON response
header('Content-Type: application/json');
echo json_encode($posts);

// Close the database connection
$conn->close();
?>