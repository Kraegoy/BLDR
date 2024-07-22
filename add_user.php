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

if (check_permission($user_level, PERMISSION_ADD_USER)) {
    $permission_name = PERMISSION_ADD_USER;
} else {
    ?>
    <script>
        alert("You do not have permission to access this page.");
        window.location.href = "bldr.php";
    </script>
    <?php
}


if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT user_level FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$user_level = $row['user_level'];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $user_level = $_POST['user_level'];

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        // Save to database
        $user_id = random_num(20);
        $query = "INSERT INTO users (user_id, user_name, password, user_level) 
                  VALUES ('$user_id', '$user_name', '$password', '$user_level')";
        mysqli_query($con, $query);

        // Insert audit trail record for added user action
        $login_user_id = $_SESSION['user_id'];
        $action = "Added User";
        $timestamp = date('Y-m-d H:i:s');
        $details = "(User Name: $user_name)";
        $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
        mysqli_query($con, $audit_query);

        echo '<script>alert("User Added.");</script>';
    } else {
        echo '<script>alert("Enter invalid input.");</script>';
    }
}
include('layouts/header.php');

?>


    <style type="text/css">
	
	#box {
  display: flex;
  justify-content: center;
  align-items: center;
  height: fit;
  background-color: #f2f2f2;
}

form {
  display: flex;
  flex-direction: column;
  align-items: center;
  justify-content: center;
  background-color: white;
  padding: 50px;
  border-radius: 5px;
  box-shadow: 0px 0px 10px rgba(0, 0, 0, 0.1);
}

form div {
  font-size: 20px;
  margin-bottom: 20px;
  color: #333;
}

form input[type="text"],
form input[type="password"] {
  width: 100%;
  padding: 12px 20px;
  margin: 8px 0;
  border: 1px solid #ccc;
  border-radius: 4px;
  box-sizing: border-box;
}

form input[type="submit"] {
  background-color: black;
  color: white;
  padding: 14px 20px;
  border: none;
  border-radius: 4px;
  cursor: pointer;
}

form input[type="submit"]:hover {
  background-color: #c24801;
}


	</style>

    <div class="main-content">
   
    <div id="box">
		
		<form method="post">
			<div style="font-size: 24px;margin: 20px;color: black;"><b>Add User</b></div>

      <label for="user_name">Username: </label>
			<input id="text" type="text" name="user_name"><br>

      <label for="password">Password: </label>
			<input id="text" type="password" name="password"><br>

      <label for="user_level">User Level: </label>
      <select id="user_level" name="user_level">
        <option value="1">Level 1</option>
        <option value="2">Level 2</option>
        <option value="3">Level 3</option>
      </select> <br>

			<input id="button" type="submit" value="Add User"><br><br>

      


		</form>
	</div>

    </div>

  </body>
</html>
