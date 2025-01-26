
<?php

// Start a session if required
session_start();
include 'database.php'; // Include your database connection file
// Get task_giver_id and Serial_id from POST or session
$taskGiverId = $_POST['Serial_id']; // or use session if it's already stored

#real-time 
$realTimeDate = date('Y-m-d');



$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

$taskQuery = "SELECT Date FROM task_details WHERE task_giver_id = ?";
$taskStmt = $conn->prepare($taskQuery);
$taskStmt->bind_param("i", $taskGiverId);

// Execute the query
$taskStmt->execute();
$taskResult = $taskStmt->get_result();

while ($taskRow = $taskResult->fetch_assoc()) {
    // Get task date
    $taskDate = $taskRow['Date'];

    // Compare the task date with the real-time date
    if ($taskDate <= $realTimeDate) {
        // Add 5 to the score if task date is less than or equal to real-time date
        $scoreChange = 5;
    } else {
        // Add 3 to the score if task date is greater than real-time date
        $scoreChange = 5;
        break;
    }
}
// Update the `sign_up` table: Add 5 to the score for the given Serial_id
$updateQuery = "UPDATE sign_up SET score = score + '$scoreChange' WHERE Serial_id = ? ";
$updateStmt = $conn->prepare($updateQuery);

// Bind the Serial_id as an integer parameter
$updateStmt->bind_param("i", $taskGiverId);

// Execute the update query and send a response
if ($updateStmt->execute()) {
    echo json_encode(['success' => true, 'message' => 'Score updated successfully']);
} else {
    echo json_encode(['success' => false, 'message' => 'Failed to update score']);
}

// Close the prepared statement and database connection
$updateStmt->close();
$conn->close();
?>
