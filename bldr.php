<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);

// Get count of users
$user_count_query = "SELECT COUNT(*) AS user_count FROM users WHERE status = 'active'";
$user_count_result = mysqli_query($con, $user_count_query);
$user_count = mysqli_fetch_assoc($user_count_result)['user_count'];

// Get count of products with specific statuses
$statuses = ['sufficient', 'reorder', 'received'];
$status_count_query = "SELECT COUNT(*) AS status_count FROM products WHERE status IN ('" . implode("','", $statuses) . "')";
$status_count_result = mysqli_query($con, $status_count_query);
$status_count = mysqli_fetch_assoc($status_count_result)['status_count'];

// Get count of projects with status 'in_progress'
$project_count_query = "SELECT COUNT(*) AS project_count FROM project WHERE status = 'in_progress'";
$project_count_result = mysqli_query($con, $project_count_query);
$project_count = mysqli_fetch_assoc($project_count_result)['project_count'];

// Get count of equipments
$equipment_count_query = "SELECT COUNT(*) AS equipment_count FROM equipment";
$equipment_count_result = mysqli_query($con, $equipment_count_query);
$equipment_count = mysqli_fetch_assoc($equipment_count_result)['equipment_count'];

// Get count of recycle bin items
$recycle_bin_query = "SELECT SUM(total_count) AS recycle_bin_count FROM (
    SELECT COUNT(*) AS total_count FROM recycle_bin_products
    UNION ALL
    SELECT COUNT(*) AS total_count FROM recycle_bin_equipment
    UNION ALL
    SELECT COUNT(*) AS total_count FROM recycle_bin_users
) AS counts";
$recycle_bin_result = mysqli_query($con, $recycle_bin_query);
$recycle_bin_count = mysqli_fetch_assoc($recycle_bin_result)['recycle_bin_count'];

// Get count of products to receive
$to_receive_query = "SELECT COUNT(*) AS to_receive_count FROM orders WHERE received_date IS NULL";
$to_receive_result = mysqli_query($con, $to_receive_query);
$to_receive_count = mysqli_fetch_assoc($to_receive_result)['to_receive_count'];

// Get the first 12 rows of the audit trail
$audit_query = "SELECT * FROM audit_trail ORDER BY id DESC LIMIT 12";
$audit_result = mysqli_query($con, $audit_query);

// Get the current month and year
$current_month = date('m');
$current_year = date('Y');

// Retrieve monthly sales data for the current month
$monthly_sales_query = "SELECT YEAR(sales_date) AS year, MONTH(sales_date) AS month, SUM(sales_amount) AS total_sales
                        FROM sales
                        WHERE YEAR(sales_date) = $current_year AND MONTH(sales_date) = $current_month
                        GROUP BY YEAR(sales_date), MONTH(sales_date)
                        ORDER BY YEAR(sales_date), MONTH(sales_date)";
$monthly_sales_result = mysqli_query($con, $monthly_sales_query);

include('layouts/header.php');
?>
<link rel="stylesheet" href="dashboard.css">
<div class="main-content">
    <div class="left-column">
        <a href="display_users.php" style="text-decoration: none; size: 50px; color: black;">
            <div class="box">
                <h2>Users</h2>
                <i class="fas fa-users"></i>
                <hr>
                <p><?php echo $user_count; ?></p>
            </div>
        </a>

        <a href="display_inventory.php" style="text-decoration: none; size: 50px; color: black;">
            <div class="box">
                <h2>Products</h2>
                <i class="fas fa-box"></i>
                <hr>
                <p><?php echo $status_count; ?></p>
            </div>
        </a>

        <a href="existing_projects.php" style="text-decoration: none; size: 50px; color: black;">
            <div class="box">
                <h2>Projects</h2>
                <i class="fas fa-project-diagram"></i>
                <hr>
                <p><?php echo $project_count; ?></p>
            </div>
        </a>

        <a href="display_equip.php" style="text-decoration: none; size: 50px; color: black;">
            <div class="box">
                <h2>Equipments</h2>
                <i class="fas fa-wrench"></i>
                <hr>
                <p><?php echo $equipment_count; ?></p>
            </div>
        </a>

        <a href="recycle_bin.php" style="text-decoration: none; size: 50px; color: black;">
            <div class="box">
                <h2>Recycle Bin</h2>
                <i class="fas fa-trash-alt"></i>
                <hr>
                <p><?php echo $recycle_bin_count; ?></p>
            </div>
        </a>

        <a href="to_received.php" style="text-decoration: none; size: 50px; color: black;">
            <div class="box">
                <h2>To Receive</h2>
                <i class="fas fa-inbox"></i>
                <hr>
                <p><?php echo $to_receive_count; ?></p>
            </div>
        </a>
    </div>
    <div class="right-column audit-box">
  <div class="audit-header">
    <h2>Audit Trail</h2>
    <a href="display_audit_trail.php" class="view-all-button">View All</a>
  </div>
  <table class="audit-table">
    <thead>
      <tr>
        <th>User ID</th>
        <th>Timestamp</th>
        <th>Action</th>
        <th>Details</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($row = mysqli_fetch_assoc($audit_result)) { ?>
        <tr>
          <td><?php echo $row['user_id']; ?></td>
          <td><?php echo $row['timestamp']; ?></td>
          <td><?php echo $row['action']; ?></td>
          <td><?php echo $row['details']; ?></td>
        </tr>
      <?php } ?>
    </tbody>
  </table>
</div>

<div class="sales-wrapper">
  <div class="sales">
    <h2>Monthly Sales</h2>
    <i class="fas fa-chart-bar"></i>
    <hr>
    <?php if (mysqli_num_rows($monthly_sales_result) > 0) : ?>
      <?php while ($row = mysqli_fetch_assoc($monthly_sales_result)) : ?>
        <div class="monthly-sale">
          <h1>₱ <?php echo number_format($row['total_sales'], 2); ?></h1>
        </div>
      <?php endwhile; ?>
    <?php else : ?>
      <p>No monthly sales data available.</p>
    <?php endif; ?>
  </div>
</div>


</div>
</body>

</html>
