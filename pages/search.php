<?php  
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include('../includes/header.php'); 
require_once('../config/db.php');

$search_query = "";
$results = [];

if (isset($_GET['q'])) {
    $search_query = $_GET['q'];
    $sql = "
        SELECT category, description, location, date_lost, 'Lost' AS type, status 
        FROM Lost_Item 
        WHERE description LIKE ? OR category LIKE ?
        UNION 
        SELECT category, description, location, date_found, 'Found' AS type, status 
        FROM Found_Item 
        WHERE description LIKE ? OR category LIKE ?
    ";
    $stmt = $conn->prepare($sql);
    if ($stmt) {
        $search_param = "%$search_query%";
        $stmt->bind_param("ssss", $search_param, $search_param, $search_param, $search_param);
        $stmt->execute();
        $result = $stmt->get_result();
        $results = $result->fetch_all(MYSQLI_ASSOC);
    } else {
        die("Error preparing the SQL statement: " . $conn->error);
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Search Results</title>
    <style>
        /* General Styling */
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
            background-color: #f9f9f9;
            color: #333;
        }

        h2 {
            text-align: center;
            margin-top: 20px;
            color: #2e7d32;
        }

        /* Search Bar Styling */
        #search-form {
            display: flex;
            justify-content: center;
            margin: 20px 0;
        }

        #search-form input[type="text"] {
            flex: 1;
            max-width: 600px;
            padding: 15px;
            border: 1px solid #ccc;
            border-radius: 5px;
            font-size: 18px;
        }

        #search-form button {
            padding: 15px 25px;
            background-color: #2e7d32;
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 18px;
            cursor: pointer;
            transition: background-color 0.3s ease;
        }

        #search-form button:hover {
            background-color: #005005;
        }

        /* Search Results Styling */
        .search-results {
            margin: 20px auto;
            padding: 30px;
            background-color: white;
            border-radius: 8px;
            box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
            max-width: 1200px;
        }

        .items-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(400px, 1fr));
            gap: 20px;
        }

        .item-card {
            padding: 20px;
            background-color: #f5f5f5;
            border-radius: 8px;
            box-shadow: 0 2px 4px rgba(0, 0, 0, 0.1);
        }

        .item-card h3 {
            margin-bottom: 15px;
            font-size: 20px;
            color: #2e7d32;
        }

        .item-card p {
            font-size: 16px;
            color: #555;
            margin-bottom: 10px;
        }

        .item-card strong {
            color: #333;
        }

        /* Back Button Styling */
        #back-btn {
            display: inline-block;
            margin: 30px auto;
            padding: 15px 30px;
            background-color: #ff6f00; /* Orange color */
            color: white;
            text-decoration: none;
            border-radius: 8px;
            font-size: 18px;
            text-align: center;
            transition: background-color 0.3s ease;
        }

        #back-btn:hover {
            background-color: #c43e00; /* Darker orange */
        }
    </style>
</head>
<body>

<h2>Search Lost & Found Items</h2>

<!-- Search Form -->
<form id="search-form" method="GET" action="search.php">
    <input type="text" id="search-input" name="q" placeholder="Search by item name or category..." value="<?php echo htmlspecialchars($search_query); ?>">
    <button type="submit">🔍 Search</button>
</form>

<!-- Search Results -->
<div class="search-results">
    <?php if (!empty($results)): ?>
        <div class="items-grid">
            <?php foreach ($results as $item): ?>
                <div class="item-card">
                    <h3><?php echo htmlspecialchars($item['category']); ?></h3>
                    <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                    <p><strong>Location:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                    <p><strong>Date:</strong> <?php echo htmlspecialchars($item['date_lost'] ?? $item['date_found']); ?></p>
                    <p><strong>Type:</strong> <?php echo htmlspecialchars($item['type']); ?></p>
                    <p><strong>Status:</strong> <?php echo htmlspecialchars($item['status']); ?></p>
                </div>
            <?php endforeach; ?>
        </div>
    <?php elseif ($search_query): ?>
        <p>No results found for "<?php echo htmlspecialchars($search_query); ?>".</p>
    <?php endif; ?>
</div>

<!-- Back Button -->
<a href="dashboard.php" id="back-btn">Back to Dashboard</a>

<?php include('../includes/footer.php'); ?>
</body>
</html>