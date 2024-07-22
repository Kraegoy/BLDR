<?php
session_start();

include("connection.php");
include("functions.php");
include('permission.php');

// Check if the user is logged in and retrieve their user level
$user_data = check_login($con);
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

if (check_permission($user_level, PERMISSION_EDIT_PRODUCT)) {
    $permission_name = PERMISSION_EDIT_PRODUCT;
} else {
    $_SESSION['flash_message'] = "You do not have permission to access this page.";
    header("Location: display_inventory.php");
    exit();
}

$product_id = $_GET['id'] ?? null;
$product_name = urldecode($_GET['name']);

if (isset($_GET['name'])) {
    $product_name = $_GET['name'];
} else {
    $product_name = $product_data['name'];
}

// Fetch product data from the database
if (isset($product_name)) {
  $query = "SELECT * FROM products WHERE name=?";
  $stmt = mysqli_prepare($con, $query);
  mysqli_stmt_bind_param($stmt, "s", $product_name);
  mysqli_stmt_execute($stmt);
  $result = mysqli_stmt_get_result($stmt);

  if (mysqli_num_rows($result) > 0) {
      $product = mysqli_fetch_assoc($result);
      $product_id = $product['id']; // Assign the product ID to $product_id

      // Fetch suppliers for the product
      $suppliers_query = "SELECT suppliers.name, suppliers.contact_info FROM suppliers JOIN product_supplier ON suppliers.id = product_supplier.supplier_id WHERE product_supplier.product_id = ?";
      $stmt = mysqli_prepare($con, $suppliers_query);
      mysqli_stmt_bind_param($stmt, "i", $product_id); // Use $product_id here
      mysqli_stmt_execute($stmt);
      $suppliers_result = mysqli_stmt_get_result($stmt);

      $suppliers = array();
      while ($row = mysqli_fetch_assoc($suppliers_result)) {
          $suppliers[] = array(
              'name' => $row['name'],
              'contact' => $row['contact_info']
          );
      }
      $product['suppliers'] = $suppliers;
  }
}
if (isset($_POST['update_product'])) {
    $product_name = $_POST['name'];
    $description = $_POST['description'];
    $quantity = $_POST['quantity'];
    $reorder_point = $_POST['reorder_point'];
    $price = $_POST['price'];

    $image_path = '';

    if (isset($_FILES['image']) && $_FILES['image']['error'] == UPLOAD_ERR_OK) {
        $image_name = $_FILES['image']['name'];
        $image_tmp_name = $_FILES['image']['tmp_name'];
        $image_size = $_FILES['image']['size'];
        $image_type = $_FILES['image']['type'];

        // Validate file type and size
        $allowed_types = array('image/jpeg', 'image/png', 'image/gif');
        $max_size = 2 * 1024 * 1024; // 2MB

        if (in_array($image_type, $allowed_types) && $image_size <= $max_size) {
            // Move file to server
            $upload_dir = 'uploads/';
            $image_path = $upload_dir . $image_name;

            if (move_uploaded_file($image_tmp_name, $image_path)) {
                // Update image path in database
                $query = "UPDATE products SET name=?, description=?, quantity=?, reorder_point=?, price=?, image=? WHERE name=?";
                $stmt = mysqli_prepare($con, $query);
                mysqli_stmt_bind_param($stmt, "ssdiiss", $product_name, $description, $quantity, $reorder_point, $price, $image_path, $product_name);
                mysqli_stmt_execute($stmt);
              

                if (mysqli_stmt_affected_rows($stmt) > 0) {
                    // Redirect to success page
                    header("Location: edit_product.php?name=$product_name&success=1");
                    exit();
                } else {
                    $_SESSION['flash_message'] = "Error updating product: " . mysqli_error($con);
                }
            } else {
                $_SESSION['flash_message'] = "Error uploading image.";
            }
        } else {
            $_SESSION['flash_message'] = "Invalid image type or size. Please upload a JPEG, PNG, or GIF file that is less than 2MB.";
        }
    } else {

      if (isset($_POST['quantity']) && $_POST['quantity'] < $product['reorder_point']) {
        $status = "reorder";
    } else {
        $status = "sufficient";
    }

    $query = "UPDATE products SET name=?, description=?, quantity=?, reorder_point=?, price=? WHERE name=?";
    $stmt = mysqli_prepare($con, $query);
    mysqli_stmt_bind_param($stmt, "ssdiis", $product_name, $description, $quantity, $reorder_point, $price, $product_name);
    mysqli_stmt_execute($stmt);

     // Insert audit trail record for edited equipment action
     $login_user_id = $_SESSION['user_id'];
     $action = "Edited Product";
     $timestamp = date('Y-m-d H:i:s');
     $details = "(Product Name: $product_name)";
     $audit_query = "INSERT INTO audit_trail (user_id, action, timestamp, details) VALUES ('$login_user_id', '$action', '$timestamp', '$details')";
     mysqli_query($con, $audit_query);

     // Redirect to success page
     header("Location: display_equip.php?name=$name&success=1");
    if (mysqli_stmt_affected_rows($stmt) > 0) {
      // Update the product's suppliers
      $supplier_names = $_POST['supplier_names'];
      $supplier_contacts = $_POST['supplier_contacts'];
  
      // Delete existing suppliers for the product
      $delete_query = "DELETE FROM product_supplier WHERE product_id=?";
      $delete_stmt = mysqli_prepare($con, $delete_query);
      mysqli_stmt_bind_param($delete_stmt, "i", $product_id);
      mysqli_stmt_execute($delete_stmt);
  
      // Insert new suppliers for the product
      $insert_query = "INSERT INTO product_supplier (product_id, supplier_id) VALUES (?, ?)";
      $insert_stmt = mysqli_prepare($con, $insert_query);
      mysqli_stmt_bind_param($insert_stmt, "ii", $product_id, $supplier_id);
  
      for ($i = 0; $i < count($supplier_names); $i++) {
          // Get the supplier_id based on the supplier name
          $supplier_name = $supplier_names[$i];
          $select_query = "SELECT id FROM suppliers WHERE name=?";
          $select_stmt = mysqli_prepare($con, $select_query);
          mysqli_stmt_bind_param($select_stmt, "s", $supplier_name);
          mysqli_stmt_execute($select_stmt);
          $select_result = mysqli_stmt_get_result($select_stmt);
          $supplier_row = mysqli_fetch_assoc($select_result);
          $supplier_id = $supplier_row['id'];
  
          // Insert the supplier for the product
          mysqli_stmt_execute($insert_stmt);
      }
  
      // Update the status based on quantity
      if (isset($_POST['quantity']) && $_POST['quantity'] < $product['reorder_point']) {
          $status = "reorder";
      } else {
          $status = "sufficient";
      }
  
      // Update the status in the database
      $update_status_query = "UPDATE products SET status=? WHERE id=?";
      $update_status_stmt = mysqli_prepare($con, $update_status_query);
      mysqli_stmt_bind_param($update_status_stmt, "si", $status, $product_id);
      mysqli_stmt_execute($update_status_stmt);
  
      // Redirect to success page
      header("Location: display_inventory.php?name=$product_name&success=1");
      exit();
  } else {
      $_SESSION['flash_message'] = "Error updating product: " . mysqli_error($con);
  }
  
    }

    // Update suppliers for the product
    $supplier_names = $_POST['supplier_names'];
    $supplier_contacts = $_POST['supplier_contacts'];

    // Delete existing product-supplier relationships
    $delete_query = "DELETE FROM product_supplier WHERE product_id = ?";
    $delete_stmt = mysqli_prepare($con, $delete_query);
    mysqli_stmt_bind_param($delete_stmt, "i", $product_id);
    mysqli_stmt_execute($delete_stmt);

    // Insert new product-supplier relationships
    $insert_query = "INSERT INTO product_supplier (product_id, supplier_id) VALUES (?, ?)";
    $insert_stmt = mysqli_prepare($con, $insert_query);
    mysqli_stmt_bind_param($insert_stmt, "ii", $product_id, $supplier_id);

    foreach ($supplier_names as $index => $supplier_name) {
      $supplier_contact = $supplier_contacts[$index];
  
      // Get the supplier ID based on the name
      $supplier_query = "SELECT id FROM suppliers WHERE name = ?";
      $supplier_stmt = mysqli_prepare($con, $supplier_query);
      mysqli_stmt_bind_param($supplier_stmt, "s", $supplier_name);
      mysqli_stmt_execute($supplier_stmt);
      $supplier_result = mysqli_stmt_get_result($supplier_stmt);
  
      if (mysqli_num_rows($supplier_result) > 0) {
          $supplier_row = mysqli_fetch_assoc($supplier_result);
          $supplier_id = $supplier_row['id'];
           // Update the status in the database
        $update_status_query = "UPDATE products SET status=? WHERE id=?";
        $update_status_stmt = mysqli_prepare($con, $update_status_query);
        mysqli_stmt_bind_param($update_status_stmt, "si", $status, $product_id);
        mysqli_stmt_execute($update_status_stmt);

  
  // Insert the product-supplier relationship
$insert_stmt = mysqli_prepare($con, $insert_query);
mysqli_stmt_bind_param($insert_stmt, "ii", $product['id'], $supplier_id);
mysqli_stmt_execute($insert_stmt);

      } else {
          // If the supplier doesn't exist, create a new one
          $create_supplier_query = "INSERT INTO suppliers (name, contact_info) VALUES (?, ?)";
          $create_supplier_stmt = mysqli_prepare($con, $create_supplier_query);
          mysqli_stmt_bind_param($create_supplier_stmt, "ss", $supplier_name, $supplier_contact);
          mysqli_stmt_execute($create_supplier_stmt);
  
          // Get the newly created supplier ID
          $supplier_id = mysqli_insert_id($con);
  
          // Insert the product-supplier relationship
          $insert_stmt = mysqli_prepare($con, $insert_query);
          mysqli_stmt_bind_param($insert_stmt, "ii", $product['id'], $supplier_id);
          mysqli_stmt_execute($insert_stmt);
      }
  }

    // Redirect to success page
    header("Location: edit_product.php?name=$product_name&success=1");
    exit();
}
include('layouts/header.php');

?>

<style>
  .edit-product-header {
      font-size: 32px;
      margin-top: 10px;
  }

  .product-name {
      margin-top: 20px;
      font-size: 25px;
      background-color: orange;
      display: inline-block;
  }

  .edit-product-form {
      margin-top: 20px;
      width: 80%;
  }

  .form-group {
      margin-bottom: 10px;
  }

  label {
      display: block;
      font-size: 18px;
      font-weight: bold;
      margin-bottom: 10px;
  }

  .form-control {
      border: 1px solid #ccc;
      border-radius: 4px;
      font-size: 16px;
      padding: 5px;
      width: 50%;
  }

  .container {
      display: flex;
      justify-content: center;
      align-items: center;
      height: auto;
  }

  textarea {
      width: 90%;
      padding: 8px;
      margin-bottom: 10px;
      border: 1px solid #ccc;
      border-radius: 4px;
      height: 100px;
      font-size: 16px;
  }

  .form-control-file {
      font-size: 16px;
      margin-top: 5px;
  }

  .cuteform {
      border: 1px solid #ccc;
      padding: 20px;
      border-radius: 10px;
  }

  .edit-product-btn {
      margin-top: 20px;
      width: 50%;
      padding: 10px;
      border-radius: 5px;
      border: none;
      background-color: black;
      color: #fff;
      font-size: 16px;
      margin-top: 15px;
  }
</style>

<div class="main-content">
  <div class="container">
    <form class="cuteform" method="post" action="edit_product.php?name=<?php echo $product_name; ?>" enctype="multipart/form-data">
      <h1 class="edit-product-header">Edit Product</h1>
      <p class="product-name"><i>Product Name: </i> <b> <?php echo $product['name']; ?></b></p>

      <input type="hidden" name="name" value="<?php echo $product_name; ?>">

      <div class="form-group">
        <label for="description">Description:</label>
        <textarea name="description" id="description" required><?php echo $product['description']; ?></textarea>
      </div>

      <div class="form-group">
        <label for="price">Price:</label>
        <input class="form-control" type="number" name="price" id="price" step="0.01" min="0" value="<?php echo $product['price']; ?>" required>
      </div>

      <div class="form-group">
        <label for="quantity">Quantity:</label>
        <input class="form-control" type="number" name="quantity" id="quantity" min="0" value="<?php echo $product['quantity']; ?>" required>
      </div>

      <div class="form-group">
        <label for="reorder_point">Reorder Point:</label>
        <input class="form-control" type="number" name="reorder_point" id="reorder_point" min="0" value="<?php echo $product['reorder_point']; ?>" required>
      </div>

      <div class="form-group">
        <label for="image">Image:</label>
        <input type="file" name="image" id="image" accept="image/*">
        <input type="hidden" name="image_path" value="<?php echo $product['image']; ?>">
      </div>

      <div class="form-group">
    <label for="suppliers">Suppliers:</label>
    <div id="supplier-inputs">
        <?php foreach ($product['suppliers'] as $supplier) { ?>
            <div class="supplier-input">
                <input type="text" name="supplier_names[]" required placeholder="Supplier Name" value="<?php echo $supplier['name']; ?>">
                <input type="text" name="supplier_contacts[]" required placeholder="Contact Info" value="<?php echo $supplier['contact']; ?>">
            </div>
        <?php } ?>
    </div>
    <button type="button" id="add-supplier-btn">Add Supplier</button>
</div>

      <button class="btn btn-primary edit-product-btn" type="submit" name="update_product">Update Product</button>
    </form>
  </div>
</div>

<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script>
    $(document).ready(function () {
        var supplierCount = <?php echo count($product['suppliers']); ?>;

        $("#add-supplier-btn").click(function () {
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