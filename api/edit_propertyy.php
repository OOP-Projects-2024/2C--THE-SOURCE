<?php
// File: C:\xampp\htdocs\propertymanagementsystem\api\edit_property.php

require_once __DIR__ . '/../src/models/Property.php';

$propertyModel = new Property();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $property = $propertyModel->getById($id);
        if ($property) {
            echo json_encode(['success' => true, 'property' => $property]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Property not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Property ID is required.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $location = $_POST['location'] ?? '';
    $type = $_POST['type'] ?? '';
    $price = $_POST['price'] ?? '';
    $description = $_POST['description'] ?? '';

    if (!empty($id) && !empty($name) && !empty($location) && !empty($type) && !empty($price)) {
        $result = $propertyModel->update($id, $name, $location, $type, $price, $description);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Property updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update property.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}