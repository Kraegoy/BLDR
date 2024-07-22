<?php
session_start();

include("connection.php");
include("functions.php");

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

$user_data = check_login($con);

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
// Check if the form is submitted
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $product_name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity']; // Get the quantity from the form
    $reorder_point = $_POST['reorder_point'];
    $supplier_names = $_POST['supplier_names'];
    $supplier_contacts = $_POST['supplier_info'];

    // Validate form input (add more validation as per your requirements)
    if (empty($product_name) || empty($reorder_point) || empty($quantity) || empty($supplier_names) || empty($supplier_contacts)) {
        echo "Please fill out all the required fields.";
    } else {
        // Insert the new product into the database
        $query = "INSERT INTO products (name, description, price, quantity, reorder_point, status) VALUES ('$product_name', '$description', NULL, 0, $reorder_point, 'pending')";
        $result = mysqli_query($con, $query);

        if ($result) {
            // Retrieve the product ID of the newly inserted product
            $product_id = mysqli_insert_id($con);

            // Insert suppliers into the database
            $supplier_ids = array();
            $supplier_count = min(count($supplier_names), count($supplier_contacts));
            for ($i = 0; $i < $supplier_count; $i++) {
                $supplier_name = $supplier_names[$i];
                $supplier_contact = $supplier_contacts[$i];
                
                // Check if the supplier already exists
                $query = "SELECT id FROM suppliers WHERE name = ? AND contact_info = ?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ss", $supplier_name, $supplier_contact);
                mysqli_stmt_execute($stmt);
                $result = mysqli_stmt_get_result($stmt);
                
                if (mysqli_num_rows($result) > 0) {
                    // Supplier already exists, retrieve the supplier ID
                    $row = mysqli_fetch_assoc($result);
                    $supplier_id = $row['id'];
                } else {
                    // Supplier doesn't exist, insert a new supplier
                    $query = "INSERT INTO suppliers (name, contact_info) VALUES (?, ?)";
                    $stmt = mysqli_prepare($con, $query);
                    mysqli_stmt_bind_param($stmt, "ss", $supplier_name, $supplier_contact);
                    mysqli_stmt_execute($stmt);
                    $supplier_id = mysqli_insert_id($con);
                }
                
                $supplier_ids[] = $supplier_id;
                
                // Associate the supplier with the product
                $query = "INSERT INTO product_supplier (product_id, supplier_id) VALUES (?, ?)";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ii", $product_id, $supplier_id);
                mysqli_stmt_execute($stmt);
            }
            
            // Insert the order details into the orders table
            foreach ($supplier_ids as $supplier_id) {
                $order_query = "INSERT INTO orders (user_id, product_id, supplier_id, quantity, order_date) VALUES ('{$user_data['id']}', '$product_id', '$supplier_id', $quantity, NOW())";
                mysqli_query($con, $order_query);
                
               // Insert audit trail record for edited equipment action
      $login_user_id = $_SESSION['user_id'];
      $action = "Ordered to $supplier_name";
      $timestamp = date('Y-m-d H:i:s');
      $details = "($product_name: $quantity)";
      $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
      mysqli_query($con, $audit_query);;
            }

            // Send email to suppliers
            foreach ($supplier_contacts as $supplier_contact) {
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
                    $mail->addAddress($supplier_contact); // Set the recipient email address

                    // Set email subject and body
                    $subject = "Reorder Request";
                    $message = "Dear $supplier_name,\n\nWe would like to place a reorder for $quantity units of the product '$product_name'.\nPlease let us know the details of the order.\n\nThank you.\nBest regards, \nKBI";

                    $mail->Subject = $subject;
                    $mail->Body = $message;

                    // Send email
                    $mail->send();
                    
                } catch (Exception $e) {
                    echo "<p>Email could not be sent. Error: {$mail->ErrorInfo}</p>";
                }
            }

            echo "Product ordered successfully.";

        } else {
            echo "Error ordering product. Please try again.";
        }
    }
}
?>

<style>
    .form-container {
        max-width: 500px;
        margin: 0 auto;
    }

    .form-group {
        margin-bottom: 20px;
    }

    label {
        display: block;
        margin-bottom: 5px;
        font-weight: bold;
    }

    input[type="text"],
    textarea {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="number"] {
        width: 100%;
        padding: 10px;
        border: 1px solid #ccc;
        border-radius: 4px;
        box-sizing: border-box;
        font-size: 16px;
    }

    input[type="submit"] {
        background-color: black;
        color: white;
        padding: 12px 20px;
        border: none;
        border-radius: 4px;
        cursor: pointer;
        font-size: 16px;
    }
</style>

<div class="main-content">
    <div class="form-container">
        <h2>Order a Product</h2>
        <form method="POST">
            <div class="form-group">
                <label for="name">Product Name:</label>
                <input type="text" name="name" required>
            </div>

            <div class="form-group">
                <label for="description">Description:</label>
                <textarea name="description"></textarea>
            </div>

            <div class="form-group">
                <label for="reorder_point">Reorder Point of the product:</label>
                <input type="number" name="reorder_point" required>
            </div>

            <div class="form-group">
                <label for="quantity">Quantity to order:</label>
                <input type="number" name="quantity" required>
            </div>

            <div id="supplier-inputs">
                <div class="supplier-input">
                    <input type="text" name="supplier_names[]" required placeholder="Supplier Name">
                    <input type="text" name="supplier_info[]" required placeholder="Supplier Info">
                </div>
            </div>

            <br><br>

            <input type="submit" value="Order Product">
        </form>
    </div>
</div>
