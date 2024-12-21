<?php

use src\models\Property;
use src\models\Tenant;
use src\models\Auth;
use src\models\Log;

header('Content-Type: application/json');
header('Access-Control-Allow-Origin: *');
header('Access-Control-Allow-Methods: POST, GET, PUT, DELETE');
header('Access-Control-Allow-Headers: Content-Type, Authorization');

// Include the necessary files
require_once 'src/config/Database.php';
require_once 'src/models/Property.php';
require_once 'src/models/Tenant.php';
require_once 'src/models/Auth.php';
require_once 'src/models/Logger.php';



// Initialize database connection
$db = new Database();
$conn = $db->getConnection();

$method = $_SERVER['REQUEST_METHOD'];
$entity = $_GET['entity'] ?? '';

// Helper function to get request data
function getRequestData() {
    return json_decode(file_get_contents("php://input"), true);
}

// Helper function to send JSON response
function sendJsonResponse($data, $statusCode = 200) {
    http_response_code($statusCode);
    echo json_encode($data);
    exit;
}

// Route requests based on entity type
switch ($entity) {
    case 'properties':
        handleProperties($method, $conn);
        break;
    case 'tenants':
        handleTenants($method, $conn);
        break;
    case 'auth':
        handleUsers($method, $conn);
        break;
    case 'logs':
        handleLogs($method, $conn);
        break;
    default:
        sendJsonResponse(['error' => 'Invalid entity'], 400);
}

// Handle Properties (CRUD)
function handleProperties($method, $conn) {
    $property = new Property();
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $property->create($data['name'], $data['address'], $data['type'], $data['rent_amount'], $data['owner_id']);
            sendJsonResponse($result);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $property->getById($id) : $property->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $property->update($_GET['id'], $data['name'], $data['address'], $data['type'], $data['rent_amount'], $data['owner_id']);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $property->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Tenants (CRUD)
function handleTenants($method, $conn) {
    $tenant = new Tenant();
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $tenant->create($data['name'], $data['email'], $data['phone'], $data['property_id'], $data['lease_start']);
            sendJsonResponse($result);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $tenant->getById($id) : $tenant->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $tenant->update($_GET['id'], $data['name'], $data['email'], $data['phone'], $data['property_id'], $data['lease_start']);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $tenant->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Users (CRUD)
function handleUsers($method, $conn) {
    $user = new Auth($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $user->create($data['username'], $data['email'], $data['password'], $data['role']);
            sendJsonResponse($result);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $user->getById($id) : $user->getAll();
            sendJsonResponse($result);
            break;
        case 'PUT':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $user->update($_GET['id'], $data['username'], $data['email'], $data['password'], $data['role']);
            sendJsonResponse($result);
            break;
        case 'DELETE':
            if (!isset($_GET['id'])) {
                sendJsonResponse(['error' => 'Missing ID'], 400);
            }
            $result = $user->delete($_GET['id']);
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}

// Handle Logs (CRUD)
function handleLogs($method, $conn) {
    $log = new Log($conn);
    $data = getRequestData();

    switch ($method) {
        case 'POST':
            $result = $log->create($data['user_id'], $data['action'], $data['entity_type'], $data['entity_id']);
            sendJsonResponse($result);
            break;
        case 'GET':
            $id = $_GET['id'] ?? null;
            $result = $id ? $log->getById($id) : $log->getAll();
            sendJsonResponse($result);
            break;
        default:
            sendJsonResponse(['error' => 'Method Not Allowed'], 405);
    }
}
