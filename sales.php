<?php
session_start();

include("connection.php");
include("functions.php");

include('layouts/header.php');

// Check if the user is logged in and retrieve their user level
$user_data = check_login($con);
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}
$user_level = $user_data['user_level'];

// Retrieve sales data based on date range
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $start_date = $_POST['start_date'];
    $end_date = $_POST['end_date'];

    $sql = "SELECT s.project_id, p.project_name, s.sales_amount, s.sales_date
            FROM sales s
            INNER JOIN project p ON s.project_id = p.project_id
            WHERE s.sales_date >= '$start_date' AND s.sales_date <= '$end_date'
            AND p.status IN ('completed', 'cancelled')";
    $result = $con->query($sql);

    if ($result->num_rows > 0) {
        // Sales data found, process the data
        $total_sales = 0; // Initialize total sales amount

        while ($row = $result->fetch_assoc()) {
            $project_id = $row['project_id'];
            $project_name = $row['project_name'];
            $sales_amount = $row['sales_amount'];
            $sales_date = $row['sales_date'];

            // Perform any necessary operations with the sales data
            // ...

            $total_sales += $sales_amount; // Add the sales amount to the total sales
        }
    } else {
        // No sales data found for the specified date range
        $_SESSION['flash_message'] = "No sales data found for the specified date range.";
    }
}
// Retrieve monthly sales data
$monthly_sales_query = "SELECT DATE_FORMAT(sales_date, '%M') AS month, SUM(sales_amount) AS total_sales
                        FROM sales
                        WHERE YEAR(sales_date) = YEAR(CURRENT_DATE)
                        GROUP BY MONTH(sales_date)";
$monthly_sales_result = mysqli_query($con, $monthly_sales_query);

// Retrieve yearly sales data
$yearly_sales_query = "SELECT YEAR(sales_date) AS year, SUM(sales_amount) AS total_sales
                       FROM sales
                       GROUP BY YEAR(sales_date)";
$yearly_sales_result = mysqli_query($con, $yearly_sales_query);


?>
<div class="main-content">
    <link rel="stylesheet" href="sales.css">

    <h1>Sales</h1>

    <!-- Display flash message if set -->
    <?php if (isset($_SESSION['flash_message'])) : ?>
        <div class="flash-message"><?php echo $_SESSION['flash_message']; ?></div>
        <?php unset($_SESSION['flash_message']); ?>
    <?php endif; ?>

    <div class="sales-wrapper">
        <div class="sales-box monthly-sales">
            <h2>Monthly Sales</h2>
            <i class="fas fa-chart-bar"></i>
            <hr>
            <?php if (mysqli_num_rows($monthly_sales_result) > 0) : ?>
                <div class="sales-dat">
                    <?php while ($row = mysqli_fetch_assoc($monthly_sales_result)) : ?>
                        <div class="sales-item">
                            <div class="sales-item-title">
                                <h3><?php echo $row['month']; ?></h3>
                            </div>
                            <div class="sales-item-amount">
                                <h2>₱ <?php echo number_format($row['total_sales'], 2); ?></h2>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p>No monthly sales data available.</p>
            <?php endif; ?>
        </div>

        <div class="sales-box yearly-sales">
            <h2>Yearly Sales</h2>
            <i class="fas fa-chart-line"></i>
            <hr>
            <?php if (mysqli_num_rows($yearly_sales_result) > 0) : ?>
                <div class="sales-dat">
                    <?php while ($row = mysqli_fetch_assoc($yearly_sales_result)) : ?>
                        <div class="sales-item">
                            <div class="sales-item-title">
                                <h3><?php echo $row['year']; ?></h3>
                            </div>
                            <div class="sales-item-amount">
                                <h2>₱ <?php echo number_format($row['total_sales'], 2); ?></h2>
                            </div>
                        </div>
                    <?php endwhile; ?>
                </div>
            <?php else : ?>
                <p>No yearly sales data available.</p>
            <?php endif; ?>
        </div>
    </div>
    <div class="sales-content">
    <form method="POST" action="">
        <div class="date-range">
            <label for="start_date">Start Date:</label>
            <input type="date" id="start_date" name="start_date" required>
        </div>

        <div class="date-range">
            <label for="end_date">End Date:</label>
            <input type="date" id="end_date" name="end_date" required>
        </div>

        <button type="submit">View Sales</button>
    </form>

    <!-- Display sales data if available -->
    <?php if (isset($project_id)) : ?>
        <div class="sales-data">
            <h2>Sales Data</h2>
            <table>
                <thead>
                    <tr>
                        <th>Project ID</th>
                        <th>Project Name</th>
                        <th>Sales Amount</th>
                        <th>Sales Date</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
                        <td><?php echo $project_id; ?></td>
                        <td><?php echo $project_name; ?></td>
                        <td><?php echo number_format($sales_amount); ?></td>
                        <td><?php echo $sales_date; ?></td>
                    </tr>
                    <!-- Add more rows for each sales data -->
                </tbody>
            </table>

            <h2>Total Sales Amount: ₱ <?php echo number_format($total_sales); ?></h2> <!-- Display total sales amount with commas -->
        </div>
    <?php endif; ?>
</div>

</div>
