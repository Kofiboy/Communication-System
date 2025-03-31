<?php
session_start();  // Start the session to use session variables

// Set user_id manually for testing purposes
$_SESSION['user_id'] = 1;  // Set user_id to 1 for testing

// Include the database connection file
include('../includes/dp.php');  // Adjusted path to correctly reference db.php

// Initialize variables for sender and recipient
$sender_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 1;  // Default sender_id for testing
$recipient_id = 2;  // For testing, set a default recipient_id (you can modify this)

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $content = $_POST['message'];  // Get message content from form
    $timestamp = date('Y-m-d H:i:s');  // Get the current timestamp for when the message is sent

    // Prepare and execute the SQL query to insert the message
    $query = "INSERT INTO message (sender_id, recipient_id, content, timestamp) 
              VALUES ('$sender_id', '$recipient_id', '$content', '$timestamp')";
    if ($conn->query($query) === TRUE) {
        echo "Message sent successfully!";
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
            <button type="submit">Send Message</button>
        </form>
    </main>
</body>
</html>
