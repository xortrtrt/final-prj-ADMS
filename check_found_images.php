<?php
require_once('config/db.php');

// Check found items with images
$sql = "SELECT found_id, image FROM found_item WHERE image IS NOT NULL AND image != ''";
$result = $conn->query($sql);

echo "<h2>Found Items with Images</h2>";
if ($result && $result->num_rows > 0) {
    echo "<table border='1'>";
    echo "<tr><th>Found ID</th><th>Image Path</th><th>File Exists</th></tr>";
    
    while ($row = $result->fetch_assoc()) {
        $image_path = "uploads/found_items/" . $row['image'];
        $file_exists = file_exists($image_path) ? "Yes" : "No";
        
        echo "<tr>";
        echo "<td>" . $row['found_id'] . "</td>";
        echo "<td>" . $row['image'] . "</td>";
        echo "<td>" . $file_exists . "</td>";
        echo "</tr>";
    }
    
    echo "</table>";
} else {
    echo "<p>No found items with images found in the database.</p>";
}

// Check if the uploads/found_items directory exists and is writable
$dir = "uploads/found_items";
echo "<h2>Directory Check</h2>";
echo "<p>Directory exists: " . (file_exists($dir) ? "Yes" : "No") . "</p>";
echo "<p>Directory is writable: " . (is_writable($dir) ? "Yes" : "No") . "</p>";

// List files in the uploads/found_items directory
echo "<h2>Files in uploads/found_items</h2>";
if (file_exists($dir)) {
    $files = scandir($dir);
    if (count($files) > 2) { // More than . and ..
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                echo "<li>" . $file . "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>No files found in the directory.</p>";
    }
}
?> 