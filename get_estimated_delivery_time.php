<?php
include("connection.php");

// Check if the supplier ID is provided
if (isset($_POST['supplierId'])) {
    $supplierId = $_POST['supplierId'];

    // Retrieve the estimated delivery time from the database
    $query = "SELECT EstimatedDeliveryTime FROM suppliers WHERE id = $supplierId";
    $result = mysqli_query($con, $query);

    if ($result) {
        // Fetch the estimated delivery time
        $row = mysqli_fetch_assoc($result);
        $estimatedDeliveryTime = $row['EstimatedDeliveryTime'];

        // Prepare the response message
        $message = " $estimatedDeliveryTime hours";

        // Return the estimated delivery time message as the response
        echo $message;
    } else {
        // Failed to fetch the estimated delivery time
        echo "Error: Failed to retrieve estimated delivery time.";
    }
} else {
    // No supplier ID provided
    echo "Error: Supplier ID not specified.";
}
?>
