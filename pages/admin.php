<?php 
session_start();
if (!isset($_SESSION['user_id']) || $_SESSION['role'] != 'admin') {
    header("Location: login.php");
    exit();
}

include('../includes/header.php'); 
require_once('../config/db.php');
require_once('../send_email.php');  
?>

<link rel="stylesheet" href="../assets/css/admin.css">

<div class="admin-dashboard">
    <h2>Admin Dashboard</h2>
    
    <!-- Tab Navigation -->
    <div class="tab-container">
        <div class="tabs">
            <button class="tab-btn active" data-tab="lost-items">Lost Items</button>
            <button class="tab-btn" data-tab="found-items">Found Items</button>
            <button class="tab-btn" data-tab="claim-requests">Claim Requests</button>
        </div>
        
        <!-- Lost Items Tab -->
        <div class="tab-content active" id="lost-items">
            <h3>Manage Lost Items</h3>
            <?php
            // Fetch all lost items
            $sql = "SELECT * FROM lost_item ORDER BY date_lost DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Location</th>
                                <th>Date Lost</th>
                                <th>Status</th>
                                <th>Reporter Info</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_lost']); ?></td>
                                    <td id="lost-status-<?php echo $row['lost_id']; ?>">
                                        <span class="status-badge <?php echo strtolower($row['status'] ?? 'pending'); ?>">
                                            <?php echo ucfirst($row['status'] ?? 'pending'); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <strong>Name:</strong> <?php echo htmlspecialchars($row['reporter_name']); ?><br>
                                        <strong>Email:</strong> <?php echo htmlspecialchars($row['reporter_email']); ?><br>
                                        <strong>Phone:</strong> <?php echo htmlspecialchars($row['reporter_phone']); ?>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['image'])): ?>
                                            <?php
                                            $image_path = "../uploads/lost_items/" . htmlspecialchars($row['image']);
                                            error_log("Lost item image path in database: " . $row['image']);
                                            error_log("Full lost item image path: " . $image_path);
                                            error_log("File exists: " . (file_exists($image_path) ? "Yes" : "No"));
                                            
                                            if (!file_exists($image_path)) {
                                                $alt_path = "../uploads/" . htmlspecialchars($row['image']);
                                                if (file_exists($alt_path)) {
                                                    $image_path = $alt_path;
                                                    error_log("Using alternative path for lost item: " . $image_path);
                                                }
                                            }
                                            ?>
                                            <button class="btn btn-info view-image-btn" data-image="<?php echo $image_path; ?>">View Image</button>
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="updateItemStatus('lost', 'approve', <?php echo $row['lost_id']; ?>)" class="btn btn-success">Approve</button>
                                            <button onclick="updateItemStatus('lost', 'reject', <?php echo $row['lost_id']; ?>)" class="btn btn-danger">Reject</button>
                                            <button onclick="updateItemStatus('lost', 'pending', <?php echo $row['lost_id']; ?>)" class="btn btn-warning">Mark as Pending</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No lost items to manage.</p>
            <?php endif; ?>
        </div>
        
        <!-- Found Items Tab -->
        <div class="tab-content" id="found-items">
            <h3>Manage Found Items</h3>
            <?php
            // Fetch all found items
            $sql = "SELECT * FROM found_item ORDER BY date_found DESC";
            $result = $conn->query($sql);

            if ($result && $result->num_rows > 0): ?>
                <div class="table-container">
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Category</th>
                                <th>Description</th>
                                <th>Location Found</th>
                                <th>Date Found</th>
                                <th>Status</th>
                                <th>Image</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            <?php while ($row = $result->fetch_assoc()): ?>
                                <tr>
                                    <td><?php echo htmlspecialchars($row['category']); ?></td>
                                    <td><?php echo htmlspecialchars($row['description']); ?></td>
                                    <td><?php echo htmlspecialchars($row['location']); ?></td>
                                    <td><?php echo htmlspecialchars($row['date_found']); ?></td>
                                    <td id="found-status-<?php echo $row['found_id']; ?>">
                                    <span class="status-badge <?= strtolower($row['status'] ?: 'unclaimed') ?>">
                                        <?= ucfirst($row['status'] ?: 'unclaimed') ?>
                                        </span>
                                    </td>
                                    <td>
                                        <?php if (!empty($row['image'])): ?>
                                            <?php
                                            $image_path = "../uploads/found_items/" . htmlspecialchars($row['image']);
                                            error_log("Image path in database: " . $row['image']);
                                            error_log("Full image path: " . $image_path);
                                            error_log("File exists: " . (file_exists($image_path) ? "Yes" : "No"));
                                            
                                            if (!file_exists($image_path)) {
                                                $alt_path = "../uploads/" . htmlspecialchars($row['image']);
                                                if (file_exists($alt_path)) {
                                                    $image_path = $alt_path;
                                                    error_log("Using alternative path: " . $image_path);
                                                } else {
                                                    $alt_path2 = "../uploads/" . basename(htmlspecialchars($row['image']));
                                                    if (file_exists($alt_path2)) {
                                                        $image_path = $alt_path2;
                                                        error_log("Using filename only path: " . $image_path);
                                                    }
                                                }
                                            }
                                            ?>
                                            <button class="btn btn-info view-image-btn" data-image="<?php echo $image_path; ?>">View Image</button>
                                        <?php else: ?>
                                            No Image
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button onclick="updateItemStatus('found', 'approve', <?php echo $row['found_id']; ?>)" class="btn btn-success">Approve</button>
                                            <button onclick="updateItemStatus('found', 'reject', <?php echo $row['found_id']; ?>)" class="btn btn-danger">Reject</button>
                                            <button onclick="updateItemStatus('found', 'pending', <?php echo $row['found_id']; ?>)" class="btn btn-warning">Mark as Pending</button>
                                        </div>
                                    </td>
                                </tr>
                            <?php endwhile; ?>
                        </tbody>
                    </table>
                </div>
            <?php else: ?>
                <p>No found items to manage.</p>
            <?php endif; ?>
        </div>
        
        <!-- Claim Requests Tab -->
<!-- Claim Requests Tab -->
<div class="tab-content" id="claim-requests">
    <h3>Manage Claim Requests</h3>
    <?php
    $sql = "SELECT c.*, f.category, f.description, f.location, f.date_found, u.name as finder_name 
            FROM claim c 
            LEFT JOIN found_item f ON c.found_id = f.found_id 
            LEFT JOIN user u ON f.user_id = u.user_id 
            ORDER BY c.date_claimed DESC";

    $result = $conn->query($sql);

    if ($result && $result->num_rows > 0): ?>
        <div class="table-container">
            <table class="table">
                <thead>
                    <tr>
                        <th>Item Details</th>
                        <th>Claimant Info</th>
                        <th>Proof Submitted</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php while ($row = $result->fetch_assoc()): 
                        $claimStatus = !empty($row['status']) ? strtolower($row['status']) : 'pending';
                        $statusText = ucfirst($claimStatus);
                        ?>
                        <tr>
                            <td>
                                <strong>Category:</strong> <?= htmlspecialchars($row['category'] ?? 'N/A') ?><br>
                                <strong>Description:</strong> <?= htmlspecialchars($row['description'] ?? 'N/A') ?><br>
                                <strong>Location:</strong> <?= htmlspecialchars($row['location'] ?? 'N/A') ?><br>
                                <strong>Date Found:</strong> <?= htmlspecialchars($row['date_found'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <strong>Name:</strong> <?= htmlspecialchars($row['claimant_name'] ?? 'N/A') ?><br>
                                <strong>Email:</strong> <?= htmlspecialchars($row['claimant_email'] ?? 'N/A') ?><br>
                                <strong>Phone:</strong> <?= htmlspecialchars($row['claimant_phone'] ?? 'N/A') ?><br>
                                <strong>Unique Features:</strong> <?= htmlspecialchars($row['unique_features'] ?? 'N/A') ?>
                            </td>
                            <td>
                                <?php if (!empty($row['proof_image'])): ?>
                                    <?php $proofImagePath = "../uploads/proof_images/" . basename($row['proof_image']); ?>
                                    <button class="btn btn-info view-image-btn" data-image="<?= htmlspecialchars($proofImagePath) ?>">View Image</button>
                                <?php else: ?>
                                    No Image
                                <?php endif; ?>
                            </td>
                            <td id="claim-status-<?= (int)$row['claim_id'] ?>">
                                <span class="status-badge <?= $claimStatus ?>">
                                    <?= $statusText ?>
                                </span>
                            </td>
                            <td>
                                <div class="action-buttons">
                                    <button onclick="updateClaimStatus('approve', <?= (int)$row['claim_id'] ?>)" class="btn btn-success">Approve</button>
                                    <button onclick="updateClaimStatus('reject', <?= (int)$row['claim_id'] ?>)" class="btn btn-danger">Reject</button>
                                </div>
                            </td>
                        </tr>
                    <?php endwhile; ?>
                </tbody>
            </table>
        </div>
    <?php else: ?>
        <p>No claim requests to manage.</p>
    <?php endif; ?>
</div>

<script>
    function updateClaimStatus(action, claim_id) {
        if (!action || !claim_id) {
            alert('Missing required parameters');
            return;
        }

        if (!confirm(`Are you sure you want to ${action} this claim?`)) {
            return;
        }

        const xhr = new XMLHttpRequest();
        xhr.open("GET", `process_claim.php?action=${action}&claim_id=${claim_id}`, true);
        
        xhr.onload = function () {
            if (xhr.status === 200) {
                try {
                    const response = JSON.parse(xhr.responseText);
                    if (response.success) {
                        // Update the status badge in the table
                        const statusCell = document.getElementById(`claim-status-${claim_id}`);
                        if (statusCell) {
                            const newStatus = action === 'approve' ? 'approved' : 'rejected';
                            statusCell.innerHTML = `
                                <span class="status-badge ${newStatus}">
                                    ${newStatus.charAt(0).toUpperCase() + newStatus.slice(1)}
                                </span>
                            `;
                            
                            // Add a visual effect
                            statusCell.classList.add("status-updated");
                            setTimeout(() => statusCell.classList.remove("status-updated"), 2000);
                            
                            // Disable the action buttons after status update
                            const actionButtons = statusCell.closest('tr').querySelector('.action-buttons');
                            if (actionButtons) {
                                actionButtons.innerHTML = '<span class="text-muted">Status Updated</span>';
                            }
                        }
                        
                        // Show success message
                        alert(response.message);
                    } else {
                        alert(response.message || 'An error occurred while updating the claim status.');
                    }
                } catch (e) {
                    alert('Error parsing server response.');
                }
            } else {
                alert("An error occurred while processing the request.");
            }
        };
        
        xhr.onerror = function () {
            alert("An error occurred while connecting to the server.");
        };
        
        xhr.send();
    }
</script>


    <!-- Back to Dashboard Button -->
    <a href="dashboard.php" class="btn btn-secondary mt-3">Back to Dashboard</a>
</div>

<!-- Image Modal -->
<div id="imageModal" class="modal">
    <div class="modal-content">
        <span class="close">&times;</span>
        <img id="modalImage" src="" alt="Item Image">
    </div>
</div>

<script>
    // Tab functionality
    document.addEventListener('DOMContentLoaded', function() {
        const tabBtns = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');
        
        tabBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                // Remove active class from all buttons and contents
                tabBtns.forEach(b => b.classList.remove('active'));
                tabContents.forEach(c => c.classList.remove('active'));
                
                // Add active class to clicked button and corresponding content
                this.classList.add('active');
                const tabId = this.getAttribute('data-tab');
                document.getElementById(tabId).classList.add('active');
            });
        });
        
        // Image modal functionality
        const modal = document.getElementById('imageModal');
        const modalImg = document.getElementById('modalImage');
        const closeBtn = document.querySelector('.close');
        const viewImageBtns = document.querySelectorAll('.view-image-btn');
        
        viewImageBtns.forEach(btn => {
            btn.addEventListener('click', function() {
                modal.style.display = 'block';
                modalImg.src = this.getAttribute('data-image');
            });
        });
        
        closeBtn.addEventListener('click', function() {
            modal.style.display = 'none';
        });
        
        window.addEventListener('click', function(event) {
            if (event.target == modal) {
                modal.style.display = 'none';
            }
        });
    });
    
    // Function to update the status of lost/found items via AJAX
    function updateItemStatus(type, action, itemId) {
    if (!type || !action || !itemId) {
        alert('Missing required parameters');
        return;
    }

    const xhr = new XMLHttpRequest();
    xhr.open("GET", `process_item.php?action=${action}&type=${type}&item_id=${itemId}`, true);

    xhr.onload = function () {
        if (xhr.status === 200) {
            let response;
            try {
                response = JSON.parse(xhr.responseText);
            } catch (e) {
                alert("Invalid server response.");
                return;
            }

            if (response.success) {
                const statusCell = document.getElementById(`${type}-status-${itemId}`);
                if (statusCell) {
                    statusCell.innerHTML = `
                        <span class="status-badge ${response.new_status.toLowerCase()}">
                            ${response.new_status.charAt(0).toUpperCase() + response.new_status.slice(1)}
                        </span>
                    `;
                    // Add a visual effect
                    statusCell.classList.add("status-updated");
                    setTimeout(() => statusCell.classList.remove("status-updated"), 2000);
                }
            } else {
                alert(response.message || 'An error occurred while updating the status.');
            }
        } else {
            alert("An error occurred while processing the request.");
        }
    };

    xhr.onerror = function () {
        alert("An error occurred while connecting to the server.");
    };

    xhr.send();
}
</script>

<?php include('../includes/footer.php'); ?>