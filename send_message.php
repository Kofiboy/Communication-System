<?php
session_start();
require_once 'includes/config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $channel_id = $_POST['channel_id'];
    $content = $_POST['content'];
    
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $stmt = $conn->prepare("
            INSERT INTO messages (sender_id, channel_id, recipient_id, content, sent_at) 
            VALUES (?, ?, NULL, ?, NOW())
        ");
        $stmt->execute([$_SESSION['user_id'], $channel_id, $content]);
        header("Location: messages.php?channel=$channel_id");
        exit;
    } catch(PDOException $e){
        die("Database error: " . $e->getMessage());
    }
}
?>