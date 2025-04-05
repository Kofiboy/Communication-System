<?php
session_start();
include('../includes/db.php');

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $email = $_POST['email'];
    $password = $_POST['password'];

    // Fetch user from the database
    $query = "SELECT * FROM user WHERE Email = '$email'";
    $result = $conn->query($query);

    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();

        // Verify password (Assuming passwords are stored as plain text. This should be changed to hashed passwords later)
        if ($password == $user['Password']) { 
            // Set session variables
            $_SESSION['user_id'] = $user['User_ID'];
            $_SESSION['user_role'] = $user['Role']; // Assuming 'Role' column exists

            // Redirect to send_message.php after successful login
            echo "Login successful! Redirecting to send message...";
            header("refresh:2; url=dashboard.php"); // Redirect to send_message.php after 2 seconds
            exit;
        } else {
            echo "Invalid password!";
        }
    } else {
        echo "User not found!";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <link rel="stylesheet" href="../styles/style.css">
</head>
<body>
    <h2>Login</h2>
    <form method="POST" action="login.php">
        <label>Email:</label>
        <input type="email" name="email" required><br>

        <label>Password:</label>
        <input type="password" name="password" required><br>

        <button type="submit">Login</button>
    </form>
</body>
</html>
