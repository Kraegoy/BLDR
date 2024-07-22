<?php 
session_start();

include("connection.php");
include("functions.php");

// Fetch project details from the database
$query = "SELECT * FROM project WHERE status = 'planning'";
$result = mysqli_query($con, $query);
try {
  if (!$result) {
    throw new Exception("Error: " . mysqli_error($con));
  }
} catch (Exception $e) {
  echo $e->getMessage();
}
include('layouts/header.php'); 

?>

   <div class="main-content">
    <h1>Planning</h1>
   <?php
    // loop through all projects
    while ($row = mysqli_fetch_assoc($result)) {
        // get project name, description, and ID
        $project_id = $row['project_id'];
        $project_name = $row['project_name'];
        $project_description = $row['project_description'];
        $image = $row['image'];

?>
        <a href="display_planning_projects.php?project_id=<?php echo $project_id ?>" style="text-decoration: none; color: black;">
            <article>
                <h2><?php echo $project_name ?></h2>
                <img src="<?php echo $image; ?>" alt="Image" width="400" height="300">
                <p><?php echo $project_description ?></p>
            </article>
        </a>
<?php
    }
?>

</div>

