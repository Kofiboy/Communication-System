<!DOCTYPE html>
<html>
<head>
    <title>FYP Comms</title>
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <div class="dashboard">
        <header>
            <h1>FYP Comms</h1>
            <nav>
                <ul>
                    <li><a href="dashboard.php">Dashboard</a></li>
                    <li><a href="messages.php">Messages</a></li>
                    <li><a href="announcements.php">Announcements</a></li>
                    <li><a href="profile.php">Profile</a></li>
                    <li><a href="logout.php">Logout</a></li>
                </ul>
            </nav>
            <div class="user-info">
                <span>Welcome, <?= $_SESSION['name'] ?? 'User' ?> (<?= $_SESSION['role'] ?>)</span>
            </div>
        </header>
        <div class="dashboard-content">