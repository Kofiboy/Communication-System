<?php
session_start();
include('../includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Logged-in user's ID

// Fetch sent messages
$query = "SELECT m.Message_ID, m.Content, m.Timestamp, u.User_ID AS Recipient_ID, u.Email AS Recipient_Email, m.is_read
          FROM message m
          JOIN user u ON m.Recipient_ID = u.User_ID
          WHERE m.Sender_ID = '$user_id'
          ORDER BY m.Timestamp DESC";

$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <title>Sent Messages</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <h1>Sent Messages</h1>
    </header>
    <main>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong>To:</strong> <?php echo htmlspecialchars($row['Recipient_Email']); ?><br>
                        <strong>Message:</strong> <?php echo nl2br(htmlspecialchars($row['Content'])); ?><br>
                        <strong>Sent At:</strong> <?php echo date("d-m-Y H:i:s", strtotime($row['Timestamp'])); ?><br>

                        <!-- Show Attachments -->
                        <?php
                        $message_id = $row['Message_ID'];
                        $attachmentQuery = "SELECT File_Name FROM attachment WHERE Message_ID = '$message_id'";
                        $attachmentResult = $conn->query($attachmentQuery);

                        if ($attachmentResult->num_rows > 0):
                            echo "<strong>Attachments:</strong><br>";
                            while ($attachment = $attachmentResult->fetch_assoc()):
                        ?>
                            <a href="../uploads/<?php echo urlencode($attachment['File_Name']); ?>" target="_blank">
                                <?php echo htmlspecialchars($attachment['File_Name']); ?>
                            </a><br>
                        <?php endwhile; endif; ?>

                        <!-- Display Seen/Unseen Status -->
                        <?php if ($row['is_read'] == 0): ?>
                            <span style="color: red;">Unseen</span>
                        <?php else: ?>
                            <span style="color: green;">Seen</span>
                        <?php endif; ?>

                    </li>
                    <hr>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No messages sent.</p>
        <?php endif; ?>
    </main>
</body>
</html>
