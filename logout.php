<?php

session_start();

if (isset($_SESSION['user_id'])) {
    // Insert logout action to audit trail
    include("connection.php");
    $logout_user_id = $_SESSION['user_id'];
    $logout_time = date('Y-m-d H:i:s');
    $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp) VALUES ('$logout_user_id', 'Logged out', '$logout_time')";
    mysqli_query($con, $audit_query);

    unset($_SESSION['user_id']);
}

header("Location: login.php");
die;
?>
