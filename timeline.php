<?php
// Start the session
session_start();

// Database connection
include 'database.php'; // Assuming this file contains your database connection settings

// Retrieve the task giver's name from the session
$task_giver_id = $_SESSION['user_id'];

// Fetch tasks from the database
$conn = new mysqli($servername, $username, $password, $dbname);

// Check the connection
if ($conn->connect_error) {
    die("Connection failed: " . $conn->connect_error);
}

// Prepare the SQL query to retrieve tasks
$sql = "SELECT task_id, Task, Date, assignee FROM task_details WHERE task_giver_id = '$task_giver_id'";

// Execute the query and fetch tasks
$result = $conn->query($sql);

$tasks = [];

if ($result->num_rows > 0) {
    while($row = $result->fetch_assoc()) {
        $tasks[] = $row;
    }
}

$conn->close();
?>


<!DOCTYPE html>
<html lang="en">
<head>
  <meta charset="UTF-8">
  <meta name="viewport" content="width=device-width, initial-scale=1.0">
  <title>Timeline</title>
  <script src="https://cdn.tailwindcss.com"></script>
  <script src="https://unpkg.com/interactjs/dist/interact.min.js"></script>
  <style>
    body {
      font-family: 'Inter', sans-serif;
    }

    .timeline-container {
      display: grid;
      grid-template-columns: 1fr;
      grid-gap: 1px;
      background: #e5e7eb;
      border: 1px solid #ddd;
      position: relative;
      border-radius: 8px;
      overflow: hidden;
    }

    .timeline-header {
      display: grid;
      grid-template-columns: repeat(31, 1fr); /* 31 days */
      background: #1e293b;
      text-align: center;
      font-size: 12px;
      font-weight: 700;
      color: #ffffff;
      border-bottom: 2px solid #64748b;
    }

    .timeline-date {
      padding: 8px;
      border-right: 1px solid #64748b;
    }

    .timeline-row {
      position: relative;
      height: 50px;
      background: #f8fafc;
      border-bottom: 1px solid #e2e8f0;
    }

    .timeline-task {
      position: absolute;
      background: #3b82f6;
      color: #fff;
      height: 40px;
      border-radius: 6px;
      box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
      display: flex;
      align-items: center;
      padding: 0 12px;
      cursor: move;
      font-size: 14px;
      font-weight: 500;
      transition: transform 0.2s;
    }

    .timeline-task:hover {
      transform: scale(1.05);
    }

    .timeline-task-name {
      margin-left: 8px;
    }

    .timeline-scroller {
      overflow-x: auto;
    }

    .drag-locked {
      cursor: default;
    }

    .custom-button {
      background: #3b82f6;
      color: #fff;
      padding: 8px 12px;
      border-radius: 6px;
      text-align: center;
      font-weight: 600;
      cursor: pointer;
      transition: background 0.3s;
    }

    .custom-button:hover {
      background: #2563eb;
    }

    .custom-input {
      border: 1px solid #cbd5e1;
      border-radius: 6px;
      padding: 8px;
      width: 100%;
      margin-bottom: 10px;
      font-size: 14px;
    }

    .custom-input:focus {
      outline: none;
      border-color: #3b82f6;
      box-shadow: 0 0 0 2px rgba(59, 130, 246, 0.3);
    }

    h1 {
      color: #1e293b;
    }
    .search-container {
      position: relative;
      width: 100%;
    }
    #search-assignee {
      width: 100%;
      padding: 10px;
      font-size: 16px;
    }
    .suggestions {
      position: absolute;
      top: 40px;
      left: 0;
      width: 100%;
      border: 1px solid #ccc;
      background: #fff;
      z-index: 1000;
      max-height: 150px;
      overflow-y: auto;
      display: none;
    }
    .suggestion-item {
      padding: 10px;
      cursor: pointer;
    }
    .suggestion-item:hover {
      background: #f0f0f0;
    }
  </style>
  <script>
    // JavaScript for live search
    function filterOptions(query) {
      console.log('Filtering options for query:', query);
      const suggestionsBox = document.getElementById('suggestions');

      if (query.trim() === '') {
          // Hide suggestions if the input is empty
          suggestionsBox.style.display = 'none';
          suggestionsBox.innerHTML = '';
          return;
      }

      // Fetch suggestions from the server
      fetch(`search_assignee.php?term=${encodeURIComponent(query)}`)
          .then(response => {
              if (!response.ok) {
                  throw new Error(`HTTP error! Status: ${response.status}`);
              }
              return response.json(); // Parse JSON from the response
          })
          .then(data => {
              console.log("Received data:", data);

              // Clear previous suggestions
              suggestionsBox.innerHTML = '';

              // Show new suggestions
              if (data.length > 0) {
                  suggestionsBox.style.display = 'block';
                  data.forEach(item => {
                      const suggestionItem = document.createElement('div');
                      suggestionItem.className = 'suggestion-item';
                      suggestionItem.textContent = item.name;
                      suggestionItem.dataset.id = item.id;

                      // Add click event to select the suggestion
                      suggestionItem.addEventListener('click', () => {
                          const searchInput = document.getElementById('search-assignee');
                          searchInput.value = item.name; // Set input value to selected name
                          suggestionsBox.style.display = 'none'; // Hide suggestions box
                          // Optionally store the selected ID if needed
                          console.log('Selected ID:', item.id);
                      });

                      suggestionsBox.appendChild(suggestionItem);
                  });
              } else {
                  suggestionsBox.style.display = 'none'; // Hide suggestions if no data
              }
          })
          .catch(error => {
              console.error('Error fetching suggestions:', error);
          });
    }
  </script>
</head>
<body class="bg-gray-100 p-6">
  <div class="container mx-auto">
    <!-- Navigation -->
    <div class="flex items-center justify-between mb-6 bg-gray-800 p-4 shadow-md rounded-lg">
      <button onclick="window.location.href='trackit_prototype.php'"
       class="custom-button bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded">
        Home
      </button>
      <div class="flex space-x-4">
        <button class="custom-button bg-blue-600 hover:bg-blue-500 text-white font-semibold py-2 px-4 rounded">
          Timeline
        </button>
        <button
        class="custom-button bg-gray-700 hover:bg-gray-600 text-white font-semibold py-2 px-4 rounded"
        onclick="window.location.href='web_prototype.php'"
         > Kanban Board </button>
      </div>
    </div>

    <h1 class="text-3xl font-bold mb-6 text-center">Timeline</h1>

    <div class="flex">
      <!-- Task List and Add Task -->
      <div class="w-1/4 bg-white p-4 shadow-md rounded-lg">
        <h2 class="font-bold text-lg mb-4 text-gray-800">Tasks</h2>
        <ul id="task-list" class="mb-4">
          <?php foreach($tasks as $task): ?>
            <li class="mb-2 text-gray-700"><?php echo $task['Task'] . " (Assigned to: " . $task['assignee'] . ")"; ?></li>
          <?php endforeach; ?>
        </ul>
        <div>
        <form id="task-form" method="POST" action="task_details.php">
        <input
          type="text"
          id="new-task-name"
          name="task_name"
          placeholder="Enter task name"
          class="custom-input"
        />
        <input
          type="date"
          id="end-time"
          name="end_time"
          placeholder="Enter End time"
          class="custom-input"
        />
        <div class="search-container">
          <!-- Search input field -->
          <input 
            type="text" 
            id="search-assignee" 
            class="custom-input" 
            name="assignee"
            placeholder="Search Assignee..." 
            onkeyup="filterOptions(this.value)"
          />
          <!-- Dropdown to select assignee -->
          <div id="suggestions" class="suggestions"></div>
        </div>
        <button type="submit" id="add-task-btn" class="custom-button w-full">
          Add Task
        </button>
      </form>

        </div>
      </div>

      <!-- Timeline -->
      <div class="w-3/4 timeline-scroller ml-4">
        <div class="timeline-container">
          <!-- Timeline Header with Dates -->
          <div class="timeline-header">
            <script>
              // Dynamically generate dates 1 to 31
              const headerContainer = document.currentScript.parentNode;
              for (let i = 1; i <= 31; i++) {
                const dateElement = document.createElement("div");
                dateElement.classList.add("timeline-date");
                dateElement.textContent = i; // Just the day number
                headerContainer.appendChild(dateElement);
              }
            </script>
          </div>

          <!-- Task Rows -->
          <div id="task-rows">
            <?php foreach($tasks as $task): ?>
              <div class="timeline-row">
                <div class="timeline-task" style="width: 100px; left: 10px;">
                  <span class="timeline-task-name">
                    <?php echo $task['Task'] . " (" . $task['assignee'] . ")"; ?>
                  </span>
                </div>
              </div>
            <?php endforeach; ?>
          </div>
        </div>
      </div>
    </div>
  </div>

  <script>
    const addTaskBtn = document.getElementById("add-task-btn");
    const newTaskNameInput = document.getElementById("new-task-name");
    const assigneeSelect = document.getElementById("assignee");
    const taskList = document.getElementById("task-list");
    const taskRows = document.getElementById("task-rows");

    // Function to add a new task
    addTaskBtn.addEventListener("click", () => {
      const taskName = newTaskNameInput.value.trim();
      const assignee = assigneeSelect.value;
      if (taskName && assignee) {
        // Add to task list
        const taskItem = document.createElement("li");
        taskItem.classList.add("mb-2", "text-gray-700");
        taskItem.textContent = `${taskName} (Assigned to: ${assignee})`;
        taskList.appendChild(taskItem);

        // Add a new row to the timeline
        const taskRow = document.createElement("div");
        taskRow.classList.add("timeline-row", "task-row");
        taskRow.setAttribute("data-row", taskName.toLowerCase());
        taskRows.appendChild(taskRow);

        createTaskBar(taskRow, taskName, assignee);
        newTaskNameInput.value = "";
        assigneeSelect.selectedIndex = 0;
      }
    });

    // Create Task Bar on timeline row
    function createTaskBar(taskRow, taskName, assignee) {
      const taskElement = document.createElement("div");
      taskElement.classList.add("timeline-task");
      taskElement.style.width = "100px"; // Initial width
      taskElement.style.left = "10px";  // Default starting position

      // Add task name and assignee next to the bar
      const taskNameElement = document.createElement("span");
      taskNameElement.classList.add("timeline-task-name");
      taskNameElement.textContent = `${taskName} (${assignee})`;

      taskElement.appendChild(taskNameElement);
      taskRow.appendChild(taskElement);

      // Make the task bar draggable and resizable
      makeTaskBarDraggable(taskElement);
      makeTaskBarResizable(taskElement);
    }

    // Make all tasks draggable and resizable
    function makeAllTasksDraggable() {
      document.querySelectorAll(".timeline-task").forEach(taskElement => {
        makeTaskBarDraggable(taskElement);
        makeTaskBarResizable(taskElement);
      });
    }

    // Function to make the task bar draggable
    function makeTaskBarDraggable(taskElement) {
      interact(taskElement)
        .draggable({
          modifiers: [
            interact.modifiers.snap({
              targets: [interact.createSnapGrid({ x: 1, y: 1 })], // Snap to a 1px grid
              range: Infinity,
            }),
            interact.modifiers.restrict({
              restriction: "parent", // Keep inside parent task row
              endOnly: true,
            }),
          ],
          listeners: {
            move(event) {
              if (!taskElement.classList.contains("drag-locked")) {
                const target = event.target;
                const dataX = parseFloat(target.getAttribute("data-x")) || 0;
                const moveX = dataX + event.dx;

                target.style.left = `${moveX}px`;
                target.setAttribute("data-x", moveX);
              }
            },
            end(event) {
              const target = event.target;
              const rect = target.getBoundingClientRect();
              const parentRect = target.parentElement.getBoundingClientRect();
              const fixedLeft = rect.left - parentRect.left;

              target.style.left = `${fixedLeft}px`;
              target.setAttribute("data-x", fixedLeft);
              target.style.transform = "none";
              target.classList.add("drag-locked");
            },
          },
        });
    }

    // Function to make the task bar resizable
    function makeTaskBarResizable(taskElement) {
      interact(taskElement)
        .resizable({
          edges: { left: true, right: true },
          modifiers: [
            interact.modifiers.snap({
              targets: [interact.createSnapGrid({ x: 1, y: 1 })],
              range: Infinity,
            }),
          ],
          listeners: {
            move(event) {
              const target = event.target;
              const width = parseFloat(target.style.width) || 0;
              target.style.width = `${width + event.deltaRect.width}px`;
            },
          },
        });
    }

    // Initialize all task bars as draggable and resizable after page load
    document.addEventListener("DOMContentLoaded", function() {
      makeAllTasksDraggable();
    });

    // Unlock task bar on double-click
    document.addEventListener("dblclick", (event) => {
      if (event.target.classList.contains("timeline-task")) {
        event.target.classList.remove("drag-locked");
      }
    });
  </script>
</body>
</html>
