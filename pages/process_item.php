<?php
session_start();
require_once('../config/db.php');

// Check if user is logged in and is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check required parameters
if (!isset($_GET['action']) || !isset($_GET['type']) || !isset($_GET['item_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$action = $_GET['action'];
$type = $_GET['type'];
$item_id = $_GET['item_id'];

// Validate action
if (!in_array($action, ['approve', 'reject', 'pending'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

// Map action to status
$status = $action === 'approve' ? 'approved' : ($action === 'reject' ? 'rejected' : 'pending');

// Validate type
if (!in_array($type, ['lost', 'found'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid item type']);
    exit();
}

try {
    // Start transaction
    $conn->begin_transaction();

    // Update item status
    $table = $type . '_item';
    $id_column = $type . '_id';
    
    $sql = "UPDATE $table SET status = ? WHERE $id_column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $item_id);
    
    // Debug logging
    error_log("Updating status in table: $table");
    error_log("Setting status to: $status");
    error_log("For item ID: $item_id");
    
    if (!$stmt->execute()) {
        error_log("SQL Error: " . $stmt->error);
        throw new Exception("Failed to update item status: " . $stmt->error);
    }

    // Log the number of affected rows
    error_log("Affected rows: " . $stmt->affected_rows);

    // If this is a found item and it's being approved, update any pending claims
    if ($type === 'found' && $action === 'approve') {
        $sql = "UPDATE claim SET status = 'rejected' WHERE found_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Send email notifications
    $sql = "SELECT u.email, u.name, i.* FROM $table i 
            JOIN user u ON i.user_id = u.user_id 
            WHERE i.$id_column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('i', $item_id);
    $stmt->execute();
    $result = $stmt->get_result();
    $item = $result->fetch_assoc();

    if ($item && !empty($item['email'])) {
        $to = $item['email'];
        $subject = "Your $type item has been $action" . ($action === 'approve' ? 'd' : 'ed');
        $message = "Dear " . $item['name'] . ",\n\n";
        $message .= "Your $type item has been $action" . ($action === 'approve' ? 'd' : 'ed') . " by the admin.\n\n";
        $message .= "Item Details:\n";
        $message .= "Category: " . $item['category'] . "\n";
        $message .= "Description: " . $item['description'] . "\n";
        $message .= "Location: " . $item['location'] . "\n\n";
        $message .= "Best regards,\nLost & Found System";

        $headers = "From: lostandfound@example.com";
        @mail($to, $subject, $message, $headers);
    }

    echo json_encode(['success' => true, 'new_status' => $status]);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    error_log("Error in process_item.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>