<?php
// This script checks and fixes item status in the database
require_once('config/db.php');

// Get the found item ID from the URL
$found_id = isset($_GET['found_id']) ? intval($_GET['found_id']) : 0;

if ($found_id <= 0) {
    echo "Please provide a valid found_id parameter in the URL.";
    exit;
}

// Fetch the found item details
$item_sql = "SELECT * FROM found_item WHERE found_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $found_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

if ($item_result->num_rows === 0) {
    echo "Item not found.";
    exit;
}

$item = $item_result->fetch_assoc();

echo "<h2>Item Status Check</h2>";
echo "<p>Found ID: " . $found_id . "</p>";
echo "<p>Current Status: <strong>" . $item['status'] . "</strong></p>";

// Check if there are any claims for this item
$claim_sql = "SELECT * FROM claim WHERE found_id = ?";
$claim_stmt = $conn->prepare($claim_sql);
$claim_stmt->bind_param("i", $found_id);
$claim_stmt->execute();
$claim_result = $claim_stmt->get_result();

echo "<p>Number of claims: " . $claim_result->num_rows . "</p>";

if ($claim_result->num_rows > 0) {
    echo "<h3>Claims for this item:</h3>";
    echo "<ul>";
    while ($claim = $claim_result->fetch_assoc()) {
        echo "<li>Claim ID: " . $claim['claim_id'] . ", Status: " . $claim['status'] . ", Date: " . $claim['created_at'] . "</li>";
    }
    echo "</ul>";
}

// Form to fix the status
echo "<h3>Fix Status</h3>";
echo "<form method='post'>";
echo "<select name='new_status'>";
echo "<option value='unclaimed'>unclaimed</option>";
echo "<option value='claimed'>claimed</option>";
echo "<option value='pending'>pending</option>";
echo "</select>";
echo "<input type='submit' name='update' value='Update Status'>";
echo "</form>";

// Handle form submission
if (isset($_POST['update'])) {
    $new_status = $_POST['new_status'];
    
    // Update the status
    $update_sql = "UPDATE found_item SET status = ? WHERE found_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("si", $new_status, $found_id);
    
    if ($update_stmt->execute()) {
        echo "<p style='color: green;'>Status updated successfully to: " . $new_status . "</p>";
    } else {
        echo "<p style='color: red;'>Error updating status: " . $conn->error . "</p>";
    }
}

echo "<p><a href='dashboard.php'>Back to Dashboard</a></p>";
?> 