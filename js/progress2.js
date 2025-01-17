
    document.addEventListener("DOMContentLoaded", () => {
  // Handle Assignee Search Dropdown
  const assigneeSearch = document.getElementById("assignee-search");
  const assigneeList = document.querySelector(".assignee-list");

  assigneeSearch.addEventListener("focus", () => {
    assigneeList.style.display = "block";
  });

  assigneeSearch.addEventListener("input", () => {
    const filter = assigneeSearch.value.toLowerCase();
    const items = document.querySelectorAll(".assignee-item");

    items.forEach((item) => {
      if (item.textContent.toLowerCase().includes(filter)) {
        item.style.display = "block";
      } else {
        item.style.display = "none";
      }
    });
  });

  document.addEventListener("click", (e) => {
    if (!e.target.closest(".assignee-dropdown")) {
      assigneeList.style.display = "none";
    }
  });

  // Handle Assignee Selection
  assigneeList.addEventListener("click", (e) => {
    if (e.target.classList.contains("assignee-item")) {
      assigneeSearch.value = e.target.textContent;
      assigneeList.style.display = "none";
    }
  });

  // Handle Form Submission
  const submitBtn = document.getElementById("submit-btn");

  submitBtn.addEventListener("click", () => {
    const projectName = document.getElementById("project-name").value;
    const summary = document.getElementById("summary").value;
    const description = document.getElementById("description").value;
    const reporter = document.getElementById("reporter").value;
    const assignee = assigneeSearch.value;

    if (!summary || !description || !assignee) {
      alert("Please fill out all fields before creating the project.");
      return;
    }

    const projectData = {
      projectName,
      summary,
      description,
      reporter,
      assignee,
    };

    console.log("Project Created:", projectData);
    alert("Project successfully created!");

    // Clear the form after submission
    document.getElementById("summary").value = "";
    document.getElementById("description").value = "";
    assigneeSearch.value = "";
  });
});