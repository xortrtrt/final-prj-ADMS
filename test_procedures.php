<?php
require_once 'includes/stored_procedures.php';

// Initialize the stored procedures class
$db = new Database();
$sp = new StoredProcedures($db->getConnection());

// Test getting dashboard stats
try {
    $stats = $sp->getDashboardStats();
    echo "<h2>Dashboard Statistics</h2>";
    echo "<pre>";
    print_r($stats);
    echo "</pre>";
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
