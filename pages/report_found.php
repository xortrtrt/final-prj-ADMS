<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
require_once('../config/db.php');

// Check if lost_id is provided (for "Mark as Found" functionality)
$lost_id = isset($_GET['lost_id']) ? intval($_GET['lost_id']) : 0;
$lost_item = null;

if ($lost_id > 0) {
    // Fetch the lost item details
    $lost_sql = "SELECT * FROM lost_item WHERE lost_id = ?";
    $lost_stmt = $conn->prepare($lost_sql);
    $lost_stmt->bind_param("i", $lost_id);
    $lost_stmt->execute();
    $lost_result = $lost_stmt->get_result();
    
    if ($lost_result->num_rows > 0) {
        $lost_item = $lost_result->fetch_assoc();
    }
}

// Handle form submission
if (isset($_POST['submit_found'])) {
    $category = trim($_POST['category']);
    // If "Other" was selected, use the custom category value
    if ($category === "Other" && !empty($_POST['custom_category'])) {
        $category = trim($_POST['custom_category']);
    }
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $date_found = $_POST['date_found'];
    $reporter_name = trim($_POST['reporter_name']);
    $reporter_email = trim($_POST['reporter_email']);
    $reporter_phone = trim($_POST['reporter_phone']);
    $status = trim($_POST['status']);
    $user_id = $_SESSION['user_id'];

    // Handle optional image upload
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/found_items/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            if (!mkdir($target_dir, 0777, true)) {
                error_log("Failed to create directory: " . $target_dir);
                echo "<script>alert('Error: Failed to create upload directory. Please contact administrator.');</script>";
            }
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('found_') . '.' . $file_extension;
        $image_path = $unique_filename; // Store only filename in database
        $target_file = $target_dir . $unique_filename;
        
        // Debug information
        error_log("Uploading image to: " . $target_file);
        
        // Check if file is an actual image
        $check = getimagesize($_FILES["image"]["tmp_name"]);
        if($check !== false) {
            if (move_uploaded_file($_FILES['image']['tmp_name'], $target_file)) {
                error_log("Image uploaded successfully to: " . $target_file);
            } else {
                error_log("Failed to upload image to: " . $target_file);
                $image_path = null; // Reset if upload failed
                echo "<script>alert('Error: Failed to upload image. Please try again.');</script>";
            }
        } else {
            error_log("File is not an image.");
            $image_path = null; // Reset if not an image
            echo "<script>alert('Error: File is not a valid image. Please upload an image file.');</script>";
        }
    }

    // Insert into the database
    $sql = "INSERT INTO found_item (user_id, category, description, location, date_found, status, reporter_name, reporter_email, reporter_phone, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
    $stmt->bind_param("isssssssss", $user_id, $category, $description, $location, $date_found, $status, $reporter_name, $reporter_email, $reporter_phone, $image_path);

    if ($stmt->execute()) {
        // If this was a "Mark as Found" action, update the lost item status
        if ($lost_id > 0) {
            $update_sql = "UPDATE lost_item SET status = 'found' WHERE lost_id = ?";
            $update_stmt = $conn->prepare($update_sql);
            $update_stmt->bind_param("i", $lost_id);
            $update_stmt->execute();
        }
        
        echo "<script>alert('Found item reported successfully!'); window.location='dashboard.php';</script>";
    } else {
        echo "<script>alert('Error: " . $conn->error . "');</script>";
    }
}
?>

<style>
    .form-section {
        background-image: url('../assets/images/bsu-facade-web-slider.jpg');
        background-size: cover;
        background-position: center;
        position: relative;
        padding: 2rem 0;
    }

    .form-section::before {
        content: '';
        position: absolute;
        top: 0;
        left: 0;
        right: 0;
        bottom: 0;
        background: rgba(0, 0, 0, 0.15);
        backdrop-filter: blur(15px);
        -webkit-backdrop-filter: blur(15px);
        z-index: 0;
    }

    .form-container {
        position: relative;
        z-index: 1;
        background: rgba(255, 255, 255, 0.95);
        padding: 2rem;
        border-radius: 15px;
        box-shadow: 0 4px 6px rgba(0, 0, 0, 0.1);
        max-width: 800px;
        margin: 0 auto;
    }

    h2 {
        color: white;
        text-align: center;
        margin-bottom: 1rem;
        font-weight: bold;
        text-transform: uppercase;
        letter-spacing: 1px;
        position: relative;
        z-index: 1;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        display: flex;
        align-items: center;
        justify-content: center;
        gap: 10px;
    }

    .thank-you-message {
        color: white;
        text-align: center;
        margin-bottom: 2rem;
        position: relative;
        z-index: 1;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
        font-size: 1.1em;
    }

    .thank-you-message .user-name {
        font-weight: bold;
        color: #FFD700;
    }

    .btn-primary {
        background-color: #CC0000;
        border: none;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
        padding: 10px 20px;
    }

    .btn-primary:hover {
        background-color: #990000;
    }

    .btn-secondary {
        background-color: #CC0000;
        border: none;
        color: white;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        font-weight: 500;
        padding: 8px 15px;
        text-decoration: none;
        display: inline-flex;
        align-items: center;
        gap: 8px;
    }

    .btn-secondary:hover {
        background-color: #990000;
        color: white;
    }

    .form-group {
        margin-bottom: 1.5rem;
    }

    .form-control, .form-select {
        border: 1px solid #ddd;
        border-radius: 8px;
        padding: 10px;
    }

    .form-control:focus, .form-select:focus {
        border-color: #CC0000;
        box-shadow: 0 0 0 0.2rem rgba(204, 0, 0, 0.25);
    }
</style>

<section class="form-section">
    <h2><i class="far fa-star" style="color: #FFD700;"></i> Report Found Item</h2>
    <div class="thank-you-message">
        Thank you for helping the community find their important things, <span class="user-name"><?php echo htmlspecialchars($_SESSION['name']); ?></span>!
    </div>
    <div class="form-container">
        <!-- Back to Dashboard Button -->
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <?php if ($lost_item): ?>
        <div class="alert alert-info">
            <h4>Marking Lost Item as Found</h4>
            <p>You are marking the following lost item as found:</p>
            <ul>
                <li><strong>Category:</strong> <?php echo htmlspecialchars($lost_item['category']); ?></li>
                <li><strong>Description:</strong> <?php echo htmlspecialchars($lost_item['description']); ?></li>
                <li><strong>Lost Location:</strong> <?php echo htmlspecialchars($lost_item['location']); ?></li>
                <li><strong>Date Lost:</strong> <?php echo htmlspecialchars($lost_item['date_lost']); ?></li>
            </ul>
        </div>
        <?php endif; ?>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Category</label>
                <select name="category" id="category" class="form-select" required onchange="toggleCustomCategory()">
                    <option value="">Select Category</option>
                    <?php if ($lost_item): ?>
                        <option value="<?php echo htmlspecialchars($lost_item['category']); ?>" selected><?php echo htmlspecialchars($lost_item['category']); ?></option>
                    <?php else: ?>
                        <option value="Electronics">Electronics</option>
                        <option value="Books">Books</option>
                        <option value="ID Cards">ID Cards</option>
                        <option value="Bags">Bags</option>
                        <option value="Clothing">Clothing</option>
                        <option value="Jewelry">Jewelry</option>
                        <option value="Other">Other (Please specify)</option>
                    <?php endif; ?>
                </select>
            </div>
            
            <div class="form-group" id="customCategoryGroup" style="display: none;">
                <label>Specify Category</label>
                <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Enter custom category">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" placeholder="Description (e.g., Blue wallet)" required><?php echo $lost_item ? htmlspecialchars($lost_item['description']) : ''; ?></textarea>
            </div>
            <div class="form-group">
                <label>Found Location</label>
                <input type="text" name="location" class="form-control" placeholder="Where did you find it?" required>
            </div>
            <div class="form-group">
                <label>Date Found</label>
                <input type="date" name="date_found" class="form-control" value="<?php echo date('Y-m-d'); ?>" required>
            </div>
            <div class="form-group">
                <label>Upload Image (Optional)</label>
                <input type="file" name="image" class="form-control" accept="image/*">
            </div>
            <div class="form-group">
                <label>Contact Information</label>
                <input type="text" name="reporter_name" class="form-control" placeholder="Your Name" required>
                <input type="email" name="reporter_email" class="form-control" placeholder="Your Email" required>
                <input type="tel" name="reporter_phone" class="form-control" placeholder="Your Phone Number" required>
            </div>
            <div class="form-group">
                <label>Current Status</label>
                <select name="status" class="form-select" required>
                    <option value="unclaimed">Unclaimed</option>
                    <option value="in_possession">I have the item</option>
                    <option value="turned_in">Turned in to lost and found</option>
                    <option value="left_in_place">Left in place where found</option>
                </select>
            </div>
            <div class="form-check">
                <input type="checkbox" name="terms" class="form-check-input" required>
                <label>I agree to the terms and conditions</label>
            </div>
            <button type="submit" name="submit_found" class="btn btn-primary">Submit Report</button>
        </form>
    </div> <!-- /.form-container -->
</section> <!-- /.form-section -->

<script>
function toggleCustomCategory() {
    const categorySelect = document.getElementById('category');
    const customCategoryGroup = document.getElementById('customCategoryGroup');
    
    if (categorySelect.value === 'Other') {
        customCategoryGroup.style.display = 'block';
        document.getElementById('custom_category').required = true;
    } else {
        customCategoryGroup.style.display = 'none';
        document.getElementById('custom_category').required = false;
    }
}

// Initialize the custom category toggle on page load
document.addEventListener('DOMContentLoaded', function() {
    toggleCustomCategory();
});
</script>

<?php include('../includes/footer.php'); ?>