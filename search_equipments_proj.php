<?php
include("connection.php");

$searchTerm = $_GET['search'];

$query = "SELECT * FROM `equipment` WHERE `name` LIKE CONCAT('%', ?, '%')";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, 's', $searchTerm);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);

if ($result) {
    $equipment = [];

    while ($row = mysqli_fetch_assoc($result)) {
        $equipment[] = [
            'id' => $row['id'],
            'name' => $row['name'],
            'category' => $row['category'],
            'quantity' => $row['quantity'],
            'available' => $row['available']
        ];
    }

    echo json_encode($equipment);
} else {
    echo "Error in executing the query: " . mysqli_error($con);
}
?>
