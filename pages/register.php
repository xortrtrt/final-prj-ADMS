<?php
include('../config/db.php');

$error = "";
$success = "";

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = trim($_POST['username']); // Use 'name' instead of 'username'
    $email = trim($_POST['email']);
    $password = password_hash(trim($_POST['password']), PASSWORD_DEFAULT); // Hash the password
    $role = 'user'; // Default role for new users

    // Check if email already exists
    $check_email = "SELECT * FROM user WHERE email = ?";
    $stmt = $conn->prepare($check_email);

    if ($stmt) {
        $stmt->bind_param("s", $email);
        $stmt->execute();
        $result = $stmt->get_result();

        if ($result->num_rows > 0) {
            $error = "Email is already registered!";
        } else {
            // Insert new user
            $sql = "INSERT INTO user (name, email, password, role) VALUES (?, ?, ?, ?)";
            $stmt = $conn->prepare($sql);

            if ($stmt) {
                $stmt->bind_param("ssss", $name, $email, $password, $role);

                if ($stmt->execute()) {
                    // Redirect to dashboard after successful registration
                    header("Location: dashboard.php");
                    exit();
                } else {
                    $error = "Something went wrong. Please try again.";
                }
            } else {
                $error = "Error preparing the SQL statement: " . $conn->error;
            }
        }
    } else {
        $error = "Error preparing the SQL statement: " . $conn->error;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Register</title>
    <style>
        body {
            font-family: "Roboto", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: url('../assets/images/Technology-Building-1-scaled.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            position: relative;
        }

        body::before {
            content: "";
            position: absolute;
            top: 0;
            left: 0;
            width: 100%;
            height: 100%;
            background: rgba(0, 0, 0, 0.22);
            z-index: -1;
        }

        .register-container {
            display: flex;
            flex-direction: column;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            padding: 20px;
        }

        .register-form {
            background-color: #ffffff;
            padding: 40px;
            border-radius: 12px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
            width: 100%;
            max-width: 450px;
            text-align: center;
            animation: fadeIn 0.5s ease-in-out;
            margin: 0 auto;
        }

        .register-form h2 {
            font-family: "Poppins", Arial, sans-serif;
            font-size: 32px;
            font-weight: bold;
            color: #2e7d32;
            margin-bottom: 25px;
            text-align: center;
        }

        .register-form form {
            display: flex;
            flex-direction: column;
            align-items: center;
            width: 100%;
        }

        .register-form input {
            width: 90%;
            padding: 15px;
            margin-bottom: 25px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 18px;
            transition: border-color 0.3s ease;
            text-align: center;
        }

        .register-form button {
            width: 90%;
            margin: 0 auto;
        }

        .register-form p {
            text-align: center;
            width: 100%;
        }

        form input:focus {
            border-color: #2e7d32;
            outline: none;
            box-shadow: 0 0 5px rgba(46, 125, 50, 0.5);
        }

        form button {
            width: 100%;
            padding: 15px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 20px;
            font-weight: bold;
            cursor: pointer;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        form button:hover {
            background-color: #005005;
            transform: translateY(-2px);
        }

        form a {
            color: #2e7d32;
            text-decoration: none;
            font-weight: bold;
        }

        form a:hover {
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
        }

        .back-button:hover {
            background-color: #005005;
            transform: translateY(-2px);
        }

        .message {
            padding: 10px;
            margin-bottom: 20px;
            border-radius: 5px;
            font-size: 16px;
        }

        .message.error {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }

        .message.success {
            background-color: #d4edda;
            color: #155724;
            border: 1px solid #c3e6cb;
        }

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

<div class="register-container">
    <button class="back-button" onclick="window.location.href='../index.php';">Back to Home</button>
    <div class="register-form">
        <h2>Register</h2>
        <?php
        if (isset($_SESSION['register_message'])) {
            echo '<div class="alert alert-info">' . $_SESSION['register_message'] . '</div>';
            unset($_SESSION['register_message']); // Clear the message after displaying
        }
        ?>
        <?php if ($error): ?>
            <div class="message error"><?= $error; ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="message success"><?= $success; ?></div>
        <?php endif; ?>

        <form action="register.php" method="POST">
            <input type="text" name="username" placeholder="Full Name" required>
            <input type="email" name="email" placeholder="Email" required>
            <input type="password" name="password" placeholder="Password" required>
            <button type="submit">Register</button>
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </form>
    </div>
</div>

</body>
</html>