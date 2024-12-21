<?php
require_once __DIR__ . '/../config/Database.php';

class Logger {
    private $conn;

    public function __construct() {
        $this->conn = Database::getConnection();
    }

    public function logs($userId, $action) {
        $query = "INSERT INTO logs (user_id, action) VALUES (:user_id, :action)";
        $stmt = $this->conn->prepare($query);

        $stmt->bindParam(':user_id', $userId);
        $stmt->bindParam(':action', $action);

        return $stmt->execute();
    }
}