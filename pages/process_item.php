<?php
session_start();
require_once('../config/db.php');

// Enable error reporting for debugging
error_reporting(E_ALL);
ini_set('display_errors', 1);

// Check if user is admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] !== 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Validate inputs
$action = $_GET['action'] ?? '';
$type = $_GET['type'] ?? '';
$item_id = $_GET['item_id'] ?? '';

if (!in_array($action, ['approve', 'reject', 'pending']) ||
    !in_array($type, ['lost', 'found']) ||
    !is_numeric($item_id)
) {
    echo json_encode(['success' => false, 'message' => 'Invalid request']);
    exit();
}

// Map action to final status value stored in DB
$status_map = [
    'approve' => 'approved',
    'reject' => 'rejected',
    'pending' => 'pending'
];
$status = $status_map[$action];

try {
    $conn->begin_transaction();

    $table = $type . '_item';
    $id_column = $type . '_id';

    // Update item status
    $sql = "UPDATE $table SET status = ? WHERE $id_column = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param('si', $status, $item_id);

    if (!$stmt->execute()) {
        throw new Exception("Database update failed: " . $stmt->error);
    }

    error_log("[$table] Updated item #$item_id to status '$status'");
    error_log("Affected rows: " . $stmt->affected_rows);

    // Reject all pending claims if item is a found item and is approved
    if ($type === 'found' && $action === 'approve') {
        $sql = "UPDATE claim SET status = 'rejected' WHERE found_id = ? AND status = 'pending'";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param('i', $item_id);
        $stmt->execute();
    }

    // Email notification
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
        $subject = "Your $type item has been $status";
        $message = "Dear {$item['name']},\n\n";
        $message .= "Your $type item has been $status by the admin.\n\n";
        $message .= "Item Details:\n";
        $message .= "Category: {$item['category']}\n";
        $message .= "Description: {$item['description']}\n";
        $message .= "Location: {$item['location']}\n\n";
        $message .= "Best regards,\nLost & Found System";

        $headers = "From: lostandfound@example.com";
        @mail($to, $subject, $message, $headers);
    }

    $conn->commit();
    echo json_encode(['success' => true, 'new_status' => $status]);

} catch (Exception $e) {
    $conn->rollback();
    error_log("Error in process_item.php: " . $e->getMessage());
    echo json_encode(['success' => false, 'message' => $e->getMessage()]);
}
?>
