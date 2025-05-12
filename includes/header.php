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
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <!-- Header CSS -->
    <link rel="stylesheet" href="/assets/css/header.css">
</head>
<body>
    <header>
        <nav>
            <a href="/index.php"><i class="fas fa-home"></i> Home</a>
            <?php if (isset($_SESSION['user_id'])) : ?>
                <a href="/pages/dashboard.php"><i class="fas fa-tachometer-alt"></i> Dashboard</a>
                <a href="/pages/logout.php"><i class="fas fa-sign-out-alt"></i> Logout</a>
            <?php else : ?>
                <a href="/pages/login.php"><i class="fas fa-sign-in-alt"></i> Login</a>
                <a href="/pages/register.php"><i class="fas fa-user-plus"></i> Register</a>
            <?php endif; ?>
        </nav>
    </header>