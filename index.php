<?php
// Database connection
$servername = "localhost";
$username = "root";
$password = "Siddharth@360";
$dbname = "blogsystem";

$conn = new mysqli($servername, $username, $password, $dbname);

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Fetch all posts with limit of 10
$sql = "SELECT p.id, p.title, p.content, p.image_path, p.created_at, u.username, u.id AS user_id
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC
        LIMIT 10";
$result = $conn->query($sql);

// Count total posts for the view more button
$count_sql = "SELECT COUNT(*) as total FROM posts";
$count_result = $conn->query($count_sql);
$total_posts = $count_result->fetch_assoc()['total'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Byte & Beyond | Home</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #4361ee;
            --secondary-color: #3f37c9;
            --light-color: #f8f9fa;
            --dark-color: #212529;
        }

        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background-color: #f5f7fa;
            color: #333;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
            background-color: white !important;
        }

        .navbar-brand {
            font-weight: 700;
            color: var(--primary-color) !important;
        }

        .nav-link {
            font-weight: 500;
        }

        .hero-section {
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            color: white;
            padding: 4rem 0;
            margin-bottom: 3rem;
            border-radius: 0 0 20px 20px;
        }

        .card {
            border: none;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            transition: transform 0.3s ease, box-shadow 0.3s ease;
            height: 100%;
            display: flex;
            flex-direction: column;
        }

        .card:hover {
            transform: translateY(-5px);
            box-shadow: 0 10px 25px rgba(0, 0, 0, 0.1);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            border-radius: 12px 12px 0 0;
            cursor: pointer;
        }

        .card-body {
            flex: 1;
            display: flex;
            flex-direction: column;
            padding: 1.5rem;
        }

        .card-title {
            font-weight: 600;
            margin-bottom: 0.75rem;
        }

        .card-text {
            color: #666;
            flex-grow: 1;
            margin-bottom: 1rem;
        }

        .post-meta {
            font-size: 0.85rem;
            color: #888;
            margin-bottom: 1rem;
        }

        .author-link {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
        }

        .btn-primary {
            background-color: var(--primary-color);
            border: none;
            padding: 0.5rem 1.25rem;
            border-radius: 8px;
            font-weight: 500;
            align-self: flex-start;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
        }

        .btn-view-more {
            background-color: white;
            color: var(--primary-color);
            border: 2px solid var(--primary-color);
            padding: 0.5rem 1.5rem;
            font-weight: 500;
            margin-top: 2rem;
        }

        .btn-view-more:hover {
            background-color: var(--primary-color);
            color: white;
        }

        .search-btn {
            background-color: var(--primary-color);
            color: white;
        }

        .search-btn:hover {
            background-color: var(--secondary-color);
            color: white;
        }

        /* Modal styles */
        .modal {
            display: none;
            position: fixed;
            z-index: 1000;
            left: 0;
            top: 0;
            width: 100%;
            height: 100%;
            background-color: rgba(0, 0, 0, 0.8);
            animation: fadeIn 0.3s;
        }

        @keyframes fadeIn {
            from { opacity: 0; }
            to { opacity: 1; }
        }

        .modal-content {
            margin: auto;
            display: block;
            max-width: 80%;
            max-height: 80%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
            border-radius: 8px;
        }

        .close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: white;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
            transition: color 0.2s;
        }

        .close:hover {
            color: #ddd;
        }

        .no-posts {
            text-align: center;
            padding: 3rem;
            background-color: white;
            border-radius: 12px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 2rem 0;
            margin-top: 3rem;
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light bg-light sticky-top">
        <div class="container">
            <a class="navbar-brand" href="#">
                <i class="fas fa-laptop-code me-2"></i>Byte & Beyond
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarSupportedContent">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarSupportedContent">
                <ul class="navbar-nav me-auto mb-2 mb-lg-0">
                    <li class="nav-item">
                        <a class="nav-link active" href="index.php"><i class="fas fa-home me-1"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="registration.html"><i class="fas fa-user-plus me-1"></i> Register</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="login.php"><i class="fas fa-sign-in-alt me-1"></i> Login</a>
                    </li>
                </ul>
                <form class="d-flex">
                    <input class="form-control me-2" type="search" placeholder="Search posts...">
                    <button class="btn search-btn" type="submit"><i class="fas fa-search"></i></button>
                </form>
            </div>
        </div>
    </nav>

    <div class="hero-section">
        <div class="container text-center">
            <h1 class="display-4 fw-bold">Welcome to Byte & Beyond</h1>
            <p class="lead">Discover amazing tech content and share your knowledge</p>
        </div>
    </div>

    <div class="container my-5">
        <h2 class="mb-4">Latest Posts</h2>

        <?php if ($result->num_rows > 0): ?>
            <div class="row row-cols-1 row-cols-md-2 row-cols-lg-3 g-4">
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col">
                        <div class="card h-100">
                            <?php if ($row['image_path']): ?>
                                <img src="<?= htmlspecialchars($row['image_path']) ?>" class="card-img-top" alt="Post Image" onclick="openModal('<?= htmlspecialchars($row['image_path']) ?>')">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/400x200?text=No+Image" class="card-img-top" alt="Default Image">
                            <?php endif; ?>

                            <div class="card-body">
                                <h5 class="card-title"><?= htmlspecialchars($row['title']) ?></h5>
                                <p class="card-text"><?= nl2br(htmlspecialchars(substr($row['content'], 0, 150) . (strlen($row['content']) > 150 ? '...' : ''))) ?></p>

                                <div class="post-meta">
                                    <i class="far fa-user"></i> By
                                    <a href="about.php?user_id=<?= $row['user_id'] ?>" class="author-link">
                                        <?= htmlspecialchars($row['username']) ?>
                                    </a>
                                    <br>
                                    <i class="far fa-calendar-alt"></i> <?= date('M j, Y', strtotime($row['created_at'])) ?>
                                </div>

                                <a href="login.php" class="btn btn-primary mt-3">
                                    <i class="far fa-eye me-1"></i> Read More
                                </a>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            </div>

            <?php if ($total_posts > 10): ?>
                <div class="text-center mt-4">
                    <a href="login.php" class="btn btn-view-more">
                        <i class="fas fa-arrow-down me-2"></i>View More Posts
                    </a>
                </div>
            <?php endif; ?>

        <?php else: ?>
            <div class="no-posts">
                <i class="far fa-newspaper fa-4x mb-3" style="color: #ccc;"></i>
                <h3>No posts available yet</h3>
                <p class="text-muted">Be the first to create a post!</p>
                <a href="login.php" class="btn btn-primary">Login to Post</a>
            </div>
        <?php endif; ?>
    </div>

    <!-- Image Modal -->
    <div id="imageModal" class="modal">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="modalImage">
    </div>

    <footer class="text-center">
        <div class="container">
            <p>&copy; <?= date('Y') ?> Byte & Beyond. All rights reserved.</p>
            <div class="social-links">
                <a href="#" class="text-white mx-2"><i class="fab fa-facebook-f"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-twitter"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-instagram"></i></a>
                <a href="#" class="text-white mx-2"><i class="fab fa-linkedin-in"></i></a>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image modal functionality
        function openModal(imgSrc) {
            const modal = document.getElementById('imageModal');
            const modalImg = document.getElementById('modalImage');
            modal.style.display = "block";
            modalImg.src = imgSrc;
        }

        function closeModal() {
            document.getElementById('imageModal').style.display = "none";
        }

        
        window.onclick = function(event) {
            const modal = document.getElementById('imageModal');
            if (event.target == modal) {
                closeModal();
            }
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>