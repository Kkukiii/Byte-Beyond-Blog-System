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

if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Get user_id from the URL
if (isset($_GET['user_id'])) {
    $user_id = intval($_GET['user_id']);

    // Fetch user details
    $user_sql = "SELECT username, profile_picture, bio, created_at FROM users WHERE id = ?";
    $stmt = $conn->prepare($user_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $user_result = $stmt->get_result();

    if ($user_result->num_rows > 0) {
        $user = $user_result->fetch_assoc();
    } else {
        die("User not found.");
    }

    // Check if current user is following the viewed user
    $follow_sql = "SELECT * FROM follows WHERE follower_id = ? AND followed_id = ?";
    $stmt = $conn->prepare($follow_sql);
    $stmt->bind_param("ii", $_SESSION['user_id'], $user_id);
    $stmt->execute();
    $is_following = $stmt->get_result()->num_rows > 0;

    // Get follower count
    $followers_sql = "SELECT COUNT(*) as follower_count FROM follows WHERE followed_id = ?";
    $stmt = $conn->prepare($followers_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $followers_result = $stmt->get_result();
    $follower_count = $followers_result->fetch_assoc()['follower_count'];

    // Get following count
    $following_sql = "SELECT COUNT(*) as following_count FROM follows WHERE follower_id = ?";
    $stmt = $conn->prepare($following_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $following_result = $stmt->get_result();
    $following_count = $following_result->fetch_assoc()['following_count'];

    // Get post count
    $posts_sql = "SELECT COUNT(*) as post_count FROM posts WHERE user_id = ?";
    $stmt = $conn->prepare($posts_sql);
    $stmt->bind_param("i", $user_id);
    $stmt->execute();
    $posts_result = $stmt->get_result();
    $post_count = $posts_result->fetch_assoc()['post_count'];

    // Format join date
    $join_date = date("F Y", strtotime($user['created_at']));
} else {
    die("No user specified.");
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?php echo htmlspecialchars($user['username']); ?>'s Profile</title>
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

        .profile-header {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-top: 30px;
            position: relative;
            overflow: hidden;
        }

        .profile-banner {
            height: 150px;
            background: linear-gradient(135deg, var(--primary-color), var(--secondary-color));
            margin: -30px -30px 20px -30px;
            position: relative;
        }

        .profile-picture {
            position: relative;
            width: 150px;
            height: 150px;
            border-radius: 50%;
            object-fit: cover;
            border: 5px solid white;
            box-shadow: 0 4px 10px rgba(0, 0, 0, 0.1);
            margin-top: -75px;
            background-color: white;

        }

        .profile-name {
            font-size: 28px;
            font-weight: 700;
            color: var(--secondary-color);
            margin: 15px 0 5px;
        }

        .profile-bio {
            color: #555;
            font-size: 16px;
            line-height: 1.6;
            max-width: 600px;
            margin: 0 auto 20px;
        }

        .profile-stats {
            display: flex;
            justify-content: center;
            gap: 30px;
            margin: 25px 0;
        }

        .stat-item {
            text-align: center;
        }

        .stat-number {
            font-size: 24px;
            font-weight: 700;
            color: var(--primary-color);
        }

        .stat-label {
            font-size: 14px;
            color: #7f8c8d;
            text-transform: uppercase;
            letter-spacing: 1px;
        }

        .profile-meta {
            color: #7f8c8d;
            font-size: 14px;
            margin-bottom: 20px;
        }

        .action-buttons {
            margin-top: 20px;
        }

        .btn-follow {
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            transition: all 0.3s;
        }

        .btn-edit {
            border-radius: 50px;
            padding: 8px 25px;
            font-weight: 600;
            background-color: var(--light-color);
            color: var(--dark-color);
            border: none;
            transition: all 0.3s;
        }

        .btn-edit:hover {
            background-color: #dfe6e9;
        }

        .nav-tabs {
            border-bottom: none;
            justify-content: center;
            margin: 30px 0;
        }

        .nav-tabs .nav-link {
            border: none;
            color: #7f8c8d;
            font-weight: 600;
            padding: 10px 20px;
            margin: 0 5px;
            border-radius: 50px;
        }

        .nav-tabs .nav-link.active {
            background-color: var(--primary-color);
            color: white;
        }

        .content-section {
            background-color: white;
            border-radius: 15px;
            box-shadow: 0 4px 20px rgba(0, 0, 0, 0.08);
            padding: 30px;
            margin-bottom: 30px;
        }

        .section-title {
            font-size: 20px;
            font-weight: 700;
            color: var(--secondary-color);
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 2px solid var(--light-color);
        }
    </style>
</head>
<body>
    <nav class="navbar navbar-expand-lg navbar-light">
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
                        <a class="nav-link" href="allpost.php"><i class="fas fa-list"></i> My Posts</a>
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

    <div class="container">
        <div class="profile-header text-center">
            <div class="profile-banner">
            </div>
            <img src="<?php echo htmlspecialchars($user['profile_picture']); ?>" class="profile-picture" alt="Profile Picture">
            <h1 class="profile-name"><?php echo htmlspecialchars($user['username']); ?></h1>

            <?php if (!empty($user['bio'])): ?>
                <p class="profile-bio"><?php echo nl2br(htmlspecialchars($user['bio'])); ?></p>
            <?php else: ?>
                <p class="profile-bio text-muted">No bio yet</p>
            <?php endif; ?>

            <div class="profile-stats">
                <div class="stat-item">
                    <div class="stat-number"><?php echo $post_count; ?></div>
                    <div class="stat-label">Posts</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $follower_count; ?></div>
                    <div class="stat-label">Followers</div>
                </div>
                <div class="stat-item">
                    <div class="stat-number"><?php echo $following_count; ?></div>
                    <div class="stat-label">Following</div>
                </div>
            </div>

            <p class="profile-meta"><i class="fas fa-calendar-alt me-2"></i>Joined <?php echo $join_date; ?></p>

            <div class="action-buttons">
                <?php if ($user_id != $_SESSION['user_id']): ?>
                    <button class="btn btn-follow <?php echo $is_following ? 'btn-danger' : 'btn-primary'; ?>"
                            onclick="toggleFollow(<?php echo $user_id; ?>, <?php echo $is_following ? 'false' : 'true'; ?>, this)">
                        <i class="fas fa-user-<?php echo $is_following ? 'minus' : 'plus'; ?> me-1"></i>
                        <?php echo $is_following ? 'Unfollow' : 'Follow'; ?>
                    </button>
                <?php else: ?>
                    <a href="edit_profile.php" class="btn btn-edit">
                        <i class="fas fa-user-edit me-1"></i> Edit Profile
                    </a>
                <?php endif; ?>
            </div>
        </div>

        <ul class="nav nav-tabs" id="profileTabs" role="tablist">
            <li class="nav-item" role="presentation">
                <button class="nav-link active" id="posts-tab" data-bs-toggle="tab" data-bs-target="#posts" type="button" role="tab">Posts</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="followers-tab" data-bs-toggle="tab" data-bs-target="#followers" type="button" role="tab">Followers</button>
            </li>
            <li class="nav-item" role="presentation">
                <button class="nav-link" id="following-tab" data-bs-toggle="tab" data-bs-target="#following" type="button" role="tab">Following</button>
            </li>
        </ul>

        <div class="tab-content" id="profileTabsContent">
            <div class="tab-pane fade show active" id="posts" role="tabpanel">
                <div class="content-section">
                    <h3 class="section-title"><i class="fas fa-newspaper me-2"></i>Recent Posts</h3>
                    <?php
                    $posts_sql = "SELECT p.id, p.title, p.content, p.image_path, p.created_at
                                FROM posts p
                                WHERE p.user_id = ?
                                ORDER BY p.created_at DESC
                                LIMIT 5";
                    $stmt = $conn->prepare($posts_sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $posts_result = $stmt->get_result();

                    if ($posts_result->num_rows > 0) {
                        while($post = $posts_result->fetch_assoc()) {
                            echo '<div class="card mb-3 border-0 shadow-sm">';
                            echo '<div class="card-body">';
                            echo '<h5 class="card-title">' . htmlspecialchars($post['title']) . '</h5>';
                            echo '<p class="card-text text-muted"><small>' . date("F j, Y", strtotime($post['created_at'])) . '</small></p>';
                            echo '<p class="card-text">' . nl2br(htmlspecialchars(substr($post['content'], 0, 200))) . '...</p>';
                            if (!empty($post['image_path'])) {
                                echo '<img src="' . htmlspecialchars($post['image_path']) . '" class="img-fluid rounded mb-2" alt="Post image">';
                            }

                            echo '</div></div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">No posts yet.</div>';
                    }
                    ?>
                    <a href="user_posts.php?user_id=<?php echo $user_id; ?>" class="btn btn-primary mt-3">View All Posts</a>
                </div>
            </div>

            <div class="tab-pane fade" id="followers" role="tabpanel">
                <div class="content-section">
                    <h3 class="section-title"><i class="fas fa-users me-2"></i>Followers</h3>
                    <?php
                    $followers_sql = "SELECT u.id, u.username, u.profile_picture
                                    FROM follows f
                                    JOIN users u ON f.follower_id = u.id
                                    WHERE f.followed_id = ?
                                    LIMIT 10";
                    $stmt = $conn->prepare($followers_sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $followers_result = $stmt->get_result();

                    if ($followers_result->num_rows > 0) {
                        while($follower = $followers_result->fetch_assoc()) {
                            echo '<div class="d-flex align-items-center mb-3">';
                            echo '<a href="about.php?user_id=' . $follower['id'] . '" class="d-flex align-items-center text-decoration-none">';
                            echo '<img src="' . htmlspecialchars($follower['profile_picture']) . '" alt="Profile" class="rounded-circle me-3" width="50" height="50">';
                            echo '<span class="fw-bold">' . htmlspecialchars($follower['username']) . '</span>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">No followers yet.</div>';
                    }
                    ?>
                </div>
            </div>

            <div class="tab-pane fade" id="following" role="tabpanel">
                <div class="content-section">
                    <h3 class="section-title"><i class="fas fa-user-friends me-2"></i>Following</h3>
                    <?php
                    $following_sql = "SELECT u.id, u.username, u.profile_picture
                                    FROM follows f
                                    JOIN users u ON f.followed_id = u.id
                                    WHERE f.follower_id = ?
                                    LIMIT 10";
                    $stmt = $conn->prepare($following_sql);
                    $stmt->bind_param("i", $user_id);
                    $stmt->execute();
                    $following_result = $stmt->get_result();

                    if ($following_result->num_rows > 0) {
                        while($following = $following_result->fetch_assoc()) {
                            echo '<div class="d-flex align-items-center mb-3">';
                            echo '<a href="about.php?user_id=' . $following['id'] . '" class="d-flex align-items-center text-decoration-none">';
                            echo '<img src="' . htmlspecialchars($following['profile_picture']) . '" alt="Profile" class="rounded-circle me-3" width="50" height="50">';
                            echo '<span class="fw-bold">' . htmlspecialchars($following['username']) . '</span>';
                            echo '</a>';
                            echo '</div>';
                        }
                    } else {
                        echo '<div class="alert alert-info">Not following anyone yet.</div>';
                    }
                    ?>
                </div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
    function toggleFollow(userId, isFollow, button) {
        const xhr = new XMLHttpRequest();
        xhr.open("POST", "toggle_follow.php", true);
        xhr.setRequestHeader("Content-Type", "application/x-www-form-urlencoded");
        xhr.onload = function() {
            if (this.status === 200) {
                const response = JSON.parse(this.responseText);
                if (isFollow) {
                    button.innerHTML = '<i class="fas fa-user-minus me-1"></i> Unfollow';
                    button.classList.remove('btn-primary');
                    button.classList.add('btn-danger');
                    button.setAttribute('onclick', `toggleFollow(${userId}, false, this)`);
                    document.querySelector('.stat-number:nth-child(1)').textContent = response.newFollowerCount;
                } else {
                    button.innerHTML = '<i class="fas fa-user-plus me-1"></i> Follow';
                    button.classList.remove('btn-danger');
                    button.classList.add('btn-primary');
                    button.setAttribute('onclick', `toggleFollow(${userId}, true, this)`);
                    document.querySelector('.stat-number:nth-child(1)').textContent = response.newFollowerCount;
                }
            }
        };
        xhr.send("user_id=" + userId + "&is_follow=" + (isFollow ? 1 : 0));
    }
    </script>
</body>
</html>

<?php
$conn->close();
?>