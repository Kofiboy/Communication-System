<?php
require_once 'includes/config.php';
require_once 'includes/header.php';

if(!isset($_SESSION['user_id'])){
    header("Location: index.php");
    exit;
}
?>
            <div class="card">
                <h2>Announcements</h2>
                <p>Recent announcements will appear here</p>
                <!-- Add announcements list later -->
            </div>
        <?php include 'includes/footer.php'; ?>