<?php
// signup_process.php

// Start the session
session_start();

// Include database connection (replace with your actual database details)
include 'database.php';

$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $first_name = trim($_POST['first_name']);
    $last_name = trim($_POST['last_name']);
    $email = trim($_POST['email']);
    $password = trim($_POST['password']);
    $workAs = trim($_POST['work_as']);

    // Hash password for security
    //$hashedPassword = password_hash($password, PASSWORD_DEFAULT);

    // SQL to insert data into the users table
    $sql = "INSERT INTO sign_up (first_name, last_name, email, password, work_as)
            VALUES ('$first_name', '$last_name', '$email', '$password', '$workAs')";

    if ($conn->query($sql) === TRUE) {
        // Get the inserted record's ID
        $userId = $conn->insert_id;

        // Store data in the session
        $_SESSION['user_id'] = $userId;
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['email'] = $email;
        $_SESSION['work_as'] = $workAs;
        $_SESSION['profile_picture']= $profile_picture; 

        // Redirect to trackit_prototype.html
        header("Location: login.html");
        exit();
    } else {
        // Error in insertion
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}

// Close connection
$conn->close();
?>
