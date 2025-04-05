<?php
session_start();
include('../includes/db.php');

// Get the logged-in user ID
$user_id = $_SESSION['user_id'];

// Check if a message ID is passed in the URL
if (isset($_GET['message_id'])) {
    $message_id = $_GET['message_id'];

    // Fetch the original message
    $query = "SELECT * FROM message WHERE Message_ID = '$message_id'";
    $result = $conn->query($query);
    if ($result->num_rows == 1) {
        $message = $result->fetch_assoc();
        $sender_id = $message['sender_id']; // The sender of the original message
    } else {
        die("Message not found!");
    }
}

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['message'];  // Get the reply message content
    $timestamp = date('Y-m-d H:i:s');  // Get the current timestamp

    // Insert the reply into the database
    $query = "INSERT INTO message (sender_id, recipient_id, content, timestamp) 
              VALUES ('$user_id', '$sender_id', '$content', '$timestamp')";
    if ($conn->query($query) === TRUE) {
        echo "Reply sent successfully!";
        header("refresh:2; url=messages.php"); // Redirect to inbox
        exit();
    } else {
        echo "Error: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Reply to Message</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <h1>Reply to Message</h1>
    </header>
    <main>
        <form method="POST" action="reply_message.php?message_id=<?php echo $message_id; ?>">
            <label for="message">Your Reply:</label>
            <textarea name="message" id="message" rows="5" required></textarea><br>
            <button type="submit">Send Reply</button>
        </form>
    </main>
</body>
</html>
