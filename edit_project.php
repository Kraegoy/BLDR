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

if (check_permission($user_level, PERMISSION_EDIT_PROJECT)) {
    $permission_name = PERMISSION_EDIT_PROJECT;
} else {
    ?>
    <script>
        alert("You do not have permission to edit projects.");
        window.location.href = "bldr.php";
    </script>
    <?php
}

// Check if form submitted with POST method
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $project_id = $_POST['project_id'];
    $project_name = $_POST['project_name'];
    $project_description = $_POST['project_description'];
    $client_name = $_POST['client_name'];
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];
    $project_manager = $_POST['project_manager'];
    $project_team = $_POST['project_team'];
    $budget = $_POST['budget'];
    $actual_cost = $_POST['actual_cost'];
    $status = $_POST['status'];
    $location = $_POST['location'];

    // Handle image upload
    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_type = $_FILES['image']['type'];

        // Validate file type and size
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $max_size = 2 * 1024 * 1024; // 2MB
        if (in_array($image_type, $allowed_types) && $image_size <= $max_size) {
            // Move file to server
            $upload_dir = 'uploads/';
            $image_path = $upload_dir . $image_name;
            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Update image path in database
                $sql = "UPDATE project SET 
                        project_name='$project_name', 
                        project_description='$project_description', 
                        client_name='$client_name', 
                        start_date='$start_date', 
                        end_date='$end_date', 
                        project_manager='$project_manager', 
                        project_team='$project_team', 
                        budget='$budget', 
                        actual_cost='$actual_cost', 
                        status='$status', 
                        location='$location', 
                        image='$image_path' 
                        WHERE project_id=$project_id";
                $result = $con->query($sql);

                if ($result) {
                    // Redirect to success page
                    header("Location: bldr.php");
                } else {
                    $_SESSION['flash_message'] = "Error updating project: " . mysqli_error($con);
                }
            } else {
                $_SESSION['flash_message'] = "Error uploading image.";
            }
        } else {
            $_SESSION['flash_message'] = "Invalid image type or size. Please upload a JPEG, PNG, or GIF file that is less than 2MB.";
        }
    } else {
 // Update project data without image
 $sql = "UPDATE project SET 
            project_name='$project_name', 
            project_description='$project_description', 
            client_name='$client_name', 
            start_date='$start_date', 
            end_date='$end_date', 
            project_manager='$project_manager', 
            project_team='$project_team', 
            budget='$budget', 
            actual_cost='$actual_cost', 
            status='$status', 
            location='$location'";

// Check if the status is "completed" or "cancelled"
if ($status == "completed" || $status == "cancelled") {
$sql .= ", end_date=NOW()";
}

$sql .= " WHERE project_id=$project_id";
$result = $con->query($sql);

 // Insert audit trail record for edited equipment action
 $login_user_id = $_SESSION['user_id'];
 $action = "Edited Project";
 $timestamp = date('Y-m-d H:i:s');
 $details = "(Project Name: $project_name)";
 $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
 mysqli_query($con, $audit_query);

// Check if the status is "completed" or "cancelled"
if ($status == "completed" || $status == "cancelled") {
// Fetch the updated end_date from the database
$sql = "SELECT end_date FROM project WHERE project_id=$project_id";
$fetch_result = $con->query($sql);
if ($fetch_result && $fetch_result->num_rows == 1) {
    $row = $fetch_result->fetch_assoc();
    $end_date = $row['end_date'];
} else {
    $_SESSION['flash_message'] = "Error fetching updated end_date from the database: " . mysqli_error($con);
}

// Calculate sales_amount
$sales_amount = $budget - $actual_cost;

// Set sales_date to the end_date of the project
$sales_date = $end_date;

// Insert the data into the sales table
$sql = "INSERT INTO sales (project_id, sales_amount, sales_date) 
        VALUES ('$project_id', '$sales_amount', '$sales_date')";
$insert_result = $con->query($sql);

if (!$insert_result) {
    $_SESSION['flash_message'] = "Error inserting data into the sales table: " . mysqli_error($con);
}
}

         // Update available quantity in the equipment table
    if ($status == "completed" || $status == "cancelled") {
      $sql = "SELECT pe.equipment_name, pe.quantity, e.available FROM project_equipment pe
              LEFT JOIN equipment e ON pe.equipment_name = e.name
              WHERE pe.project_id = $project_id";
      $result = $con->query($sql);

      if ($result->num_rows > 0) {
          while ($row = $result->fetch_assoc()) {
              $equipment_name = $row['equipment_name'];
              $quantity = $row['quantity'];
              $available = $row['available'];

              // Update the available quantity in the equipment table
              $new_available = $available + $quantity;
              $sql = "UPDATE equipment SET available = $new_available WHERE name = '$equipment_name'";
              $update_result = $con->query($sql);

              if (!$update_result) {
                  $_SESSION['flash_message'] = "Error updating available quantity in the equipment table: " . mysqli_error($con);
              }
          }
      }
  }
        if ($result) {
            // Redirect to success page
            header("Location: bldr.php");
            exit();
        } else {
            $_SESSION['flash_message'] = "Error updating project: " . mysqli_error($con);
        }
    }

    header("Location: bldr.php");
    exit();
}

// Fetch project from the database
if (isset($_GET['project_id'])) {
    $project_id = $_GET['project_id'];
    $sql = "SELECT * FROM project WHERE project_id=$project_id";
    $result = $con->query($sql);
    if ($result->num_rows == 1) {
        $row = $result->fetch_assoc();
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
    } else {
        echo "Project not found";
    }
}
include('layouts/header.php');
?>


<link rel="stylesheet" href="edit_project.css">

    <div class="main-content">
    <form method="post" action="edit_project.php" enctype="multipart/form-data">
    <input type="hidden" name="project_id" value="<?php echo $row['project_id']; ?>">

    <label>Project Name:</label><br>
    <input type="text" name="project_name" value="<?php echo $row['project_name']; ?>"><br>

    <label>Project Description:</label><br>
    <textarea name="project_description"><?php echo $row['project_description']; ?></textarea><br>

    <label>Client Name:</label><br>
    <input type="text" name="client_name" value="<?php echo $row['client_name']; ?>"><br>

    <label>Start Date:</label><br>
    <input type="date" name="start_date" value="<?php echo $row['start_date']; ?>"><br>
    
    <label>End Date:</label><br>
    <input type="date" name="end_date" value="<?php echo $row['end_date']; ?>"><br>

    <label>Project Manager:</label><br>
    <input type="text" name="project_manager" value="<?php echo $row['project_manager']; ?>"><br>

    <label>Project Team:</label><br>
    <textarea name="project_team"><?php echo $row['project_team']; ?></textarea><br>

    <label>Budget:</label><br>
    <input type="text" name="budget" value="<?php echo $row['budget']; ?>"><br>

    <label>Actual Cost:</label><br>
    <input type="text" name="actual_cost" value="<?php echo $row['actual_cost']; ?>"><br>
    <label>Status:</label><br>
<select name="status">
\  <option value="in_progress" <?php if($status == 'in_progress') echo 'selected'; ?>>In Progress</option>
  <option value="completed" <?php if($status == 'completed') echo 'selected'; ?>>Completed</option>
  <option value="cancelled" <?php if($status == 'cancelled') echo 'selected'; ?>>Cancelled</option>
  <option value="planning" <?php if($status == 'planning') echo 'selected'; ?>>Planning</option>

</select>
<br><br>

<label>Location:</label><br>
<input type="text" name="location" value="<?php echo $location; ?>">
<br><br>


<label for="image">Image:</label>
<input type="file" name="image" id="image" accept="image/*">
<input type="hidden" name="image_path" value="<?php echo $product['image']; ?>">




<input type="submit" name="submit" value="Update Project">


    </div>

  </body>
</html>
