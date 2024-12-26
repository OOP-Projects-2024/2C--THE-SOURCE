<?php
require_once __DIR__ . '/../config/Database.php';

class Logger {
    private $conn;
    private $table = 'logs';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    // Changed from 'logs' to 'log' for consistency
    public function log($userId, $action, $entityType = null, $entityId = null) {
        // Create logs directory if it doesn't exist
        $logDir = __DIR__ . '/../../logs';
        if (!file_exists($logDir)) {
            mkdir($logDir, 0777, true);
        }

        // Log to file
        $date = date('Y-m-d');
        $time = date('H:i:s');
        $logFile = $logDir . '/' . $date . '.log';
        $logMessage = sprintf(
            "[%s %s] User ID: %d - Action: %s - Entity: %s - ID: %s\n",
            $date,
            $time,
            $userId,
            $action,
            $entityType ?? 'N/A',
            $entityId ?? 'N/A'
        );
        file_put_contents($logFile, $logMessage, FILE_APPEND);

        // Log to database
        $query = "INSERT INTO logs (user_id, action, entity_type, entity_id) VALUES (:user_id, :action, :entity_type, :entity_id)";
        $stmt = $this->conn->prepare($query);

        return $stmt->execute([
            ':user_id' => $userId,
            ':action' => $action,
            ':entity_type' => $entityType,
            ':entity_id' => $entityId
        ]);
    }

    public function getLogs($limit = 100, $offset = 0) {
        $query = "SELECT l.*, u.username 
                 FROM logs l 
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
                 FROM logs l 
                 LEFT JOIN users u ON l.user_id = u.id 
                 WHERE l.user_id = :user_id 
                 ORDER BY l.timestamp DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':user_id', $userId);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getLogsByDate($date) {
        $query = "SELECT l.*, u.username 
                 FROM logs l 
                 LEFT JOIN users u ON l.user_id = u.id 
                 WHERE DATE(l.timestamp) = :date 
                 ORDER BY l.timestamp DESC";
        
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(':date', $date);
        $stmt->execute();
        
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}