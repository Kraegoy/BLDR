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

// Handle receiving of products
if (isset($_GET['receive']) && isset($_GET['product_id'])) {
    $productID = $_GET['product_id'];
    $toReceive = $_GET['receive'];

    // Update product quantity
    $updateQuantityQuery = "UPDATE products SET quantity = quantity + $toReceive WHERE id = $productID";
    mysqli_query($con, $updateQuantityQuery);

    // Check if the updated quantity is above the reorder point
    $checkReorderQuery = "SELECT quantity, reorder_point, status FROM products WHERE id = $productID";
    $checkReorderResult = mysqli_query($con, $checkReorderQuery);
    $row = mysqli_fetch_assoc($checkReorderResult);
    $updatedQuantity = $row['quantity'];
    $reorderPoint = $row['reorder_point'];
    $productStatus = $row['status'];

    // Update product status
    $updateStatus = $updatedQuantity >= $reorderPoint ? 'sufficient' : 'reorder';
    $updateStatusQuery = "UPDATE products SET status = '$updateStatus' WHERE id = $productID";
    mysqli_query($con, $updateStatusQuery);

    // Update received_date column in the orders table
    $updateReceivedDateQuery = "UPDATE orders SET received_date = NOW() WHERE product_id = $productID";
    mysqli_query($con, $updateReceivedDateQuery);

    // Check if the product status is 'pending'
    if ($productStatus === 'pending') {
        // Check if the price parameter is provided
        if (isset($_GET['price'])) {
            $price = $_GET['price'];
            // Update the price column in the products table
            $updatePriceQuery = "UPDATE products SET price = '$price' WHERE id = $productID";
            mysqli_query($con, $updatePriceQuery);
        } else {
            // Redirect to the same page with the received and product_id parameters
            header("Location: to_received.php?receive=$toReceive&product_id=$productID");
            exit();
        }
    }

    // Update delivery time
    $updateDeliveryTimeQuery = "UPDATE suppliers SET EstimatedDeliveryTime = (
        SELECT CEIL((EstimatedDeliveryTime + TIMESTAMPDIFF(HOUR, order_date, received_date)) / 2)
        FROM orders
        WHERE received_date IS NOT NULL AND product_id = $productID
        LIMIT 1
    )
    WHERE id = (SELECT supplier_id FROM orders WHERE product_id = $productID LIMIT 1)";

    mysqli_query($con, $updateDeliveryTimeQuery);
}

// Retrieve orders with a status of "received" and a null value in received_date along with product details
$query = "SELECT o.product_id, p.name AS product_name, o.quantity AS to_receive, p.quantity AS current_quantity, p.reorder_point, o.order_date, o.received_date, p.status
          FROM orders o
          INNER JOIN products p ON o.product_id = p.id
          WHERE p.status IN ('pending', 'received') AND o.received_date IS NULL";
$result = mysqli_query($con, $query);

// Check if there are orders with a "received" status
if (mysqli_num_rows($result) > 0) {
    echo "<div class='main-content'>";
    echo "<h2>To Receive Products</h2>";

    // Display the to receive products
    echo "<div class='received-products'>";
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Current Quantity</th><th>Quantity to Receive</th><th>Order Date</th><th>Action</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $productId = $row['product_id'];
        $productName = $row['product_name'];
        $currentQuantity = $row['current_quantity'];
        $toReceive = $row['to_receive'];
        $reorderPoint = $row['reorder_point'];
        $orderDate = $row['order_date'];
        $productStatus = $row['status'];
        echo "<tr>";
        echo "<td>$productId</td>";
        echo "<td>$productName</td>";

        // Highlight current quantity in red if below reorder point
        $quantityClass = $currentQuantity < $reorderPoint ? 'low-quantity' : '';
        echo "<td class='$quantityClass'>$currentQuantity</td>";

        echo "<td>$toReceive</td>";
        echo "<td>$orderDate</td>";

        // Check if the product status is 'pending'
        if ($productStatus === 'pending') {
            // Create the "Receive" button with the onclick event to show the prompt for entering the price
            echo "<td><a class='receive-link' href='javascript:void(0)' onclick=\"showPrompt('$productId', '$toReceive')\"><i class='fas fa-check orange-icon'></i></a></td>";
        } else {
            // Create the "Receive" button without the prompt for products with status other than 'pending'
            echo "<td><a class='receive-link' href='to_received.php?receive=$toReceive&product_id=$productId'><i class='fas fa-check orange-icon'></i></a></td>";
        }

        echo "</tr>";
    }

    echo "</table>";
    echo "</div>"; // End of received-products
    echo "</div>";
} else {
    echo "<div class='main-content'>";
    echo "<h2>No Products to Receive</h2>";
    echo "<p>No products are currently pending for receiving.</p>";
    echo "</div>";
}

mysqli_free_result($result);
?>

<link rel="stylesheet" href="demands.css">

<script>
    function showPrompt(productID, toReceive) {
        const price = prompt('Please enter the price of the product:');
        if (price !== null) {
            window.location.href = `to_received.php?receive=${toReceive}&product_id=${productID}&price=${price}`;
        }
    }
</script>
