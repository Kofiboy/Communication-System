<?php
session_start();  // Start the session to use session variables

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the sender's ID from the session
$sender_id = $_SESSION['user_id'];  // The logged-in user's ID

// Include the database connection file
include('../includes/db.php');

// Fetch all users (for recipient selection)
$recipients = [];
$query = "SELECT User_ID, Email FROM user";
$result = $conn->query($query);
while ($row = $result->fetch_assoc()) {
    $recipients[] = $row;
}

// Check if replying to a message
$recipient_id = isset($_GET['reply_to']) ? $_GET['reply_to'] : ''; // If replying, get recipient ID

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    // Get recipient ID from the form submission (overwrite reply_to if manually selected)
    $recipient_id = $_POST['recipient']; 
    $content = $_POST['message'];
    $timestamp = date('Y-m-d H:i:s'); 

    // Ensure recipient_id is valid before inserting into the database
    if (!empty($recipient_id)) {
        $query = "INSERT INTO message (Sender_ID, Recipient_ID, Content, Timestamp) 
                  VALUES ('$sender_id', '$recipient_id', '$content', '$timestamp')";
        if ($conn->query($query) === TRUE) {
            echo "Message sent successfully!";
            header("refresh:2; url=messages.php");  // Redirect to inbox after sending
            exit;
        } else {
            echo "Error: " . $conn->error;
        }
    } else {
        echo "Error: No recipient selected.";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Send Message</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <h1>Send Message</h1>
    </header>
    <main>
        <form method="POST" action="send_message.php">
            <label for="message">Message Content:</label>
            <textarea name="message" id="message" rows="5" required></textarea><br>

            <!-- Dropdown to select recipient -->
            <label for="recipient">Select Recipient:</label>
            <select name="recipient" id="recipient" required>
                <?php foreach ($recipients as $recipient) { ?>
                    <option value="<?php echo $recipient['User_ID']; ?>"
                        <?php echo ($recipient['User_ID'] == $recipient_id) ? 'selected' : ''; ?>>
                        <?php echo $recipient['Email']; ?>
                    </option>
                <?php } ?>
            </select><br>

            <button type="submit">Send Message</button>
        </form>
    </main>
</body>
</html>
