<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include('includes/header.php'); 
require_once('config/db.php');

// Get current user ID
$current_user_id = $_SESSION['user_id'];

// Fetch all lost and found items
$sql_lost = "SELECT * FROM lost_item";
$sql_found = "SELECT * FROM found_item";

// If a search query is provided, filter the results
$search_query = "";
if (isset($_GET['q']) && !empty($_GET['q'])) {
    $search_query = $_GET['q'];
    $sql_lost = "SELECT * FROM lost_item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%' OR status LIKE '%$search_query%'";
    $sql_found = "SELECT * FROM found_item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%' OR status LIKE '%$search_query%'";
}

$lost_items = $conn->query($sql_lost);
$found_items = $conn->query($sql_found);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Browse All Items</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            position: relative;
            margin: 0;
            padding: 0;
            min-height: 100vh;
            background: transparent;
            background-size: cover;
            color: #333;
        }

        body::before {
            content: '';
            position: fixed;
            top: 0;
            left: 0;
            right: 0;
            bottom: 0;
            background: url('../assets/images/homepage-webslider-1 (1)2.jpg') no-repeat center center fixed;
            background-size: cover;
            filter: blur(15px);
            z-index: -1;
            margin: -30px;
        }

        .content-wrapper {
            position: relative;
            z-index: 1;
            background: rgba(255, 255, 255, 0.1);
            min-height: calc(100vh - 60px);
        }

        footer {
            position: relative;
            z-index: 2;
            background: #fff;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #ffffff;
            font-size: 28px;
            text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.7);
        }

        .dashboard-search {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        .dashboard-search form {
            display: flex;
            gap: 10px;
            width: 100%;
            max-width: 600px;
        }

        .dashboard-search input[type="text"] {
            flex: 1;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 8px;
            font-size: 18px;
        }

        .dashboard-search button {
            padding: 15px 25px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 8px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        .dashboard-search button:hover {
            background-color: #005005;
        }

        .dashboard-section {
            margin: 20px auto;
            padding: 30px;
            background-color: rgba(255, 255, 255, 0.9);
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

        .dashboard-section h3 {
            margin-bottom: 20px;
            color: #2e7d32;
            font-size: 24px;
        }

        table {
            width: 100%;
            border-collapse: collapse;
            margin-bottom: 20px;
        }

        table th, table td {
            border: 1px solid #ddd;
            padding: 10px;
            text-align: center;
        }

        table th {
            background-color: #2e7d32;
            color: white;
            font-size: 18px;
        }

        table td {
            font-size: 16px;
        }

        .status {
            font-weight: bold;
            padding: 5px 10px;
            border-radius: 5px;
            color: white;
        }

        .status.approved {
            background-color: #28a745;
        }

        .status.pending {
            background-color: #ffc107;
        }

        .status.claimed {
            background-color: #007bff;
        }

        .status.rejected {
            background-color: #dc3545;
        }

        .back-btn {
            display: inline-block;
            margin: 20px auto;
            padding: 15px 30px;
            background-color: #ff6f00;
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        .back-btn:hover {
            background-color: #c43e00;
        }
        
        .action-btn {
            display: inline-block;
            padding: 8px 15px;
            border-radius: 5px;
            color: white;
            text-decoration: none;
            font-weight: bold;
            transition: background-color 0.3s ease;
        }
        
        .mark-found-btn {
            background-color: #28a745;
        }
        
        .mark-found-btn:hover {
            background-color: #1e7e34;
        }
        
        .claim-btn {
            background-color: #007bff;
        }
        
        .claim-btn:hover {
            background-color: #0056b3;
        }
    </style>
</head>
<body>

<h2>Browse All Items</h2>

<!-- Search Bar -->
<div class="dashboard-search">
    <form action="browseallItems.php" method="GET">
        <input type="text" name="q" placeholder="Search by category, description, or status..." value="<?php echo htmlspecialchars($search_query); ?>">
        <button type="submit">🔍 Search</button>
    </form>
</div>

<!-- Lost Items Section -->
<div class="dashboard-section">
    <h3>⏳ Lost Items</h3>
    <?php if ($lost_items->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Date Lost</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $lost_items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td><?php echo htmlspecialchars($item['date_lost']); ?></td>
                        <td><span class="status <?php echo strtolower($item['status']); ?>"><?php echo ucfirst($item['status']); ?></span></td>
                        <td>
                            <?php if ($current_user_id != $item['user_id']): ?>
                                <a href="report_found.php?lost_id=<?php echo $item['lost_id']; ?>" class="action-btn mark-found-btn">Mark as Found</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No lost items found.</p>
    <?php endif; ?>
</div>

<!-- Found Items Section -->
<div class="dashboard-section">
    <h3>✨ Found Items</h3>
    <?php if ($found_items->num_rows > 0): ?>
        <table>
            <thead>
                <tr>
                    <th>Category</th>
                    <th>Description</th>
                    <th>Date Found</th>
                    <th>Status</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                <?php while ($item = $found_items->fetch_assoc()): ?>
                    <tr>
                        <td><?php echo htmlspecialchars($item['category']); ?></td>
                        <td><?php echo htmlspecialchars($item['description']); ?></td>
                        <td><?php echo htmlspecialchars($item['date_found']); ?></td>
                        <td><span class="status <?php echo strtolower($item['status']); ?>"><?php echo ucfirst($item['status']); ?></span></td>
                        <td>
                            <?php if ($current_user_id != $item['user_id'] && $item['status'] == 'unclaimed'): ?>
                                <a href="claim_form.php?found_id=<?php echo $item['found_id']; ?>" class="action-btn claim-btn">Claim</a>
                            <?php endif; ?>
                        </td>
                    </tr>
                <?php endwhile; ?>
            </tbody>
        </table>
    <?php else: ?>
        <p>No found items available.</p>
    <?php endif; ?>
</div>

<!-- Back to Dashboard Button -->
<a href="dashboard.php" class="back-btn">Back to Dashboard</a>

<?php include('includes/footer.php'); ?>
</body>
</html>