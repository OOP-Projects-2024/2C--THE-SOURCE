<?php  
session_start();  
require_once __DIR__ . '/../src/models/Auth.php';  

if ($_SERVER['REQUEST_METHOD'] == 'POST') {  
    // Get the raw POST data  
    $data = json_decode(file_get_contents("php://input"), true);  

    // Check if username and password are set  
    if (isset($data['username']) && isset($data['password'])) {  
        $username = $data['username'];  
        $password = $data['password'];  

        // Create an instance of Auth and attempt to login  
        $auth = new Auth();  
        $result = $auth->login($username, $password);  

        // Return the result as a JSON response  
        echo json_encode(['success' => $result]);  
    } else {  
        // Handle the case where username or password is missing  
        echo json_encode(['success' => false, 'message' => 'Missing username or password.']);  
    }  
} else {  
    // Handle unsupported request methods  
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);  
}  
?>