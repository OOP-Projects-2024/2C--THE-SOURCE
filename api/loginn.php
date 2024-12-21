<?php
session_start();
require_once __DIR__ . '/../src/models/Auth.php';
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    $username = $_POST['username'];
    $password = $_POST['password'];
    $auth = new Auth();
    $result = $auth->login($username, $password);
    echo json_encode(['success' => $result]);
}
?>