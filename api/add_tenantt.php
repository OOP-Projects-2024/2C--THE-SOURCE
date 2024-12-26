<?php  
header('Content-Type: application/json');  
header('Access-Control-Allow-Origin: *');  
header('Access-Control-Allow-Methods: POST');  
header('Access-Control-Allow-Headers: Content-Type');  

require_once '../src/models/Tenant.php';  
require_once '../src/models/Logger.php';  

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {  
    http_response_code(405);  
    echo json_encode(['success' => false, 'message' => 'Method not allowed']);  
    exit;  
}  

try {  
    // Get JSON input  
    $data = json_decode(file_get_contents('php://input'), true);  

    // Validate required fields  
    $required_fields = ['name', 'email', 'contact', 'property_id'];  
    $missing_fields = [];  

    foreach ($required_fields as $field) {  
        if (!isset($data[$field]) || empty($data[$field])) {  
            $missing_fields[] = $field;  
        }  
    }  

    if (!empty($missing_fields)) {  
        echo json_encode([  
            'success' => false,  
            'message' => 'Missing required fields: ' . implode(', ', $missing_fields)  
        ]);  
        exit;  
    }  

    // Verify if the property_id exists in the properties table  
    $tenant = new Tenant();  
    if (!$tenant->propertyExists($data['property_id'])) {  
        http_response_code(400); // Bad request  
        echo json_encode([  
            'success' => false,  
            'message' => 'Invalid property_id: ' . $data['property_id'] . ' does not exist'  
        ]);  
        exit;  
    }  

    // Create new tenant  
    $result = $tenant->create(  
        $data['name'],  
        $data['email'],  
        $data['contact'],  
        $data['address'] ?? '',  
        $data['property_id']  
    );  

    if ($result) {  
        // Log the action  
        $logger = new Logger();  
        $logger->log(  
            $_SESSION['user_id'] ?? 0,  
            'create',  
            'tenant',  
            $tenant->getConnection()->lastInsertId()  
        );  

        http_response_code(201);  
        echo json_encode([  
            'success' => true,  
            'message' => 'Tenant added successfully'  
        ]);  
    } else {  
        http_response_code(500);  
        echo json_encode([  
            'success' => false,  
            'message' => 'Failed to add tenant'  
        ]);  
    }  
} catch (Exception $e) {  
    http_response_code(500);  
    echo json_encode([  
        'success' => false,  
        'message' => 'Server error: ' . $e->getMessage()  
    ]);  
}