<?php 
session_start();
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
require_once('../config/db.php');

// Handle form submission
if (isset($_POST['submit_lost'])) {
    $category = trim($_POST['category']);
    // If "Other" was selected, use the custom category value
    if ($category === "Other" && !empty($_POST['custom_category'])) {
        $category = trim($_POST['custom_category']);
    }
    $description = trim($_POST['description']);
    $location = trim($_POST['location']);
    $date_lost = $_POST['date_lost'];
    $reporter_name = trim($_POST['reporter_name']);
    $reporter_email = trim($_POST['reporter_email']);
    $reporter_phone = trim($_POST['reporter_phone']);
    $status = "pending"; // Default status for lost items
    $user_id = $_SESSION['user_id'];

    // Handle optional image upload
    $image_path = null;
    if (!empty($_FILES['image']['name'])) {
        $target_dir = "../uploads/lost_items/";
        // Create directory if it doesn't exist
        if (!file_exists($target_dir)) {
            mkdir($target_dir, 0777, true);
        }
        
        // Generate unique filename
        $file_extension = pathinfo($_FILES['image']['name'], PATHINFO_EXTENSION);
        $unique_filename = uniqid('lost_') . '.' . $file_extension;
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
            }
        } else {
            error_log("File is not an image.");
            $image_path = null; // Reset if not an image
        }
    }

    // Insert into the database
    $sql = "INSERT INTO lost_item (user_id, category, description, location, date_lost, status, reporter_name, reporter_email, reporter_phone, image) 
            VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
    $stmt = $conn->prepare($sql);
    if (!$stmt) {
        die("SQL error: " . $conn->error);
    }
    $stmt->bind_param("isssssssss", $user_id, $category, $description, $location, $date_lost, $status, $reporter_name, $reporter_email, $reporter_phone, $image_path);

    if ($stmt->execute()) {
        echo "<script>alert('Lost item reported successfully!'); window.location='dashboard.php';</script>";
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
    <h2><i class="far fa-star" style="color: #FFD700;"></i> Report Lost Item</h2>
    <div class="thank-you-message">
        We'll help you find your important things, <span class="user-name"><?php echo htmlspecialchars($_SESSION['name']); ?></span>!
    </div>
    <div class="form-container">
        <!-- Back to Dashboard Button at the top -->
        <div style="margin-bottom: 20px;">
            <a href="dashboard.php" class="btn btn-secondary">
                <i class="fas fa-arrow-left"></i> Back to Dashboard
            </a>
        </div>
        
        <form method="POST" enctype="multipart/form-data">
            <div class="form-group">
                <label>Category</label>
                <select name="category" id="category" class="form-select" required onchange="toggleCustomCategory()">
                    <option value="">Select Category</option>
                    <option value="Electronics">Electronics</option>
                    <option value="Books">Books</option>
                    <option value="ID Cards">ID Cards</option>
                    <option value="Bags">Bags</option>
                    <option value="Clothing">Clothing</option>
                    <option value="Jewelry">Jewelry</option>
                    <option value="Other">Other (Please specify)</option>
                </select>
            </div>
            
            <div class="form-group" id="customCategoryGroup" style="display: none;">
                <label>Specify Category</label>
                <input type="text" name="custom_category" id="custom_category" class="form-control" placeholder="Enter custom category">
            </div>
            
            <div class="form-group">
                <label>Description</label>
                <textarea name="description" class="form-control" placeholder="Description (e.g., Blue wallet)" required></textarea>
            </div>
            <div class="form-group">
                <label>Lost Location</label>
                <input type="text" name="location" class="form-control" placeholder="Where did you lose it?" required>
            </div>
            <div class="form-group">
                <label>Date Lost</label>
                <input type="date" name="date_lost" class="form-control" required>
            </div>
            <div class="form-group">
                <label>Contact Information</label>
                <input type="text" name="reporter_name" class="form-control" placeholder="Your Name" required>
                <input type="email" name="reporter_email" class="form-control" placeholder="Your Email" required>
                <input type="tel" name="reporter_phone" class="form-control" placeholder="Your Phone Number" required>
            </div>
            <div class="form-group">
                <label>Image</label>
                <input type="file" name="image" class="form-control">
            </div>
            <div class="form-check">
                <input type="checkbox" name="terms" class="form-check-input" required>
                <label>I agree to the terms and conditions</label>
            </div>
            <button type="submit" name="submit_lost" class="btn btn-primary">Submit Report</button>
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
</script>

<?php include('../includes/footer.php'); ?>