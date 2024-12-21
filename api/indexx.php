<?php
// File: C:\xampp\htdocs\propertymanagementsystem\api\index.php

session_start();
require_once __DIR__ . '/../src/models/Auth.php';
require_once __DIR__ . '/../src/models/Property.php';
require_once __DIR__ . '/../src/models/Tenant.php';

$auth = new Auth();
$propertyModel = new Property();
$tenantModel = new Tenant();

$response = [
    'isAuthenticated' => $auth->isAuthenticated(),
    'username' => $_SESSION['username'] ?? null,
    'dashboardData' => []
];

if ($auth->isAuthenticated()) {
    // Fetch dashboard data
    $response['dashboardData'] = [
        'totalProperties' => count($propertyModel->getAll()),
        'availableProperties' => count($propertyModel->getAvailableProperties()),
        'totalTenants' => count($tenantModel->getAll()),
        'recentProperties' => $propertyModel->getRecent(5),
        'recentTenants' => $tenantModel->getRecent(5)
    ];
}

echo json_encode($response);