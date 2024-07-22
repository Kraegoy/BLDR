<?php
session_start();

include("connection.php");
include("functions.php");
include("permission.php");


// ADD USER
if (isset($_POST['add_user'])) {
  $username = $_POST['user_name'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
  $user_level = $_POST['user_level'];
  $created_date = date('Y-m-d H:i:s');

  // Check if user_name already exists
  $query = "SELECT * FROM users WHERE user_name='$username' AND status='active'";
  $result = mysqli_query($con, $query);
  if (mysqli_num_rows($result) > 0) {
    $_SESSION['message'] = "Username already exists";
    $_SESSION['message_type'] = "error";
    header("Location: add_user.php");
    exit();
  }

  // Add user to database
  $query = "INSERT INTO users (user_name, password, user_level, created_date) VALUES ('$username', '$password', '$user_level', '$created_date')";
  mysqli_query($con, $query); 
  $_SESSION['message'] = "User added successfully";
  $_SESSION['message_type'] = "success";
  header("Location: add_user.php");
  exit();
}

// EDIT USER
if (isset($_POST['edit_user'])) {
  $user_id = $_POST['user_id'];
  $username = $_POST['user_name'];
  $password = password_hash($_POST['password'], PASSWORD_DEFAULT); // Hash the password for security
  $user_level = $_POST['user_level'];

  // Update user in database
  $query = "UPDATE users SET user_name='$username', password='$password', user_level='$user_level' WHERE id='$user_id'";
  mysqli_query($con, $query);
  $_SESSION['message'] = "User updated successfully";
  $_SESSION['message_type'] = "success";
  header("Location: display_users.php");
  exit();
}

// DELETE USER
if (isset($_GET['delete_id'])) {
  $user_id = $_GET['delete_id'];

  // Delete user from database
  $query = "DELETE FROM users WHERE id='$user_id'";
  mysqli_query($con, $query);
  $_SESSION['message'] = "User deleted successfully";
  $_SESSION['message_type'] = "success";
  header("Location: display_users.php");
  exit();
}
include('layouts/header.php'); 

?>

<link rel="stylesheet" href="display_user.css">


    <div class="main-content">
        <?php if(isset($_COOKIE['flash_message'])): ?>
      <div class="flash-message">
        <?php echo $_COOKIE['flash_message']; ?>
      </div>
      <?php setcookie('flash_message', '', time()-3600); ?>
    <?php endif; ?>

    <style>
    .hide-id {
        display: none;
    }
</style>

<table class="table table-striped">
    <thead>
        <tr>
            <th class="hide-id">ID</th>
            <th>User ID</th>
            <th>Username</th>
            <th>User Level</th>
            <th>Date Created</th>
            <th>Edit</th>
            <th>Delete</th>
        </tr>
    </thead>
    <tbody>
        <?php
        $query = "SELECT * FROM users WHERE status = 'active'";
        $result = mysqli_query($con, $query);

        while ($row = mysqli_fetch_assoc($result)) {
            $id = $row['id'];
            $user_id = $row['user_id'];
            $username = $row['user_name'];
            $user_level = $row['user_level'];
            $created_date = $row['date'];
        ?>
        <tr>
            <td class="hide-id"><?php echo $id; ?></td>
            <td><?php echo $user_id; ?></td>
            <td><?php echo $username; ?></td>
            <td><?php echo $user_level; ?></td>
            <td><?php echo $created_date; ?></td>
            <td>
                <a href="edit_user.php?user_id=<?php echo $id; ?>" class="btn btn-primary">
                    <i class="fas fa-edit" style="color: black;"></i>
                </a>
            </td>
            <td>

    <a href="#" onclick="deleteUser('<?php echo $id; ?>')" class="btn btn-danger" id="delete_<?php echo $id; ?>"><i class="fas fa-trash" style="color: black;"></i></a>
<?php } ?>
</td>


            </tr>        </tbody>
    </table>

	<a href="add_user.php" class="add-user-btn">Add User</a>
  <a href="edit_permissions.php" class="add-user-btn">Edit User Level permissions.</a>


    </div>

    <script>
function deleteUser(userId) {
  if (confirm("Are you sure you want to delete this user?")) {
    window.location.href = "delete_user.php?user_id=" + userId;
  }
}
</script>

  </body>
</html>
