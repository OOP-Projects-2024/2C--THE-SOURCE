<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';

class Post {
    private $conn;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }

    public function execute($table, $data) {
        try {
            // Prepare column names and values
            $columns = implode(', ', array_keys($data));
            $values = ':' . implode(', :', array_keys($data));
            
            $query = "INSERT INTO $table ($columns) VALUES ($values)";
            $stmt = $this->conn->prepare($query);
            
            // Bind all values
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            
            $stmt->execute();
            $lastId = $this->conn->lastInsertId();

            // Log the action
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'create',
                $table,
                $lastId
            );

            return [
                'success' => true,
                'id' => $lastId,
                'message' => 'Record created successfully'
            ];
        } catch (PDOException $e) {
            error_log("Post Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to create record'
            ];
        }
    }

    public function validateData($data, $required_fields = []) {
        $missing = [];
        foreach ($required_fields as $field) {
            if (!isset($data[$field]) || empty($data[$field])) {
                $missing[] = $field;
            }
        }
        
        return [
            'valid' => empty($missing),
            'missing' => $missing
        ];
    }
}