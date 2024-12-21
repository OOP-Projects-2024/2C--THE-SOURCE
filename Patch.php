<?php
// File: src/Patch.php

require_once 'Common.php';

class Patch extends Common {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function updateProperty($propertyId, $data) {
        $setClauses = [];
        $params = [];
        
        $allowedFields = ['name', 'location', 'type', 'price', 'description'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $setClauses[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($setClauses)) {
            return $this->generateResponse([], "failed", "No valid fields to update.", 400);
        }

        $setClause = implode(", ", $setClauses);
        $sqlString = "UPDATE properties SET $setClause WHERE id = :propertyId";

        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(':propertyId', $propertyId, PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $stmt->bindParam($key, $value);
            }

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $this->generateResponse([], "success", "Property updated successfully.", 200);
            } else {
                return $this->generateResponse([], "failed", "No changes were made or property not found.", 404);
            }
        } catch (\PDOException $e) {
            return $this->generateResponse([], "failed", $e->getMessage(), 500);
        }
    }

    public function updateTenant($tenantId, $data) {
        $setClauses = [];
        $params = [];
        
        $allowedFields = ['name', 'email', 'contact', 'address', 'property_id'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $setClauses[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($setClauses)) {
            return $this->generateResponse([], "failed", "No valid fields to update.", 400);
        }

        $setClause = implode(", ", $setClauses);
        $sqlString = "UPDATE tenants SET $setClause WHERE id = :tenantId";

        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(':tenantId', $tenantId, PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $stmt->bindParam($key, $value);
            }

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return $this->generateResponse([], "success", "Tenant updated successfully.", 200);
            } else {
                return $this->generateResponse([], "failed", "No changes were made or tenant not found.", 404);
            }
        } catch (\PDOException $e) {
            return $this->generateResponse([], "failed", $e->getMessage(), 500);
        }
    }
}