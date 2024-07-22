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

// Retrieve all products initially
$query = "SELECT * FROM products";
$result = mysqli_query($con, $query);

// Check if there are products
if (mysqli_num_rows($result) > 0) {
    echo "<div class='main-content'>";
    echo "<h2>Select Product to Order</h2>";
    echo "<p>The quantity displayed in red has either reached or fallen below the reorder point.</p>";
    echo "<input type='text' class='search-input' id='searchInput' onkeyup='searchProducts()' placeholder='Search for a product...'>";
    echo "<a href='new_order.php' class='add-product-button black-bg'>Order New Product</a>"; 

    // Display the products that need to be reordered
    echo "<div class='notification-box'>";
    echo "<table>";
    echo "<tr><th>Product ID</th><th>Product Name</th><th>Current Quantity</th><th>Reorder Point</th></tr>";

    while ($row = mysqli_fetch_assoc($result)) {
        $productId = $row['id'];
        $productName = $row['name'];
        $currentQuantity = $row['quantity'];
        $reorder_point = $row['reorder_point'];

        $readClass = ""; // Default to unread
        if (isset($_SESSION['read_notifications']) && in_array($productId, $_SESSION['read_notifications'])) {
            $readClass = "read"; // Apply read class
        }

        echo "<tr class='notification-row $readClass' onclick=\"window.location='order_demand.php?id=$productId'\">";
        echo "<td>$productId</td>";
        echo "<td><b>$productName</b></td>";
        if ($currentQuantity < $reorder_point) {
            echo "<td><b><span class='low-quantity'>$currentQuantity</span></b></td>";
        } else {
            echo "<td><b><span class='green'>$currentQuantity</span></b></td>";
        }
        echo "<td>$reorder_point</td>";
        echo "</tr>";

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

<script>
    function searchProducts() {
        var input = document.getElementById("searchInput").value.toLowerCase();
        var rows = document.getElementsByClassName("notification-row");

        for (var i = 0; i < rows.length; i++) {
            var productName = rows[i].getElementsByTagName("td")[1].innerText.toLowerCase();

            if (productName.includes(input)) {
                rows[i].style.display = "";
            } else {
                rows[i].style.display = "none";
            }
        }
    }
</script>

<link rel="stylesheet" href="demands.css">
