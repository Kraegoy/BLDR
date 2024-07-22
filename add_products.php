<?php
session_start();

include("connection.php");
include("functions.php");
include('permission.php');

include('layouts/header.php');
// Check if the user is logged in and retrieve their user level
$user_data = check_login($con);
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

if (check_permission($user_level, PERMISSION_ADD_PRODUCTS)) {
    $permission_name = PERMISSION_ADD_PRODUCTS;
} else {
    ?>
    <script>
        alert("You do not have permission to add a product.");
        window.location.href = "bldr.php";
    </script>
    <?php
}

if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id'];
$query = "SELECT user_level FROM users WHERE user_id = ?";
$stmt = mysqli_prepare($con, $query);
mysqli_stmt_bind_param($stmt, "i", $user_id);
mysqli_stmt_execute($stmt);
$result = mysqli_stmt_get_result($stmt);
$row = mysqli_fetch_assoc($result);
$user_level = $row['user_level'];

if ($_SERVER['REQUEST_METHOD'] == "POST") {
    // Something was posted
    $name = $_POST['name'];
    $description = $_POST['description'];
    $price = floatval($_POST['price']);
    $quantity = intval($_POST['quantity']);
    $reorder_point = intval($_POST['reorder_point']);
    $supplier_names = $_POST['supplier_names'];
    $supplier_contacts = $_POST['supplier_contacts'];

    // Handle file upload
    $image = '';
    if (isset($_FILES['image']) && $_FILES['image']['error'] == 0) {
        $tmp_name = $_FILES['image']['tmp_name'];
        $image_name = $_FILES['image']['name'];
        $extension = pathinfo($image_name, PATHINFO_EXTENSION);
        $filename = uniqid() . '.' . $extension;
        $destination = 'uploads/' . $filename;
        move_uploaded_file($tmp_name, $destination);
        $image = $destination;
    }

    if (!empty($name) && !empty($description) && !empty($price) && !empty($quantity)) {
        // Check if product with the same name already exists
        $query = "SELECT * FROM products WHERE name=?";
        $stmt = mysqli_prepare($con, $query);
        mysqli_stmt_bind_param($stmt, "s", $name);
        mysqli_stmt_execute($stmt);
        $result = mysqli_stmt_get_result($stmt);

        if (mysqli_num_rows($result) > 0) {
            echo '<script>alert("The product is already in the inventory. You can edit it there.");</script>';
        } else {
            try {
                // Insert new product into database
                $query = "INSERT INTO products (name, description, price, quantity, reorder_point, status, image) VALUES (?, ?, ?, ?, ?, ?, ?)";
                $stmt = mysqli_prepare($con, $query);

                // Set the initial status value
                $status = ($quantity > $reorder_point) ? 'sufficient' : 'reorder';

                mysqli_stmt_bind_param($stmt, "ssdiiss", $name, $description, $price, $quantity, $reorder_point, $status, $image);
                mysqli_stmt_execute($stmt);
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

                // Insert audit trail record for added product action
                $login_user_id = $_SESSION['user_id'];
                $action = "Added Product";
                $timestamp = date('Y-m-d H:i:s');
                $details = "(Product Name: $name)";
                $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
                mysqli_query($con, $audit_query);

                echo '<script>alert("Product Added.");</script>';
            } catch (mysqli_sql_exception $e) {
                $error_message = $e->getMessage();

                // Check if the error message indicates a duplicate entry
                if (strpos($error_message, "Duplicate entry") !== false) {
                    // Handle the duplicate entry scenario here (e.g., ignore it and proceed)

                    // You can display a message or perform any necessary action
                    echo "Duplicate entry. Ignoring and proceeding...";
                } else {
                    // Handle other types of exceptions
                    // You can display an error message or perform appropriate error handling
                    echo "An error occurred: " . $error_message;
                }
            }
        }
    } else {
        echo '<script>alert("Please fill all required fields.");</script>';
    }
}
?>

         
<style>
    .add-product-form {
        max-width: 500px;
        margin: 0 auto;
    }

    .form-container {
        border: 1px solid #ddd;
        padding: 20px;
        border-radius: 5px;
        margin: 20px auto;
        max-width: 500px;
    }

    h2 {
        text-align: center;
        margin-bottom: 30px;
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
    input[type="number"],
    textarea {
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

   /* Style for "Choose File" button */
input[type="file"] {
  display: none;
}

.choose-file-btn {
  display: inline-block;
  padding: 8px 12px;
  background-color: black;
  color: #fff;
  border: none;
  cursor: pointer;
}

/* Style for "Add Supplier" button */
#add-supplier-btn {
  display: inline-block;
  padding: 8px 12px;
  background-color: black;
  color: #fff;
  border: none;
  cursor: pointer;
}


</style>
<div class="main-content">
    <div class="form-container">
        <h2>Add a Product</h2>
        <form action="add_products.php" method="post" enctype="multipart/form-data">
            <label for="name">Name:</label>
            <input type="text" name="name" required>

            <label for="description">Description:</label>
            <textarea name="description"></textarea>

            <label for="price">Price:</label>
            <input type="number" name="price" step="0.01" required>

            <label for="quantity">Quantity:</label>
            <input type="number" name="quantity" required>

            <label for="reorder_point">Reorder Point:</label>
            <input type="number" name="reorder_point" required>

            <label for="image" class="choose-file-btn">Image File</label>
<input type="file" name="image" id="image" style="display: none;">


<label for="suppliers">Suppliers:</label>
<div id="supplier-inputs">
    <div class="supplier-input">
        <input type="text" name="supplier_names[]" required placeholder="Supplier Name">
        <input type="text" name="supplier_contacts[]" required placeholder="Contact Info">
    </div>
</div>
<button type="button" id="add-supplier-btn">Add Supplier</button>
<br>
<br>


<input type="submit" value="Add Product">
</form>
</div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
$(document).ready(function() {
var supplierCount = 1;

$("#add-supplier-btn").click(function() {
supplierCount++;
var newSupplierInput = `
    <div class="supplier-input">
        <input type="text" name="supplier_names[]" required placeholder="Supplier ${supplierCount} Name">
        <input type="text" name="supplier_contacts[]" required placeholder="Contact Info">
    </div>
`;
$("#supplier-inputs").append(newSupplierInput);
});
});
</script>
</body>
</html>


