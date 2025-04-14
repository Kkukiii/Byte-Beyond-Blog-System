<?php
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Database connection
    $servername = "localhost"; // Change if your database server is different
    $username = "root"; // Your MySQL username
    $password = "Siddharth@360"; // Your MySQL password
    $dbname = "blogsystem"; // Your database name

    // Create connection
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Retrieve and trim input values
    $full_name = trim($_POST['full_name']);
    $username = trim($_POST['username']);
    $email = trim($_POST['email']);
    $password = $_POST['password'];
    $confirm_password = $_POST['confirm_password'];
    $bio = trim($_POST['bio']);
    $phone_number = trim($_POST['phone_number']);
    $date_of_birth = $_POST['date_of_birth'];
    $gender = $_POST['gender'];

    $errors = [];

    // Validate Full Name
    if (empty($full_name) || !preg_match("/^[a-zA-Z\s]+$/", $full_name)) {
        $errors[] = "Full Name is required and must contain only letters and spaces.";
    }

    // Validate Username
    if (empty($username)) {
        $errors[] = "Username is required.";
    }

    // Validate Email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $errors[] = "Email must follow a proper email format.";
    }

    // Validate Password
    if (strlen($password) < 8) {
        $errors[] = "Password must be at least 8 characters long.";
    }

    // Validate Confirm Password
    if ($password !== $confirm_password) {
        $errors[] = "Password and Confirm Password must match.";
    }

    // Validate Bio
    if (empty($bio)) {
        $errors[] = "Bio is required.";
    }

    // Validate Phone Number
    if (empty($phone_number) || !is_numeric($phone_number)) {
        $errors[] = "Phone Number must be valid and numeric.";
    }

    // Validate Date of Birth
    if (empty($date_of_birth)) {
        $errors[] = "Date of Birth is required.";
    }

    // Validate Gender
    if (empty($gender)) {
        $errors[] = "You must select a gender.";
    }

    // Validate Profile Picture
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == UPLOAD_ERR_OK) {
        $file = $_FILES['profile_picture'];
        $allowed_types = ['image/jpeg', 'image/png', 'image/gif'];
        if (!in_array($file['type'], $allowed_types)) {
            $errors[] = "Profile Picture must be a JPEG, PNG, or GIF image.";
        }
    } else {
        $errors[] = "Profile Picture is required.";
    }

    // If there are no errors, proceed with registration
    if (empty($errors)) {
        // Hash the password for security
        $hashed_password = password_hash($password, PASSWORD_DEFAULT);

        // Handle file upload
        $profile_picture_path = 'uploads/' . basename($_FILES['profile_picture']['name']);
        move_uploaded_file($_FILES['profile_picture']['tmp_name'], $profile_picture_path);

        // Construct the SQL query
        $sql = "INSERT INTO users (full_name, username, email, password, bio, phone_number, date_of_birth, gender, profile_picture)
                VALUES ('$full_name', '$username', '$email', '$hashed_password', '$bio', '$phone_number', '$date_of_birth', '$gender', '$profile_picture_path')";

        // Execute the query
        if ($conn->query($sql) === TRUE) {
            // Redirect to login page
            header("Location: login.php");
            exit();
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        // Display errors
        foreach ($errors as $error) {
            echo "<p style='color:red;'>$error</p>";
        }
    }

    // Close the connection
    $conn->close();
}
?>