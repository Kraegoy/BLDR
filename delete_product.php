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

// Check if name is set
if (isset($_GET['name'])) {
    $name = $_GET['name'];

    // Check if the product exists
    $query = "SELECT * FROM products WHERE name='$name'";
    $result = mysqli_query($con, $query);

    if (mysqli_num_rows($result) > 0) {
        $row = mysqli_fetch_assoc($result);
        $product_id = $row['id'];

        // Update product status to 'inactive'
        $updateStatusQuery = "UPDATE products SET status='inactive' WHERE id='$product_id'";
        $updateStatusResult = mysqli_query($con, $updateStatusQuery);

        if ($updateStatusResult) {
            // Move the product to the recycle bin table
            $item_type = 'products';
            $insertQuery = "INSERT INTO recycle_bin_products (item_id, item_type, name, description, price, quantity, image, created_at, updated_at, reorder_point) 
                            SELECT id, '$item_type', name, description, price, quantity, image, created_at, updated_at, reorder_point FROM products WHERE id='$product_id'";
            $insertResult = mysqli_query($con, $insertQuery);

            if ($insertResult) {
                // Insert audit trail record for deleted product action
                $login_user_id = $_SESSION['user_id'];
                $action = "Deleted Product";
                $timestamp = date('Y-m-d H:i:s');
                $details = "(Product Name: $name)";
                $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
                mysqli_query($con, $audit_query);

                header("Location: display_inventory.php");
                exit();
            } else {
                echo "Error moving product to the recycle bin: " . mysqli_error($con);
            }
        } else {
            echo "Error updating product status: " . mysqli_error($con);
        }
    } else {
        echo "Product not found.";
    }
}

// Close database connection
mysqli_close($con);
?>
