<?php
session_start();

// Check if the user is logged in
if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}

// Database connection
$servername = "localhost"; // Change if your database server is different
$username = "root"; // Your MySQL username
$password = "Siddharth@360"; // Your MySQL password
$dbname = "blogsystem"; // Your database name

$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get the post title and content
    $postTitle = $conn->real_escape_string(trim($_POST['postTitle']));
    $postContent = $conn->real_escape_string(trim($_POST['postContent']));

    // Initialize image path
    $imagePath = "posts/download.png";

    // Handle file upload
    if (isset($_FILES['postImage']) && $_FILES['postImage']['error'] == 0) {
        $targetDir = "posts/"; // Directory where images will be uploaded
        $targetFile = $targetDir . basename($_FILES['postImage']['name']);

        // Move the uploaded file to the target directory
        if (move_uploaded_file($_FILES['postImage']['tmp_name'], $targetFile)) {
            $imagePath = $targetFile; // Save the image path
        } else {
            echo "Sorry, there was an error uploading your file.";
            exit();
        }
    }

    // Insert post details into the database
    $sql = "INSERT INTO posts (user_id, title, content, image_path, created_at) VALUES (" .
            $_SESSION['user_id'] . ", '$postTitle', '$postContent', '$imagePath', NOW())";

    if ($conn->query($sql) === TRUE) {
        echo "New post created successfully!";

        header("Location: welcome.php");
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}

// Close the database connection
$conn->close();
?>