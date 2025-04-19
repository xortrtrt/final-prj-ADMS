DELIMITER $$

-- Stored procedure to approve a lost item
CREATE PROCEDURE IF NOT EXISTS ApproveLostItem(IN p_lost_id INT)
BEGIN
    UPDATE lost_item
    SET status = 'approved'
    WHERE lost_id = p_lost_id;
END$$

-- Stored procedure to reject a lost item
CREATE PROCEDURE IF NOT EXISTS RejectLostItem(IN p_lost_id INT)
BEGIN
    UPDATE lost_item
    SET status = 'rejected'
    WHERE lost_id = p_lost_id;
END$$

-- Stored procedure to mark a lost item as pending
CREATE PROCEDURE IF NOT EXISTS MarkLostItemAsPending(IN p_lost_id INT)
BEGIN
    UPDATE lost_item
    SET status = 'pending'
    WHERE lost_id = p_lost_id;
END$$

-- Stored procedure to get lost items by status
CREATE PROCEDURE IF NOT EXISTS GetLostItemsByStatus(IN p_status VARCHAR(20))
BEGIN
    SELECT 
        l.*,
        u.username,
        u.email
    FROM lost_item l
    JOIN user u ON l.user_id = u.user_id
    WHERE l.status = p_status;
END$$

-- Stored procedure to get found items by status
CREATE PROCEDURE IF NOT EXISTS GetFoundItemsByStatus(IN p_status VARCHAR(20))
BEGIN
    SELECT 
        f.*,
        u.username,
        u.email
    FROM found_item f
    JOIN user u ON f.user_id = u.user_id
    WHERE f.status = p_status;
END$$

-- Stored procedure to approve a claim
CREATE PROCEDURE IF NOT EXISTS ApproveClaim(IN p_claim_id INT)
BEGIN
    UPDATE claim
    SET status = 'approved'
    WHERE claim_id = p_claim_id;
END$$

-- Stored procedure to reject a claim
CREATE PROCEDURE IF NOT EXISTS RejectClaim(IN p_claim_id INT)
BEGIN
    UPDATE claim
    SET status = 'rejected'
    WHERE claim_id = p_claim_id;
END$$

-- Stored procedure to get claims by status
CREATE PROCEDURE IF NOT EXISTS GetClaimsByStatus(IN p_status VARCHAR(20))
BEGIN
    SELECT 
        c.*,
        u.username as claimant_username,
        u.email as claimant_email,
        f.item_name as found_item_name,
        f.location as found_location
    FROM claim c
    JOIN user u ON c.user_id = u.user_id
    LEFT JOIN found_item f ON c.found_id = f.found_id
    WHERE c.status = p_status;
END$$

-- Stored procedure to get dashboard statistics
CREATE PROCEDURE IF NOT EXISTS GetDashboardStats()
BEGIN
    SELECT
        (SELECT COUNT(*) FROM lost_item WHERE status = 'pending') as pending_lost_items,
        (SELECT COUNT(*) FROM lost_item WHERE status = 'approved') as approved_lost_items,
        (SELECT COUNT(*) FROM lost_item WHERE status = 'rejected') as rejected_lost_items,
        (SELECT COUNT(*) FROM found_item WHERE status = 'pending') as pending_found_items,
        (SELECT COUNT(*) FROM found_item WHERE status = 'approved') as approved_found_items,
        (SELECT COUNT(*) FROM found_item WHERE status = 'rejected') as rejected_found_items,
        (SELECT COUNT(*) FROM claim WHERE status = 'pending') as pending_claims,
        (SELECT COUNT(*) FROM claim WHERE status = 'approved') as approved_claims,
        (SELECT COUNT(*) FROM claim WHERE status = 'rejected') as rejected_claims;
END$$

DELIMITER ;
