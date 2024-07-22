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

if (check_permission($user_level, PERMISSION_ADD_EQUIP)) {
  $permission_name = PERMISSION_ADD_EQUIP;
} else {
  ?>
  <script>
      alert("You do not have permission to access this page.");
      window.location.href = "bldr.php";
  </script>
  <?php
}

if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT user_level FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$user_level = $row['user_level'];

if($_SERVER['REQUEST_METHOD'] == "POST") {
    //something was posted
    $name = $_POST['name'];
    $quantity = intval($_POST['quantity']);
    $category = $_POST['category'];
    $description = !empty($_POST['description']) ? $_POST['description'] : null;

    // handle file upload
    $image = '';
    if(isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $image_name = $_FILES['image']['name'];
        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = 'uploads/' . $filename;
        move_uploaded_file($tmp_name, $destination);
        $image = $destination;
    }

    if(!empty($name) && !empty($description) && !empty($category) && !empty($quantity)) {
        // Check if product with the same name already exists
        $query = "SELECT * FROM equipment WHERE name=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);
        
        if(mysqli_num_rows($result) > 0) {
            echo '<script>alert("The equipment is already in inventory. You can edit it there.");</script>';
        } else {
            // Insert new product into database
            $query = "INSERT INTO equipment (name,quantity,category,description,image) VALUES (?,?,?,?,?)";
            $stmt = mysqli_prepare($con, $query);
            mysqli_stmt_bind_param($stmt, "sssss", $name, $quantity, $category, $description, $image);
            mysqli_stmt_execute($stmt);

            // Get the ID of the inserted equipment
            $equipment_id = mysqli_insert_id($con);

            // Insert audit trail record
            $login_user_id = $user_id;
            $action = "Added equipment";
            $details = "(Name: $name ID: $equipment_id )";
            $timestamp = date('Y-m-d H:i:s');
            $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
            mysqli_query($con, $audit_query);

            echo '<script>alert("Equipment Added.");</script>';
            
            if ($category == 'heavy' || $category == 'light') {
                header('Location: display_equip.php');
            } else {
                header('Location: display_equip.php'); // replace "#" with the URL you want to redirect to
            }
        }
    } else {
        echo '<script>alert("Please fill all required fields.");</script>';
    }
}
include('layouts/header.php'); 
?>

    <style>
  .add-product-form {
    max-width: 500px;
    margin: 0 auto;
  }

  .form-container {
  border: 1px solid #ddd;
  padding: 20px;
  border-radius: 5px;
  margin: 20px auto;
  max-width: 500px;
}

  h2 {
    text-align: center;
    margin-bottom: 30px;
  }

  .form-group {
    margin-bottom: 20px;
  }

  label {
    display: block;
    margin-bottom: 5px;
    font-weight: bold;
  }

  input[type="text"],
  input[type="number"],
  textarea {
    width: 100%;
    padding: 10px;
    border: 1px solid #ccc;
    border-radius: 4px;
    box-sizing: border-box;
    font-size: 16px;
  }

  input[type="submit"] {
    background-color: black;
    color: white;
    padding: 12px 20px;
    border: none;
    border-radius: 4px;
    cursor: pointer;
    font-size: 16px;
  }

  input[type="submit"]:hover {
    background-color: #45a049;
  }
</style>
  

    <div class="main-content">
    <div class="form-container">
    <h2>Add Equipment</h2>
<form action="add_equip.php" method="post" enctype="multipart/form-data">
  <label for="name">Name:</label>
  <input type="text" name="name" required>

  <label for="description">Description:</label>
  <textarea name="description"></textarea>

  <label for="category">Category: </label>
      <select id="category" name="category">
        <option value="heavy">Heavy</option>
        <option value="light">Light</option>
        <option value="vehicle">Vehicle</option>
      </select> <br>

  <label for="quantity">Quantity:</label>
  <input type="number" name="quantity" required>

  <label for="image">Image:</label>
  <input type="file" name="image">

  <input type="submit" value="Add Equipment">
</form>

</div>

</div>

  </body>
</html>
