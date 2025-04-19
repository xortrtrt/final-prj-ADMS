<?php
session_start();
require_once('../config/db.php');

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check if required parameters are present
if (!isset($_GET['action']) || !isset($_GET['claim_id'])) {
    echo json_encode(['success' => false, 'message' => 'Missing required parameters']);
    exit();
}

$action = $_GET['action'];
$claim_id = $_GET['claim_id'];

// Validate action
if (!in_array($action, ['approve', 'reject'])) {
    echo json_encode(['success' => false, 'message' => 'Invalid action']);
    exit();
}

// Get claim details
$sql = "SELECT c.*, f.user_id as finder_id, f.status as item_status, u.email as finder_email, u.name as finder_name 
        FROM claim c 
        JOIN found_item f ON c.found_id = f.found_id 
        JOIN user u ON f.user_id = u.user_id 
        WHERE c.claim_id = ?";
$stmt = $conn->prepare($sql);
$stmt->bind_param("i", $claim_id);
$stmt->execute();
$result = $stmt->get_result();

if ($result->num_rows === 0) {
    echo json_encode(['success' => false, 'message' => 'Claim not found']);
    exit();
}

$claim = $result->fetch_assoc();

// Start transaction
$conn->begin_transaction();

try {
    // Update claim status
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';
    $sql = "UPDATE claim SET status = ? WHERE claim_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $claim_id);
    $stmt->execute();

    // If claim is approved, update found item status
    if ($action === 'approve') {
        $sql = "UPDATE found_item SET status = 'claimed' WHERE found_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $claim['found_id']);
        $stmt->execute();
    }

    // Commit transaction
    $conn->commit();

    // Send email notifications
    $claimant_email = $claim['claimant_email'];
    $finder_email = $claim['finder_email'];
    $finder_name = $claim['finder_name'];

    // Email to claimant
    $claimant_subject = "Your Claim Request has been " . ucfirst($new_status);
    $claimant_message = "Dear " . $claim['claimant_name'] . ",\n\n";
    $claimant_message .= "Your claim request has been " . $new_status . " by the administrator.\n";
    if ($action === 'approve') {
        $claimant_message .= "Please contact the finder at: " . $finder_email . " to arrange for item retrieval.\n";
    }
    $claimant_message .= "\nBest regards,\nLost & Found System";

    // Email to finder
    $finder_subject = "Claim Request " . ucfirst($new_status) . " for Your Found Item";
    $finder_message = "Dear " . $finder_name . ",\n\n";
    $finder_message .= "A claim request for your found item has been " . $new_status . " by the administrator.\n";
    if ($action === 'approve') {
        $finder_message .= "The claimant will contact you at: " . $finder_email . " to arrange for item retrieval.\n";
    }
    $finder_message .= "\nBest regards,\nLost & Found System";

    // Send emails (suppress errors for local development)
    @mail($claimant_email, $claimant_subject, $claimant_message);
    @mail($finder_email, $finder_subject, $finder_message);

    echo json_encode(['success' => true, 'message' => 'Claim status updated successfully']);

} catch (Exception $e) {
    // Rollback transaction on error
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating claim status: ' . $e->getMessage()]);
}

$conn->close();
?> 