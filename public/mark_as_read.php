<?php
session_start();
include('../includes/db.php');

// Ensure user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

$user_id = $_SESSION['user_id']; // Get the logged-in user's ID

// Get the Message_ID from the URL query parameter
if (isset($_GET['message_id'])) {
    $message_id = $_GET['message_id'];

    // Update the message status to "read"
    $conn->query("UPDATE message SET is_read = 1 WHERE Message_ID = '$message_id' AND Recipient_ID = '$user_id'");

    // Redirect back to the messages page
    header("Location: messages.php");
    exit;
} else {
    // If no message_id is provided, redirect back to messages page
    header("Location: messages.php");
    exit;
}
