<?php
require_once __DIR__ . '/../src/models/Property.php';
$propertyModel = new Property();
$properties = $propertyModel->getAll();
echo json_encode($properties);
?>

