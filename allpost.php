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

// Handle post deletion
if (isset($_POST['delete_post'])) {
    $post_id = intval($_POST['post_id']);

    // First delete all likes associated with this post
    $delete_likes = "DELETE FROM likes WHERE post_id = $post_id";
    if ($conn->query($delete_likes)) {
        // Then delete the post
        $delete_post = "DELETE FROM posts WHERE id = $post_id";
        if ($conn->query($delete_post) === TRUE) {
            header("Location: allpost.php");
            exit();
        } else {
            echo "Error deleting post: " . $conn->error;
        }
    } else {
        echo "Error deleting likes: " . $conn->error;
    }
}

// Fetch all posts
$sql = "SELECT p.id, p.title, p.content, p.image_path, p.created_at, u.username, u.id AS user_id
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC";
$result = $conn->query($sql);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>All Posts | Byte & Beyond</title>
    <link rel="stylesheet" href="style.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #4d44db;
            --dark-color: #2a2a72;
            --light-color: #f8f9fa;
            --success-color: #28a745;
            --danger-color: #dc3545;
            --warning-color: #fd7e14;
            --info-color: #17a2b8;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7ff;
            color: #333;
        }

        .navbar {
            background-color: white;
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
            font-size: 1.5rem;
        }

        .nav-link {
            color: #555 !important;
            font-weight: 500;
            margin: 0 5px;
            transition: all 0.3s;
        }

        .nav-link:hover {
            color: var(--primary-color) !important;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
        }

        .btn-outline-primary {
            color: var(--primary-color);
            border-color: var(--primary-color);
        }

        .btn-outline-primary:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .card {
            border: none;
            border-radius: 10px;
            overflow: hidden;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s, box-shadow 0.3s;
            margin-bottom: 25px;
            background-color: white;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            width: 100%;
        }

        .card-title {
            font-weight: 600;
            color: var(--dark-color);
            margin-bottom: 15px;
        }

        .card-text {
            color: #666;
            margin-bottom: 15px;
        }

        .post-meta {
            font-size: 0.85rem;
            color: #888;
        }

        .post-author {
            color: var(--primary-color);
            font-weight: 500;
        }

        .section-title {
            position: relative;
            margin-bottom: 30px;
            color: var(--dark-color);
            font-weight: 700;
        }

        .section-title:after {
            content: '';
            display: block;
            width: 50px;
            height: 3px;
            background: var(--primary-color);
            margin-top: 10px;
        }

        .empty-state {
            text-align: center;
            padding: 50px 20px;
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        .empty-state i {
            font-size: 3rem;
            color: #ddd;
            margin-bottom: 20px;
        }

        .empty-state h3 {
            color: #888;
            font-weight: 500;
        }

        .action-buttons {
            display: flex;
            justify-content: space-between;
            margin-top: 15px;
        }
        .mypost{
            color: var(--secondary-color) !important;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
        }

        .footer-links a {
            color: rgba(255, 255, 255, 0.7);
            margin-right: 15px;
            text-decoration: none;
            transition: color 0.3s;
        }

        .footer-links a:hover {
            color: white;
        }

        .social-icons a {
            color: white;
            font-size: 1.2rem;
            margin-right: 15px;
            transition: color 0.3s;
        }

        .social-icons a:hover {
            color: var(--primary-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light sticky-top">
        <div class="container">
            <a class="navbar-brand fw-bold" href="welcome.php">Byte & Beyond</a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="welcome.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="newpost.php"><i class="fas fa-plus-circle"></i> New Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active mypost" href="allpost.php"><i class="fas fa-list"></i> My Posts</a>
                    </li>
                </ul>
                <div class="d-flex align-items-center">
                    <a href="about.php?user_id=<?php echo $_SESSION['user_id']; ?>" class="d-flex align-items-center text-decoration-none me-3">
                        <img src="<?php echo htmlspecialchars($_SESSION['profile_picture']); ?>" alt="Profile" width="40" height="40" class="rounded-circle me-2">
                        <span class="d-none d-md-inline"><?php echo htmlspecialchars($_SESSION['username']); ?></span>
                    </a>
                    <a href="logout.php" class="btn btn-outline-danger"><i class="fas fa-sign-out-alt"></i> Logout</a>
                </div>
            </div>
        </div>
    </nav>

    <div class="container py-5">
        <h2 class="section-title">All Posts</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="row">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-lg-4 col-md-6">
                        <div class="card h-100">
                            <?php if ($row['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top" alt="Post Image">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x200?text=No+Image" class="card-img-top" alt="Default Image">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars(substr($row['content'], 0, 150) . (strlen($row['content']) > 150 ? '...' : ''))); ?></p>

                                <div class="post-meta mb-3">
                                    <span class="post-author">@<?php echo htmlspecialchars($row['username']); ?></span> •
                                    <span><?php echo date('M j, Y', strtotime($row['created_at'])); ?></span>
                                </div>

                                <div class="action-buttons">
                                    <a href="viewpost.php?id=<?php echo $row['id']; ?>" class="btn btn-sm btn-outline-primary">Read More</a>
                                    <form method="POST" action="" onsubmit="return confirm('Are you sure you want to delete this post?');">
                                        <input type="hidden" name="post_id" value="<?php echo $row['id']; ?>">
                                        <button type="submit" name="delete_post" class="btn btn-sm btn-danger">
                                            <i class="fas fa-trash"></i> Delete
                                        </button>
                                    </form>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <div class="empty-state">
                <i class="far fa-newspaper"></i>
                <h3>No posts available</h3>
                <p class="mt-3">You haven't created any posts yet. Start by creating your first post!</p>
                <a href="newpost.php" class="btn btn-primary mt-3"><i class="fas fa-plus"></i> Create New Post</a>
            </div>
        <?php endif; ?>
    </div>



    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>

<?php
// Close the database connection
$conn->close();
?>