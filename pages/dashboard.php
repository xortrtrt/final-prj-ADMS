<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Debug: Check session variables
if (!isset($_SESSION['email'])) {
    error_log("Email not found in session on dashboard. Session variables: " . print_r($_SESSION, true));
}

include('../includes/header.php'); 
require_once('../config/db.php');

// Get current user ID
$current_user_id = $_SESSION['user_id'];

// Fetch recent lost/found items (limit 5)
$sql_lost = "SELECT * FROM lost_item ORDER BY date_lost DESC LIMIT 5";
$lost_items = $conn->query($sql_lost);
if (!$lost_items) {
    $lost_items = new stdClass();
    $lost_items->num_rows = 0;
}

$sql_found = "SELECT * FROM found_item ORDER BY date_found DESC LIMIT 5";
$found_items = $conn->query($sql_found);
if (!$found_items) {
    $found_items = new stdClass();
    $found_items->num_rows = 0;
}

// Handle search functionality
$search_results = new stdClass();
$search_results->num_rows = 0;
if (isset($_POST['search'])) {
    $search_query = $conn->real_escape_string($_POST['search_query']);
    $sql_search = "SELECT * FROM found_item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%' 
                   UNION 
                   SELECT * FROM lost_item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%'";
    $search_results = $conn->query($sql_search);
    if (!$search_results) {
        $search_results = new stdClass();
        $search_results->num_rows = 0;
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/5.15.4/css/all.min.css">
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background: linear-gradient(rgba(0, 0, 0, 0.22), rgba(0, 0, 0, 0.22)), url('../assets/images/campus-slider-main-1.jpg');
            background-size: cover;
            background-position: center;
            background-repeat: no-repeat;
            background-attachment: fixed;
            color: #333;
            min-height: 100vh;
            backdrop-filter: blur(9px);
            -webkit-backdrop-filter: blur(9px);
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: white;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.5);
            font-size: 2.5em;
            padding: 20px;
        }

        .header-actions {
            display: flex;
            justify-content: center;
            gap: 15px;
            margin: 20px 0;
            flex-wrap: wrap;
        }

        .header-actions a {
            padding: 15px 25px;
            text-decoration: none;
            color: white;
            border-radius: 8px;
            font-size: 18px;
            font-weight: bold;
            transition: background-color 0.3s ease, transform 0.2s ease;
        }

        .header-actions a.btn-primary,
        .header-actions a.btn-info,
        .header-actions a.btn-success,
        .header-actions a.btn-warning {
            background-color: #CC0000;
            color: white;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            font-weight: 500;
            text-transform: uppercase;
            letter-spacing: 0.5px;
        }

        .header-actions a:hover {
            background-color: #990000;
            transform: translateY(-2px);
        }

        .header-actions a i {
            font-size: 1.1em;
        }

        .btn-primary:hover, .btn-info:hover, .btn-success:hover, .btn-warning:hover {
            background-color: #990000;
        }

        .action-btn, .mark-found-btn, .claim-btn {
            display: inline-block;
            padding: 8px 15px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
            margin-top: 10px;
            background-color: #CC0000;
            text-transform: uppercase;
            letter-spacing: 0.5px;
            border-radius: 4px;
        }
        
        .action-btn:hover, .mark-found-btn:hover, .claim-btn:hover {
            background-color: #990000;
        }
        
        .item-actions {
            margin-top: 10px;
        }

        .search-bar {
            text-align: center;
            margin: 20px 0;
            background: rgba(255, 255, 255, 0.95);
            padding: 20px;
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 800px;
            margin-left: auto;
            margin-right: auto;
        }

        .search-bar form {
            display: inline-block;
            width: 100%;
            max-width: 600px;
        }

        .search-bar input[type="text"] {
            width: 80%;
            padding: 10px;
            font-size: 16px;
            border: 1px solid #ccc;
            border-radius: 8px;
            margin-right: 10px;
        }

        .search-bar button {
            padding: 10px 20px;
            background-color: #CC0000;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .search-bar button:hover {
            background-color: #990000;
        }

        .dashboard-section {
            margin: 20px auto;
            padding: 30px;
            background: rgba(255, 255, 255, 0.95);
            border-radius: 15px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            position: relative;
            z-index: 1;
            max-width: 1200px;
        }

        .dashboard-section.dark {
            background: rgba(255, 255, 255, 0.15);
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.2);
        }

        .dashboard-section.dark h3 {
            color: #fff;
            font-size: 1.5em;
            text-align: center;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 10px;
        }

        .dashboard-section.dark h3 i {
            color: #FFD700;
        }

        .dashboard-section.dark .item-card,
        .dashboard-section.dark p {
            color: #000;
        }

        .dashboard-section h3 {
            margin-bottom: 20px;
            color: #333;
            position: relative;
            z-index: 1;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(300px, 1fr));
            gap: 20px;
            position: relative;
            z-index: 1;
        }

        .item-card {
            padding: 20px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
            text-align: center;
        }

        .item-card h4 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #333;
        }

        .item-card p {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        .item-card small {
            display: block;
            margin-bottom: 10px;
            color: #777;
        }

        .item-card a {
            display: inline-block;
            padding: 10px 20px;
            background-color: #007bff;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 16px;
            transition: background-color 0.3s ease;
        }

        .item-card a:hover {
            background-color: #0056b3;
        }

        .item-card form button {
            margin-top: 10px;
            padding: 10px 20px;
            background-color: #28a745;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 16px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .item-card form button:hover {
            background-color: #1e7e34;
        }
    </style>
</head>
<body>

<h2>Welcome, <?php echo $_SESSION['name']; ?>!</h2>

<!-- Action Buttons in Header -->
<div class="header-actions">
    <a href="report_lost.php" class="btn btn-primary"><i class="fas fa-plus"></i> Report Lost Item</a>
    <a href="report_found.php" class="btn btn-info"><i class="fas fa-search"></i> Report Found Item</a>
    <a href="search.php" class="btn btn-success"><i class="fas fa-list"></i> Browse All Items</a>
    <?php if ($_SESSION['role'] == 'admin'): ?>
        <a href="admin.php" class="btn btn-warning"><i class="fas fa-crown"></i> Admin Panel</a>
    <?php endif; ?>
</div>

<!-- Search Bar -->
<div class="search-bar">
    <form method="POST" action="">
        <input type="text" name="search_query" placeholder="Search for items..." required>
        <button type="submit" name="search">Search</button>
    </form>
</div>

<!-- Search Results -->
<?php if (isset($_POST['search'])): ?>
    <div class="dashboard-section">
        <h3>🔍 Search Results</h3>
        <?php if ($search_results->num_rows > 0): ?>
            <div class="items-grid">
                <?php while ($item = $search_results->fetch_assoc()): ?>
                    <div class="item-card">
                        <h4><?php echo $item['category']; ?></h4>
                        <p><?php echo $item['description']; ?></p>
                        <small><?php echo isset($item['date_found']) ? "Found on: " . $item['date_found'] : "Lost on: " . $item['date_lost']; ?></small>
                        <?php if ($current_user_id != $item['user_id']): ?>
                            <div class="item-actions">
                                <a href="report_found.php?lost_id=<?php echo $item['lost_id']; ?>" class="action-btn mark-found-btn">MARK AS FOUND</a>
                            </div>
                        <?php endif; ?>
                    </div>
                <?php endwhile; ?>
            </div>
        <?php else: ?>
            <p>No items match your search query.</p>
        <?php endif; ?>
    </div>
<?php endif; ?>

<!-- Recent Lost Items -->
<div class="dashboard-section dark">
    <h3><i class="far fa-star"></i> Recently Lost Items</h3>
    <?php if ($lost_items->num_rows > 0): ?>
        <div class="items-grid">
            <?php while ($item = $lost_items->fetch_assoc()): ?>
                <div class="item-card">
                    <h4><?php echo $item['category']; ?></h4>
                    <p><?php echo $item['description']; ?></p>
                    <small>Lost on: <?php echo $item['date_lost']; ?></small>
                    <?php if ($current_user_id != $item['user_id']): ?>
                        <div class="item-actions">
                            <a href="report_found.php?lost_id=<?php echo $item['lost_id']; ?>" class="action-btn mark-found-btn">MARK AS FOUND</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No lost items reported recently.</p>
    <?php endif; ?>
</div>

<!-- Recent Found Items -->
<div class="dashboard-section dark">
    <h3><i class="far fa-star"></i> Recently Found Items</h3>
    <?php if ($found_items->num_rows > 0): ?>
        <div class="items-grid">
            <?php while ($item = $found_items->fetch_assoc()): ?>
                <div class="item-card">
                    <h4><?php echo $item['category']; ?></h4>
                    <p><?php echo $item['description']; ?></p>
                    <small>Found on: <?php echo $item['date_found']; ?></small>
                    <?php if ($current_user_id != $item['user_id'] && $item['status'] != 'claimed'): ?>
                        <div class="item-actions">
                            <a href="claim_form.php?found_id=<?php echo $item['found_id']; ?>" class="action-btn claim-btn">CLAIM</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    <?php else: ?>
        <p>No found items reported recently.</p>
    <?php endif; ?>
</div>


<?php include('../includes/footer.php'); ?>
</body>
</html>