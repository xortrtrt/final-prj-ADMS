<?php
require_once('config/db.php');

// Get the found item ID from the URL
$found_id = isset($_GET['found_id']) ? intval($_GET['found_id']) : 0;

// First, check the current status of the found item
$check_sql = "SELECT status FROM found_item WHERE found_id = ?";
$check_stmt = $conn->prepare($check_sql);
$check_stmt->bind_param("i", $found_id);
$check_stmt->execute();
$check_result = $check_stmt->get_result();

if ($check_result->num_rows === 0) {
    echo "Item not found.";
    exit();
}

$item = $check_result->fetch_assoc();
echo "<h2>Current Status</h2>";
echo "<p>Found ID: " . $found_id . "</p>";
echo "<p>Current Status: " . $item['status'] . "</p>";

// Update the found item status to 'unclaimed' if it's not already
if ($item['status'] !== 'unclaimed') {
    $update_sql = "UPDATE found_item SET status = 'unclaimed' WHERE found_id = ?";
    $update_stmt = $conn->prepare($update_sql);
    $update_stmt->bind_param("i", $found_id);
    
    if ($update_stmt->execute()) {
        echo "<p>Item status updated to 'unclaimed' successfully.</p>";
    } else {
        echo "<p>Error updating item status: " . $update_stmt->error . "</p>";
    }
} else {
    echo "<p>Item is already marked as 'unclaimed'.</p>";
}

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

// Provide a link to go back to the claim form
echo "<p><a href='pages/claim_form.php?found_id=" . $found_id . "'>Go to Claim Form</a></p>";
?> 