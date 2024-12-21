<?php
session_start();
require_once __DIR__ . '/../src/models/Auth.php';

// Set headers
header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, OPTIONS');
header('Access-Control-Allow-Headers: Content-Type');

// Handle preflight requests
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(200);
    exit();
}

error_log("Request Method: " . $_SERVER['REQUEST_METHOD']);

// Get JSON input
$json_input = file_get_contents('php://input');
$data = json_decode($json_input, true);

error_log("Raw input: " . $json_input);

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = $data['username'] ?? null;
    $password = $data['password'] ?? null;
    
    error_log("Username: " . ($username ?? 'not provided'));
    error_log("Password: " . (!empty($password) ? 'provided' : 'empty'));
    
    if (!empty($username) && !empty($password)) {
        $auth = new Auth();
        $result = $auth->register($username, $password);
        
        if ($result['success']) {
            http_response_code(201); // Created
            echo json_encode([
                'success' => true,
                'hashed_password' => $result['hashed_password'],
                'message' => 'User registered successfully.'
            ]);
        } else {
            http_response_code(400); // Bad Request
            echo json_encode([
                'success' => false,
                'message' => 'User registration failed.'
            ]);
        }
    } else {
        http_response_code(400); // Bad Request
        echo json_encode([
            'success' => false,
            'message' => 'Username and password are required.'
        ]);
    }
} else {
    http_response_code(405); // Method Not Allowed
    echo json_encode([
        'success' => false,
        'message' => 'Invalid request method. Use POST.'
    ]);
}

