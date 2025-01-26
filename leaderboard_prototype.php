<?php
// Include your database connection
session_start();
include 'database.php';

$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 

// Fetch top contributors from the database (assuming you have a 'users' table with 'name' and 'score')
$query = "SELECT first_name, last_name, score, profile_picture FROM sign_up ORDER BY score DESC LIMIT 5"; // Adjust table/column names as necessary
$result = $conn->query($query);

// Check if we have results
if ($result->num_rows > 0) {
    // Fetch the data and prepare it for display
    $contributors = [];
    while ($row = $result->fetch_assoc()) {
        $contributors[] = $row;
    }
} else {
    $contributors = [];
}

if (isset($_POST['dashboard_button'])) {
  header("Location: trackit_prototype.php");  // Redirect to dashboard.php
  exit();  // Always call exit after header redirect
}

// Check if the Profile button is clicked
if (isset($_POST['profile_button'])) {
  header("Location: profile_prototype.php");  // Redirect to profile.php
  exit();
}
$conn->close();


?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>TrackIT Leaderboard</title>
  <a href="profile.html"></a>
  <script src="script.js" defer></script>
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
      background-color: #FFD1D1;
      transition: all 0.3s ease-in-out;
    }

    .sidebar-button:hover {
      transform: scale(1.1);
      box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
      background-color: #B5FFFC;
      color: #333;
    }

    .sidebar-button:active {
      transform: scale(0.95);
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2);
    }

    /* Highlight Active Button */
    .sidebar-button.active {
      background-color: #FFD700;
      color: white;
    }

   #Sadman {
      width: 850px;
      height: 100px;
      margin-left: 200px;
   } 
   #prapti {
      width: 800px;
      height: 100px;
      margin-left: 200px;
   }
   #robin {
      width: 750px;
      height: 100px;
      margin-left: 200px;
   }
   #awal {
      width: 700px;
      height: 100px;
      margin-left: 200px;
   }
   #back2 {
      background-color: #0d4d52;
      background-image: linear-gradient(62deg, #538e8e 0%, #FFE6FA 100%);
   }
  </style>
</head>
<body id="back2" class="bg-[#79461e] text-white min-h-screen flex">
  <!-- Sidebar -->
  <aside class="w-80 p-5">
    <h1 class="text-4xl font-bold mb-10 ml-12 text-black">TrackIT</h1>
    <nav>
      <ul>
      <li class="mb-4">
    <form method="POST">
        <button type="submit" name="dashboard_button" class="w-56 py-3 rounded-xl sidebar-button">DashBoard</button>
    </form>
    </li>
    <li class="mb-4">
    <form method="POST">
        <button type="submit" name="leaderboard_button" class="w-56 py-3 rounded-xl sidebar-button">LeaderBoard</button>
    </form>
    </li>

    <li>
    <form method="POST">
        <button type="submit" name="profile_button" class="w-56 py-3 rounded-xl sidebar-button">Profile</button>
    </form>
    </li>
      </ul>
    </nav>
  </aside>

  <!-- Main Content -->
  <div class="flex-1 p-6">
    <div class="flex justify-between items-center mb-6">
      <h2 class="text-3xl font-semibold">Top Contributors</h2>
      <input
        type="text"
        placeholder="Search"
        class="rounded-lg px-4 py-2 text-black"
      />
    </div>

    <!-- Contributors List -->
    <div class="space-y-6">
  <?php foreach ($contributors as $index => $contributor): ?>
    <!-- Calculate the width dynamically based on the index -->
    <?php 
    $width = 90 - ($index * 5); // Decrease the width by 5% for each subsequent item
    if ($width < 50) {
        $width = 50; // Prevent width from going below 50%
    }

    // Calculate the margin
    $margin = (100 - $width) / 2;
?>

    <div class="flex justify-between items-center bg-[#f1c9a8] p-4 rounded-xl" id="contributor-<?php echo $index; ?>" style="width: <?php echo $width; ?>%;">
      <div class="flex items-center gap-4">
        <img src="<?php echo htmlspecialchars($contributor['profile_picture']); ?>" alt="Contributor Image" class="rounded-full w-12 h-12">
        <p class="text-[#5b3412] font-semibold"><?php echo htmlspecialchars($contributor['first_name']); ?> <?php echo htmlspecialchars($contributor['last_name']); ?></p>
      </div>
      <div class="flex items-center gap-2 text-[#5b3412]">
        <span><?php echo $contributor['score']; ?></span>
        <div class="bg-yellow-400 w-6 h-6 rounded-full"></div>
      </div>
    </div>
  <?php endforeach; ?>
</div>

  </div>
</body>
</html>
