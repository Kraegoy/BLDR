<?php
session_start();

include("connection.php");
include("functions.php");
require_once 'vendor/autoload.php';

// Check if project ID is set and fetch project details from the database
$project_id = mysqli_real_escape_string($con, $_GET['id']);

$query = "SELECT * FROM project WHERE project_id = $project_id";
$result = mysqli_query($con, $query);

include('layouts/header.php');
?>

<div class="main-content">
  <div class="container">
    <div class="project-list">
      <h1>Project Information</h1>

      <?php
      // Check if the project details are available
      if ($result && mysqli_num_rows($result) > 0) {
          $row = mysqli_fetch_assoc($result);
          $project_name = $row['project_name'];
          $project_description = $row['project_description'];
          $client_name = $row['client_name'];
          $start_date = $row['start_date'];
          $end_date = $row['end_date'];
          $project_manager = $row['project_manager'];
          $project_team = $row['project_team'];
          $budget = $row['budget'];
          $actual_cost = $row['actual_cost'];
          $status = $row['status'];
          $location = $row['location'];

          // Display the project details
          ?>
          <div class="project">
            <h3><strong>Project Name:</strong></h3>
            <form action="generate_pdf.php" method="POST" target="_blank">
              <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
              <div style="display: inline-block;">
                <h2 style="display: inline; margin-right: 110px;"><?php echo $project_name ?></h2>
                <button type="submit" class="edit-button">PDF</button>
                <br>
              </div>
              <br>

            </form>
            <form action="edit_project.php" method="GET">
              <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
              <button type="submit" class="edit-button">Edit Project Information</button>
            </form>

            <strong>Description:</strong>
            <p><?php echo $project_description ?></p>
            <div class="project-details">
              <div class="detail">
                <strong>Client Name:</strong>
                <span><?php echo $client_name ?></span>
              </div>
              <div class="detail">
                <strong>Start Date:</strong>
                <span><?php echo $start_date ?></span>
              </div>
              <div class="detail">
                <strong>End Date:</strong>
                <span><?php echo $end_date ?></span>
              </div>
              <div class="detail">
                <strong>Project Manager:</strong>
                <span><?php echo $project_manager ?></span>
              </div>
              <div class="detail">
                <strong>Project Team:</strong>
                <span><?php echo $project_team ?></span>
              </div>
              <div class="detail">
                <strong>Budget:</strong>
                <span><?php echo $budget ?></span>
              </div>
              <div class="detail">
                <strong>Actual Cost:</strong>
                <span><?php echo $actual_cost ?></span>
              </div>
              <div class="detail">
                <strong>Status:</strong>
                <?php if ($status == 'canceled'): ?>
                  <span style="background-color: #FFCDD2;"><?php echo $status ?></span>
                <?php elseif ($status == 'completed'): ?>
                  <span style="background-color: green;"><?php echo $status ?></span>
                <?php else: ?>
                  <span><?php echo $status ?></span>
                <?php endif; ?>
              </div>
              <div class="detail">
                <strong>Location:</strong>
                <span><?php echo $location ?></span>
              </div>
              <div class="detail">
                <!-- Display additional project details here -->
              </div>
              <br>
            </div>
          </div>
        <?php
      } else {
          echo "Error retrieving project details.";
      }
      ?>
    </div>
  </div>
</div>

</body>

</html>
