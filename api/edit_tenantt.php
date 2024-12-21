<?php
// File: C:\xampp\htdocs\propertymanagementsystem\api\edit_tenant.php

require_once __DIR__ . '/../src/models/Tenant.php';
require_once __DIR__ . '/../src/models/Property.php';

$tenantModel = new Tenant();
$propertyModel = new Property();

if ($_SERVER['REQUEST_METHOD'] == 'GET') {
    if (isset($_GET['id'])) {
        $id = $_GET['id'];
        $tenant = $tenantModel->getById($id);
        $properties = $propertyModel->getAll();
        if ($tenant) {
            echo json_encode([
                'success' => true, 
                'tenant' => $tenant,
                'properties' => $properties
            ]);
        } else {
            echo json_encode(['success' => false, 'message' => 'Tenant not found.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'Tenant ID is required.']);
    }
} elseif ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $id = $_POST['id'] ?? '';
    $name = $_POST['name'] ?? '';
    $email = $_POST['email'] ?? '';
    $contact = $_POST['contact'] ?? '';
    $address = $_POST['address'] ?? '';
    $property_id = $_POST['property_id'] ?? '';

    if (!empty($id) && !empty($name) && !empty($email) && !empty($contact) && !empty($property_id)) {
        $result = $tenantModel->update($id, $name, $email, $contact, $address, $property_id);
        if ($result) {
            echo json_encode(['success' => true, 'message' => 'Tenant updated successfully.']);
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to update tenant.']);
        }
    } else {
        echo json_encode(['success' => false, 'message' => 'All fields are required.']);
    }
} else {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
}