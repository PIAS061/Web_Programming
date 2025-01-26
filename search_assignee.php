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

// Fetch developers where role = 'developer'
$query = "SELECT Serial_id, email, first_name, last_name FROM sign_up WHERE work_as = 'Developer' LIMIT 3";
$result = $conn->query($query);
$suggestions = [];
while ($row = $result->fetch_assoc()) {
    $suggestions[] = [
        'id' => $row['Serial_id'],
        'name' => $row['first_name'] . ' ' . $row['last_name']
    ];
}
header('Content-Type: application/json');
echo json_encode($suggestions);


// Close the connection
$conn->close();
?>

