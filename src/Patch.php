<?php
// File: src/Patch.php

namespace src;

class Patch {
    protected $pdo;

    public function __construct(\PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function updateProperty($propertyId, $data) {
        $setClauses = [];
        $params = [];
        
        $allowedFields = ['name', 'address', 'type', 'rent_amount', 'owner_id'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $setClauses[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($setClauses)) {
            return ['error' => 'No valid fields to update.', 'code' => 400];
        }

        $setClause = implode(", ", $setClauses);
        $sqlString = "UPDATE properties SET $setClause WHERE id = :propertyId";

        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(':propertyId', $propertyId, \PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $stmt->bindParam($key, $value);
            }

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['message' => 'Property updated successfully.', 'code' => 200];
            } else {
                return ['error' => 'No changes were made or property not found.', 'code' => 404];
            }
        } catch (\PDOException $e) {
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }

    public function updateTenant($tenantId, $data) {
        $setClauses = [];
        $params = [];
        
        $allowedFields = ['name', 'email', 'phone', 'property_id', 'lease_start'];
        
        foreach ($allowedFields as $field) {
            if (isset($data[$field])) {
                $setClauses[] = "$field = :$field";
                $params[":$field"] = $data[$field];
            }
        }

        if (empty($setClauses)) {
            return ['error' => 'No valid fields to update.', 'code' => 400];
        }

        $setClause = implode(", ", $setClauses);
        $sqlString = "UPDATE tenants SET $setClause WHERE id = :tenantId";

        try {
            $stmt = $this->pdo->prepare($sqlString);
            $stmt->bindParam(':tenantId', $tenantId, \PDO::PARAM_INT);
            foreach ($params as $key => $value) {
                $stmt->bindParam($key, $value);
            }

            $stmt->execute();

            if ($stmt->rowCount() > 0) {
                return ['message' => 'Tenant updated successfully.', 'code' => 200];
            } else {
                return ['error' => 'No changes were made or tenant not found.', 'code' => 404];
            }
        } catch (\PDOException $e) {
            return ['error' => $e->getMessage(), 'code' => 500];
        }
    }
}