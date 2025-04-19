<?php
// Test script to upload a sample image to the found_items directory
require_once('config/db.php');

// Create the found_items directory if it doesn't exist
$target_dir = "uploads/found_items/";
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, true);
    echo "Created directory: $target_dir<br>";
} else {
    echo "Directory already exists: $target_dir<br>";
}

// Check if the directory is writable
if (is_writable($target_dir)) {
    echo "Directory is writable: $target_dir<br>";
} else {
    echo "Directory is NOT writable: $target_dir<br>";
    exit("Cannot proceed with test upload. Directory is not writable.");
}

// Create a sample image
$image = imagecreatetruecolor(100, 100);
$bg_color = imagecolorallocate($image, 255, 255, 255);
$text_color = imagecolorallocate($image, 0, 0, 0);

// Fill the background
imagefilledrectangle($image, 0, 0, 100, 100, $bg_color);

// Add text to the image
imagestring($image, 5, 10, 40, "Test Image", $text_color);

// Generate a unique filename
$filename = "test_found_" . uniqid() . ".jpg";
$target_file = $target_dir . $filename;

// Save the image
if (imagejpeg($image, $target_file)) {
    echo "Test image created successfully: $target_file<br>";
    
    // Insert a test record into the found_item table
    $sql = "INSERT INTO found_item (user_id, category, description, location, date_found, status, reporter_name, reporter_email, reporter_phone, image) 
            VALUES (1, 'Test', 'Test Found Item', 'Test Location', NOW(), 'pending', 'Test User', 'test@example.com', '1234567890', ?)";
    
    $stmt = $conn->prepare($sql);
    $stmt->bind_param("s", $filename);
    
    if ($stmt->execute()) {
        echo "Test record inserted into found_item table with image: $filename<br>";
        echo "Found ID: " . $conn->insert_id . "<br>";
    } else {
        echo "Error inserting test record: " . $conn->error . "<br>";
    }
} else {
    echo "Failed to create test image.<br>";
}

// Free up memory
imagedestroy($image);

echo "<p><a href='pages/admin.php'>Go to Admin Panel</a></p>";
?> 