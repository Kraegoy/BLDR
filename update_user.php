<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);
if($user_level != 1){
    ?>
    <script>
        alert("You do not have permission to access this page.");
        window.location.href = "bldr.php";
    </script>
    <?php
        exit();
  }

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $user_id = $_POST['user_id'];
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];
    $user_level = $_POST['user_level'];

    $sql = "UPDATE users SET user_name='$user_name', password='$password', user_level='$user_level' WHERE id='$user_id'";
    $result = mysqli_query($con, $sql);

    if ($result) {
        header("Location: display_users.php");
        exit();
    } else {
        echo "Error updating user information";
    }
} else {
    header("Location: edit_user.php");
    exit();
}
?>
 