<?php
session_start();

include("connection.php");
include("functions.php");
include('permission.php');

// Check if the user is logged in and retrieve their user level
$user_data = check_login($con);
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

if (check_permission($user_level, PERMISSION_ADD_PROJECT)) {
    $permission_name = PERMISSION_ADD_PROJECT;
} else {
    ?>
    <script>
        alert("You do not have permission to access this page.");
        window.location.href = "bldr.php";
    </script>
    <?php
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get form data
    $project_name = $_POST['project_name'];
    $project_desc = $_POST['project_description'];
    $client_name = $_POST['client_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $project_manager = $_POST['project_manager'];
    $project_team = $_POST['project_team'];
    $budget = $_POST['budget'];
    $status = $_POST['status'];
    $location = isset($_POST['location']) ? $_POST['location'] : '';
    $actual_cost = isset($_POST['actual_cost']) ? $_POST['actual_cost'] : '';

    // Check for empty required fields
    $errors = array();
    if (empty($project_name)) {
        $errors[] = 'Please enter a project name';
    }
    if (empty($project_desc)) {
        $errors[] = 'Please enter a project description';
    }
    if (empty($client_name)) {
        $errors[] = 'Please enter a client name';
    }
    if (empty($start_date)) {
        $errors[] = 'Please enter a start date';
    }
    if (empty($end_date)) {
        $errors[] = 'Please enter an end date';
    }
    if (empty($project_manager)) {
        $errors[] = 'Please enter a project manager';
    }
    if (empty($project_team)) {
        $errors[] = 'Please enter at least one team member';
    }
    if (empty($budget)) {
        $errors[] = 'Please enter a budget';
    }
    if (empty($status)) {
        $errors[] = 'Please enter a status';
    }
    if (empty($location)) {
        $errors[] = 'Please enter a location';
    }

    // Insert data into database if no errors
    if (empty($errors)) {
        // Prepare SQL statement
        $stmt = $con->prepare('INSERT INTO project(project_name, project_description, client_name, start_date, end_date, project_manager, project_team, budget, actual_cost, status, location) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)');
        // Bind parameters
        $stmt->bind_param('sssssssdsss', $project_name, $project_desc, $client_name, $start_date, $end_date, $project_manager, $project_team, $budget, $actual_cost, $status, $location);
        // Execute statement
        if ($stmt->execute()) {
            // Insert audit trail record for added project action
            $login_user_id = $_SESSION['user_id'];
            $action = "Added Project";
            $timestamp = date('Y-m-d H:i:s');
            $details = "(Project Name: $project_name)";
            $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
            mysqli_query($con, $audit_query);

            ?>
            <script>
                alert("Project Added.");
                window.location.href = "existing_projects.php";
            </script>
            <?php
            exit();
        } else {
            // Display error message
            $errors[] = 'Failed to add project to database';
        }
    }
}
include('layouts/header.php');
?>

    <link rel="stylesheet" href="add_project.css">

    <div class="main-content">
    <h1 class="awits">Add Project</h1>
<form method="POST" action="">
  <div class="form-row">
    <div class="form-group">
      <label for="project_name">Project Name</label>
      <input type="text" name="project_name" id="project_name" required>
    </div>

    <div class="form-group">
      <label for="project_manager">Project Manager</label>
      <input type="text" name="project_manager" id="project_manager" required>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="client_name">Client Name</label>
      <input type="text" name="client_name" id="client_name" required>
    </div>

    <div class="form-group">
      <label for="project_team">Project Team</label>
      <textarea name="project_team" id="project_team" rows="3" required></textarea>
    </div>
  </div>

  <label for="start_date">Start Date</label>
<div class="date-inputs">
  <input type="date" name="start_date" id="start_date" required>
  <input type="date" name="end_date" id="end_date" required>
</div>

  <div class="form-row">
    <div class="form-group">
      <label for="project_description">Project Description</label>
      <textarea name="project_description" id="project_description" rows="5" required></textarea>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="budget">Budget</label>
      <input type="number" name="budget" id="budget" min="0" step=".01" required>
    </div>

    <div class="form-group">
      <label for="actual_cost">Actual Cost</label>
      <input type="number" name="actual_cost" id="actual_cost" min="0" step=".01">
    </div>

    <div class="form-group">
      <label for="status">Status</label>
      <select name="status" id="status" required>
        <option value="planning">Planning</option>
        <option value="in_progress">In Progress</option>
        <option value="completed">Completed</option>

      </select>
    </div>
  </div>

  <div class="form-row">
    <div class="form-group">
      <label for="location">Location</label>
      <input type="text" name="location" id="location">
    </div>


  <button type="submit" name="submit">Add Project</button>
  <a href="projects.php">Cancel</a>
  </form>
  <?php if (!empty($errors)) { ?>
    <div class="add-project-errors">
      <p>The following errors were found:</p>
      <ul>
        <?php foreach ($errors as $error) { ?>
          <li><?php echo $error; ?></li>
        <?php } ?>
      </ul>
   

      </div>
    <?php } ?>
  </div>
    </div>

  </body>
</html>
