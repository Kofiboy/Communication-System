<?php
session_start();
include('../includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    echo json_encode(['unread_count' => 0]);
    exit;
}

$user_id = $_SESSION['user_id'];

// Get the unread messages count
$query = "SELECT COUNT(*) AS unread_count FROM message WHERE Recipient_ID = '$user_id' AND is_read = 0";
$result = $conn->query($query);
$row = $result->fetch_assoc();

echo json_encode(['unread_count' => $row['unread_count']]);
?>
