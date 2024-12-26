<?php  
// Set content type to JSON  
header('Content-Type: application/json');  

// Database connection configuration  
$servername = "localhost";  // Usually localhost  
$username = "root";          // Default XAMPP MySQL username  
$password = "";              // Default XAMPP MySQL password is empty  
$dbname = "property_management";   // Replace with your actual database name  

// Create database connection  
$conn = new mysqli($servername, $username, $password, $dbname);  

// Check connection  
if ($conn->connect_error) {  
    die(json_encode(["success" => false, "message" => "Database connection failed: " . $conn->connect_error]));  
}  

// Get the raw POST data  
$json = file_get_contents('php://input');  

// Decode the JSON data  
$data = json_decode($json, true);  

// Validate the incoming data  
if (isset($data['name']) && isset($data['location']) && isset($data['type']) && isset($data['price'])) {  
    // Prepare an SQL statement  
    $stmt = $conn->prepare("INSERT INTO properties (name, location, type, price, description, created_at, updated_at) VALUES (?, ?, ?, ?, ?, NOW(), NOW())");  

    // Check if description is set, and provide a default if not  
    $description = isset($data['description']) ? $data['description'] : "";  
    
    // Update bind_param to include description  
    $stmt->bind_param("ssssd", $data['name'], $data['location'], $data['type'], $data['price'], $description);  
    
    // Execute the statement  
    if ($stmt->execute()) {  
        $response = [  
            "success" => true,  
            "message" => "Property added successfully.",  
            "property" => $data  
        ];  
    } else {  
        // Error during execution  
        $response = [  
            "success" => false,  
            "message" => "Error adding property: " . $stmt->error  
        ];  
    }  

    // Close the statement  
    $stmt->close();  
} else {  
    // Return an error response if validation fails  
    $response = [  
        "success" => false,  
        "message" => "Invalid input data."  
    ];  
}  

// Close the database connection  
$conn->close();  

// Return the response as JSON  
echo json_encode($response);  
?>