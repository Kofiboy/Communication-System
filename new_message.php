<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

if($_SERVER['REQUEST_METHOD'] == 'POST'){
    // Handle message sending
    $recipient = $_POST['recipient'];
    $content = $_POST['content'];
    
    try {
        $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
        $stmt = $conn->prepare("
            INSERT INTO messages (sender_id, recipient_id, content, sent_at) 
            VALUES (?, ?, ?, NOW())
        ");
        $stmt->execute([$_SESSION['user_id'], $recipient, $content]);
        header("Location: messages.php");
        exit;
    } catch(PDOException $e){
        die("Database error: " . $e->getMessage());
    }
}

// Get possible recipients
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $stmt = $conn->query("SELECT user_id, name, role FROM users WHERE user_id != {$_SESSION['user_id']}");
    $recipients = $stmt->fetchAll();
} catch(PDOException $e){
    die("Database error: " . $e->getMessage());
}
?>
        <div class="dashboard-content">
            <div class="card">
                <h2>New Message</h2>
                <form method="POST">
                    <div class="form-group">
                        <label>Recipient</label>
                        <select name="recipient" required>
                            <?php foreach($recipients as $user): ?>
                                <option value="<?= $user['user_id'] ?>">
                                    <?= $user['name'] ?> (<?= $user['role'] ?>)
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Message</label>
                        <textarea name="content" required></textarea>
                    </div>
                    <button type="submit" class="btn">Send</button>
                </form>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>