<?php
// Check if GD library is available
if (extension_loaded('gd')) {
    echo "GD library is available.<br>";
    echo "GD version: " . gd_info()['GD Version'] . "<br>";
} else {
    echo "GD library is NOT available. Please install the GD library to use image upload functionality.<br>";
}

// Check if the uploads directory exists and is writable
$uploads_dir = "uploads";
if (file_exists($uploads_dir)) {
    echo "Uploads directory exists.<br>";
    if (is_writable($uploads_dir)) {
        echo "Uploads directory is writable.<br>";
    } else {
        echo "Uploads directory is NOT writable.<br>";
    }
} else {
    echo "Uploads directory does NOT exist.<br>";
}

// Check if the found_items directory exists and is writable
$found_items_dir = "uploads/found_items";
if (file_exists($found_items_dir)) {
    echo "Found items directory exists.<br>";
    if (is_writable($found_items_dir)) {
        echo "Found items directory is writable.<br>";
    } else {
        echo "Found items directory is NOT writable.<br>";
    }
} else {
    echo "Found items directory does NOT exist.<br>";
}

// List files in the uploads directory
echo "<h2>Files in uploads directory:</h2>";
if (file_exists($uploads_dir)) {
    $files = scandir($uploads_dir);
    if (count($files) > 2) { // More than . and ..
        echo "<ul>";
        foreach ($files as $file) {
            if ($file != "." && $file != "..") {
                echo "<li>" . $file . "</li>";
            }
        }
        echo "</ul>";
    } else {
        echo "<p>No files found in the uploads directory.</p>";
    }
}
?> 