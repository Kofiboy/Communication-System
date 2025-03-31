<?php
// Include the database connection file
include('../includes/db.php');

// Initialize user ID (you would get this from the logged-in user)
$user_id = ''; // Fetch this from session or authentication system

// Query to fetch messages for the logged-in user
$query = "SELECT * FROM message WHERE recipient_id = '$user_id' ORDER BY timestamp DESC";
$result = $conn->query($query);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Messages</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <header>
        <h1>Your Messages</h1>
    </header>
    <main>
        <?php if ($result->num_rows > 0): ?>
            <ul>
                <?php while ($row = $result->fetch_assoc()): ?>
                    <li>
                        <strong>From:</strong> <?php echo $row['sender_id']; ?><br>
                        <strong>Messag:</strong> <?php echo $row['content']; ?><br>
                        <strong>Sent At:</strong> <?php echo $row['timestamp']; ?>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php else: ?>
            <p>No messages found.</p>
        <?php endif; ?>
    </main>
</body>
</html>
