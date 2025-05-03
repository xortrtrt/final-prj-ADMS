<?php
session_start();
require_once('../config/db.php');
require_once('../send_email.php');  // Include PHPMailer email function

// Check if user is logged in and is an admin
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    echo json_encode(['success' => false, 'message' => 'Unauthorized access']);
    exit();
}

// Check required parameters
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

// Fetch claim details
$sql = "SELECT c.*, f.user_id AS finder_id, f.status AS item_status, u.email AS finder_email, u.name AS finder_name 
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


$conn->begin_transaction();

try {
    $new_status = ($action === 'approve') ? 'approved' : 'rejected';

    // Update claim status
    $sql = "UPDATE claim SET status = ? WHERE claim_id = ?";
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("si", $new_status, $claim_id);
    $stmt->execute();

    // Update item status if approved
    if ($action === 'approve') {
        $sql = "UPDATE found_item SET status = 'claimed' WHERE found_id = ?";
        $stmt = $conn->prepare($sql);
        $stmt->bind_param("i", $claim['found_id']);
        $stmt->execute();
    }

    $conn->commit();

    // Send email notifications
    $claimant_email = $claim['claimant_email'];
    $claimant_name = $claim['claimant_name'];
    $finder_email = $claim['finder_email'];
    $finder_name = $claim['finder_name'];

    // Email content the claimant will receive
    $claimant_subject = "Your Claim Request has been " . ucfirst($new_status);
    $claimant_message = "
        <p>Dear $claimant_name,</p>
        <p>Your claim request has been <strong>$new_status</strong> by the administrator.</p>";

    if ($action === 'approve') {
        $claimant_message .= "<p>Please contact the finder at <strong>$finder_email</strong> to arrange the item pickup.</p>";
    }

    $claimant_message .= "<br><p>Best regards,<br>Lost & Found System</p>";

    // email content the finder will receive
    $finder_subject = "Claim Request " . ucfirst($new_status) . " for Your Found Item";
    $finder_message = "
        <p>Dear $finder_name,</p>
        <p>A claim request for your found item has been <strong>$new_status</strong> by the administrator.</p>";

    if ($action === 'approve') {
        $finder_message .= "<p>The claimant will reach out to you soon.</p>";
    }

    $finder_message .= "<br><p>Best regards,<br>Lost & Found System</p>";

    // Send emails using PHPMailer
    $email1 = sendClaimNotificationEmail($claimant_email, $claimant_name, $claimant_subject, $claimant_message);
    $email2 = sendClaimNotificationEmail($finder_email, $finder_name, $finder_subject, $finder_message);

    if (!$email1 || !$email2) {
        echo json_encode(['success' => false, 'message' => 'Status updated, but email failed to send.']);
    } else {
        echo json_encode(['success' => true, 'message' => 'Claim status updated and email sent.']);
    }

} catch (Exception $e) {
    $conn->rollback();
    echo json_encode(['success' => false, 'message' => 'Error updating claim status: ' . $e->getMessage()]);
}

$conn->close();
