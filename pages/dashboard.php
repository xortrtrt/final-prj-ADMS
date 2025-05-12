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

// Fetch recent lost/found items 
$sql_lost = "SELECT * FROM lost_item ORDER BY date_lost DESC LIMIT 10";
$lost_items = $conn->query($sql_lost);
if (!$lost_items) {
    $lost_items = new stdClass();
    $lost_items->num_rows = 0;
}

$sql_found = "SELECT * FROM found_item ORDER BY date_found DESC LIMIT 10";
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
    $sql_search = "SELECT  * FROM found_item WHERE category LIKE '%$search_query%' OR description LIKE '%$search_query%' 
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
    <link rel="stylesheet" href="../assets/css/dashboard.css">
</head>
<body>

<h2>Welcome, <?php echo htmlspecialchars($_SESSION['name']); ?>!</h2>

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
        <h3><i class="fas fa-search"></i> Search Results</h3>
        <?php if ($search_results->num_rows > 0): ?>
            <div class="items-grid">
                <?php while ($item = $search_results->fetch_assoc()): ?>
                    <div class="item-card">
                        <h4><?php echo htmlspecialchars($item['category']); ?></h4>
                        <p><?php echo htmlspecialchars($item['description']); ?></p>
                        <small><?php echo isset($item['date_found']) ? "Found on: " . htmlspecialchars($item['date_found']) : "Lost on: " . htmlspecialchars($item['date_lost']); ?></small>
                        <?php if ($current_user_id != $item['user_id']): ?>
                            <div class="item-actions">
                                <a href="report_found.php?lost_id=<?php echo (int)$item['lost_id']; ?>" class="action-btn mark-found-btn">MARK AS FOUND</a>
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
    <h3><i class="fas fa-exclamation-circle"></i> Recently Lost Items</h3>
    <?php if ($lost_items->num_rows > 0): ?>
        <div class="items-grid">
            <?php while ($item = $lost_items->fetch_assoc()): ?>
                <div class="item-card">
                    <h4><?php echo htmlspecialchars($item['category']); ?></h4>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <small>Lost on: <?php echo htmlspecialchars($item['date_lost']); ?></small>
                    <?php if ($current_user_id != $item['user_id']): ?>
                        <div class="item-actions">
                            <a href="report_found.php?lost_id=<?php echo (int)$item['lost_id']; ?>" class="action-btn mark-found-btn">MARK AS FOUND</a>
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
    <h3><i class="fas fa-check-circle"></i> Recently Found Items</h3>
    <?php if ($found_items->num_rows > 0): ?>
        <div class="items-grid">
            <?php while ($item = $found_items->fetch_assoc()): ?>
                <div class="item-card">
                    <h4><?php echo htmlspecialchars($item['category']); ?></h4>
                    <p><?php echo htmlspecialchars($item['description']); ?></p>
                    <small>Found on: <?php echo htmlspecialchars($item['date_found']); ?></small>
                    <?php if ($current_user_id != $item['user_id'] && $item['status'] != 'claimed'): ?>
                        <div class="item-actions">
                            <a href="claim_form.php?found_id=<?php echo (int)$item['found_id']; ?>" class="action-btn claim-btn">CLAIM</a>
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