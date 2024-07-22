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

if (check_permission($user_level, PERMISSION_EDIT_EQUIPMENTS)) {
    $permission_name = PERMISSION_EDIT_EQUIPMENTS;
} else {
    ?>
    <script>
        alert("You do not have permission to access this page.");
        window.location.href = "display_equip.php";
    </script>
    <?php
}

if (isset($_GET['name'])) {
    $name = $_GET['name'];
} else {
    $name = "";
}

if (isset($_POST['update_equipment'])) {
    $name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $category = $_POST['category'];

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
                $query = "UPDATE equipment SET name='$name', description='$description', quantity='$quantity', category='$category', image='$image_path' WHERE name='$name'";
                $result = mysqli_query($con, $query);

                if ($result) {
                    // Insert audit trail record for edited equipment action
                    $login_user_id = $_SESSION['user_id'];
                    $action = "Edited Equipment";
                    $timestamp = date('Y-m-d H:i:s');
                    $details = "Equipment Name: $name";
                    $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
                    mysqli_query($con, $audit_query);

                    // Redirect to success page
                    header("Location: edit_equipments.php?name=$name&success=1");
                    exit();
                } else {
                    $_SESSION['flash_message'] = "Error updating equipment: " . mysqli_error($con);
                }
            } else {
                $_SESSION['flash_message'] = "Error uploading image.";
            }
        } else {
            $_SESSION['flash_message'] = "Invalid image type or size. Please upload a JPEG, PNG, or GIF file that is less than 2MB.";
        }
    } else {
        // Update equipment data in database without changing the image
        $query = "UPDATE equipment SET name='$name', description='$description', quantity='$quantity', category='$category' WHERE name='$name'";
        $result = mysqli_query($con, $query);

        if ($result) {
            // Insert audit trail record for edited equipment action
            $login_user_id = $_SESSION['user_id'];
            $action = "Edited Equipment";
            $timestamp = date('Y-m-d H:i:s');
            $details = "(Equipment Name: $name)";
            $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
            mysqli_query($con, $audit_query);

            // Redirect to success page
            header("Location: display_equip.php?name=$name&success=1");
            exit();
        } else {
            $_SESSION['flash_message'] = "Error updating equipment: " . mysqli_error($con);
        }
    }

    header("Location: edit_equipments.php?name=$name");
    exit();
}

// Fetch equipment data from database
if (isset($name)) {
    $query = "SELECT * FROM equipment WHERE name='$name'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $equipment_data = mysqli_fetch_assoc($result);
    }
}

include('layouts/header.php');
?>

    <style>
.edit-product-header {
  font-size: 32px;
  margin-top: 10px;
}

.equipment-name {
  margin-top: 20px;
  font-size: 25px;
  background-color: orange;
  display: inline-block;

}

.edit-product-form {
  margin-top: 20px;
  width: 60%;
}

.form-group {
  margin-bottom: 10px;
}

label {
  display: block;
  font-size: 18px;
  font-weight: bold;
  margin-bottom: 10px;
}

.form-control {
  border: 1px solid #ccc;
  border-radius: 4px;
  font-size: 16px;
  padding: 5px;
  width: 100%;
}
.container {
  display: flex;
  justify-content: center;
  align-items: center;
  height: auto;
 
}


.form-control-file {
  font-size: 16px;
  margin-top: 5px;
  
}
.cuteform {
  border: 1px solid #ccc;
  padding: 20px;
  border-radius: 10px;
}

.edit-product-btn {
  margin-top: 20px;
  width: 50%;
  padding: 10px;
  border-radius: 5px;
  border: none;
  background-color: black;
  color: #fff;
  font-size: 16px;
  margin-top: 15px;
}

</style>

    <div class="main-content">
    <div class="container">

    <form class="cuteform" method="post" action="edit_equipments.php?name=<?php echo $name; ?>" enctype="multipart/form-data">
  <h1 class="edit-product-header">Edit Equipment</h1>
  <p class="equipment-name"><i>Equipment Name: </i> <b> <?php echo $equipment_data['name']; ?></b></p>
  <form class="edit-product-form" method="post" action="edit_equipments.php?name=<?php echo $name; ?>" enctype="multipart/form-data">
    <input type="hidden" name="name" value="<?php echo $name; ?>">

    <div class="form-group">
      <label for="description">Description:</label>
      <textarea class="form-control" name="description" id="description" required><?php echo $equipment_data['description']; ?></textarea>
    </div>
    <div class="form-group">
  <label for="category">Category:</label>
  <select class="form-control" id="category" name="category" required>
    <option value="" disabled selected><?php echo $equipment_data['category']; ?></option>
    <option value="heavy" <?php if($equipment_data['category'] == 'heavy') echo 'selected'; ?>>Heavy</option>
    <option value="light" <?php if($equipment_data['category'] == 'light') echo 'selected'; ?>>Light</option>
    <option value="vehicle" <?php if($equipment_data['category'] == 'vehicle') echo 'selected'; ?>>Vehicle</option>
  </select>
</div>


    <div class="form-group">
      <label for="quantity">Quantity:</label>
      <input class="form-control" type="number" name="quantity" id="quantity" min="0" value="<?php echo $equipment_data['quantity']; ?>" required>
    </div>

    <div class="form-group">
      <label for="image">Image:</label>
      <input type="file" name="image" id="image" accept="image/*">
      <input type="hidden" name="image_path" value="<?php echo $equipment_data['image']; ?>">
    </div>

    <button class="edit-product-btn" type="submit" name="update_equipment">Update Equipment</button>
  </form>
</div>
</div>

  </body>
</html>
