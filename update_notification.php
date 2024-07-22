    <?php
    session_start();

    include("connection.php");

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $data = json_decode(file_get_contents('php://input'), true);
        $notificationId = $data['notificationId'];

        // Update the read_status in the database
        $query = "UPDATE notifications SET read_status = 1 WHERE id = $notificationId";
        mysqli_query($con, $query);

        echo "Notification marked as read.";
    }
    ?>
