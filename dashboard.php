<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}
?>
        <div class="dashboard-content">
            <div class="card">
                <h2>Dashboard</h2>
                <p>Role: <?= $_SESSION['role'] ?></p>
                <p>Welcome to the University Communication System!</p>
            </div>
        </div>
        <?php include 'includes/footer.php'; ?>