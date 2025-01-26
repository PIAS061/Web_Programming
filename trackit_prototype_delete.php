<?php
include 'database.php';
$conn = new mysqli($servername, $username, $password, $dbname);

$ProjectId = $_POST['id'];
$sql = "DELETE FROM project_details WHERE id= $ProjectId";
$result = mysqli_query($conn, $sql);

// Debugging SQL Execution
if (!$result) {
    die("Query failed: " . mysqli_error($conn));
}

header("Location: trackit_prototype.php");
exit();

?>