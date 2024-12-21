<?php
require_once __DIR__ . '/../src/controllers/TenantController.php';
$tenantController = new TenantController();
$tenants = $tenantController->getTenants();
echo json_encode($tenants);
?>

