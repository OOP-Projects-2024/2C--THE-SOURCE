<?php
// File: C:\xampp\htdocs\propertymanagementsystem\src\models\Tenant.php

require_once __DIR__ . '/../config/Database.php';

class Tenant {
    private $conn;
    private $table = 'tenants';

    public function __construct() {
        $database = new Database();
        $this->conn = $database->getConnection();
    }

    public function getAll() {
        $query = "SELECT t.*, p.name as property_name FROM " . $this->table . " t LEFT JOIN properties p ON t.property_id = p.id";
        $stmt = $this->conn->prepare($query);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getById($id) {
        $query = "SELECT t.*, p.name as property_name FROM " . $this->table . " t LEFT JOIN properties p ON t.property_id = p.id WHERE t.id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $id);
        $stmt->execute();
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    public function create($name, $email, $contact, $address, $property_id) {
        $query = "INSERT INTO " . $this->table . " (name, email, contact, address, property_id) VALUES (?, ?, ?, ?, ?)";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $contact, $address, $property_id]);
    }

    public function update($id, $name, $email, $contact, $address, $property_id) {
        $query = "UPDATE " . $this->table . " SET name = ?, email = ?, contact = ?, address = ?, property_id = ? WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$name, $email, $contact, $address, $property_id, $id]);
    }

    public function delete($id) {
        $query = "DELETE FROM " . $this->table . " WHERE id = ?";
        $stmt = $this->conn->prepare($query);
        return $stmt->execute([$id]);
    }

    public function search($keyword) {
        $query = "SELECT t.*, p.name as property_name FROM " . $this->table . " t LEFT JOIN properties p ON t.property_id = p.id WHERE t.name LIKE ? OR t.email LIKE ? OR t.contact LIKE ?";
        $stmt = $this->conn->prepare($query);
        $keyword = "%{$keyword}%";
        $stmt->execute([$keyword, $keyword, $keyword]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getTenantsByProperty($property_id) {
        $query = "SELECT * FROM " . $this->table . " WHERE property_id = ?";
        $stmt = $this->conn->prepare($query);
        $stmt->execute([$property_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    public function getRecent($limit = 5) {
        $query = "SELECT t.*, p.name as property_name FROM " . $this->table . " t LEFT JOIN properties p ON t.property_id = p.id ORDER BY t.id DESC LIMIT ?";
        $stmt = $this->conn->prepare($query);
        $stmt->bindParam(1, $limit, PDO::PARAM_INT);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
    
}