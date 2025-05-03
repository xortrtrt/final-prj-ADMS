<?php
// Start the session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Lost & Found System</title>
    <!-- Use absolute paths for CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
    <header>
        <nav>
            <a href="/index.php">Home</a>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a href="/pages/dashboard.php">Dashboard</a>
                <a href="/pages/logout.php">Logout</a>
            <?php else : ?>
                <a href="/pages/login.php">Login</a>
                <a href="/pages/register.php">Register</a>
            <?php endif; ?>
        </nav>
    </header>