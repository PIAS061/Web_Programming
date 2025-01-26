<?php
// Create a connection
session_start();
// Fetch data from the database
include 'database.php';
$conn = new mysqli($servername, $username, $password, $dbname);
// Check connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
} 
$task_giver_id = $_SESSION['user_id'];
// Query to fetch tasks
$sql = "SELECT task_id, Task FROM task_details WHERE task_giver_id = '$task_giver_id'";
$result = $conn->query($sql);

$tasks = [];
if ($result->num_rows > 0) {
    while ($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
};

$conn->close();
?>

<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Kanban Board</title>
  <link rel="stylesheet" href="Css/web.css">
  <style>
    button[type="submit"] {
      font-size: 1.3em;
      background: none;
      border: none;
      color: red;
      cursor: pointer;
      padding: 0;
      margin-left: 10px;
    }

    button[type="submit"]:hover {
      color: darkred;
    }
   

  </style>
</head>
<body>
  <div class="container">
    <div class="sidebar">
      <h1>TrackIT</h1>
      <button onclick="navigateTo('trackit_prototype.php')">DashBoard</button>
      <button onclick="navigateTo('leaderboard_prototype.php')">LeaderBoard</button>
      <button onclick="navigateTo('profile_prototype.php')">Profile</button>
    </div>
    <div class="main">
      <header>
        <h2>Kanban Board</h2>
      </header>
      <div class="kanban-board">
       <!-- TO DO Column -->
        <div class="column" id="todo">
          <h3>TO DO ☕</h3>
          <?php foreach ($tasks as $task): ?>
            <?php if (true): ?> <!-- Adjust this condition if needed -->
              <div class="task" draggable="true">
                <p>
                  <?php echo htmlspecialchars($task['Task']); ?>
                  <!-- Add a delete icon -->
                  <form action="delete_task.php" method="POST" style="display: inline;">
                    <input type="hidden" name="task_id" value="<?php echo $task['task_id']; ?>">
                    <button type="submit" style="background: none; border: none; color: red; cursor: pointer;">
                      🗑️
                    </button>
                  </form>
                </p>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>


        <!-- IN PROGRESS Column -->
        <div class="column" id="in-progress">
          <h3>IN PROGRESS 🍵</h3>
          <?php foreach ($tasks as $task): ?>
            <?php if (false): ?>
              <div class="task" draggable="true">
                <p><?php echo htmlspecialchars($task['Task']); ?></p>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>

        <!-- DONE Column -->
        <div class="column" id="done">
          <h3>DONE 💤</h3>
          <?php foreach ($tasks as $task): ?>
            <?php if (false): ?>
              <div class="task" draggable="true">
                <p><?php echo htmlspecialchars($task['Task']); ?></p>
              </div>
            <?php endif; ?>
          <?php endforeach; ?>
        </div>
      </div>
    </div>
  </div>

  <script>
    function navigateTo(url) {
      window.location.href = url; // Navigate to the specified URL
    }
    let draggedTask = null;

    // Drag-and-Drop Functionality
    function enableDragAndDrop(task) {
      task.addEventListener('dragstart', dragStart);
      task.addEventListener('dragend', dragEnd);
    }

    function dragStart(e) {
      draggedTask = e.target;
      e.dataTransfer.effectAllowed = 'move';
      setTimeout(() => (e.target.style.opacity = '0.5'), 0);
    }

    function dragEnd(e) {
      setTimeout(() => {
        draggedTask.style.opacity = '1';
        draggedTask = null;
      }, 0);
    }

    document.querySelectorAll('.column').forEach(column => {
      column.addEventListener('dragover', dragOver);
      column.addEventListener('drop', drop);
    });

    function dragOver(e) {
      e.preventDefault();
      const column = e.target.closest('.column');
      if (column && column !== draggedTask.closest('.column')) {
        column.style.border = '2px dashed #4caf50'; // Highlight the column being hovered over
      }
    }

    function drop(e) {
      e.preventDefault();
      const column = e.target.closest('.column');
      if (column && column !== draggedTask.closest('.column')) {
        column.style.border = 'none'; // Remove the border highlight
        column.appendChild(draggedTask); // Append task to the new column
      }
    }

    // Enable drag-and-drop for all tasks
    document.querySelectorAll('.task').forEach(enableDragAndDrop);

    document.querySelectorAll('form[action="delete_task.php"]').forEach(form => {
    form.addEventListener('submit', function (e) {
      if (!confirm('Are you sure you want to delete this task?')) {
        e.preventDefault(); // Prevent form submission
      }
    });
  });
  document.addEventListener('DOMContentLoaded', function () {
    // Function to check tasks in each section
    function checkTaskStatus() {
      const todoColumn = document.getElementById('todo');
      const inProgressColumn = document.getElementById('in-progress');
      const doneColumn = document.getElementById('done');
      
      const todoTasks = todoColumn.getElementsByClassName('task');
      const inProgressTasks = inProgressColumn.getElementsByClassName('task');
      const doneTasks = doneColumn.getElementsByClassName('task');
      
      // Check if there are any tasks in "To Do" or "In Progress" sections
      // Check if there are any tasks in "To Do" or "In Progress" sections
      if (todoTasks.length === 0 && inProgressTasks.length === 0 && doneTasks.length > 0) {
        // Alert the user
          const taskGiverId = <?php echo isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 'null'; ?>;  // Example task_giver_id, replace with actual value
          const task = 'Sample Task';  // Example task, replace with actual task name

          fetch('check_and_update_score.php', {
            method: 'POST',
            headers: {
              'Content-Type': 'application/x-www-form-urlencoded', // Set the content type for form data
            },
            body: `updateScore=true&Serial_id=${encodeURIComponent(taskGiverId)}` // Include task_giver_id and task
          })
            .then(response => {
              if (!response.ok) {
                throw new Error('Network response was not ok');
              }
              return response.text(); // If the server returns a response
            })
            .then(data => {
              console.log('Score updated successfully:', data); // Log success message
            })
            .catch(error => {
              console.error('Error updating score:', error); // Log error message
            });

        }

    }
    

    // Call the checkTaskStatus function when the page loads
    checkTaskStatus();

    // If you want to automatically check when tasks are dragged to another section
    document.querySelectorAll('.column').forEach(column => {
      column.addEventListener('drop', function () {
        checkTaskStatus();
      });
    });
  });
  </script>
</body>
</html>
