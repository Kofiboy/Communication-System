<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="send_message.php">Send Message</a></li>
            <li><a href="messages.php">Inbox</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <p>Hello, you are logged in as <?php echo $_SESSION['user_role']; ?>.</p>
    </main>
</body>
</html>
