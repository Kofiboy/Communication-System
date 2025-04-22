<?php
session_start();
include('../includes/db.php');

// Check if the user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit;
}

// Get the file_id from the URL
if (isset($_GET['file_id'])) {
    $file_id = intval($_GET['file_id']);

    // Fetch the file name from the database
    $query = "SELECT File_Name FROM attachment WHERE File_ID = $file_id";
    $result = $conn->query($query);

    if ($result->num_rows > 0) {
        $row = $result->fetch_assoc();
        $file_name = $row['File_Name'];

        // Delete the file from the uploads directory
        $file_path = "../uploads/" . $file_name;
        if (file_exists($file_path)) {
            unlink($file_path); // Delete the file from the server
        }

        // Delete the attachment record from the database
        $deleteQuery = "DELETE FROM attachment WHERE File_ID = $file_id";
        $conn->query($deleteQuery);
    }
}

header("Location: messages.php"); // Redirect to the messages page after deletion
exit;
?>
