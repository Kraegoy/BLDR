<?php
session_start();
include("connection.php");
include("functions.php");

$user_data = check_login($con);

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

// Check if the user is logged in and retrieve their user level
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}

require 'PHPMailer-master/src/PHPMailer.php';
require 'PHPMailer-master/src/SMTP.php';
require 'PHPMailer-master/src/Exception.php';

include('layouts/header.php');
// Check if the product ID is provided in the URL
if (isset($_GET['id'])) {
    $productId = $_GET['id'];

    // Retrieve the product information
    $query = "SELECT * FROM products WHERE id = $productId";
    $result = mysqli_query($con, $query);

    // Check if the product exists
    if (mysqli_num_rows($result) > 0) {
        $product = mysqli_fetch_assoc($result);
        $productName = $product['name'];
        $currentQuantity = $product['quantity'];
        $reorderPoint = $product['reorder_point'];

        // Retrieve the supplier information
        $supplierQuery = "SELECT id, name, contact_info
                          FROM suppliers
                          WHERE id IN (SELECT supplier_id FROM product_supplier WHERE product_id = $productId)";
        $supplierResult = mysqli_query($con, $supplierQuery);

        // Check if the supplier exists
        if (mysqli_num_rows($supplierResult) > 0) {
            // Display the product and supplier information
            echo "<div class='main-content'>";
            echo "<div class='eyy'>";
            echo "<h2>Product Details</h2>";
            echo "<p><b>Product ID: $productId</p></b>";
            echo "<p><b>Product Name: $productName</b></p>";
            echo "<p<b>Current Quantity: $currentQuantity</b></p>";
            echo "<p><b>Reorder Point: $reorderPoint</b></p><br>";
            echo "<hr><br>";

            // Display the reorder form
            echo "<h3>Reorder Form</h3>";
            echo "<form method='post'>";
            echo "<label for='supplier'>Choose Supplier:</label>";
            echo "<select id='supplier' name='supplier'>";
            while ($supplier = mysqli_fetch_assoc($supplierResult)) {
                $supplierId = $supplier['id'];
                $supplierName = $supplier['name'];
                echo "<option value='$supplierId'>$supplierName</option>";
            }
            echo "</select><br>";
            echo "Estimated delivery time: ";
            echo "<b><span id='EstimatedDeliveryTime' style='color: green;'></span></b><br><br>";
            echo "<label for='quantity'>Quantity:</label>";
            echo "<input type='number' id='quantity' name='quantity' min='1' required><br>";
            echo "<input type='submit' name='submit' value='Place Order'>";
            echo "</form>";

        // Check if the reorder form is submitted
        if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['submit'])) {
            // Get the submitted supplier ID and quantity
            $selectedSupplierId = $_POST['supplier'];
            $quantity = $_POST['quantity'];

            // Validate the quantity
            if ($quantity <= 0) {
                echo "<p>Please enter a valid quantity.</p>";
            } else {
                // Get the current date
                $orderDate = date('Y-m-d');

                // Insert the order details into the orders table
        $insertQuery = "INSERT INTO orders (user_id, product_id, supplier_id, quantity, order_date)
        VALUES ('{$user_data['id']}', '$productId', '$selectedSupplierId', '$quantity', NOW())";
        mysqli_query($con, $insertQuery);


                // Retrieve the selected supplier's contact information
                $selectedSupplierQuery = "SELECT name, contact_info FROM suppliers WHERE id = $selectedSupplierId";
                $selectedSupplierResult = mysqli_query($con, $selectedSupplierQuery);
                $selectedSupplier = mysqli_fetch_assoc($selectedSupplierResult);
                $selectedSupplierName = $selectedSupplier['name'];
                $selectedSupplierContactInfo = $selectedSupplier['contact_info'];

                 // Insert audit trail record for edited equipment action
        $login_user_id = $_SESSION['user_id'];
        $action = "Ordered to $selectedSupplierName";
        $timestamp = date('Y-m-d H:i:s');
        $details = "($productName: $quantity)";
        $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
        mysqli_query($con, $audit_query);;

                // Send email to the selected supplier
                $subject = "Reorder Request";
                $message = "Dear $selectedSupplierName,\n\nWe would like to place a reorder for $quantity units of the product '$productName'.\nPlease let us know the details of the order.\n\nThank you.\nBest regards, \nKBI";

        
                $mail = new PHPMailer(true);

                try {
                // Configure SMTP settings
                $mail->isSMTP();
                $mail->Host = 'smtp.gmail.com'; // Replace with your SMTP server address
                $mail->Port = 465; // Replace with your SMTP server port
                $mail->SMTPAuth = true;
                $mail->Username = 'avilakraeg@gmail.com'; // Replace with your Gmail email address
                $mail->Password = 'jarmdxrmqidpofid'; // Replace with your Gmail app password
                $mail->SMTPSecure = 'ssl';

                // Set the sender and recipient
                $mail->setFrom('avilakraeg@gmail.com'); // Replace with your email address
                $mail->addAddress($selectedSupplierContactInfo); // Set the recipient email address

                // Set email subject and body
                $mail->Subject = $subject;
                $mail->Body = $message;

                // Send email
                $mail->send();

                // Update the status of the product to "receive"
                $updateQuery = "UPDATE products SET status = 'received' WHERE id = $productId";
                mysqli_query($con, $updateQuery);
                echo "<p><br></p>";
                echo "<p>Email sent to supplier successfully</p>";
                } catch (Exception $e) {
                echo "<p>Email could not be sent. Error: {$mail->ErrorInfo}</p>";
                }

                }
            }

            echo "</div>";
            echo "</div>";
        } else {
            echo "<div class='main-content'>";
            echo "<div class='eyy'>";
            echo "<h2>Supplier Not Found</h2>";
            echo "<p>The supplier information for this product could not be found.</p>";
            echo "</div>";
            echo "<div class='eyy'>";
        }

        mysqli_free_result($supplierResult);
    } else {
        echo "<div class='main-content'>";
        echo "<div class='eyy'>";
        echo "<h2>Product Not Found</h2>";
        echo "<p>The product with ID $productId could not be found.</p>";
        echo "</div>";
        echo "</div>";
    }

    mysqli_free_result($result);
} else {
    echo "<div class='main-content'>";
    echo "<div class='eyy'>";
    echo "<h2>Invalid Request</h2>";
    echo "<p>No product ID specified.</p>";
    echo "</div>";
    echo "</div>";
}
?>
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>

<script>
$(document).ready(function() {
  // Function to fetch and update the EstimatedDeliveryTime
  function updateEstimatedDeliveryTime() {
    var selectedSupplierId = $("#supplier").val(); // Get the selected supplier ID
    $.ajax({
      url: "get_estimated_delivery_time.php", // Replace with the file that retrieves the EstimatedDeliveryTime
      method: "POST",
      data: { supplierId: selectedSupplierId }, // Pass the selected supplier ID to the server
      success: function(response) {
        $("#EstimatedDeliveryTime").text(response); // Update the display with the retrieved EstimatedDeliveryTime
      }
    });
  }

  // Event listener for supplier selection change
  $("#supplier").change(function() {
    updateEstimatedDeliveryTime(); // Call the function to update the EstimatedDeliveryTime
  });

  // Initial call to update the EstimatedDeliveryTime based on the default selected supplier
  updateEstimatedDeliveryTime();
});
</script>


<style>
 .eyy {
    width: 400px;
    margin: 0px auto;
    padding: 20px;
    background-color: #f5f5f5;
    border: 1px solid #ccc;
    border-radius: 4px;
    font-family: Arial, sans-serif;
}

    h2 {
        font-size: 24px;
        margin-bottom: 10px;
    }

    h3 {
        font-size: 18px;
        margin-bottom: 10px;
    }

    p {
        margin-bottom: 10px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="number"] {
        width: 50%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 10px;
    }

    select {
        width: 80%;
        padding: 5px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        margin-bottom: 10px;
    }

    input[type="submit"] {
        padding: 10px 20px;
        background-color: black;
        color: #fff;
        border: none;
        border-radius: 4px;
        cursor: pointer;
    }

    input[type="submit"]:hover {
        background-color: orange;
    }
</style>
