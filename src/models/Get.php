<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';

class Get {
    private $conn;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }

    public function execute($table, $id = null, $joins = [], $conditions = []) {
        try {
            $query = "SELECT * FROM " . $table;

            // Add any JOIN clauses
            foreach ($joins as $join) {
                $query .= " " . $join;
            }

            // Add WHERE clause if ID is provided or conditions exist
            if ($id) {
                $query .= " WHERE $table.id = :id";
            } elseif (!empty($conditions)) {
                $query .= " WHERE " . implode(' AND ', $conditions);
            }

            $stmt = $this->conn->prepare($query);

            // Bind ID if provided
            if ($id) {
                $stmt->bindParam(':id', $id);
            }

            $stmt->execute();
            $result = $id ? $stmt->fetch() : $stmt->fetchAll();

            // Log the action
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'read',
                $table,
                $id
            );

            return [
                'success' => true,
                'data' => $result
            ];
        } catch (PDOException $e) {
            error_log("Get Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to retrieve data'
            ];
        }
    }

    public function search($table, $searchTerm, $fields) {
        try {
            $conditions = [];
            $params = [];
            
            foreach ($fields as $field) {
                $conditions[] = "$field LIKE :search_$field";
                $params["search_$field"] = "%$searchTerm%";
            }

            $query = "SELECT * FROM $table WHERE " . implode(' OR ', $conditions);
            $stmt = $this->conn->prepare($query);
            
            foreach ($params as $key => $value) {
                $stmt->bindValue($key, $value);
            }

            $stmt->execute();
            $result = $stmt->fetchAll();

            // Log the search
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'search',
                $table,
                null
            );

            return [
                'success' => true,
                'data' => $result
            ];
        } catch (PDOException $e) {
            error_log("Search Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Search failed'
            ];
        }
    }
}