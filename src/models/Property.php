<?php
// File: C:\xampp\htdocs\propertymanagementsystem\src\models\Property.php

require_once __DIR__ . '/../config/Database.php';

class Property {
    private $conn;
    private $table = 'properties';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT * FROM " . $this->table;
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT * FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $location, $type, $price, $description) {
        $query = "INSERT INTO " . $this->table . " (name, location, type, price, description) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $location, $type, $price, $description]);
    }

    public function update($id, $name, $location, $type, $price, $description) {
        $query = "UPDATE " . $this->table . " SET name = ?, location = ?, type = ?, price = ?, description = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $location, $type, $price, $description, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function search($keyword) {
        $query = "SELECT * FROM " . $this->table . " WHERE name LIKE ? OR location LIKE ? OR type LIKE ?";
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getAvailableProperties() {
        $query = "SELECT p.* FROM " . $this->table . " p LEFT JOIN tenants t ON p.id = t.property_id WHERE t.id IS NULL";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRecent($limit = 5) {
        $query = "SELECT * FROM " . $this->table . " ORDER BY id DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}