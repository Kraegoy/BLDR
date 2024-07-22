<?php
session_start();

include("connection.php");
include("functions.php");
include("permission.php");

$user_data = check_login($con);

$search_query = isset($_GET['search']) ? $_GET['search'] : '';

$query = "SELECT * FROM products WHERE name LIKE '%$search_query%' AND status IN ('sufficient', 'reorder', 'received')";
$result = mysqli_query($con, $query);
include('layouts/header.php');
?>

<link rel="stylesheet" href="display_user.css">
<script>
  function searchInventory() {
    event.preventDefault();
    var searchQuery = document.getElementById("searchInput").value;
    var xhttp = new XMLHttpRequest();
    xhttp.onreadystatechange = function() {
      if (this.readyState == 4 && this.status == 200) {
        document.getElementById("products-table-body").innerHTML = this.responseText;
      }
    };
    xhttp.open("GET", "search_products.php?search=" + searchQuery, true);
    xhttp.send();
  }

  function deleteProduct(name, itemId) {
    if (confirm("Are you sure you want to delete '" + name + "'?")) {
      window.location.href = "delete_product.php?name=" + name + "&item_type=" + itemId;
    }
  }
</script>

<div class="main-content">
  <h1>All Products</h1>
  <style>
    #searchInput {
      width: 280px;
      height: 25px;
      font-size: 16px;
    }
  </style>

  <div class="search-container">
    <form>
      <input type="text" id="searchInput" placeholder="Search product" onfocus="clearPlaceholder()" onkeyup="searchInventory()">
    </form>
  </div> <br>
  <a href="add_products.php" class="add-user-btn">Add Product</a>

  <table>
    <thead>
      <tr>
        <th>Image</th>
        <th>Name</th>
        <th>Description</th>
        <th>Price (₱)</th>
        <th>Quantity</th>
        <th>Reorder Point</th>
        <th>Created At</th>
        <th>Last Updated</th>
        <th>Actions</th>
      </tr>
    </thead>
    <tbody id="products-table-body">
      <?php
      if (!empty($search_query)) {
        $query = "SELECT * FROM products WHERE name LIKE '%$search_query%' AND status IN ('sufficient', 'reorder', 'received')";
      } else {
        $query = "SELECT * FROM products WHERE status IN ('sufficient', 'reorder', 'received')";
      }
      $result = mysqli_query($con, $query);
      if (mysqli_num_rows($result) > 0) {
        while ($row = mysqli_fetch_assoc($result)) {
          $id = $row['id'];
          $image = $row['image'];
          $name = $row['name'];
          $description = $row['description'];
          $price = $row['price'];
          $quantity = $row['quantity'];
          $reorder_point = $row['reorder_point'];
          $created_at = $row['created_at'];
          $updated_at = $row['updated_at'];
      ?>
          <tr>
            <td><a href="<?php echo $image; ?>" target="_blank"><img src="<?php echo $image; ?>" alt="<?php echo $name; ?>" width="50"></a></td>
            <td><b><?php echo $name; ?></b></td>
            <td><?php echo $description; ?></td>
            <td><?php echo $price; ?></td>
            <td><?php echo $quantity; ?></td>
            <td><?php echo $reorder_point; ?></td>
            <td><?php echo $created_at; ?></td>
            <td><?php echo $updated_at; ?></td>
            <td>
              <a href="edit_product.php?id=<?php echo $row['id']; ?>&name=<?php echo urlencode($row['name']); ?>" class="btn btn-primary">
                <i class="fas fa-edit" style="color: black;"></i>
              </a>
              <?php if (check_permission($user_level, PERMISSION_DELETE_PRODUCT)) { ?>
                <a href="#" onclick="<?php if (check_permission($user_level, PERMISSION_DELETE_PRODUCT)) { ?>
                  deleteProduct('<?php echo $name; ?>', <?php echo $row['id']; ?>) <?php } else { ?> alert('You do not have permission to delete materials1.') <?php } ?>" class="btn btn-danger" id="delete_<?php echo $id; ?>"><i class="fas fa-trash" style="color: black;"></i></a>
              <?php } else { ?>
                <button class="btn btn-danger" disabled>
                  <i class="fas fa-trash" style="color: black;"></i>
                </button>
                <span class="text-muted"></span>
              <?php } ?>
            </td>
          </tr>
      <?php
        }
      } else {
        ?>
        <tr>
          <td colspan="9">No products found.</td>
        </tr>
      <?php
      }
      ?>
    </tbody>
  </table>
</div>

</body>

</html>
