<?php
//var_dump($_POST);
session_start();

include 'database.php';
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} else {
    echo "Connection Done<br>"; // Change this to echo
}

// Check if the form is submitted
if ($_SERVER["REQUEST_METHOD"] === "POST") {
    echo "Connected2<br>";
    if (isset($_POST['projectName']) && isset($_POST['summary']) && isset($_POST['description']) && isset($_POST['reporter'])) {
        $projectName = $_POST['projectName'];
        $summary = $_POST['summary'];
        $description = $_POST['description'];
        $reporter = $_POST['reporter'];
        echo "Connected2<br>"; // Debugging output
    } else {
        die("Data are not set");
    }

    // SQL to insert data into the users table
    $sql = "INSERT INTO project_details (projectName, summary, description, reporter)
            VALUES ('$projectName', '$summary', '$description', '$reporter')";

    if ($conn->query($sql) === TRUE) {
        // Get the inserted record's ID
        $projectId = $conn->insert_id;

        // Store data in the session
        $_SESSION['user_id'] = $projectId;
        $_SESSION['projectName'] = $projectName;
        $_SESSION['summary'] = $summary;
        $_SESSION['description'] = $description;
        $_SESSION['reporter'] = $reporter;
        

        // Redirect to trackit_prototype.html
        header("Location: trackit_prototype.php");
        exit();
    } else {
        // Error in insertion
        echo "Error: " . $sql . "<br>" . $conn->error;
    }
}
?>
