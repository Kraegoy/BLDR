<?php
include("connection.php");

$searchTerm = $_GET['search'];

$query = "SELECT * FROM `products` WHERE `name` LIKE CONCAT('%', ?, '%')";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $products = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $products[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'quantity' => $row['quantity']
        ];
    }

    echo json_encode(['products' => $products]);
} else {
    echo "Error in executing the query: " . mysqli_error($con);
}
?>
