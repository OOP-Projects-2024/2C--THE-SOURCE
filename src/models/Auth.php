<?php  
// File: C:\xampp\htdocs\propertymanagementsystem\src\models\Auth.php  

require_once __DIR__ . '/../config/Database.php';  

class Auth {  
    private $conn;  
    private $table = 'users';  

    public function __construct() {  
        $database = new Database();  
        $this->conn = $database->getConnection();  
    }  

    public function register($username, $password) {  
        // Use Argon2 for hashing the password  
        $hashedPassword = password_hash($password, PASSWORD_ARGON2ID);  
        $query = "INSERT INTO users (username, password) VALUES (:username, :password)";  
        $stmt = $this->conn->prepare($query);  
        $stmt->bindParam(':username', $username);  
        $stmt->bindParam(':password', $hashedPassword);  

        // Execute the statement and check for success  
        if ($stmt->execute()) {  
            return [  
                'success' => true,  
                'hashed_password' => $hashedPassword,  // Include the hashed password  
                'message' => 'User registered successfully.'  
            ];  
        } else {  
            return [  
                'success' => false,  
                'message' => 'User registration failed.'  
            ];  
        }  
    }  

    public function login($username, $password) {  
        $query = "SELECT id, username, password FROM " . $this->table . " WHERE username = ?";  
        $stmt = $this->conn->prepare($query);  
        $stmt->execute([$username]);  
        
        if ($user = $stmt->fetch(PDO::FETCH_ASSOC)) {  
            if (password_verify($password, $user['password'])) {  
                $_SESSION['user_id'] = $user['id'];  
                $_SESSION['username'] = $user['username'];  
                return true;  
            }  
        }  
        
        return false;  
    }  

    public function isAuthenticated() {  
        return isset($_SESSION['user_id']);  
    }  

    public function logout() {  
        unset($_SESSION['user_id']);  
        unset($_SESSION['username']);  
        session_destroy();  
    }  

    public function getCurrentUser() {  
        if ($this->isAuthenticated()) {  
            $query = "SELECT id, username FROM " . $this->table . " WHERE id = ?";  
            $stmt = $this->conn->prepare($query);  
            $stmt->execute([$_SESSION['user_id']]);  
            return $stmt->fetch(PDO::FETCH_ASSOC);  
        }  
        return null;  
    }  
}