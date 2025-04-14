<?php
session_start(); // Start a session to store user information

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

    // Get the email and password from the form
    $email = trim($_POST['email']);
    $password = $_POST['password'];

    // Validate email
    if (empty($email) || !filter_var($email, FILTER_VALIDATE_EMAIL)) {
        echo "Invalid email format.";
        exit();
    }

    // Prepare and execute the SQL query to find the user
    $sql = "SELECT * FROM users WHERE email = '$email'";
    $result = $conn->query($sql);

    if ($result->num_rows > 0) {
        // User found, fetch the user data
        $user = $result->fetch_assoc();

        // Verify the password
        if (password_verify($password,$user['password'] )) {
            // Password is correct, set session variables
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['full_name'] = $user['full_name']; // Updated to match the new field
            $_SESSION['username'] = $user['username']; // Added username
            $_SESSION['email'] = $user['email'];
            $_SESSION['bio'] = $user['bio']; // Added bio
            $_SESSION['phone_number'] = $user['phone_number']; // Added phone number
            $_SESSION['date_of_birth'] = $user['date_of_birth']; // Added date of birth
            $_SESSION['gender'] = $user['gender'];
            $_SESSION['profile_picture'] = $user['profile_picture']; // Added gender
            // Note: Removed event_list as it is not in the new table structure

            // Redirect to welcome page
            header("Location: welcome.php");
            exit();
        } else {
            echo "Invalid password.";
        }
    } else {
        echo "No user found with that email.";
    }

    // Close the connection
    $conn->close();
}
?>