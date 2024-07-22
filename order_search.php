<?php
include("connection.php");

$searchQuery = $_GET['search'];

// Check if the search query is empty
if (!empty($searchQuery)) {
    // Retrieve the products matching the search query from the database
    $query = "SELECT id, name FROM products WHERE name LIKE '%$searchQuery%'";
    $result = mysqli_query($con, $query);

    // Generate the HTML for the search results
    if (mysqli_num_rows($result) > 0) {
        while ($product = mysqli_fetch_assoc($result)) {
            $productId = $product['id'];
            $productName = $product['name'];
            echo "<div class='search-result' onclick='selectProduct($productId, \"$productName\")'>$productName</div>";
        }
    } else {
        echo "<div class='search-result'>No products found</div>";
    }

    mysqli_free_result($result);
}
?>
