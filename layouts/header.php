
<!DOCTYPE html>
<html lang="en" dir="ltr">
  <head>
    <meta charset="utf-8">
    <link rel="stylesheet" href="bldr.css">
    <link rel="stylesheet" href="existing.css">
    <script src="menu.js"></script>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.3/css/all.min.css"/>
    <link href="https://fonts.googleapis.com/icon?family=Material+Icons" rel="stylesheet">
    <link rel="icon" type="image/x-icon" href="logo.png">
    <title>BLDR</title>

  </head>
  <body>
    <header>
      <img src="logo.png" alt="Logo" class="logo">
      <h1><a href="bldr.php" style="text-decoration: none; size: 50px; color: white;">BLDR</a></h1>
    </header>
    <input type="checkbox" id="check">
    <label for="check">
      <i class="fas fa-bars" id="btn"></i>
      <i class="fas fa-times" id="cancel"></i>
    </label>
    <div class="sidebar">
    <a href="bldr.php"><i class="material-icons">home</i> Dashboard</a>


    <a href="notifications.php">
  <i class="material-icons">notifications</i> Notifications
 
        <a href="#" class="category-link"  onclick="toggleSubMenu(event)"><i class="material-icons">people</i> Users</a>
        <ul class="sub-menu">
        <li><a href="display_users.php">Display Users</a></li>
            <li class="highlight"><a href="add_user.php">Add Users</a></li>
          </ul>

        <a href="#" class="category-link" onclick="toggleSubMenu(event)"><i class="material-icons">build</i> Projects</a>
        <ul class="sub-menu">
        <li><a href="existing_projects.php">Existing Projects</a></li>
          <li><a href="past_projects.php">Past Projects</a></li>
            <li><a href="planning_projects.php">Planning</a></li>
            <li class="highlight"><a href="add_project.php">Add Project</a></li>

          </ul>

          <a href="#" class="category-link"  onclick="toggleSubMenu(event)"><i class="material-icons">inventory</i> Inventory</a>
        <ul class="sub-menu">
        <li><a href="display_inventory.php">Display Inventory</a></li>
        <li class="highlight"><a href="add_products.php">Add Materials</a></li>
          </ul>   
        <a href="#" class="category-link" onclick="toggleSubMenu(event)"><i class="material-icons">shopping_cart</i> Orders</a>
        <ul class="sub-menu">
            <li><a href="demands.php">Demands</a></li>
            <li><a href="to_received.php">To Recieve</a></li>
            <li class="highlight"><a href="order.php">Add Order</a></li>

          </ul>
        <a href="#" class="category-link" onclick="toggleSubMenu(event)"><i class="material-icons">storage</i> Equipments</a>
        <ul class="sub-menu">
        <li><a href="display_equip.php">Display Equipment</a></li>
        <li class="highlight"><a href="add_equip.php">Add Equipment</a></li>

          </ul>
          <a href="sales.php"><i class="material-icons">attach_money</i> Sales</a>

<a href="recycle_bin.php"><i class="fas fa-recycle"></i> Recycle Bin</a>


        <a href="logout.php">Logout</a>
      </div>
    <button class="sidebar-toggle"><i class="fas fa-bars"></i></button>
