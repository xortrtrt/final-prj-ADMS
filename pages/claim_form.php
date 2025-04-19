<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}
include('../includes/header.php'); 
require_once('../config/db.php');

// Get the found item ID from the URL
$found_id = isset($_GET['found_id']) ? intval($_GET['found_id']) : 0;

// Debug: Log the found_id
error_log("Attempting to claim found_id: " . $found_id);

// Fetch the found item details
$item_sql = "SELECT * FROM found_item WHERE found_id = ?";
$item_stmt = $conn->prepare($item_sql);
$item_stmt->bind_param("i", $found_id);
$item_stmt->execute();
$item_result = $item_stmt->get_result();

if ($item_result->num_rows === 0) {
    echo "<div class='alert alert-danger'>Item not found.</div>";
    echo "<a href='dashboard.php' class='btn btn-primary'>Back to Dashboard</a>";
    include('../includes/footer.php');
    exit();
}

$item = $item_result->fetch_assoc();

// Debug: Log the item status
error_log("Item status: " . $item['status']);

// Check if the current user is the one who found the item
if ($item['user_id'] == $_SESSION['user_id']) {
    echo "<div class='alert alert-danger'>You cannot claim an item that you found yourself.</div>";
    echo "<a href='dashboard.php' class='btn btn-primary'>Back to Dashboard</a>";
    include('../includes/footer.php');
    exit();
}

// Check if the current user already has a pending claim for this item
$check_user_claim_sql = "SELECT * FROM claim WHERE found_id = ? AND user_id = ? AND status = 'pending'";
$check_user_claim_stmt = $conn->prepare($check_user_claim_sql);
$check_user_claim_stmt->bind_param("ii", $found_id, $_SESSION['user_id']);
$check_user_claim_stmt->execute();
$check_user_claim_result = $check_user_claim_stmt->get_result();

if ($check_user_claim_result->num_rows > 0) {
    echo "<div class='modern-alert modern-alert-warning' style='
        background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), 
                   url(\"../images/BATANGAS-STATE-U_thumbnail.png\") center/cover;
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    '>
        <div class='modern-alert-icon' style='
            font-size: 3rem;
            color: #ff9800;
            margin-bottom: 20px;
        '>
            <i class='fas fa-clock'></i>
            </div>
            <div class='modern-alert-content'>
            <h3 style='
                color: #2c3e50;
                font-size: 24px;
                margin-bottom: 15px;
                font-weight: 600;
            '>Already Claimed</h3>
            <p style='
                color: #34495e;
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            '>You've already submitted the request claim form, it is currently pending for the approval of the Admin.</p>
            <div class='status-badge' style='
                display: inline-block;
                background: #fff3e0;
                padding: 8px 20px;
                border-radius: 20px;
                margin: 15px 0;
                box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
            '>
                <span style='
                    color: #f57c00;
                    font-weight: 600;
                    text-transform: uppercase;
                    font-size: 14px;
                '>Current Status: Pending</span>
            </div>
        </div>
    </div>";
    echo "<div class='modern-button-container' style='text-align: center; margin-top: 20px;'>
            <a href='dashboard.php' class='modern-button' style='
                display: inline-flex;
                align-items: center;
                padding: 12px 30px;
                background: linear-gradient(135deg, #ff9800, #f57c00);
                color: white;
                text-decoration: none;
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(255, 152, 0, 0.3);
            '>
                <i class='fas fa-home' style='margin-right: 10px;'></i>
                Return to Dashboard
            </a>
          </div>";
    include('../includes/footer.php');
    exit();
}

// Check if the item is already claimed by someone else
$check_other_claims_sql = "SELECT * FROM claim WHERE found_id = ? AND status IN ('pending', 'approved')";
$check_other_claims_stmt = $conn->prepare($check_other_claims_sql);
$check_other_claims_stmt->bind_param("i", $found_id);
$check_other_claims_stmt->execute();
$check_other_claims_result = $check_other_claims_stmt->get_result();

if ($check_other_claims_result->num_rows > 0) {
    echo "<div class='modern-alert modern-alert-error' style='
        background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), 
                   url(\"../images/BATANGAS-STATE-U_thumbnail.png\") center/cover;
        max-width: 600px;
        margin: 50px auto;
        padding: 30px;
        border-radius: 15px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        text-align: center;
    '>
        <div class='modern-alert-icon' style='
            font-size: 3rem;
            color: #e74c3c;
            margin-bottom: 20px;
        '>
            <i class='fas fa-exclamation-circle'></i>
        </div>
        <div class='modern-alert-content'>
            <h3 style='
                color: #2c3e50;
                font-size: 24px;
                margin-bottom: 15px;
                font-weight: 600;
            '>Item Already Claimed</h3>
            <p style='
                color: #34495e;
                font-size: 16px;
                line-height: 1.6;
                margin-bottom: 20px;
            '>Item has already been claimed by someone else.</p>
        </div>
    </div>";
    echo "<div class='modern-button-container' style='text-align: center; margin-top: 20px;'>
            <a href='dashboard.php' class='modern-button' style='
                display: inline-flex;
                align-items: center;
                padding: 12px 30px;
                background: linear-gradient(135deg, #e74c3c, #c0392b);
                color: white;
                text-decoration: none;
                border-radius: 25px;
                font-weight: 600;
                transition: all 0.3s ease;
                box-shadow: 0 4px 15px rgba(231, 76, 60, 0.3);
            '>
                <i class='fas fa-home' style='margin-right: 10px;'></i>
                Return to Dashboard
            </a>
          </div>";
    include('../includes/footer.php');
    exit();
}

// Fetch user information
$user_sql = "SELECT * FROM user WHERE user_id = ?";
$user_stmt = $conn->prepare($user_sql);
$user_stmt->bind_param("i", $_SESSION['user_id']);
$user_stmt->execute();
$user_result = $user_stmt->get_result();
$user = $user_result->fetch_assoc();

// Process form submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $unique_features = $_POST['unique_features'];
    $claimant_name = $_POST['claimant_name'];
    $claimant_email = $_POST['claimant_email'];
    $claimant_phone = $_POST['claimant_phone'];
    $user_id = $_SESSION['user_id'];
    
    // Handle proof image upload
    $proof_image = null;
    if (!empty($_FILES['proof_image']['name'])) {
        $target_dir = "../uploads/proof_images/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['proof_image']['name'], PATHINFO_EXTENSION);
        $unique_filename = "proof_" . uniqid() . '.' . $file_extension;
        $proof_image = $unique_filename; // Store only filename in database
        $target_file = $target_dir . $unique_filename;
        
        // Debug information
        error_log("Uploading proof image to: " . $target_file);
        
        // Check if file is an actual image
        $check = getimagesize($_FILES["proof_image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES['proof_image']['tmp_name'], $target_file)) {
                error_log("Proof image uploaded successfully to: " . $target_file);
            } else {
                error_log("Failed to upload proof image to: " . $target_file);
                $proof_image = null; // Reset if upload failed
            }
        } else {
            error_log("File is not an image.");
            $proof_image = null; // Reset if not an image
        }
    }
    
    // Start transaction to ensure atomicity
    $conn->begin_transaction();
    
    try {
        // Double-check if the item is still available for claiming
        $check_availability_sql = "SELECT status FROM found_item WHERE found_id = ? AND status = 'unclaimed'";
        $check_availability_stmt = $conn->prepare($check_availability_sql);
        $check_availability_stmt->bind_param("i", $found_id);
        $check_availability_stmt->execute();
        $availability_result = $check_availability_stmt->get_result();
        
        if ($availability_result->num_rows === 0) {
            // If the item is not 'unclaimed', try to update it to 'unclaimed' first
            $update_to_unclaimed_sql = "UPDATE found_item SET status = 'unclaimed' WHERE found_id = ?";
            $update_to_unclaimed_stmt = $conn->prepare($update_to_unclaimed_sql);
            $update_to_unclaimed_stmt->bind_param("i", $found_id);
            $update_to_unclaimed_stmt->execute();
            
            // Check again if the item is now available
            $check_availability_stmt->execute();
            $availability_result = $check_availability_stmt->get_result();
            
            if ($availability_result->num_rows === 0) {
                throw new Exception("This item is no longer available for claiming.");
            }
        }
        
        // Double-check if the user already has a claim for this item
        $check_duplicate_sql = "SELECT * FROM claim WHERE found_id = ? AND user_id = ?";
        $check_duplicate_stmt = $conn->prepare($check_duplicate_sql);
        $check_duplicate_stmt->bind_param("ii", $found_id, $user_id);
        $check_duplicate_stmt->execute();
        $duplicate_result = $check_duplicate_stmt->get_result();
        
        if ($duplicate_result->num_rows > 0) {
            throw new Exception("You've already submitted a claim for this item.");
        }
        
        // Insert claim request
        $claim_sql = "INSERT INTO claim (found_id, user_id, claimant_name, claimant_email, claimant_phone, unique_features, proof_description, proof_image, status, created_at) 
                      VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending', NOW())";
        $claim_stmt = $conn->prepare($claim_sql);
        if ($claim_stmt === false) {
            throw new Exception("Error preparing statement: " . $conn->error);
        }
        $claim_stmt->bind_param("iissssss", $found_id, $user_id, $claimant_name, $claimant_email, $claimant_phone, $unique_features, $unique_features, $proof_image);
        
        if ($claim_stmt->execute()) {
            // Update found item status to 'pending'
            $update_sql = "UPDATE found_item SET status = 'pending' WHERE found_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $found_id);
            $update_stmt->execute();
            
            // Commit the transaction
            $conn->commit();
            
            // Redirect to dashboard with success message
            $_SESSION['success_message'] = "Your claim has been submitted successfully. Please wait for admin approval.";
            header("Location: dashboard.php");
            exit();
        } else {
            $conn->rollback();
            echo "<div class='alert alert-danger'>Error submitting claim: " . $claim_stmt->error . "</div>";
        }
    } catch (Exception $e) {
        $conn->rollback();
        echo "<div class='alert alert-danger'>Error: " . $e->getMessage() . "</div>";
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Claim Item - Lost & Found System</title>
    <style>
        body {
            font-family: Arial, sans-serif;
            background-color: #f8f9fa;
            margin: 0;
            padding: 0;
        }
        
        .container {
            max-width: 800px;
            margin: 0 auto;
            padding: 20px;
        }
        
        .back-btn {
            display: inline-flex;
            align-items: center;
            padding: 10px 20px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            text-decoration: none;
            border-radius: 5px;
            margin-bottom: 20px;
            font-weight: 600;
            transition: all 0.3s ease;
        }
        
        .back-btn:hover {
            background: linear-gradient(135deg, #2980b9, #1c6ea4);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        h2 {
            color: #2c3e50;
            margin-bottom: 30px;
            text-align: center;
        }
        
        .form-container {
            background: white;
            border-radius: 10px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            padding: 30px;
            margin-bottom: 30px;
        }
        
        .form-section {
            margin-bottom: 30px;
            padding-bottom: 20px;
            border-bottom: 1px solid #eee;
        }
        
        .form-section:last-child {
            border-bottom: none;
        }
        
        .form-section h3 {
            color: #3498db;
            margin-bottom: 20px;
            font-size: 20px;
        }
        
        .item-details, .user-details {
            background: #f8f9fa;
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .item-details p, .user-details p {
            margin: 10px 0;
            color: #34495e;
        }
        
        .form-group {
            margin-bottom: 20px;
        }
        
        label {
            display: block;
            margin-bottom: 8px;
            font-weight: 600;
            color: #2c3e50;
        }
        
        .required-field::after {
            content: " *";
            color: #e74c3c;
        }
        
        .form-control {
            width: 100%;
            padding: 12px;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 16px;
            transition: border-color 0.3s;
        }
        
        .form-control:focus {
            border-color: #3498db;
            outline: none;
            box-shadow: 0 0 0 3px rgba(52, 152, 219, 0.2);
        }
        
        textarea.form-control {
            min-height: 120px;
            resize: vertical;
        }
        
        small {
            display: block;
            margin-top: 5px;
            color: #7f8c8d;
            font-size: 14px;
        }
        
        .btn-submit {
            display: block;
            width: 100%;
            padding: 15px;
            background: linear-gradient(135deg, #3498db, #2980b9);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 16px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
        }
        
        .btn-submit:hover {
            background: linear-gradient(135deg, #2980b9, #1c6ea4);
            transform: translateY(-2px);
            box-shadow: 0 4px 8px rgba(0, 0, 0, 0.1);
        }
        
        .alert {
            padding: 15px;
            border-radius: 5px;
            margin-bottom: 20px;
        }
        
        .alert-danger {
            background-color: #f8d7da;
            color: #721c24;
            border: 1px solid #f5c6cb;
        }
        
        .modern-alert {
            background: linear-gradient(rgba(255, 255, 255, 0.9), rgba(255, 255, 255, 0.9)), 
                       url("../images/BATANGAS-STATE-U_thumbnail.png") center/cover;
            max-width: 600px;
            margin: 50px auto;
            padding: 30px;
            border-radius: 15px;
            box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
            text-align: center;
        }
        
        .modern-alert-icon {
            font-size: 3rem;
            margin-bottom: 20px;
        }
        
        .modern-alert-content h3 {
            color: #2c3e50;
            font-size: 24px;
            margin-bottom: 15px;
            font-weight: 600;
        }
        
        .modern-alert-content p {
            color: #34495e;
            font-size: 16px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        
        .status-badge {
            display: inline-block;
            padding: 8px 20px;
            border-radius: 20px;
            margin: 15px 0;
            box-shadow: 0 2px 5px rgba(0, 0, 0, 0.05);
        }
        
        .modern-alert-warning .modern-alert-icon {
            color: #ff9800;
        }
        
        .modern-alert-warning .status-badge {
            background: #fff3e0;
        }
        
        .modern-alert-warning .status-badge span {
            color: #f57c00;
        }
        
        .modern-alert-error .modern-alert-icon {
            color: #e74c3c;
        }
        
        .modern-alert-error .status-badge {
            background: #fde8e8;
        }
        
        .modern-alert-error .status-badge span {
            color: #e74c3c;
        }
        
        .modern-alert-success .modern-alert-icon {
            color: #48bb78;
        }
        
        .modern-alert-success .status-badge {
            background: #e6ffed;
        }
        
        .modern-alert-success .status-badge span {
            color: #48bb78;
        }
    </style>
</head>
<body>
    <div class="container">
        <a href="dashboard.php" class="back-btn">Back to Dashboard</a>
        
        <h2>Claim Item</h2>
        
        <div class="form-container">
            <form action="claim_form.php?found_id=<?php echo $found_id; ?>" method="POST" enctype="multipart/form-data">
                <!-- Item Details Section -->
                <div class="form-section">
                    <h3>Item Details</h3>
                    <div class="item-details">
                        <p><strong>Category:</strong> <?php echo htmlspecialchars($item['category']); ?></p>
                        <p><strong>Description:</strong> <?php echo htmlspecialchars($item['description']); ?></p>
                        <p><strong>Found at:</strong> <?php echo htmlspecialchars($item['location']); ?></p>
                        <p><strong>Date Found:</strong> <?php echo htmlspecialchars($item['date_found']); ?></p>
                    </div>
                </div>
                
                <!-- Claimant Information Section -->
                <div class="form-section">
                    <h3>Claimant Information</h3>
                    <div class="user-details">
                        <p><strong>Name:</strong> <?php echo htmlspecialchars($user['name']); ?></p>
                        <p><strong>Email:</strong> <?php echo htmlspecialchars($user['email']); ?></p>
                        <p><strong>Phone:</strong> <?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : 'Not provided'; ?></p>
                    </div>
                </div>
                
                <!-- Proof of Ownership Section -->
                <div class="form-section">
                    <h3>Proof of Ownership</h3>
                    
                    <div class="form-group">
                        <label for="unique_features" class="required-field">Describe Unique Features of the Item:</label>
                        <textarea id="unique_features" name="unique_features" class="form-control" required></textarea>
                        <small>Please provide specific details about unique features, markings, or damage that would help identify this as your item.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="proof_image">Upload Proof Images:</label>
                        <input type="file" id="proof_image" name="proof_image" class="form-control" accept="image/*">
                        <small>Upload receipts, old photos, or any other images that can help prove ownership.</small>
                    </div>
                </div>
                
                <!-- Contact Information Section -->
                <div class="form-section">
                    <h3>Contact Information</h3>
                    
                    <div class="form-group">
                        <label for="claimant_name" class="required-field">Your Name:</label>
                        <input type="text" id="claimant_name" name="claimant_name" class="form-control" value="<?php echo htmlspecialchars($user['name']); ?>" required readonly>
                        <small>This information is taken from your account profile.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="claimant_email" class="required-field">Your Email Address:</label>
                        <input type="email" id="claimant_email" name="claimant_email" class="form-control" value="<?php echo htmlspecialchars($user['email']); ?>" required readonly>
                        <small>This information is taken from your account profile.</small>
                    </div>
                    
                    <div class="form-group">
                        <label for="claimant_phone" class="required-field">Your Phone Number:</label>
                        <input type="tel" id="claimant_phone" name="claimant_phone" class="form-control" value="<?php echo isset($user['phone']) ? htmlspecialchars($user['phone']) : ''; ?>" required>
                        <small>Please provide a phone number where you can be reached.</small>
                    </div>
                </div>
                
                <button type="submit" class="btn-submit">Submit Claim</button>
            </form>
        </div>
    </div>
    
    <?php include('../includes/footer.php'); ?>
</body>
</html> 