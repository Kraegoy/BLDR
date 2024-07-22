<?php
// Connect to the database
session_start();
include("connection.php");
include("functions.php");

// Check connection
if (mysqli_connect_errno()) {
    echo "Failed to connect to MySQL: " . mysqli_connect_error();
    exit();
}

// Check if name and item_id are set
if (isset($_GET['name']) && isset($_GET['item_id'])) {
    $name = $_GET['name'];
    $item_id = $_GET['item_id'];

    // Check if the equipment exists
    $query = "SELECT * FROM equipment WHERE id='$item_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $item_type = 'equipment';

        // Move the equipment to the recycle bin table
        $insertQuery = "INSERT INTO recycle_bin_equipment (item_id, item_type, name, description, category, quantity, image) 
                        SELECT id, ?, name, description, category, quantity, image FROM equipment WHERE id=?";
        $stmt = mysqli_prepare($con, $insertQuery);
        mysqli_stmt_bind_param($stmt, "si", $item_type, $item_id);
        $insertResult = mysqli_stmt_execute($stmt);

        if ($insertResult) {
            // Delete the equipment from the equipment table
            $deleteEquipmentQuery = "DELETE FROM equipment WHERE id='$item_id'";
            $deleteEquipmentResult = mysqli_query($con, $deleteEquipmentQuery);

            if ($deleteEquipmentResult) {
                // Insert audit trail record for deleted equipment action
                $login_user_id = $_SESSION['user_id'];
                $action = "Deleted Equipment";
                $timestamp = date('Y-m-d H:i:s');
                $details = "(Equipment Name: $name)";
                $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
                mysqli_query($con, $audit_query);

                header("Location: display_equip.php");
                exit();
            } else {
                echo "Error deleting equipment: " . mysqli_error($con);
            }
        } else {
            echo "Error moving equipment to the recycle bin: " . mysqli_error($con);
        }
    } else {
        echo "Equipment not found.";
    }
} else {
    echo "Equipment name or ID not provided.";
}

// Close the database connection
mysqli_close($con);
?>
