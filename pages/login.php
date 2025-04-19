<?php
require_once('../config/db.php');

if (isset($_POST['login'])) {
    $email = $conn->real_escape_string($_POST['email']);
    $password = $_POST['password'];
    
    $sql = "SELECT * FROM User WHERE email = '$email'";
    $result = $conn->query($sql);
    
    if ($result->num_rows == 1) {
        $user = $result->fetch_assoc();
        if ($user['password'] === $password || password_verify($password, $user['password'])) {
            session_start();
            $_SESSION['user_id'] = $user['user_id'];
            $_SESSION['name'] = $user['name'];
            $_SESSION['role'] = $user['role'];
            header("Location: dashboard.php");
            exit();
        }
    }
    echo "<script>alert('Invalid email or password');</script>";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login</title>
    <style>
        /* ===== MODERN LOGIN PAGE STYLES ===== */
        body {
            font-family: "Roboto", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../assets/images/campus-slider-main-2.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            position: relative;
        }

        body::before {
            content: '';
            position: absolute;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: rgba(0, 0, 0, 0.15);
            backdrop-filter: blur(15px);
            -webkit-backdrop-filter: blur(15px);
            z-index: 0;
        }

        .login-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .login-form {
            background-color: #fff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            position: relative;
            z-index: 2;
        }

        .login-form h2 {
            font-family: "Poppins", Arial, sans-serif;
            font-size: 32px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 25px;
        }

        .login-form input {
            width: 80%;
            padding: 15px;
            margin: 0 auto 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 18px;
            transition: border-color 0.3s ease;
            display: block;
        }

        .login-form input:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 5px rgba(46, 125, 50, 0.5);
        }

        .login-form button[type='submit'] {
            width: auto;
            padding: 10px 20px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            margin: 0 auto;
            display: block;
        }

        .login-form button:hover {
            background-color: #005005;
            transform: translateY(-2px);
        }

        .login-form p {
            font-size: 16px;
            color: #555;
            margin-top: 20px;
        }

        .login-form a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
        }

        .login-form a:hover {
            text-decoration: underline;
        }

        .back-button {
            margin-bottom: 20px;
            padding: 10px 20px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
            position: relative;
            z-index: 2;
        }

        .back-button:hover {
            background-color: #005005;
            transform: translateY(-2px);
        }

        .login-info {
            margin-top: 30px;
            text-align: center;
            position: relative;
            z-index: 2;
        }

        .login-info button {
            margin: 10px;
            padding: 12px 20px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .login-info button:hover {
            background-color: #005005;
            transform: translateY(-2px);
        }

        /* Animation for smooth fade-in */
        @keyframes fadeIn {
            from {
                opacity: 0;
                transform: translateY(-20px);
            }
            to {
                opacity: 1;
                transform: translateY(0);
            }
        }
    </style>
</head>
<body>

<div class="login-container">
    <button class="back-button" onclick="window.location.href='../index.php';">Back to Home</button>
    <div class="login-form">
        <h2>Login</h2>
        <form method="POST">
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit" name="login">Login</button>
        </form>
        <p>Don't have an account? <a href="register.php">Register here</a></p>
    </div>

    <!-- Additional Sections -->
    <div class="login-info">
        <!-- Corrected About Us link -->
        <button onclick="window.location.href='aboutus.php';">About Us</button>
        <button onclick="window.location.href='faqs.php';">FAQs</button>
    </div>
</div>

</body>
</html>