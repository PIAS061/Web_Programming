<?php
// Start the session to access session variables
session_start();

// Database connection
include 'database.php';

// Retrieve the task giver's name from the session
$task_giver_id = $_SESSION['user_id'];

// Ensure form data was submitted via POST
if ($_SERVER["REQUEST_METHOD"] == "POST") {
  // Retrieve the form data
  $task_name = $_POST['task_name'];
  $end_time = $_POST['end_time'];
  $assignee = $_POST['assignee']; // Assuming you handle this dynamically

  // Create connection
  $conn = new mysqli($servername, $username, $password, $dbname);

  // Check the connection
  if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
  }

  // Prepare the SQL query to insert data into task_details table
  $sql = "INSERT INTO task_details (task_giver_id, Task, Date, assignee) VALUES ('$task_giver_id', '$task_name', '$end_time', '$assignee')";

  // Execute the query
  if ($conn->query($sql) === TRUE) {
    $_SESSION['task_giver_id'] = $task_giver_id;
    $_SESSION['task_name'] = $task_name;
    header("Location: timeline.php");
    //echo "New task added successfully!";
  } else {
    echo "Error: " . $sql . "<br>" . $conn->error;
  }

  // Close the connection
  $conn->close();
} else {
  echo "Invalid request.";
}
?>