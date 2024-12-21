<?php
session_start();
$data = [
    'username' => $_SESSION['auth']['username'] ?? null
];
echo json_encode($data);
?>