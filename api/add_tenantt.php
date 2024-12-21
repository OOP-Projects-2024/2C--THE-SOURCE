<?php
// File: C:\xampp\htdocs\propertymanagementsystem\api\add_tenant.php

require_once __DIR__ . '/../src/models/Property.php';
require_once __DIR__ . '/../src/models/Tenant.php';

if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $address = $_POST['address'] ?? '';
    $property_id = $_POST['property_id'] ?? '';

    if (!empty($name) && !empty($email) && !empty($contact) && !empty($property_id)) {
        $tenantModel = new Tenant();
        $result = $tenantModel->create($name, $email, $contact, $address, $property_id);
        echo json_encode(['success' => $result]);
    } else {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    }
} else {
    $propertyModel = new Property();
    $properties = $propertyModel->getAvailableProperties();
    echo json_encode($properties);
}