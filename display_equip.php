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

// Retrieve equipment categories
$categories = array("light", "heavy", "vehicle");

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
  <h2>Equipment</h2>
  <link rel="stylesheet" href="display_user.css">

  <div class="tab">
    <?php foreach ($categories as $category): ?>
      <button class="tablinks" onclick="openTab(event, '<?php echo $category; ?>')"><b><?php echo ucfirst($category); ?></b></button>
    <?php endforeach; ?>
  </div>

  <?php foreach ($categories as $category): ?>
    <?php
    // Retrieve equipment for the specific category
    $query = "SELECT * FROM equipment WHERE category = '$category'";
    $result = mysqli_query($con, $query);
    ?>
    <div id="<?php echo $category; ?>" class="tabcontent">
    <a href="add_equip.php" class="add-user-btn">Add Equipment</a>
    <br><br>
      <h3><?php echo ucfirst($category); ?> Equipment</h3>
      <?php if (mysqli_num_rows($result) > 0): ?>
        <table>
          <tr>
          <th>Image</th>
            <th>Name</th>
            <th>Description</th>
            <th>Category</th>
            <th>Quantity</th>
            <th>Available</th>
            <th>Actions</th>
            
          </tr>
          <?php while ($row = mysqli_fetch_assoc($result)): ?>
            <tr>
            <td><img src="<?php echo $row['image']; ?>" alt="<?php echo $row['name']; ?>" style="width: 50px;"></td>
              <td><?php echo $row['name']; ?></td>
              <td><?php echo $row['description']; ?></td>
              <td><?php echo $row['category']; ?></td>
              <td><?php echo $row['quantity']; ?></td>
              <td><?php echo $row['available']; ?></td>
              <td>
                <a href="edit_equipments.php?name=<?php echo urlencode($row['name']); ?>" class="btn btn-primary"><i class="fas fa-edit" style="color: black;"></i></a>
                <a href="#" onclick="deleteEquipment('<?php echo $row['name']; ?>', <?php echo $row['id']; ?>)" class="btn btn-danger"><i class="fas fa-trash" style="color: black;"></i></a>
              </td>
            </tr>
          <?php endwhile; ?>
        </table>
      <?php else: ?>
        <p>No <?php echo $category; ?> equipment found.</p>
      <?php endif; ?>
    </div>
  <?php endforeach; ?>
</div>

<script>
  function openTab(evt, tabName) {
    var i, tabcontent, tablinks;
    tabcontent = document.getElementsByClassName("tabcontent");
    for (i = 0; i < tabcontent.length; i++) {
      tabcontent[i].style.display = "none";
    }
    tablinks = document.getElementsByClassName("tablinks");
    for (i = 0; i < tablinks.length; i++) {
      tablinks[i].className = tablinks[i].className.replace(" active", "");
    }
    document.getElementById(tabName).style.display = "block";
    evt.currentTarget.className += " active";
  }

  // Set the default tab to be opened
  document.getElementsByClassName("tablinks")[0].click();

  function deleteEquipment(name, item_id) {
  if (confirm("Are you sure you want to delete '" + name + "'?")) {
    window.location.href = "delete_equipment.php?name=" + name + "&item_id=" + item_id;
  }
}

</script>

<?php
?>
