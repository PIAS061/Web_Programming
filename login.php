
<?php
//var_dump($_POST);
session_start();

//session_regenerate_id();
// Include database connection
include 'database.php';
$conn = new mysqli($servername, $username, $password, $dbname);

// Check for connection errors
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    if (isset($_POST['email1']) && isset($_POST['password1'])) {
        $email = $_POST['email1'];
        $password = $_POST['password1'];
        echo "Connected2<br>"; // Debugging output
    } else {
        die("Email or Password not set");
    }

    // SQL query to validate login
    $sql = "SELECT * FROM sign_up WHERE email='$email' AND password='$password'";
    $result = mysqli_query($conn, $sql);

    // Debugging SQL Execution
    if (!$result) {
        die("Query failed: " . mysqli_error($conn));
    }

    echo "Connected3<br>"; // Debugging output

    // Check the number of rows
    $num = mysqli_num_rows($result);
    echo "Number of rows: $num<br>"; // Debugging output

    if ($num == 1) {
        $row = mysqli_fetch_assoc($result);
        // Store data in the session
        $userId = $conn->insert_id;

        // Store data in the session
        $_SESSION['user_id'] = $row['Serial_id'];  // Assuming 'user_id' is the column name
        $_SESSION['first_name'] = $row['first_name'];
        $_SESSION['last_name'] = $row['last_name'];
        $_SESSION['email'] = $row['email'];
        $_SESSION['work_as'] = $row['work_as'];
        $_SESSION['profile_picture'] = $row['profile_picture'];
        // Redirect to another page
        // Check if user is logged in
        if (isset($_SESSION['user_id'])) {
            // Redirect to login page if not logged in
            header("Location: trackit_prototype.php");
            exit();
        }
        } else {
            // Error in validation
            echo "Invalid email or password.";
        }
} else {
    die("Form data is missing.");
}

// Close connection
$conn->close();
?>




























