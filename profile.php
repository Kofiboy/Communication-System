<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}

// Get user data
try {
    $conn = new PDO("mysql:host=".DB_HOST.";dbname=".DB_NAME, DB_USER, DB_PASS);
    $stmt = $conn->prepare("SELECT * FROM users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch();
} catch(PDOException $e){
    die("Database error: " . $e->getMessage());
}
?>
        <div class="dashboard-content">
            <div class="card">
                <h2>Profile</h2>
                <p>Name: <?= $user['name'] ?></p>
                <p>Email: <?= $user['email'] ?></p>
                <p>Role: <?= $user['role'] ?></p>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>