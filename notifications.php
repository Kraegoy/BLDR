<?php
session_start();

include("connection.php");
include("functions.php");

$user_data = check_login($con);
include('layouts/header.php');

// Check if the user is logged in and retrieve their user level
if (!$user_data) {
    // Redirect the user to the login page if they are not logged in
    header("Location: login.php");
    exit();
}

echo "<div class='main-content'>";
echo "<h2>Notifications</h2>";
echo "<div class='notification-box'>";

// Retrieve notifications from the database in descending order
$query = "SELECT * FROM notifications ORDER BY created_at DESC";
$result = mysqli_query($con, $query);

// Check if there are notifications
if (mysqli_num_rows($result) > 0) {
    while ($row = mysqli_fetch_assoc($result)) {
        $notificationId = $row['id'];
        $notificationType = $row['type'];
        $notificationMessage = $row['message'];
        $readStatus = $row['read_status'];
        $createdAt = $row['created_at'];

        $readClass = $readStatus ? 'read' : 'unread'; // Apply appropriate class based on read status

        // Format the timestamp
        $timestamp = date('F j, Y, g:i a', strtotime($createdAt));

        // Extract the project ID from the notification message
        preg_match('/\(ID: (\d+)\)/', $notificationMessage, $matches);
        $projectId = isset($matches[1]) ? $matches[1] : '';

        // Modify the HTML output for each notification card
        echo "<div class='notification-box $readClass' data-notification-id='$notificationId' data-notification-type='$notificationType'>";
        echo "<a href='" . getNotificationLink($notificationType, $projectId) . "' class='notification-link'>";
        echo "<div class='notification-content'>";
        echo "<h3>$timestamp</h3>";
        echo "<p class='notification-message'>$notificationMessage</p>";
        echo "</div>";
        echo "</a>";
        echo "</div>"; // End of notification-box
    }
} else {
    echo "<p>No notifications.</p>";
}

echo "</div>"; // End of notification-box
echo "</div>"; // End of main-content

mysqli_free_result($result);

function getNotificationLink($notificationType, $projectId) {
    if ($notificationType === 'Product Reorder') {
        $link = "order_demand.php?id=$projectId";
    } elseif ($notificationType === 'project') {
        $link = "display_project.php?id=$projectId";
    } else {
        $link = "#";
    }

    return $link;
}
?>

<link rel="stylesheet" href="notifications.css">

<script>
  document.addEventListener('click', function(event) {
    const notificationLink = event.target.closest('.notification-link');
    if (notificationLink) {
      const notificationBox = notificationLink.closest('.notification-box');
      const notificationId = notificationBox.getAttribute('data-notification-id');
      const notificationType = notificationBox.getAttribute('data-notification-type');

      fetch('update_notification.php', {
        method: 'POST',
        body: JSON.stringify({ notificationId: notificationId }),
        headers: {
          'Content-Type': 'application/json'
        }
      })
      .then(response => response.text())
      .then(data => {
        console.log(data);
        notificationBox.classList.add('read');
        const href = notificationLink.getAttribute('href');
        const params = new URLSearchParams(href.split('?')[1]);
        const projectId = params.get('id');
        const link = getNotificationLink(notificationType, projectId);
        window.location.href = link;
      })
      .catch(error => console.log(error));
    }
  });
</script>
