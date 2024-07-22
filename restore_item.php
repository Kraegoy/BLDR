<?php
// Connect to the database
session_start();
include("connection.php");
include("functions.php");

// Check if the user is logged in and has permission to restore items
$user_data = check_login($con);
if (!$user_data) {
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

// Check if item_id and item_type are provided
if (isset($_GET['item_id']) && isset($_GET['item_type'])) {
    $item_id = $_GET['item_id'];
    $item_type = $_GET['item_type'];

    // Check if the item exists in the recycle bin
    $table_name = "recycle_bin_" . $item_type; // Build the table name
    $query = "SELECT * FROM $table_name WHERE item_id = '$item_id'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        // Item found in the recycle bin, restore it

        // Restore the item depending on its type
        if ($item_type === 'products') {
            // Get the product details from the recycle bin
            $recycle_query = "SELECT * FROM recycle_bin_products WHERE item_id = '$item_id'";
            $recycle_result = mysqli_query($con, $recycle_query);
            $recycle_row = mysqli_fetch_assoc($recycle_result);

            // Determine the status based on quantity and reorder point
            $status = ($recycle_row['quantity'] >= $recycle_row['reorder_point']) ? 'sufficient' : 'reorder';
            echo "Quantity: " . $recycle_row['quantity'] . "<br>";
            echo "Reorder Point: " . $recycle_row['reorder_point'] . "<br>";
            echo "Status: " . $status . "<br>";

            $query = "UPDATE products
                      SET name = '{$recycle_row['name']}',
                          description = '{$recycle_row['description']}',
                          quantity = {$recycle_row['quantity']},
                          image = '{$recycle_row['image']}',
                          price = {$recycle_row['price']},
                          reorder_point = {$recycle_row['reorder_point']},
                          status = '$status'
                      WHERE id = {$recycle_row['id']}";

            $result = mysqli_query($con, $query);
            
            // Add restore action to audit trail
            $audit_query = "INSERT INTO audit_trail (user_id, action, details) VALUES ('{$user_data['id']}', 'Item Restored', 'Product: {$recycle_row['name']}')";
            mysqli_query($con, $audit_query);

        } elseif ($item_type === 'equipment') {
            // Restore the equipment by adding it back to the equipment table
            $query = "INSERT INTO equipment (id, name, description, category, quantity, available, image)
                      SELECT id, name, description, category, quantity, available, image
                        FROM $table_name WHERE item_id = '$item_id'";
            $result = mysqli_query($con, $query);
            
            // Insert audit trail record for edited equipment action
        $login_user_id = $_SESSION['user_id'];
        $action = "Restored equipment";
        $timestamp = date('Y-m-d H:i:s');
        $details = "(Equipment ID: $item_id)";
        $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
        mysqli_query($con, $audit_query);;
        } elseif ($item_type === 'users') {
            // Restore the user by adding it back to the users table
            $query = "UPDATE users
                      SET status = 'active'
                      WHERE id = (SELECT item_id FROM $table_name WHERE item_id = '$item_id')";
            $result = mysqli_query($con, $query);
            
           // Insert audit trail record for edited equipment action
        $login_user_id = $_SESSION['user_id'];
        $action = "Restored user";
        $timestamp = date('Y-m-d H:i:s');
        $details = "(User ID: $item_id)";
        $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
        mysqli_query($con, $audit_query);;
        }

        if ($result) {
            // Item restored successfully
            // Remove the item from the recycle bin
            $query = "DELETE FROM $table_name WHERE item_id = '$item_id'";
            $result = mysqli_query($con, $query);

            if ($result) {
                // Item removed from the recycle bin
                // Redirect to the recycle bin page
                header("Location: recycle_bin.php");
                exit(); // Add this line to terminate the script after the redirect
            } else {
                // Error removing the item from the recycle bin
                echo "Error restoring item: " . mysqli_error($con);
            }
        } else {
            // Error restoring the item
            echo "Error restoring item: " . mysqli_error($con);
        }
    } else {
        // Item not found in the recycle bin
        echo "Item not found in the recycle bin.";
    }
} else {
    // Item ID and item type not provided
    echo "Item ID and item type not provided.";
}
?>
