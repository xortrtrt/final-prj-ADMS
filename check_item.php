<?php
require_once('config/db.php');

// Get the found item ID from the URL
$found_id = isset($_GET['found_id']) ? intval($_GET['found_id']) : 0;

// Fetch the found item details
$item_sql = "SELECT * FROM found_item WHERE found_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $found_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

if ($item_result->num_rows === 0) {
    echo "Item not found.";
    exit();
}

$item = $item_result->fetch_assoc();

echo "<h2>Item Details</h2>";
echo "<p>Found ID: " . $item['found_id'] . "</p>";
echo "<p>Category: " . $item['category'] . "</p>";
echo "<p>Description: " . $item['description'] . "</p>";
echo "<p>Status: " . $item['status'] . "</p>";
echo "<p>User ID: " . $item['user_id'] . "</p>";

// Check if there are any claims for this item
$claim_sql = "SELECT * FROM claim WHERE found_id = ?";
$claim_stmt = $conn->prepare($claim_sql);
$claim_stmt->bind_param("i", $found_id);
$claim_stmt->execute();
$claim_result = $claim_stmt->get_result();

echo "<h2>Claims for this item</h2>";
if ($claim_result->num_rows === 0) {
    echo "<p>No claims found for this item.</p>";
} else {
    echo "<table border='1'>";
    echo "<tr><th>Claim ID</th><th>User ID</th><th>Status</th><th>Created At</th></tr>";
    while ($claim = $claim_result->fetch_assoc()) {
        echo "<tr>";
        echo "<td>" . $claim['claim_id'] . "</td>";
        echo "<td>" . $claim['user_id'] . "</td>";
        echo "<td>" . $claim['status'] . "</td>";
        echo "<td>" . $claim['created_at'] . "</td>";
        echo "</tr>";
    }
    echo "</table>";
}
?> 