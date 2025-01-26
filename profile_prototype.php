<?php
// Initialize session or database connection
session_start();

// Assuming user ID is stored in session
$userId = $_SESSION['user_id']; // Replace with your actual session variable
//echo $userId;
// Database connection details
include 'database.php';

// Create a connection
$conn = new mysqli($servername, $username, $password, $dbname);

// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare a SQL query to fetch data for the logged-in user
$sql = "SELECT first_name, last_name, work_as, score, department, institution, job_title, contact, profile_picture FROM sign_up WHERE Serial_id = ?";

$stmt = $conn->prepare($sql);

// Bind parameters
$stmt->bind_param("i", $userId); // "i" indicates that the user ID is an integer

// Execute the query
$stmt->execute();

// Bind the result to variables
$stmt->bind_result($first_name, $last_name, $work_as, $score, $department, $institution, $job_title, $contact, $profile_picture);

// Fetch the data
$stmt->fetch();

// Store the user data in the $userData array
$userData = [
    'first_name' => $first_name,
    'last_name' => $last_name,
    'department' => $department,
    'institution' => $institution,
    'job_title' => $job_title,
    'contact' => $contact,
    'profile_picture' => $profile_picture,
    'work_as' => $work_as,
    'score' => $score // Assuming the file path for profile picture is stored in the database
];
?>
<?php

/// Handle form submission to update profile data
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
   
    // Check if the user is logged in by ensuring the user_id is in the session
    if (!isset($_SESSION['user_id'])) {
        echo "Error: User is not logged in.";
        exit;
    }

    // Sanitize user inputs
    $first_name = mysqli_real_escape_string($conn, $_POST['first_name'] ?? '');
    $last_name = mysqli_real_escape_string($conn, $_POST['last_name'] ?? '');
    $department = mysqli_real_escape_string($conn, $_POST['department'] ?? '');
    $institution = mysqli_real_escape_string($conn, $_POST['institution'] ?? '');
    $job_title = mysqli_real_escape_string($conn, $_POST['job_title'] ?? '');
    $contact = mysqli_real_escape_string($conn, $_POST['contact'] ?? '');
    $Serial_id = $userId;

    // Set the profile picture to the current session value or a default one
    $profile_picture = isset($_SESSION['profile_picture']) ? $_SESSION['profile_picture'] : 'uploads/default.jpg';

    // Handle profile picture upload (if any)
    if (isset($_FILES['profile_picture']) && $_FILES['profile_picture']['error'] == 0) {
        $uploadDir = 'uploads/';
        $uploadFile = $uploadDir . basename($_FILES['profile_picture']['name']);

    // Validate file type (allow only image files)
    $allowedFileTypes = ['image/jpeg', 'image/png', 'image/jpg'];
    if (in_array($_FILES['profile_picture']['type'], $allowedFileTypes)) {
        // Move the uploaded file to the desired directory
        if (move_uploaded_file($_FILES['profile_picture']['tmp_name'], $uploadFile)) {
            // If the picture is successfully uploaded, update the profile picture path
            $profile_picture = $uploadFile;

            // Update session with the new profile picture path
            $_SESSION['profile_picture'] = $profile_picture;
        } else {
            echo "Error uploading file.";
        }
    } else {
        echo "Invalid file type. Only JPG and PNG are allowed.";
    }
} 

// Use the selected or existing profile picture for the update operation (e.g., in the database)


    // Get the user_id from session to update the correct record
    $userId = $_SESSION['user_id'];
    $conn = new mysqli($servername, $username, $password, $dbname);

    // Check connection
    if ($conn->connect_error) {
        die("Connection failed: " . $conn->connect_error);
    }

    // Prepare the SQL query to update the profile data
    $sql = "UPDATE sign_up SET first_name = ?, last_name = ?, department = ?, institution = ?,  job_title = ?, contact = ?, profile_picture = ? WHERE Serial_id = '$userid'";

    echo "Debug: Preparing statement";
    echo "First Name: " . $first_name . ", Last Name: " . $last_name;

    // Prepare the statement
    $stmt = $conn->prepare($sql);
    // if ($stmt === false) {
    //     echo "Error preparing statement: " . $conn->error;
    //     exit;
    // }

    // Bind the parameters to the prepared statement
    $stmt->bind_param("sssssssi",$first_name, $last_name,$department, $institution, $job_title, $contact,$profile_picture,$Serial_id);
    $stmt->execute();
    // Execute the query
    if ($stmt->execute()) {
        //echo "Profile updated successfully!";
    }// else {
    //    // echo "Error updating profile: " . $stmt->error;
    // }

    



    // Get user ID from session
    //$userId2 = $_SESSION['Serial_id']; // Assuming Serial_id is stored in the session

    // Construct the SQL query
    // $sql = "
    //     UPDATE sign_up 
    //     SET 
    //         first_name = '$first_name', 
    //         last_name = '$last_name', 
    //         department = '$department', 
    //         institution = '$institution', 
    //         job_title = '$job_title', 
    //         contact = '$contact', 
    //         profile_picture = '$profile_picture',
    //     WHERE Serial_id = '$userId'
    // ";

    // Debugging: Check the constructed SQL query
    // echo "SQL Query: " . $sql . "<br>";

    // Execute the query
    if (mysqli_query($conn, $sql)) {
        // Update session data
        $_SESSION['first_name'] = $first_name;
        $_SESSION['last_name'] = $last_name;
        $_SESSION['work_as'] = $work_as;
        $_SESSION['score'] = $score;
        $_SESSION['department'] = $department;
        $_SESSION['institution'] = $institution;
        $_SESSION['job_title'] = $job_title;
        $_SESSION['contact'] = $contact;
        $_SESSION['profile_picture'] = $profile_picture;

        // Redirect or show success message
        //echo "Profile updated successfully!";
        header("Location: profile_prototype.php");
        exit;
    } 
    // else {
    //     echo "Error updating profile: " . $conn->error;
    // }

    // For demonstration, you can save the updated data in a session or database.
    // $_SESSION['userData'] = $userData;
    // Close the statement and the connection
    $stmt->close();
    $conn->close();
}

?>
<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TrackIT Profile</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <style>
     /* Sidebar Styling */
     aside {
      background-color: #FFDEE9;
      background-image: linear-gradient(0deg, #FFDEE9 0%, #B5FFFC 100%);
    }

    /* Sidebar Buttons */
    .sidebar-button {
      transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
      font-size: 1rem;
      font-weight: bold;
      color: #333;
      background-color: #FFD1D1; /* Default button color */
    }

    .sidebar-button:hover {
      transform: scale(1.1);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
      background-color: #B5FFFC; /* Button hover color */
      color: #333; /* Keep text color readable */
    }

    .sidebar-button:active {
      transform: scale(0.95);
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    /* Highlight Active Button */
    .sidebar-button.active {
      background-color: #FFD700; /* Golden color for active */
      color: white;
    }
    
    /* Page Background */
    body {
      background-color: #0d4d52; /* Soft teal for a clean, fresh look */
      background-image: linear-gradient(62deg, #538e8e 0%, #FFE6FA 100%);
      font-family: 'Poppins', sans-serif;
    }

    /* Typography */
    h1, h2, h3 {
      font-weight: 600;
    }

    p, span {
      font-weight: 400;
    }
    .modal {
      position: fixed;
      top: 0;
      left: 0;
      width: 100%;
      height: 100%;
      background-color: rgba(0, 0, 0, 0.5);
      display: none;
      justify-content: center;
      align-items: center;
      z-index: 50;
    }
    .modal.active {
      display: flex;
    }
  </style>
</head>
<body class="text-white">
  <div class="flex h-screen">
    <!-- Sidebar -->
    <aside class="w-80 p-5">
      <h1 class="text-4xl font-bold mb-10 ml-12 text-black">TrackIT</h1>
      <nav>
        <ul>
        <li class="mb-4">
    <button class="w-56 py-3 rounded-xl sidebar-button" onclick="navigateTo('trackit_prototype.php')">DashBoard</button>
      </li>
      <li class="mb-4">
    <button class="w-56 py-3 rounded-xl sidebar-button" onclick="navigateTo('leaderboard_protototype.php')">LeaderBoard</button>
      </li>
    <li>
    <button id="profileWork" class="w-56 py-3 rounded-xl sidebar-button" onclick="navigateTo('profile')">Profile</button>
    </li>
        </ul>
      </nav>
    </aside>

    <!-- Profile Section -->
    <div class="w-3/4 p-10">
      <div class="flex items-center space-x-8">
        <img src="<?= $userData['profile_picture'] ?>" alt="Profile Picture" class="w-32 h-32 rounded-full border-4 border-[#FFECC9]" id="profilePic">
        <div>
          <h2 class="text-2xl font-bold text-black">Name</h2>
          <p class="text-xl text-black" id="firstName"><?= $userData['first_name'] ?></p>
          <p class="text-xl text-black" id="lastName"><?= $userData['last_name'] ?></p>
        </div>
        <div>
          <h2 class="text-2xl font-bold text-black">Role</h2>
          <p class="text-xl text-black"><?= $userData['work_as'] ?></p>
        </div>
        <div class="items-center">
          <h2 class="text-2xl font-bold text-black">Score</h2>
          <div class="flex items-center ml-2">
            <span class="text-black text-3xl">🏆</span>
            <p class="text-xl text-black ml-2"><?= $userData['score'] ?></p>
          </div>
        </div>
      </div>

      <div class="mt-12">
        <h3 class="text-2xl font-bold mb-4 text-black">About</h3>
        <div class="space-y-4 bg-[#eae5e3e4] p-6 rounded-lg profile-card">
          <div class="flex justify-between">
            <span class="font-semibold text-black">Department</span>
            <span class="text-black" id="department"><?= $userData['department'] ?></span>
          </div>
          <div class="flex justify-between">
            <span class="font-semibold text-black">Institution</span>
            <span class="text-black" id="institution"><?= $userData['institution'] ?></span>
          </div>
          <div class="flex justify-between">
            <span class="font-semibold text-black">Job Title</span>
            <span class="text-black" id="jobTitle"><?= $userData['job_title'] ?></span>
          </div>
          <div class="flex justify-between">
            <span class="font-semibold text-black">Contact</span>
            <span class="text-black" id="contact"><?= $userData['contact'] ?></span>
          </div>
        </div>

        <!-- Edit Button -->
        <div class="mt-6 flex justify-end">
          <button id="editButton" class="py-2 px-6 bg-blue-500 hover:bg-blue-600 text-white font-bold rounded-lg shadow-md transition duration-300">
            Edit Profile
          </button>
        </div>
      </div>
    </div>
  </div>

  <!-- Edit Profile Modal -->
  <div id="editModal" class="modal">
    <div class="bg-white p-8 rounded-lg w-1/3 shadow-lg">
      <h2 class="text-2xl font-bold mb-4 text-center">Edit Profile</h2>
      <form id="editForm" method="post" enctype="multipart/form-data">
        <div class="mb-4">
          <label for="profilePicture" class="block text-gray-700 font-semibold">Profile Picture</label>
          <input type="file" name="profile_picture" id="profilePicture" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="mb-4">
          <label for="firstNameInput" class="block text-gray-700 font-semibold">First Name</label>
          <input type="text" name="first_name" id="firstNameInput" value="<?= $userData['first_name'] ?>" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="mb-4">
          <label for="lastNameInput" class="block text-gray-700 font-semibold">Last Name</label>
          <input type="text" name="last_name" id="lastNameInput" value="<?= $userData['last_name'] ?>" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="mb-4">
          <label for="departmentInput" class="block text-gray-700 font-semibold">Department</label>
          <input type="text" name="department" id="departmentInput" value="<?= $userData['department'] ?>" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="mb-4">
          <label for="institutionInput" class="block text-gray-700 font-semibold">Institution</label>
          <input type="text" name="institution" id="institutionInput" value="<?= $userData['institution'] ?>" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="mb-4">
          <label for="jobTitleInput" class="block text-gray-700 font-semibold">Job Title</label>
          <input type="text" name="job_title" id="jobTitleInput" value="<?= $userData['job_title'] ?>" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="mb-4">
          <label for="contactInput" class="block text-gray-700 font-semibold">Contact</label>
          <input type="text" name="contact" id="contactInput" value="<?= $userData['contact'] ?>" class="block w-full mt-2 border rounded-md p-2 bg-white text-black">
        </div>
        <div class="flex justify-end space-x-4">
          <button type="button" id="cancelButton" class="py-2 px-4 bg-gray-400 hover:bg-gray-500 text-white rounded-lg">Cancel</button>
          <button type="submit" class="py-2 px-4 bg-blue-500 hover:bg-blue-600 text-white rounded-lg">Save</button>
        </div>
      </form>
    </div>
  </div>

  <script>
    function navigateTo(page) {
        switch(page) {
            case 'dashboard':
                window.location.href = 'trackit_prototype.php';  // Navigate to the dashboard page
                break;
            case 'leaderboard':
                window.location.href = 'leaderboard_prototype.php';  // Navigate to the leaderboard page
                break;
            
            default:
                console.log("Page not found");
        }
    }
    const editButton = document.getElementById('editButton');
    const editModal = document.getElementById('editModal');
    const cancelButton = document.getElementById('cancelButton');

    // Show modal on "Edit Profile" button click
    editButton.addEventListener('click', () => {
      editModal.classList.add('active');
    });

    // Hide modal on "Cancel" button click
    cancelButton.addEventListener('click', () => {
      editModal.classList.remove('active');
    });
    function navigateTo(page) {
    window.location.href = page;
  }
  </script>
</body>
</html>
