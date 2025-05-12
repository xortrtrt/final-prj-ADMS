<?php
require_once('config/db.php'); // Include the database connection
include 'includes/header.php'; // Include the header

// Get current user ID if logged in
$current_user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : 0;
?>

<!-- Hero Section -->
<section class="hero">
    <div class="container">
        <div class="hero-content">
            <h1>Lost Something? Found Something?</h1>
            <p>Our platform helps connect people who have lost items with those who have found them.</p>
            <div class="d-flex justify-content-center">
                <a href="pages/report_lost.php" class="btn btn-secondary btn-lg">Report Lost Item</a>
                <a href="pages/report_found.php" class="btn btn-outline btn-lg ml-3">Report Found Item</a>
            </div>
        </div>
    </div>
</section>

<?php
// Fetch recently lost items
$sql_lost = "SELECT * FROM lost_item ORDER BY date_lost DESC LIMIT 4";
$result_lost = $conn->query($sql_lost);

if (!$result_lost) {
    die("Error fetching lost items: " . $conn->error);
}

// Fetch recently found items
$sql_found = "SELECT * FROM found_item ORDER BY date_found DESC LIMIT 4";
$result_found = $conn->query($sql_found);

if (!$result_found) {
    die("Error fetching found items: " . $conn->error);
}
?>

<style>
    /* Hero Section */
    .hero {
        position: relative;
        padding: 100px 0;
        background-image: url('assets/images/campus-slider-main-2.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        margin-bottom: 0;
        width: 100%;
    }

    .hero::before {
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

    .hero .container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .hero-content {
        text-align: center;
        color: white;
        max-width: 800px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .hero-content h1 {
        font-size: 3.5em;
        margin-bottom: 20px;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
    }

    .hero-content p {
        font-size: 1.3em;
        margin-bottom: 30px;
        text-shadow: 1px 1px 3px rgba(0, 0, 0, 0.3);
    }

    /* Button Styles */
    .btn {
        padding: 12px 30px;
        border-radius: 5px;
        font-weight: 600;
        text-transform: uppercase;
        letter-spacing: 0.5px;
        transition: all 0.3s ease;
        display: inline-block;
        text-align: center;
        margin: 0 5px;
    }

    .btn-secondary {
        background-color: #CC0000;
        color: white;
        border: none;
    }

    .btn-outline {
        background-color: transparent;
        color: white;
        border: 2px solid white;
    }

    .btn-secondary:hover {
        background-color: #990000;
        transform: translateY(-2px);
    }

    .btn-outline:hover {
        background-color: white;
        color: #CC0000;
        transform: translateY(-2px);
    }

    /* Items Section */
    .section.items-section {
        position: relative;
        padding: 80px 0;
        margin: 0;
        background-image: url('assets/images/campus-slider-main-2.jpg');
        background-size: cover;
        background-position: center;
        background-attachment: fixed;
        width: 100%;
    }

    .section.items-section::before {
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

    .section.items-section .container {
        position: relative;
        z-index: 1;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
        padding: 0 15px;
    }

    .section-title {
        text-align: center;
        font-size: 2.5em;
        color: white;
        margin-bottom: 40px;
        font-weight: bold;
        text-shadow: 2px 2px 4px rgba(0, 0, 0, 0.3);
        padding: 0 15px;
    }

    /* Dashboard Search */
    .dashboard-search {
        max-width: 600px;
        margin: 0 auto 40px;
        padding: 0 15px;
    }

    .dashboard-search form {
        display: flex;
        gap: 10px;
        align-items: center;
    }

    .dashboard-search .form-control {
        padding: 12px 20px;
        border-radius: 5px;
        border: none;
        box-shadow: 0 2px 5px rgba(0, 0, 0, 0.1);
        flex: 1;
    }

    .dashboard-search .btn {
        margin: 0;
        white-space: nowrap;
    }

    /* Items Grid */
    .items-grid {
        display: grid;
        grid-template-columns: repeat(auto-fit, minmax(280px, 1fr));
        gap: 25px;
        padding: 20px 15px;
        width: 100%;
        max-width: 1200px;
        margin: 0 auto;
    }

    .item-card {
        background: rgba(255, 255, 255, 0.95);
        border-radius: 15px;
        padding: 25px;
        box-shadow: 0 4px 15px rgba(0, 0, 0, 0.1);
        transition: transform 0.3s ease;
        height: 100%;
        display: flex;
        flex-direction: column;
        justify-content: space-between;
        margin: 0;
    }

    .item-card:hover {
        transform: translateY(-5px);
    }

    .item-card h3 {
        color: #333;
        font-size: 1.4em;
        margin-bottom: 15px;
    }

    .item-card p {
        color: #666;
        margin-bottom: 15px;
        line-height: 1.6;
    }

    .item-card small {
        color: #888;
        display: block;
        margin-bottom: 15px;
    }

    /* Action Buttons */
    .item-actions {
        margin-top: auto;
    }

    .action-btn {
        display: block;
        padding: 10px 20px;
        border-radius: 5px;
        color: white;
        text-decoration: none;
        font-weight: 600;
        transition: all 0.3s ease;
        text-align: center;
        width: 100%;
    }

    .mark-found-btn {
        background-color: #28a745;
    }

    .mark-found-btn:hover {
        background-color: #1e7e34;
        transform: translateY(-2px);
    }

    .claim-btn {
        background-color: #007bff;
    }

    .claim-btn:hover {
        background-color: #0056b3;
        transform: translateY(-2px);
    }

    /* Responsive Design */
    @media (max-width: 768px) {
        .hero-content h1 {
            font-size: 2.5em;
        }

        .hero-content p {
            font-size: 1.1em;
        }

        .section-title {
            font-size: 2em;
        }

        .items-grid {
            grid-template-columns: 1fr;
            padding: 20px 15px;
        }

        .btn {
            padding: 10px 20px;
            font-size: 0.9em;
            margin: 5px;
        }

        .dashboard-search form {
            flex-direction: column;
        }

        .dashboard-search .btn {
            width: 100%;
        }
    }
</style>

<!-- Dashboard Section - Lost Items -->
<section class="section items-section">
    <div class="container">
        <h2 class="section-title">Recently Lost Items</h2>
        
        <div class="dashboard-search">
            <form action="pages/search.php" method="GET">
                <input type="hidden" name="type" value="lost">
                <input type="text" name="query" class="form-control" placeholder="Search lost items...">
                <button type="submit" class="btn">Search</button>
            </form>
        </div>
        
        <div class="items-grid">
            <?php while ($row = $result_lost->fetch_assoc()): ?>
                <div class="item-card">
                    <div>
                        <h3><?php echo htmlspecialchars($row['category']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <small>Lost on: <?php echo htmlspecialchars($row['date_lost']); ?></small>
                    </div>
                    <?php if ($current_user_id > 0 && $current_user_id != $row['user_id']): ?>
                        <div class="item-actions">
                            <a href="pages/report_found.php?lost_id=<?php echo $row['lost_id']; ?>" class="action-btn mark-found-btn">Mark as Found</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Dashboard Section - Found Items -->
<section class="section items-section">
    <div class="container">
        <h2 class="section-title">Recently Found Items</h2>
        
        <div class="items-grid">
            <?php while ($row = $result_found->fetch_assoc()): ?>
                <div class="item-card">
                    <div>
                        <h3><?php echo htmlspecialchars($row['category']); ?></h3>
                        <p><?php echo htmlspecialchars($row['description']); ?></p>
                        <small>Found on: <?php echo htmlspecialchars($row['date_found']); ?></small>
                    </div>
                    <?php if ($current_user_id > 0 && isset($_SESSION['email']) && $_SESSION['email'] != $row['reporter_email'] && $row['status'] == 'unclaimed'): ?>
                        <div class="item-actions">
                            <a href="pages/claim_form.php?found_id=<?php echo $row['found_id']; ?>" class="action-btn claim-btn">Claim</a>
                        </div>
                    <?php endif; ?>
                </div>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>