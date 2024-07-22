<?php
// Connect to the database
session_start();
include("connection.php");
include("functions.php");

// Check if the user is logged in and retrieve their user level
$user_data = check_login($con);
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

// Retrieve deleted products
$query_products = "SELECT * FROM recycle_bin_products";
$result_products = mysqli_query($con, $query_products);

// Retrieve deleted equipments
$query_equipments = "SELECT * FROM recycle_bin_equipment";
$result_equipments = mysqli_query($con, $query_equipments);

// Retrieve deleted users
$query_users = "SELECT * FROM recycle_bin_users";
$result_users = mysqli_query($con, $query_users);

include('layouts/header.php');
?>
<style>
  /* Global styles */
  /* Container styles */
  .container {
    max-width: 800px;
    margin: 0 auto;
    padding: 20px;
  }
  
  /* Heading styles */
  h2 {
    font-size: 24px;
    margin-bottom: 20px;
    color: #333;
  }
  
  h3 {
    font-size: 20px;
    margin-top: 30px;
    margin-bottom: 10px;
    color: #333;
  }
  
  /* Table styles */
  table {
    width: 100%;
    border-collapse: collapse;
    margin-top: 10px;
  }
  
  th, td {
    padding: 10px;
    text-align: left;
  }
  
  th {
    background-color: #1e1e1e;
    color: #fff;
    font-weight: bold;
  }
  
  tr:nth-child(even) {
    background-color: #f9f9f9;
  }
  
  tr:hover {
    background-color: #eaeaea;
  }
  
  /* Link styles */
  a {
    color: #337ab7;
    text-decoration: none;
  }
  
  a:hover {
    text-decoration: underline;
  }
  
  /* No items message */
  .no-items {
    margin-top: 10px;
    color: #666;
  }
  
  /* Tab styles */
  .tab {
    overflow: hidden;
    margin-bottom: 20px;
  }
  
  .tab button {
    background-color: #f1f1f1;
    border: none;
    outline: none;
    cursor: pointer;
    padding: 10px 20px;
    transition: background-color 0.3s;
  }
  
  .tab button:hover {
    background-color: #ddd;
  }
  
  .tab button.active {
    background-color: #ccc;
  }
  
  .tabcontent {
    display: none;
    padding: 20px;
    border: 1px solid #ccc;
  }
</style>

<div class="main-content">
  <h2>Recycle Bin</h2>

  <div class="tab">
    <button class="tablinks" onclick="openTab(event, 'products')"><b>Products</b></button>
    <button class="tablinks" onclick="openTab(event, 'equipments')"><b>Equipments</b></button>
    <button class="tablinks" onclick="openTab(event, 'users')"><b>Users</b></button>
  </div>

  <div id="products" class="tabcontent" style="display: block;">
    <h3>Deleted Products</h3>
    <?php if (mysqli_num_rows($result_products) > 0): ?>
<table>
<tr>
<th>ID</th>
<th>Name</th>
<th>Description</th>
<th>Quantity</th>
<th>Deleted</th>
<th>Restore</th>
</tr>
<?php while ($row = mysqli_fetch_assoc($result_products)): ?>
<tr>
<td><?php echo $row['item_id']; ?></td>
<td><?php echo $row['name']; ?></td>
<td><?php echo $row['description']; ?></td>
<td><?php echo $row['quantity']; ?></td>
<td><?php echo $row['deleted_at']; ?></td>
<td><a href="restore_product.php?item_id=<?php echo $row['item_id']; ?>&item_type=products">Restore</a></td>
</tr>
<?php endwhile; ?>
</table>
<?php else: ?>
<p>No deleted products.</p>
<?php endif; ?>

  </div>
  <div id="equipments" class="tabcontent">
    <h3>Deleted Equipments</h3>
    <?php if (mysqli_num_rows($result_equipments) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>Name</th>
                <th>Category</th>
                <th>Quantity</th>
                <th>Deleted</th>
                <th>Restore</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result_equipments)): ?>
                <tr>
                    <td><?php echo $row['item_id']; ?></td>
                    <td><?php echo $row['name']; ?></td>
                    <td><?php echo $row['category']; ?></td>
                    <td><?php echo $row['quantity']; ?></td>
                    <td><?php echo $row['deleted_at']; ?></td>
                    <td><a href="restore_item.php?item_id=<?php echo $row['item_id']; ?>&item_type=equipment">Restore</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No deleted equipments.</p>
    <?php endif; ?>
  </div>
  <div id="users" class="tabcontent">
    <h3>Deleted Users</h3>
    <?php if (mysqli_num_rows($result_users) > 0): ?>
        <table>
            <tr>
                <th>ID</th>
                <th>User ID</th>
                <th>User Name</th>
                <th>User Level</th>
                <th>Deleted</th>
                <th>Restore</th>
            </tr>
            <?php while ($row = mysqli_fetch_assoc($result_users)): ?>
                <tr>
                    <td><?php echo $row['item_id']; ?></td>
                    <td><?php echo $row['user_id']; ?></td>
                    <td><?php echo $row['user_name']; ?></td>
                    <td><?php echo $row['user_level']; ?></td>
                    <td><?php echo $row['deleted_at']; ?></td>
                    <td><a href="restore_item.php?item_id=<?php echo $row['item_id']; ?>&item_type=users">Restore</a></td>
                </tr>
            <?php endwhile; ?>
        </table>
    <?php else: ?>
        <p>No deleted users.</p>
    <?php endif
; ?>

  </div>
</div>
<script>
  // Function to open a specific tab content
  function openTab(event, tabName) {
    // Get all elements with class="tabcontent" and hide them
    var tabcontent = document.getElementsByClassName("tabcontent");
    for (var i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }

    // Get all elements with class="tablinks" and remove the class "active"
    var tablinks = document.getElementsByClassName("tablinks");
    for (var i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }

    // Show the current tab and add the "active" class to the button that opened the tab
    document.getElementById(tabName).style.display = "block";
    event.currentTarget.className += " active";
  }

  // Set the default tab to be opened
  document.getElementsByClassName("tablinks")[0].click();
</script>