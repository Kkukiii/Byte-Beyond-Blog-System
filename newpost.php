<?php
session_start();

if (!isset($_SESSION['username'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Create New Post | Byte & Beyond</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #6c63ff;
            --secondary-color: #4d44db;
            --dark-color: #2a2a72;
            --light-color: #f8f9fa;
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

        .form-container {
            background-color: white;
            border-radius: 10px;
            box-shadow: 0 5px 15px rgba(0, 0, 0, 0.05);
            padding: 30px;
            margin-top: 30px;
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

        .form-control, .form-select {
            border-radius: 8px;
            padding: 12px 15px;
            border: 1px solid #e0e0e0;
            transition: all 0.3s;
        }

        .form-control:focus, .form-select:focus {
            border-color: var(--primary-color);
            box-shadow: 0 0 0 0.25rem rgba(108, 99, 255, 0.25);
        }

        .btn-primary {
            background-color: var(--primary-color);
            border-color: var(--primary-color);
            padding: 10px 25px;
            border-radius: 8px;
            font-weight: 500;
            transition: all 0.3s;
        }

        .btn-primary:hover {
            background-color: var(--secondary-color);
            border-color: var(--secondary-color);
            transform: translateY(-2px);
        }

        .image-upload {
            border: 2px dashed #ddd;
            border-radius: 10px;
            padding: 30px;
            text-align: center;
            cursor: pointer;
            transition: all 0.3s;
            background-color: #fafafa;
            margin-bottom: 20px;
        }

        .image-upload:hover {
            border-color: var(--primary-color);
            background-color: rgba(108, 99, 255, 0.05);
        }

        .image-upload i {
            font-size: 2.5rem;
            color: var(--primary-color);
            margin-bottom: 10px;
        }

        .image-upload p {
            color: #666;
            margin-bottom: 0;
        }

        .image-preview {
            margin-top: 20px;
            display: none;
            text-align: center;
        }

        .image-preview img {
            max-height: 300px;
            border-radius: 8px;
            box-shadow: 0 3px 10px rgba(0, 0, 0, 0.1);
        }
        .newpost{
            color: var(--primary-color) !important;
        }

        .remove-image {
            margin-top: 10px;
            color: #dc3545;
            cursor: pointer;
            font-weight: 500;
        }

        footer {
            background-color: var(--dark-color);
            color: white;
            padding: 30px 0;
            margin-top: 50px;
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
                        <a class="nav-link active newpost" href="newpost.php"><i class="fas fa-plus-circle"></i> New Post</a>
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

    <div class="container py-5">
        <div class="row justify-content-center">
            <div class="col-lg-8">
                <div class="form-container">
                    <h2 class="section-title">Create New Post</h2>
                    <form action="upload_post.php" method="POST" enctype="multipart/form-data">
                        <div class="mb-4">
                            <label for="postTitle" class="form-label fw-bold">Post Title</label>
                            <input type="text" class="form-control" id="postTitle" name="postTitle" placeholder="Enter a catchy title..." required>
                        </div>

                        <div class="mb-4">
                            <label for="postContent" class="form-label fw-bold">Post Content</label>
                            <textarea class="form-control" id="postContent" name="postContent" rows="6" placeholder="Write your post content here..." required></textarea>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold">Featured Image</label>
                            <div class="image-upload" id="imageUpload">
                                <i class="fas fa-cloud-upload-alt"></i>
                                <p>Click to upload an image</p>
                                <input type="file" id="postImage" name="postImage" accept="image/*" style="display: none;">
                            </div>
                            <div class="image-preview" id="imagePreview">
                                <img id="previewImg" src="" alt="Image Preview" class="img-fluid">
                                <div class="remove-image" id="removeImage"><i class="fas fa-times"></i> Remove Image</div>
                            </div>
                        </div>

                        <div class="d-grid">
                            <button type="submit" class="btn btn-primary btn-lg">
                                <i class="fas fa-paper-plane me-2"></i> Publish Post
                            </button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>

    <footer>
        <div class="container">
            <div class="row">
                <div class="col-md-6">
                    <h5>Byte & Beyond</h5>
                    <p>A platform for sharing knowledge and ideas about technology and beyond.</p>
                </div>
                <div class="col-md-3">
                    <h5>Links</h5>
                    <div class="footer-links">
                        <a href="welcome.php">Home</a>
                        <a href="about.php">About</a>
                        <a href="allpost.php">Posts</a>
                        <a href="contact.php">Contact</a>
                    </div>
                </div>
                <div class="col-md-3">
                    <h5>Follow Us</h5>
                    <div class="social-icons">
                        <a href="#"><i class="fab fa-facebook-f"></i></a>
                        <a href="#"><i class="fab fa-twitter"></i></a>
                        <a href="#"><i class="fab fa-instagram"></i></a>
                        <a href="#"><i class="fab fa-linkedin-in"></i></a>
                    </div>
                </div>
            </div>
            <hr class="mt-4 mb-4" style="background-color: rgba(255,255,255,0.1);">
            <div class="text-center">
                <p class="mb-0">&copy; <?php echo date('Y'); ?> Byte & Beyond. All rights reserved.</p>
            </div>
        </div>
    </footer>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script>
        // Image upload functionality
        document.getElementById('imageUpload').onclick = function() {
            document.getElementById('postImage').click();
        };

        document.getElementById('postImage').onchange = function(event) {
            const file = event.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(e) {
                    document.getElementById('previewImg').src = e.target.result;
                    document.getElementById('imagePreview').style.display = 'block';
                    document.getElementById('imageUpload').style.display = 'none';
                };
                reader.readAsDataURL(file);
            }
        };

        // Remove image functionality
        document.getElementById('removeImage').onclick = function() {
            document.getElementById('previewImg').src = '';
            document.getElementById('imagePreview').style.display = 'none';
            document.getElementById('imageUpload').style.display = 'block';
            document.getElementById('postImage').value = '';
        };
    </script>
</body>
</html>