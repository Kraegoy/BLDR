<?php
session_start();

include("connection.php");
include("functions.php");

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $user_name = $_POST['user_name'];
    $password = $_POST['password'];

    if (!empty($user_name) && !empty($password) && !is_numeric($user_name)) {
        // Read from database
        $query = "SELECT * FROM users WHERE user_name = '$user_name' AND status = 'active' LIMIT 1";
        $result = mysqli_query($con, $query);

        if ($result) {
            if ($result && mysqli_num_rows($result) > 0) {
                $user_data = mysqli_fetch_assoc($result);

                if ($user_data['password'] === $password) {
                    $_SESSION['user_id'] = $user_data['user_id'];

                        // Insert audit trail record for edited equipment action
      $login_user_id = $_SESSION['user_id'];
      $action = "Logged in";
      $timestamp = date('Y-m-d H:i:s');
      $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp) VALUES ('$login_user_id', '$action', '$timestamp')";
      mysqli_query($con, $audit_query);

                    echo '<script>alert("Login Successful!");</script>';
                    header("Location: bldr.php");
                    die;
                }
            }
        }

        echo '<script>alert("Wrong username or password.");</script>';
        header("Location: login.php");
        die;
    } else {
        echo '<script>alert("Wrong username or password.");</script>';
        header("Location: login.php");
        die;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <title>BLDR</title>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="login.css">
</head>
<body style="background-color: black;">
<div class="center">
    <h1>Login</h1>
    <form method="post">
        <div class="txt_field">
            <input type="text" name="user_name" id="text" required>
            <label for="username">Username</label>
            <span></span>
        </div>
        <div class="txt_field">
            <input type="password" name="password" id="text" required>
            <label for="password">Password</label>
            <span></span>
        </div>
        <div class="pass">Forgot Password?</div>
        <input type="submit" value="Login" style="
            width: 80%;
            height: 50px;
            border: 1px solid;
            background-color: rgba(255, 255, 255, 0.1);
            border-radius: 25px;
            font-size: 18px;
            font-weight: 700;
            cursor: pointer;
            outline: none;
            margin-bottom: 15px;
        ">
    </form>
</div>
<footer>
    <img src="logo.png" alt="Logo" class="logo">
</footer>
</body>
</html>
