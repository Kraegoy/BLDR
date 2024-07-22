<?php
session_start();

include("connection.php");
include("functions.php");
require_once 'vendor/autoload.php';

// Check if project ID is set and fetch project details from the database
$project_id = mysqli_real_escape_string($con, $_GET['project_id']);

if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
    $query = "SELECT * FROM project WHERE project_id = $project_id AND status = 'planning'";
    $result = mysqli_query($con, $query);
    if (!$result) {
        echo "Error: " . mysqli_error($con);
    } else {
        $row = mysqli_fetch_assoc($result);
        $project_id = $row['project_id'];
        $project_name = $row['project_name'];
        $project_description = $row['project_description'];
        $client_name = $row['client_name'];
        $start_date = $row['start_date'];
        $end_date = $row['end_date'];
        $project_manager = $row['project_manager'];
        $project_team = $row['project_team'];
        $budget = $row['budget'];

        // Update the actual_cost field as the sum of total_price from the material table
        $actual_cost_query = "SELECT SUM(total_price) AS total_cost FROM material WHERE project_id = $project_id";
        $actual_cost_result = mysqli_query($con, $actual_cost_query);
        $actual_cost_data = mysqli_fetch_assoc($actual_cost_result);
        $actual_cost = $actual_cost_data['total_cost'];

        $status = $row['status'];
        $location = $row['location'];
        $materials = $row['materials'];
        $equipment = $row['equipment'];
    }
}
include('layouts/header.php');
?>
   <div class="main-content">
    <div class="container">
    <div class="header">
    <div class="project-list">
      <h1>Project Information</h1> 


      <?php
// retrieve the project details based on the project_id passed in the URL parameter
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
    $query = "SELECT * FROM project WHERE project_id = $project_id";
    $result = mysqli_query($con, $query);
    if ($result) {
        $row = mysqli_fetch_assoc($result);
        $project_name = $row['project_name'];
        $project_description = $row['project_description'];
        // display the project details
        ?>
   <div class="project">
    <p>
        <h3><strong>Project Name:</strong></h3>
        <form action="generate_pdf.php" method="POST" target="_blank">
            <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
            <div style="display: inline-block;">
                <h2 style="display: inline; margin-right: 110px;"><?php echo $project_name ?></h2>
                <button type="submit" class="edit-button">PDF</button>
            </div>
        </form>
        <form action="edit_project.php" method="GET">
  <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
  <button type="submit" class="edit-button">Edit Project Information</button>
</form>
    </p>
</div>
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
                  <span><?php echo $status ?></span>
                </div>
                <div class="detail">
                  <strong>Location:</strong>
                  <span><?php echo $location ?></span>
                </div>
                <div class="detail">
                  <strong>Materials:</strong>
                  <form action="add_materials_to_project.php" method="POST">
<input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
<button type="submit" class="edit-button">Add Materials</button>
</form>                </div>

                <div class="detail">
                  <strong>Equipments:</strong>
                  <form action="add_equipments_to_project.php" method="POST">
<input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
<button type="submit" class="edit-button">Add Equipments</button>
</form>    
                </div><br>

                <form action="edit_project.php" method="GET">
  <input type="hidden" name="project_id" value="<?php echo $project_id; ?>">
  <button type="submit" class="edit-button">Edit Project Information</button>
</form>

                
              </div>
            </div>
        <?php
    } else {
        echo "Error retrieving project details.";
    }
} else {
    echo "No project selected.";
}
?>

    </div>
  </div>
    </div>

  </body>
</html>
