<?php
session_start();
include('../includes/db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Fetch messages where the logged-in user is the recipient
$query = "SELECT m.Message_ID, m.Content, m.Timestamp, u.User_ID AS Sender_ID, u.Email AS Sender_Email
          FROM message m
          JOIN user u ON m.Sender_ID = u.User_ID
          WHERE m.Recipient_ID = '$user_id'
          ORDER BY m.Timestamp DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Received Messages</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <h1>Received Messages</h1>
    </header>
    <main>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong>From:</strong> <?php echo htmlspecialchars($row['Sender_Email']); ?><br>
                        <strong>Message:</strong> <?php echo htmlspecialchars($row['Content']); ?><br>
                        <strong>Sent At:</strong> <?php echo date("d-m-Y H:i:s", strtotime($row['Timestamp'])); ?><br>
                        <!-- Reply Link with sender_id passed as a query parameter -->
                        <a href="send_message.php?reply_to=<?php echo $row['Sender_ID']; ?>">Reply</a>
                    </li>
                    <hr>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No messages received.</p>
        <?php endif; ?>
    </main>
</body>
</html>
