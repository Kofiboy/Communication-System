<?php
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/db.php');

$user_id = $_SESSION['user_id'];

// Fetch count of unread messages
$query = "SELECT COUNT(*) AS unread_count FROM message WHERE Recipient_ID = '$user_id' AND is_read = 0";
$result = $conn->query($query);
$row = $result->fetch_assoc();
$unread_count = $row['unread_count'];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="../styles/style.css">
    <style>
        .badge {
            background-color: red;
            color: white;
            padding: 2px 6px;
            border-radius: 50%;
            font-size: 0.8em;
            vertical-align: top;
            margin-left: 4px;
        }
    </style>
</head>
<body>
    <header>
        <h1>Welcome to Your Dashboard</h1>
    </header>
    <nav>
        <ul>
            <li><a href="send_message.php">Send Message</a></li>
            <li><a href="messages.php">Inbox 
                <?php if ($unread_count > 0): ?>
                    <span class="badge"><?php echo $unread_count; ?></span>
                <?php endif; ?>
            </a></li>
            <li><a href="sent_messages.php">Sent Messages</a></li>
            <li><a href="login.php">Logout</a></li>
        </ul>
    </nav>
    <main>
        <p>Hello, you are logged in as <?php echo $_SESSION['user_role']; ?>.</p>
    </main>

    <script>
        function fetchUnreadCount() {
            fetch('get_unread_count.php')
                .then(response => response.json())
                .then(data => {
                    const badge = document.querySelector('.badge');
                    const inboxLink = document.querySelector('a[href="messages.php"]');

                    if (data.unread_count > 0) {
                        if (badge) {
                            badge.textContent = data.unread_count;
                            badge.style.display = 'inline-block';
                        } else {
                            const newBadge = document.createElement('span');
                            newBadge.className = 'badge';
                            newBadge.textContent = data.unread_count;
                            inboxLink.appendChild(newBadge);
                        }
                    } else {
                        if (badge) badge.style.display = 'none';
                    }
                })
                .catch(err => console.error("Error fetching unread count:", err));
        }

        fetchUnreadCount(); // Load once
        setInterval(fetchUnreadCount, 5000); // Update every 5 seconds
    </script>
</body>
</html>
