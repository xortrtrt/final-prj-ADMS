<?php
class Database {
    private $host = "localhost";
    private $db_name = "my_project_db";
    private $username = "root";
    private $password = "";
    private $conn = null;

    public function getConnection() {
        try {
            if ($this->conn === null) {
                $this->conn = new PDO(
                    "mysql:host=" . $this->host . ";dbname=" . $this->db_name,
                    $this->username,
                    $this->password
                );
                $this->conn->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
            }
            return $this->conn;
        } catch(PDOException $e) {
            echo "Connection error: " . $e->getMessage();
            return null;
        }
    }
}
?>
