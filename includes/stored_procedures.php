<?php
require_once 'db_connection.php';

class StoredProcedures {
    private $conn;

    public function __construct($connection) {
        $this->conn = $connection;
    }

    // Lost Item Management
    public function approveLostItem($lostId) {
        $stmt = $this->conn->prepare("CALL ApproveLostItem(?)");
        return $stmt->execute([$lostId]);
    }

    public function rejectLostItem($lostId) {
        $stmt = $this->conn->prepare("CALL RejectLostItem(?)");
        return $stmt->execute([$lostId]);
    }

    public function markLostItemAsPending($lostId) {
        $stmt = $this->conn->prepare("CALL MarkLostItemAsPending(?)");
        return $stmt->execute([$lostId]);
    }

    public function getLostItemsByStatus($status) {
        $stmt = $this->conn->prepare("CALL GetLostItemsByStatus(?)");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Found Item Management
    public function getFoundItemsByStatus($status) {
        $stmt = $this->conn->prepare("CALL GetFoundItemsByStatus(?)");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Claim Management
    public function approveClaim($claimId) {
        $stmt = $this->conn->prepare("CALL ApproveClaim(?)");
        return $stmt->execute([$claimId]);
    }

    public function rejectClaim($claimId) {
        $stmt = $this->conn->prepare("CALL RejectClaim(?)");
        return $stmt->execute([$claimId]);
    }

    public function getClaimsByStatus($status) {
        $stmt = $this->conn->prepare("CALL GetClaimsByStatus(?)");
        $stmt->execute([$status]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    // Dashboard Statistics
    public function getDashboardStats() {
        $stmt = $this->conn->prepare("CALL GetDashboardStats()");
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }
}

// Example usage:
/*
$db = new Database();
$sp = new StoredProcedures($db->getConnection());

// Get dashboard statistics
$stats = $sp->getDashboardStats();

// Approve a lost item
$sp->approveLostItem(1);

// Get all pending claims
$pendingClaims = $sp->getClaimsByStatus('pending');
*/
?>
