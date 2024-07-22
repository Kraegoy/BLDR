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

if (check_permission($user_level, PERMISSION_EDIT_USER)) {
  $permission_name = PERMISSION_EDIT_USER;
} else {
  ?>
  <script>
      alert("You do not have permission to edit user.");
      window.location.href = "display_users.php";
  </script>
  <?php
}

if(isset($_GET['user_id'])){
    $user_id = $_GET['user_id'];
}else{
    $user_id = $user_data['user_id'];
}

if (isset($_POST['update_user'])) {
  $user_id = $_POST['user_id'];
  $user_name = $_POST['user_name'];
  $password = $_POST['password'];
  $user_level = $_POST['user_level'];

  // Update user data in database
  $query = "UPDATE users SET user_name='$user_name', password='$password', user_level='$user_level' WHERE id='$user_id'";
  $result = mysqli_query($con, $query);

  if ($result) {
    $_SESSION['flash_message'] = "User updated successfully!";
  } else {
    $_SESSION['flash_message'] = "Error updating user: " . mysqli_error($con);
  }

  header("Location: edit_user.php?user_id=$user_id");
  exit();
}

// Fetch user data from database
$query = "SELECT * FROM users WHERE id='$user_id'";
$result = mysqli_query($con, $query);

if (mysqli_num_rows($result) > 0) {
  $user = mysqli_fetch_assoc($result);
}
include('layouts/header.php'); 

?>

    <link rel="stylesheet" href="edit_user.css">

    <div class="main-content">
  <div class="form-container">
  <?php if(isset($_SESSION['flash_message'])): ?>
  <div class="flash-message">
    <?php echo $_SESSION['flash_message']; unset($_SESSION['flash_message']); ?>
  </div>
  <?php header("Refresh:0.5; url=display_users.php"); ?>
<?php endif; ?>
    <h2>Edit User</h2>
    <?php
    if (isset($_POST['update_user'])) {
      $user_id = $_POST['user_id'];
      $user_name = $_POST['user_name'];
      $password = $_POST['password'];
      $user_level = $_POST['user_level'];

      // Update user data in database
      $query = "UPDATE users SET user_name='$user_name', password='$password', user_level='$user_level' WHERE id='$user_id'";
      $result = mysqli_query($con, $query);



      if ($result) {
        echo "<p class='success'>User updated successfully!</p>";
      } else {
        echo "<p class='error'>Error updating user: " . mysqli_error($con) . "</p>";
      }
    } else {
      $user_id = isset($_GET['user_id']) ? $_GET['user_id'] : $user_data['user_id'];

      // Fetch user data from database
      $query = "SELECT * FROM users WHERE id='$user_id'";
      $result = mysqli_query($con, $query);

      if (mysqli_num_rows($result) > 0) {
        $user = mysqli_fetch_assoc($result);
       // Insert audit trail record for edited equipment action
      $login_user_id = $_SESSION['user_id'];
      $action = "Edited User";
      $timestamp = date('Y-m-d H:i:s');
      $details = "User Name: " . $user['user_name'];
      $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
      mysqli_query($con, $audit_query);


    ?>
        <form method="post" action="">
          <div class="form-group">
            <label for="username">Username:</label>
            <input type="text" name="user_name" value="<?php echo $user['user_name']; ?>">
          </div>

          <div class="form-group">
            <label for="password">Password:</label>
            <input type="password" name="password" value="<?php echo $user['password']; ?>">
          </div>

          <div class="form-group">
  <label for="user_level">User Level:</label>
  <select name="user_level">
    <option value="1" <?php if ($user['user_level'] == 1) echo "selected"; ?>>Level 1</option>
    <option value="2" <?php if ($user['user_level'] == 2) echo "selected"; ?>>Level 2</option>
    <option value="3" <?php if ($user['user_level'] == 3) echo "selected"; ?>>Level 3</option>
  </select>
</div>


          <input type="hidden" name="user_id" value="<?php echo $user_id; ?>">

          <button type="submit" name="update_user">Update User</button>
        </form>
    <?php
      } else {
        echo "<p class='error'>User not found.</p>";
      }
    }
    ?>
  </div>
</div>

  </body>
</html>
