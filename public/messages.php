<?php
session_start();
include('../includes/db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Query to count unread messages
$unreadQuery = "SELECT COUNT(*) AS unread_count FROM message WHERE Recipient_ID = '$user_id' AND is_read = 0";
$unreadResult = $conn->query($unreadQuery);
$row = $unreadResult->fetch_assoc();
$unread_count = $row['unread_count']; // Store the unread count

// Fetch messages where the logged-in user is the recipient
$query = "SELECT m.Message_ID, m.Content, m.Timestamp, u.User_ID AS Sender_ID, u.Email AS Sender_Email, m.is_read
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
        <p>You have <strong><?php echo $unread_count; ?></strong> unread message(s).</p>
    </header>
    <main>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong>From:</strong> <?php echo htmlspecialchars($row['Sender_Email']); ?><br>
                        <strong>Message:</strong> <?php echo nl2br(htmlspecialchars($row['Content'])); ?><br>
                        <strong>Sent At:</strong> <?php echo date("d-m-Y H:i:s", strtotime($row['Timestamp'])); ?><br>

                        <!-- Display Attachments -->
<?php
$message_id = $row['Message_ID'];
$attachmentQuery = "SELECT File_Name FROM attachment WHERE Message_ID = '$message_id'"; // Remove File_Type
$attachmentResult = $conn->query($attachmentQuery);

if ($attachmentResult->num_rows > 0):
    while ($attachment = $attachmentResult->fetch_assoc()):
?>
    <strong>Attachment:</strong> 
    <a href="../uploads/<?php echo htmlspecialchars($attachment['File_Name']); ?>" target="_blank">
        <?php echo htmlspecialchars($attachment['File_Name']); ?>
    </a>
    <br>
<?php endwhile; endif; ?>


                        <!-- Mark as Read link for unread messages -->
                        <?php if ($row['is_read'] == 0): ?>
                            <a href="mark_as_read.php?message_id=<?php echo $row['Message_ID']; ?>">Mark as Read</a>
                        <?php else: ?>
                            <span style="color: green;">Seen</span>
                        <?php endif; ?>

                        &nbsp;|&nbsp;

                        <!-- Reply Link with sender_id and message_id passed as query parameters -->
                        <a href="send_message.php?reply_to=<?php echo $row['Sender_ID']; ?>&message_id=<?php echo $row['Message_ID']; ?>">Reply</a>
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
