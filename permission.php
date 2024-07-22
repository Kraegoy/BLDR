<?php
include("connection.php");
// Check if the user is logged in and retrieve their user level
$user_data = check_login($con);

if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

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
define('PERMISSION_ADD_MATERIAL_PROJ', 'Add Materials to Project');
define('PERMISSION_ADD_EQUIPMENT_PROJ', 'Add Equipment to Project');



// Define a function to check if a user has permission to perform a certain action
function check_permission($user_level, $permission_key) {
    global $con;
    
    // Query the database to check the permission
    $query = "SELECT has_permission FROM permissions WHERE user_level = $user_level AND permission_key = '$permission_key'";
    $result = mysqli_query($con, $query);

    if ($result && mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        return ($row['has_permission'] == 1);
    }
    
    return false; // Return false if permission is not found or an error occurs
}
?>
