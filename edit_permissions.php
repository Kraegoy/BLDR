<?php
session_start();

include("connection.php");
include("functions.php");
$user_data = check_login($con);
if(!isset($_SESSION['user_id'])){
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT user_level FROM users WHERE user_id = '$user_id'";
$result = mysqli_query($con, $query);
$row = mysqli_fetch_assoc($result);
$user_level = $row['user_level'];

if($user_level != 1){
  ?>
  <script>
      alert("You do not have permission to access this page.");
      window.location.href = "bldr.php";
  </script>
  <?php
      exit();
}
// Check if the user is logged in and retrieve their user level
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}

// Define constants for each permission key
define('PERMISSION_VIEW_DASHBOARD', 'View Dashboard');
define('PERMISSION_ADD_EQUIP', 'Add Equipment');
define('PERMISSION_ADD_PRODUCTS', 'Add Product');
define('PERMISSION_ADD_PROJECT', 'Add Project');
define('PERMISSION_ADD_USER', 'Add User');
define('PERMISSION_DELETE_EQUIPMENT', 'Delete Equipment');
define('PERMISSION_DELETE_PRODUCT', 'Delete Product');
define('PERMISSION_DELETE_USER', 'Delete User');
define('PERMISSION_EDIT_EQUIPMENTS', 'Edit Equipment');
define('PERMISSION_EDIT_PRODUCT', 'Edit Product');
define('PERMISSION_EDIT_PROJECT', 'Edit Product');
define('PERMISSION_EDIT_USER', 'Edit User');

// Retrieve the permissions from the database
$query = "SELECT * FROM permissions";
$result = mysqli_query($con, $query);

// Create an empty permissions array
$allPermissions = [];

// Retrieve the permissions and store them in the array
while ($row = mysqli_fetch_assoc($result)) {
    $user_level = $row['user_level'];
    $permission_key = $row['permission_key'];
    $has_permission = ($row['has_permission'] == 1);

    // Initialize the array for the user level if it's not already set
    if (!isset($allPermissions[$user_level])) {
        $allPermissions[$user_level] = [];
    }

    // Store the permission in the array based on user level and permission key
    $allPermissions[$user_level][$permission_key] = $has_permission;
}

// Update permissions in the database
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Retrieve the submitted user level
    $user_level = $_POST['user_level'];

   // Retrieve the existing permissions for the selected user level from the database
$query = "SELECT permission_key FROM permissions WHERE user_level = '$user_level'";
$result = mysqli_query($con, $query);

// Create an empty array to store the selected permissions
$selectedPermissions = [];

// Store the selected permissions in the array
while ($row = mysqli_fetch_assoc($result)) {
    $permission_key = $row['permission_key'];
    $selectedPermissions[$permission_key] = isset($_POST['permissions'][$user_level][$permission_key]);
}


    // Check if any permissions are selected
    if (count($selectedPermissions) == 0) {
        ?>
        <script>
            alert("Please select at least one permission.");
            header("Location: edit_permissions.php");
        </script>
        <?php
        exit();
    }

   // Construct the update query
$updateQuery = "UPDATE permissions SET has_permission = CASE permission_key";

// Generate the SQL conditions and values for each permission
foreach ($selectedPermissions as $permission_key => $has_permission) {
    $has_permission = $has_permission ? 1 : 0;
    $updateQuery .= " WHEN '" . mysqli_real_escape_string($con, $permission_key) . "' THEN '" . mysqli_real_escape_string($con, $has_permission) . "'";
}

// Set default value for unchecked permissions
$updateQuery .= " ELSE '0' END WHERE user_level = '$user_level'";


    // Execute the update query
    mysqli_query($con, $updateQuery);

    // Redirect to a success page or perform any other actions
    header("Location: display_users.php");
    exit();
}
include('layouts/header.php');

mysqli_free_result($result);
?>

<link rel="stylesheet" href="edit_permissions.css">

<div class="main-content">
    <div class="form-container">
        <h1 class="eyy">Edit Permissions</h1>
        <form method="POST" action="edit_permissions.php">
            <label for="user_level">Select User Level:</label>
            <select name="user_level" id="user_level">
                <option value="">Select User Level</option>
                <option value="1">User Level 1</option>
                <option value="2">User Level 2</option>
                <option value="3">User Level 3</option>
            </select>
            <br>
            <div class="user-level-permissions" id="user-level-permissions">
                <!-- Permissions checkboxes will be dynamically added here -->
            </div>
            <br>
            <button type="submit">Save Permissions</button>
        </form>
    </div>
</div>

<script>
   window.onload = function() {
    var userLevelSelect = document.getElementById('user_level');
    var permissionsContainer = document.getElementById('user-level-permissions');
    var allPermissions = <?php echo json_encode($allPermissions); ?>;

    userLevelSelect.addEventListener('change', function() {
        var selectedLevel = parseInt(this.value);

        if (selectedLevel && allPermissions[selectedLevel]) {
            var levelPermissions = allPermissions[selectedLevel];
            permissionsContainer.innerHTML = ''; // Clear previous checkboxes

            Object.keys(levelPermissions).forEach(function(permission) {
                var permissionLabel = document.createElement('label');
                permissionLabel.textContent = permission;

                var permissionCheckbox = document.createElement('input');
                permissionCheckbox.type = 'checkbox';
                permissionCheckbox.name = 'permissions[' + selectedLevel + '][' + permission + ']';
                permissionCheckbox.value = '1';

                if (levelPermissions[permission]) {
                    permissionCheckbox.checked = true;
                }

                permissionLabel.prepend(permissionCheckbox);
                permissionsContainer.appendChild(permissionLabel);

                permissionsContainer.appendChild(document.createElement('br')); // Add a line break after each checkbox
            });
        } else if (!selectedLevel) {
            permissionsContainer.innerHTML = ''; // Clear checkboxes if no level selected
        }
    });

    userLevelSelect.dispatchEvent(new Event('change')); // Trigger change event on page load
};

</script>

</body>
</html>
