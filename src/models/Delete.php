<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';

class Delete {
    private $conn;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }

    public function execute($table, $id, $conditions = []) {
        try {
            // Start transaction
            $this->conn->beginTransaction();

            // Build query
            $query = "DELETE FROM $table WHERE id = :id";
            
            // Add additional conditions if any
            if (!empty($conditions)) {
                $query .= " AND " . implode(' AND ', $conditions);
            }

            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            // Execute and check if any rows were affected
            $stmt->execute();
            $rowCount = $stmt->rowCount();

            if ($rowCount === 0) {
                $this->conn->rollBack();
                return [
                    'success' => false,
                    'error' => 'Record not found or could not be deleted'
                ];
            }

            // Commit transaction
            $this->conn->commit();

            // Log the action
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'delete',
                $table,
                $id
            );

            return [
                'success' => true,
                'message' => 'Record deleted successfully'
            ];
        } catch (PDOException $e) {
            $this->conn->rollBack();
            error_log("Delete Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to delete record'
            ];
        }
    }

    public function softDelete($table, $id) {
        try {
            $query = "UPDATE $table SET deleted_at = CURRENT_TIMESTAMP WHERE id = :id";
            $stmt = $this->conn->prepare($query);
            $stmt->bindParam(':id', $id);
            
            $stmt->execute();

            // Log the soft delete
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'soft_delete',
                $table,
                $id
            );

            return [
                'success' => true,
                'message' => 'Record soft deleted successfully'
            ];
        } catch (PDOException $e) {
            error_log("Soft Delete Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to soft delete record'
            ];
        }
    }
}