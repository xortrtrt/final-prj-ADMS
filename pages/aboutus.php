<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>About Us - Lost and Found System</title>
    <style>
        body {
            font-family: "Roboto", Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: 
                /* 22% dark overlay */
                linear-gradient(rgba(0, 0, 0, 0.22), rgba(0, 0, 0, 0.22)),
                /* Background image */
                url('../assets/images/campus-slider-main-1.jpg') no-repeat center center fixed;
            background-size: cover;
            color: #333;
            min-height: 100vh;
        }

        .container {
            max-width: 900px;
            margin: 50px auto;
            padding: 20px;
            background: rgba(255, 255, 255, 0.9);
            border-radius: 10px;
            box-shadow: 0 8px 15px rgba(0, 0, 0, 0.2);
        }

        h1 {
            text-align: center;
            color: #2e7d32;
            margin-bottom: 30px;
            font-size: 28px;
        }

        .section {
            margin-bottom: 30px;
            padding: 20px;
            background: #f9f9f9;
            border-left: 4px solid #2e7d32;
            border-radius: 5px;
        }

        .section h2 {
            color: #1b5e20;
            margin-top: 0;
            font-size: 22px;
        }

        .section p {
            line-height: 1.6;
            font-size: 16px;
        }

        .back-button {
            display: block;
            margin: 20px auto;
            padding: 10px 20px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: bold;
            cursor: pointer;
            text-align: center;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .back-button:hover {
            background-color: #1b5e20;
            transform: translateY(-2px);
        }
    </style>
</head>
<body>

<div class="container">
    <h1>About Our Lost and Found System</h1>

    <div class="section">
        <h2>Our Mission</h2>
        <p>To provide a reliable, efficient, and user-friendly platform that helps reunite lost items with their owners while fostering a culture of honesty and community responsibility within our campus.</p>
    </div>

    <div class="section">
        <h2>Our Vision</h2>
        <p>To become the most trusted lost and found solution in academic institutions, leveraging technology to create meaningful connections between finders and owners, and transforming the way lost items are recovered.</p>
    </div>

    <div class="section">
        <h2>Importance of the System</h2>
        <p>Our Lost and Found System plays a crucial role in campus life by:</p>
        <ul>
            <li>Reducing stress and inconvenience for students and staff who lose valuable items</li>
            <li>Providing a centralized platform for reporting and searching lost/found items</li>
            <li>Encouraging honesty and community cooperation</li>
            <li>Saving time and resources compared to traditional lost and found methods</li>
            <li>Maintaining records for accountability and tracking purposes</li>
            <li>Supporting the campus sustainability initiative by reducing waste from unclaimed items</li>
        </ul>
    </div>

    <div class="section">
        <h2>How It Works</h2>
        <p>Our system operates on three simple principles:</p>
        <ol>
            <li><strong>Report:</strong> Users can easily report lost or found items through our intuitive forms</li>
            <li><strong>Match:</strong> Our system automatically matches lost reports with found reports</li>
            <li><strong>Reunite:</strong> We facilitate secure and verified reunions between items and their owners</li>
        </ol>
    </div>

    <button class="back-button" onclick="window.location.href='login.php';">Back to Login</button>
</div>

</body>
</html>