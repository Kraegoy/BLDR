<?php 
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);
include('layouts/header.php'); 

// Check if the user is logged in and retrieve their user level
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}

// Retrieve products that have reached the reorder point and have a status of 'reorder'
$query = "SELECT * FROM products WHERE quantity <= reorder_point AND status = 'reorder'";
$result = mysqli_query($con, $query);

// Check if there are products that require reorder
if (mysqli_num_rows($result) > 0) {
    echo "<div class='main-content'>";
    echo "<h2>Warning: Reorder Required!</h2>";
    echo "<p>The following products have reached or fallen below the reorder point:</p>";
    
    // Display the products that need to be reordered
    echo "<div class='notification-box'>";
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Current Quantity</th><th>Reorder Point</th></tr>";
    
    while ($row = mysqli_fetch_assoc($result)) {
        $productId = $row['id'];
        $productName = $row['name'];
        $currentQuantity = $row['quantity'];
        $reorder_point = $row['reorder_point'];

        
        // Check if the notification has been read
        $readClass = ""; // Default to unread
        if (isset($_SESSION['read_notifications']) && in_array($productId, $_SESSION['read_notifications'])) {
            $readClass = "read"; // Apply read class
        }
        
        echo "<tr class='notification-row $readClass' onclick=\"window.location='order_demand.php?id=$productId'\">";
        echo "<td>$productId</td>";
        echo "<td><b>$productName</b></td>";
        echo "<td><b><span style='color: red;'>$currentQuantity</span></b></td>";
        echo "<td>$reorder_point</td>";

        echo "</tr>";
        
        // Add the current notification ID to the read_notifications array
        $_SESSION['read_notifications'][] = $productId;
    }
    
    echo "</table>";
    echo "</div>"; // End of notification-box
    echo "</div>";
} else {
    echo "<div class='main-content'>";
    echo "<h2>No Reorder Required</h2>";
    echo "<p>All products are currently stocked above the reorder point or have a different status.</p>";
    echo "</div>";
}
mysqli_free_result($result);
?>
<link rel="stylesheet" href="demands.css">
