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

// Fetch the total number of posts
$totalPostsQuery = "SELECT COUNT(*) as total FROM posts";
$totalPostsResult = $conn->query($totalPostsQuery);
$totalPosts = $totalPostsResult->fetch_assoc()['total'];

// Fetch initial posts (10)
$limit = 10;
$sql = "SELECT p.id, p.title, p.content, p.image_path, p.created_at, u.id as user_id, u.username, u.profile_picture,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id) as like_count,
        (SELECT COUNT(*) FROM likes l WHERE l.post_id = p.id AND l.user_id = ?) as user_liked
        FROM posts p
        JOIN users u ON p.user_id = u.id
        ORDER BY p.created_at DESC LIMIT $limit";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $_SESSION['user_id']);
$stmt->execute();
$result = $stmt->get_result();
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Welcome</title>
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
            box-shadow: 0 8px 30px rgba(0, 0, 0, 0.15);
        }

        .card-img-top {
            height: 200px;
            object-fit: cover;
            cursor: pointer;
            transition: transform 0.3s;
        }

        .card-img-top:hover {
            transform: scale(1.03);
        }

        .card-body {
            padding: 20px;
            display: flex;
            flex-direction: column;
        }

        .user-info {
            display: flex;
            align-items: center;
            margin-bottom: 15px;
        }
        .home{
            color: var(--secondary-color) !important;
        }
        .user-info img {
            width: 40px;
            height: 40px;
            border-radius: 50%;
            margin-right: 10px;
            object-fit: cover;
            border: 2px solid #007bff;
        }

        .username {
            font-weight: 600;
            color: #333;
            text-decoration: none;
        }

        .username:hover {
            color: #007bff;
        }

        .card-title {
            font-size: 1.25rem;
            font-weight: bold;
            margin-bottom: 10px;
            color: #2c3e50;
        }

        .card-text {
            font-size: 0.95rem;
            color: #555;
            margin-bottom: 15px;
            flex-grow: 1;
        }

        .post-date {
            font-size: 0.8rem;
            color: #6c757d;
            margin-bottom: 15px;
        }

        .interaction-section {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-top: auto;
        }

        .like-count {
            font-weight: 600;
            color: #333;
        }

        .heart-icon {
            font-size: 24px;
            cursor: pointer;
            transition: all 0.3s;
        }

        .heart-icon:hover {
            transform: scale(1.1);
        }

        .heart-icon.liked {
            color: #ff4757;
        }

        .heart-icon.unliked {
            color: #adb5bd;
        }

        .follow-btn {
            font-size: 0.8rem;
            padding: 5px 10px;
            border-radius: 20px;
            margin-left: 10px;
        }

        #loadMore {
            display: block;
            margin: 30px auto;
            padding: 10px 25px;
            border-radius: 30px;
            font-weight: 600;
            background-color: #007bff;
            border: none;
        }

        #loadMore:hover {
            background-color: #0069d9;
            transform: translateY(-2px);
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
            background-color: rgba(0, 0, 0, 0.9);
            overflow: auto;
        }

        .modal-content {
            display: block;
            margin: auto;
            max-width: 90%;
            max-height: 90%;
            position: absolute;
            top: 50%;
            left: 50%;
            transform: translate(-50%, -50%);
        }

        .close {
            position: absolute;
            top: 20px;
            right: 30px;
            color: #fff;
            font-size: 35px;
            font-weight: bold;
            cursor: pointer;
        }

        .close:hover {
            color: #ccc;
        }

        .navbar {
            box-shadow: 0 2px 10px rgba(0, 0, 0, 0.1);
        }

        .profile-pic {
            width: 50px;
            height: 50px;
            border-radius: 50%;
            object-fit: cover;
            border: 2px solid #007bff;
            transition: transform 0.3s;
        }

        .profile-pic:hover {
            transform: scale(1.05);
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
                        <a class="nav-link home" href="welcome.php"><i class="fas fa-home"></i> Home</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link newpost" href="newpost.php"><i class="fas fa-plus-circle"></i> New Post</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link active" href="allpost.php"><i class="fas fa-list"></i> My Posts</a>
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

    <div class="container py-4">
        <h2 class="mb-4">All Posts</h2>
        <div id="posts" class="row">
            <?php if ($result->num_rows > 0): ?>
                <?php while($row = $result->fetch_assoc()): ?>
                    <div class="col-md-4">
                        <div class="card">
                            <?php if ($row['image_path']): ?>
                                <img src="<?php echo htmlspecialchars($row['image_path']); ?>" class="card-img-top" alt="Post Image" onclick="openModal(this.src)">
                            <?php else: ?>
                                <img src="https://via.placeholder.com/600x400?text=No+Image" class="card-img-top" alt="Default Image" onclick="openModal(this.src)">
                            <?php endif; ?>

                            <div class="card-body">
                                <div class="user-info">
                                    <a href="about.php?user_id=<?php echo htmlspecialchars($row['user_id']); ?>" class="d-flex align-items-center text-decoration-none">
                                        <img src="<?php echo htmlspecialchars($row['profile_picture']); ?>" alt="Profile Picture">
                                        <span class="username"><?php echo htmlspecialchars($row['username']); ?></span>
                                    </a>

                                    <?php if ($row['user_id'] != $_SESSION['user_id']): ?>
                                        <?php
                                            $followCheck = $conn->prepare("SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?");
                                            $followCheck->bind_param("ii", $_SESSION['user_id'], $row['user_id']);
                                            $followCheck->execute();
                                            $isFollowing = $followCheck->get_result()->num_rows > 0;
                                        ?>
                                        <button class="btn btn-sm <?php echo $isFollowing ? 'btn-danger' : 'btn-primary'; ?> follow-btn ms-auto follow-user-<?php echo $row['user_id']; ?>"
                                                onclick="toggleFollow(<?php echo $row['user_id']; ?>, <?php echo $isFollowing ? 'false' : 'true'; ?>, this)">
                                            <?php echo $isFollowing ? 'Unfollow' : 'Follow'; ?>
                                        </button>
                                    <?php endif; ?>
                                </div>

                                <h5 class="card-title"><?php echo htmlspecialchars($row['title']); ?></h5>
                                <p class="card-text"><?php echo nl2br(htmlspecialchars($row['content'])); ?></p>
                                <p class="post-date">Posted on <?php echo htmlspecialchars($row['created_at']); ?></p>

                                <div class="interaction-section">
                                    <span class="like-count">Likes: <span id="like-count-<?php echo $row['id']; ?>"><?php echo $row['like_count']; ?></span></span>
                                    <i class="heart-icon <?php echo $row['user_liked'] ? 'fas fa-heart liked' : 'far fa-heart unliked'; ?> like-post-<?php echo $row['id']; ?>"
                                       onclick="toggleLike(<?php echo $row['id']; ?>, <?php echo $row['user_liked'] ? 'false' : 'true'; ?>, this)"></i>
                                </div>
                            </div>
                        </div>
                    </div>
                <?php endwhile; ?>
            <?php else: ?>
                <div class="col-12 text-center py-5">
                    <h4 class="text-muted">No posts available yet.</h4>
                    <a href="newpost.php" class="btn btn-primary mt-3">Create Your First Post</a>
                </div>
            <?php endif; ?>
        </div>

        <?php if ($totalPosts > $limit): ?>
            <button id="loadMore" class="btn btn-primary" onclick="loadMorePosts()">Load More Posts</button>
        <?php endif; ?>
    </div>

    <!-- Image Modal -->
    <div id="myModal" class="modal" onclick="closeModal()">
        <span class="close" onclick="closeModal()">&times;</span>
        <img class="modal-content" id="img01">
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        let offset = <?php echo $limit; ?>;
        const totalPosts = <?php echo $totalPosts; ?>;

        function openModal(src) {
            const modal = document.getElementById("myModal");
            const img = document.getElementById("img01");
            img.src = src;
            modal.style.display = "block";
            document.body.style.overflow = "hidden";
        }

        function closeModal() {
            document.getElementById("myModal").style.display = "none";
            document.body.style.overflow = "auto";
        }

        function toggleLike(postId, isLike, element) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "toggle_like.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (this.status === 200) {
                    const response = JSON.parse(this.responseText);
                    // Update all like counts for this post
                    document.querySelectorAll(`#like-count-${postId}`).forEach(el => {
                        el.innerText = response.like_count;
                    });

                    // Update all like buttons for this post
                    document.querySelectorAll(`.like-post-${postId}`).forEach(icon => {
                        if (isLike) {
                            icon.classList.remove('far', 'unliked');
                            icon.classList.add('fas', 'liked');
                            icon.setAttribute('onclick', `toggleLike(${postId}, false, this)`);
                        } else {
                            icon.classList.remove('fas', 'liked');
                            icon.classList.add('far', 'unliked');
                            icon.setAttribute('onclick', `toggleLike(${postId}, true, this)`);
                        }
                    });
                }
            };
            xhr.send("post_id=" + postId + "&is_like=" + (isLike ? 1 : 0));
        }

        function toggleFollow(userId, isFollow, button) {
            const xhr = new XMLHttpRequest();
            xhr.open("POST", "toggle_follow.php", true);
            xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
            xhr.onload = function() {
                if (this.status === 200) {
                    // Update all follow buttons for this user
                    document.querySelectorAll(`.follow-user-${userId}`).forEach(btn => {
                        if (isFollow) {
                            btn.innerHTML = 'Unfollow';
                            btn.classList.remove('btn-primary');
                            btn.classList.add('btn-danger');
                            btn.setAttribute('onclick', `toggleFollow(${userId}, false, this)`);
                        } else {
                            btn.innerHTML = 'Follow';
                            btn.classList.remove('btn-danger');
                            btn.classList.add('btn-primary');
                            btn.setAttribute('onclick', `toggleFollow(${userId}, true, this)`);
                        }
                    });
                }
            };
            xhr.send("user_id=" + userId + "&is_follow=" + (isFollow ? 1 : 0));
        }

        function loadMorePosts() {
            if (offset >= totalPosts) {
                document.getElementById("loadMore").style.display = "none";
                return;
            }

            const xhr = new XMLHttpRequest();
            xhr.open("GET", `load_more_posts.php?offset=${offset}&user_id=<?php echo $_SESSION['user_id']; ?>`, true);
            xhr.onload = function() {
                if (this.status === 200) {
                    const posts = JSON.parse(this.responseText);
                    const postsContainer = document.getElementById("posts");

                    posts.forEach(post => {
                        const postDiv = document.createElement("div");
                        postDiv.className = "col-md-4";
                        postDiv.innerHTML = `
                            <div class="card">
                                ${post.image_path ?
                                    `<img src="${post.image_path}" class="card-img-top" alt="Post Image" onclick="openModal('${post.image_path}')">` :
                                    `<img src="https://via.placeholder.com/600x400?text=No+Image" class="card-img-top" alt="Default Image" onclick="openModal('https://via.placeholder.com/600x400?text=No+Image')">`}
                                <div class="card-body">
                                    <div class="user-info">
                                        <a href="about.php?user_id=${post.user_id}" class="d-flex align-items-center text-decoration-none">
                                            <img src="${post.profile_picture}" alt="Profile Picture">
                                            <span class="username">${post.username}</span>
                                        </a>
                                        ${post.user_id != <?php echo $_SESSION['user_id']; ?> ?
                                            `<button class="btn btn-sm ${post.is_following ? 'btn-danger' : 'btn-primary'} follow-btn ms-auto follow-user-${post.user_id}"
                                                     onclick="toggleFollow(${post.user_id}, ${post.is_following ? 'false' : 'true'}, this)">
                                                ${post.is_following ? 'Unfollow' : 'Follow'}
                                            </button>` : ''}
                                    </div>
                                    <h5 class="card-title">${post.title}</h5>
                                    <p class="card-text">${post.content.replace(/\n/g, '<br>')}</p>
                                    <p class="post-date">Posted on ${post.created_at}</p>
                                    <div class="interaction-section">
                                        <span class="like-count">Likes: <span id="like-count-${post.id}">${post.like_count}</span></span>
                                        <i class="heart-icon ${post.user_liked ? 'fas fa-heart liked' : 'far fa-heart unliked'} like-post-${post.id}"
                                           onclick="toggleLike(${post.id}, ${post.user_liked ? 'false' : 'true'}, this)"></i>
                                    </div>
                                </div>
                            </div>
                        `;
                        postsContainer.appendChild(postDiv);
                    });

                    offset += <?php echo $limit; ?>;
                    if (offset >= totalPosts) {
                        document.getElementById("loadMore").style.display = "none";
                    }
                }
            };
            xhr.send();
        }
    </script>
</body>
</html>

<?php
$conn->close();
?>