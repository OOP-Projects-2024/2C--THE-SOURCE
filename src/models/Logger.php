<?php
require_once __DIR__ . '/../config/Database.php';

class Logger {
    private $conn;
    private $table = 'logs';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function logs($userId, $action) {
        $query = "INSERT INTO logs (user_id, action) VALUES (:user_id, :action)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);

        return $stmt->execute();
    }

    // Add these new methods

    public function getLogCount() {
        $query = "SELECT COUNT(*) as total FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function getAllLogs($limit = 100, $offset = 0) {
        $query = "SELECT l.*, u.username 
                 FROM " . $this->table . " l
                 LEFT JOIN users u ON l.user_id = u.id
                 ORDER BY l.timestamp DESC
                 LIMIT :limit OFFSET :offset";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':limit', $limit, PDO::PARAM_INT);
        $stmt->bindParam(':offset', $offset, PDO::PARAM_INT);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogsByUser($userId) {
        $query = "SELECT l.*, u.username 
                 FROM " . $this->table . " l
                 LEFT JOIN users u ON l.user_id = u.id
                 WHERE l.user_id = :user_id
                 ORDER BY l.timestamp DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogsByDateRange($startDate, $endDate) {
        $query = "SELECT l.*, u.username 
                 FROM " . $this->table . " l
                 LEFT JOIN users u ON l.user_id = u.id
                 WHERE l.timestamp BETWEEN :start_date AND :end_date
                 ORDER BY l.timestamp DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':start_date', $startDate);
        $stmt->bindParam(':end_date', $endDate);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}