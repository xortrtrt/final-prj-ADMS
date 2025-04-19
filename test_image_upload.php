<?php
// Test script to verify image upload and viewing functionality
require_once('config/db.php');

// Create necessary directories if they don't exist
$directories = [
    'uploads',
    'uploads/lost_items',
    'uploads/found_items',
    'uploads/proof_images'
];

foreach ($directories as $dir) {
    if (!file_exists($dir)) {
        mkdir($dir, 0777, true);
        echo "Created directory: $dir<br>";
    } else {
        echo "Directory already exists: $dir<br>";
    }
}

// Check if directories are writable
foreach ($directories as $dir) {
    if (is_writable($dir)) {
        echo "Directory is writable: $dir<br>";
    } else {
        echo "Directory is NOT writable: $dir<br>";
    }
}

// Check database tables for image columns
$tables = ['lost_item', 'found_item', 'claim'];
foreach ($tables as $table) {
    $sql = "SHOW COLUMNS FROM $table LIKE 'image'";
    $result = $conn->query($sql);
    
    if ($result && $result->num_rows > 0) {
        echo "Table $table has an 'image' column<br>";
    } else {
        echo "Table $table does NOT have an 'image' column<br>";
    }
}

// Test image paths
$test_paths = [
    '../uploads/lost_items/test.jpg',
    '../uploads/found_items/test.jpg',
    '../uploads/proof_images/test.jpg'
];

foreach ($test_paths as $path) {
    $full_path = realpath($path);
    echo "Path: $path<br>";
    echo "Full path: $full_path<br>";
    echo "File exists: " . (file_exists($path) ? "Yes" : "No") . "<br><br>";
}

// Display sample images if they exist
echo "<h2>Sample Images</h2>";
$sample_images = [
    'uploads/lost_items' => glob('uploads/lost_items/*'),
    'uploads/found_items' => glob('uploads/found_items/*'),
    'uploads/proof_images' => glob('uploads/proof_images/*')
];

foreach ($sample_images as $dir => $images) {
    echo "<h3>$dir</h3>";
    if (count($images) > 0) {
        foreach ($images as $image) {
            echo "<div style='margin-bottom: 20px;'>";
            echo "<p>$image</p>";
            echo "<img src='$image' style='max-width: 200px; max-height: 200px;'><br>";
            echo "</div>";
        }
    } else {
        echo "<p>No images found in this directory.</p>";
    }
}

// Test database connection
if ($conn) {
    echo "<h2>Database Connection</h2>";
    echo "Database connection successful.<br>";
    echo "Server info: " . $conn->server_info . "<br>";
    echo "Host info: " . $conn->host_info . "<br>";
} else {
    echo "<h2>Database Connection</h2>";
    echo "Database connection failed.<br>";
}
?> 