<?php
// Connect to the database
session_start();
include("connection.php");
include("functions.php");

// Check the database connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Check if user_id is set
if (isset($_GET['user_id'])) {
    $user_id = $_GET['user_id'];

    // Check if the user exists
    $query = "SELECT * FROM users WHERE id='$user_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $item_type = 'users';

        // Move the user to the recycle bin table
        $insertQuery = "INSERT INTO recycle_bin_users (item_id, user_id, item_type, user_name, date, user_level, password, status) 
                        SELECT id, user_id, '$item_type', user_name, date, user_level, password, 'inactive' FROM users WHERE id='$user_id'";
        $insertResult = mysqli_query($con, $insertQuery);

        if ($insertResult) {
            // Update the user status to default value ('inactive')
            $updateUserQuery = "UPDATE users SET status='inactive' WHERE id='$user_id'";
            $updateUserResult = mysqli_query($con, $updateUserQuery);

            if ($updateUserResult) {
                // Insert audit trail record for deleted user action
                $login_user_id = $_SESSION['user_id'];
                $action = "Deleted User";
                $timestamp = date('Y-m-d H:i:s');
                $details = "(User ID: $user_id)";
                $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
                mysqli_query($con, $audit_query);

                header("Location: display_users.php");
                exit();
            } else {
                echo "Error updating user status: " . mysqli_error($con);
            }
        } else {
            echo "Error moving user to the recycle bin: " . mysqli_error($con);
        }
    } else {
        echo "User not found.";
    }
} else {
    echo "User ID not provided.";
}

// Close the database connection
mysqli_close($con);
?>
