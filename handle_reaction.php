<?php
session_start();
require_once 'includes/config.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    $message_id = $_POST['message_id'];
    $reaction_type = $_POST['reaction_type'];
    
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        
        // Check if user already reacted
        $stmt = $conn->prepare("
            SELECT * FROM reactions 
            WHERE message_id = ? AND user_id = ? AND reaction_type = ?
        ");
        $stmt->execute([$message_id, $_SESSION['user_id'], $reaction_type]);
        $existing = $stmt->fetch();
        
        if($existing) {
            // Remove reaction
            $stmt = $conn->prepare("
                DELETE FROM reactions 
                WHERE message_id = ? AND user_id = ? AND reaction_type = ?
            ");
            $stmt->execute([$message_id, $_SESSION['user_id'], $reaction_type]);
        } else {
            // Add reaction
            $stmt = $conn->prepare("
                INSERT INTO reactions (message_id, user_id, reaction_type) 
                VALUES (?, ?, ?)
            ");
            $stmt->execute([$message_id, $_SESSION['user_id'], $reaction_type]);
        }
        
        // Return updated count
        $stmt = $conn->prepare("
            SELECT COUNT(*) as count 
            FROM reactions 
            WHERE message_id = ? AND reaction_type = ?
        ");
        $stmt->execute([$message_id, $reaction_type]);
        $count = $stmt->fetch()['count'];
        
        echo json_encode(['success' => true, 'count' => $count]);
        exit;
    } catch(PDOException $e){
        echo json_encode(['success' => false, 'error' => $e->getMessage()]);
        exit;
    }
}
?>