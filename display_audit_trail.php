<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);

// Retrieve users from the users table
$user_query = "SELECT * FROM users";
$user_result = mysqli_query($con, $user_query);

// Check if a user is selected
if (isset($_GET['user_id'])) {
    $selected_user_id = $_GET['user_id'];
    $audit_query = "SELECT * FROM audit_trail WHERE user_id = '$selected_user_id' ORDER BY id DESC";
} else {
    $audit_query = "SELECT * FROM audit_trail ORDER BY id DESC";
}

$audit_result = mysqli_query($con, $audit_query);

include('layouts/header.php');
?>
<link rel="stylesheet" href="audit.css">

<div class="main-content">
    <div class="audit-header">
        <h2>Audit Trail</h2>
    </div>
    <div class="user-selection">
        <form action="" method="GET">
            <label for="user_id">Select User:</label>
            <select name="user_id" id="user_id">
                <option value="">All Users</option>
                <?php while ($user_row = mysqli_fetch_assoc($user_result)) { ?>
                    <option value="<?php echo $user_row['user_id']; ?>"><?php echo $user_row['user_name']; ?></option>
                <?php } ?>
            </select>
            <button type="submit">Filter</button>
        </form>
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

</div>
</body>
</html>
