<?php
  session_start();
  include 'database.php';
  $conn = new mysqli($servername, $username, $password, $dbname);
  
  // Check for connection errors
  if ($conn->connect_error) {
      die("Connection failed: " . $conn->connect_error);
  } 

  $sql = "SELECT * FROM Project_Details";
    $all_projects = mysqli_query($conn, $sql);

    
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <script src="https://cdn.tailwindcss.com"></script>
  <title>TrackIT</title>
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
      transition: all 0.3s ease-in-out;
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

    /* Main background styling */
    #create {
        background-color: #0d4d52; /* Soft teal for a clean, fresh look */
        background-image: linear-gradient(62deg, #538e8e 0%, #FFE6FA 100%);
      width: 1920px;
      height: 800px;
    }
    
    #MyButton{
        margin-top: 10px;
    }

    #navigateToPage1, #navigateToPage2, #navigateToPage3, #navigateToPage4{
        width: 850px;
        align-items: center;
    }

    /* Interactive hover effects for project cards and sidebar buttons */
    #navigateToPage1, #navigateToPage2, #navigateToPage3, #navigateToPage4,
    #profileWork, .sidebar-btn {
      transition: transform 0.3s ease, box-shadow 0.3s ease, background-color 0.3s ease;
    }

    /* Hover effects to simulate clickability */
    #navigateToPage1:hover, #navigateToPage2:hover, #navigateToPage3:hover, #navigateToPage4:hover,
    #profileWork:hover, .sidebar-btn:hover {
      transform: scale(1.05); /* Slight zoom effect */
      box-shadow: 0 10px 20px rgba(0, 0, 0, 0.3); /* Stronger shadow */
      background-color: #fef3c7; /* Light yellow glow on hover */
      cursor: pointer;
    }

    /* Active/pressed effect for buttons */
    #navigateToPage1:active, #navigateToPage2:active, #navigateToPage3:active, #navigateToPage4:active,
    #profileWork:active, .sidebar-btn:active {
      transform: scale(0.98); /* Simulate press */
      box-shadow: 0 5px 10px rgba(0, 0, 0, 0.2); /* Reduced shadow */
    }

    /* Default shadow for sidebar buttons */
    .sidebar-btn {
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1); /* Subtle shadow */
    }
    .space-y-6{
        margin-left: 150px;
    }
  </style>
</head>
<body class="bg-red-300 font-sans">
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
              <button class="w-56 py-3 rounded-xl sidebar-button" onclick="navigateTo('leaderboard_prototype.php')">LeaderBoard</button>
            </li>
            <li>
              <button id="profileWork" class="w-56 py-3 rounded-xl sidebar-button" onclick="navigateTo('profile_prototype.php')">Profile</button>
            </li>
          </ul>
        </nav>
      </aside>

    <!-- Main Content -->
    <main class="flex-1 p-8 relative" id="create">
      <header class="flex justify-between items-center mb-10">
        <h2 class="text-3xl font-bold text-white ml-44 flex items-center space-x-4 group">
            <span>Create Project</span>
            <svg id="MyButton" xmlns="http://www.w3.org/2000/svg" fill="none" viewBox="0 0 24 24" stroke-width="1.5" 
              stroke="currentColor" class="w-8 h-8 text-white transition-transform group-hover:scale-125 group-hover:text-yellow-300 group-active:scale-90" onclick="navigateTo('create_prototype.html')">
              <path stroke-linecap="round" stroke-linejoin="round" d="M12 9v6m3-3H9m12 0a9 9 0 1 1-18 0 9 9 0 0 1 18 0Z" />
            </svg>
          </h2>
        <!-- <div class="flex items-center space-x-3">
          <input type="text" placeholder="Search" class="py-2 px-4 border rounded-lg">
          <button class="py-2 px-4 mb-2 bg-yellow-400 hover:bg-yellow-500 text-white rounded-lg transition-all">
            Search
          </button>
        </div> -->
      </header>

      <?php
        while( $row = mysqli_fetch_assoc($all_projects)){
      ?>
      <!-- Project Cards -->
      <div class="space-y-6">
          <div id="navigateToPage1" class="bg-orange-100 opacity-80 p-5 h-28 rounded-xl shadow-lg text-xl font-semibold flex justify-between items-center" onclick="navigateTo('timeline.php')">
            <span><?php echo $row["projectName"] ?></span>

            <form action="trackit_prototype_delete.php" method="POST">
              <input type= hidden name="id" value="<?php echo $row ['id']; ?>">
            <button class="py-2 px-1 hover:bg-red-600 text-black rounded-lg transition-all flex items-center space-x-2" type= submit >
              <svg xmlns="http://www.w3.org/2000/svg" class="w-5 h-5" fill="none" viewBox="0 0 24 24" stroke="black" stroke-width="2">
                <path stroke-linecap="round" stroke-linejoin="round" d="M19 7l-.867 12.142A2 2 0 0116.137 21H7.863a2 2 0 01-1.996-1.858L5 7m5-4h4m-4 0a1 1 0 00-1 1v1h6V4a1 1 0 00-1-1m-4 0h4" />
              </svg>
              <span>Delete</span>
            </button>
            </form>

          </div>
        <br>
      </div>

      <!-- <div class="space-y-6">
        <div id="navigateToPage1" class="bg-orange-100 opacity-80 p-5 h-28 rounded-xl shadow-lg text-xl font-semibold" onclick="navigateTo('web_prototype.html')">
         <?php echo $row["projectName"] ?>
        </div>
        <br>
      </div>
      <?php
        }
      ?> -->
    </main>
  </div>

  <script>
    function navigateTo(page) {
      window.location.href = page; 
    }
  </script>

  <!--<script src="js/script2.js"></script>
  <script src="js/web dev.js"></script>
  <script src="js/mybutton.js"></script>
  <script src="js/profile1.js"></script>
  <script src="js/security.js"></script> -->
</body>
</html>
