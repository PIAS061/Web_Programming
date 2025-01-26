<?php
include 'database.php'; // Include database connection
$task_id = ($_POST['task_id']); 
echo $task_id;
// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if task_id is sent via POST
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['task_id'])) {
    $task_id = intval($_POST['task_id']); // Sanitize task_id
    echo $task_id;
    // Delete query
    $sql = "DELETE FROM task_details WHERE task_id = ?";
    $stmt = $conn->prepare($sql);
    // Check if prepare() succeeded
    if (!$stmt) {
        die("Prepare failed: " . $conn->error);
    }

    $stmt->bind_param("i", $task_id);

    if ($stmt->execute()) {
        echo "Task deleted successfully.";
    } else {
        echo "Error deleting task: " . $conn->error;
    }

    $stmt->close();
}
header("Location: web_prototype.php");
$conn->close();
?>
