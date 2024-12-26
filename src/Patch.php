<?php
require_once __DIR__ . '/../config/Database.php';
require_once __DIR__ . '/Logger.php';

class Patch {
    private $conn;
    private $logger;

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
        $this->logger = new Logger();
    }

    public function execute($table, $id, $data) {
        try {
            // Build SET clause
            $sets = [];
            foreach ($data as $key => $value) {
                $sets[] = "$key = :$key";
            }
            $setClause = implode(', ', $sets);

            // Prepare and execute query
            $query = "UPDATE $table SET $setClause WHERE id = :id";
            $stmt = $this->conn->prepare($query);

            // Bind all values
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }
            $stmt->bindValue(':id', $id);

            $stmt->execute();

            // Check if record was actually updated
            if ($stmt->rowCount() === 0) {
                return [
                    'success' => false,
                    'error' => 'Record not found or no changes made'
                ];
            }

            // Log the action
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'update',
                $table,
                $id
            );

            return [
                'success' => true,
                'message' => 'Record updated successfully'
            ];
        } catch (PDOException $e) {
            error_log("Patch Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to update record'
            ];
        }
    }

    public function validatePatch($data, $allowed_fields = []) {
        $invalid_fields = [];
        foreach ($data as $field => $value) {
            if (!in_array($field, $allowed_fields)) {
                $invalid_fields[] = $field;
            }
        }

        return [
            'valid' => empty($invalid_fields),
            'invalid_fields' => $invalid_fields
        ];
    }

    public function bulkUpdate($table, $ids, $data) {
        try {
            // Build SET clause
            $sets = [];
            foreach ($data as $key => $value) {
                $sets[] = "$key = :$key";
            }
            $setClause = implode(', ', $sets);

            // Create placeholders for IDs
            $idPlaceholders = implode(',', array_fill(0, count($ids), '?'));

            // Prepare and execute query
            $query = "UPDATE $table SET $setClause WHERE id IN ($idPlaceholders)";
            $stmt = $this->conn->prepare($query);

            // Bind update values
            foreach ($data as $key => $value) {
                $stmt->bindValue(":$key", $value);
            }

            // Bind IDs
            foreach ($ids as $index => $id) {
                $stmt->bindValue($index + 1, $id);
            }

            $stmt->execute();

            // Log the bulk update
            $userId = $_SESSION['user_id'] ?? 0;
            $this->logger->log(
                $userId,
                'bulk_update',
                $table,
                implode(',', $ids)
            );

            return [
                'success' => true,
                'message' => 'Records updated successfully',
                'affected_rows' => $stmt->rowCount()
            ];
        } catch (PDOException $e) {
            error_log("Bulk Update Error: " . $e->getMessage());
            return [
                'success' => false,
                'error' => 'Failed to update records'
            ];
        }
    }
}