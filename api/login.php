<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/login_errors.log');

header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

session_start();

$response = [
    'status' => 'error',
    'message' => 'Invalid request',
    'debug' => []
];

try {
    if (!file_exists(__DIR__.'/../db.php')) {
        throw new Exception('db.php file not found');
    }
    
    require_once __DIR__.'/../db.php';
    
    if ($con->connect_error) {
        throw new Exception('Database connection failed: '.$con->connect_error);
    }

    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        $input = file_get_contents('php://input');
        $data = json_decode($input, true);
        
        if (!$data) {
            throw new Exception('Invalid JSON data');
        }

        if (empty($data['email']) || empty($data['password'])) {
            $response['message'] = 'Email and password are required';
        } else {
            $email = $con->real_escape_string($data['email']);
            $password = $data['password'];
            
            $stmt = $con->prepare("SELECT user_id, first_name, password FROM user_info WHERE email = ?");
            if (!$stmt) {
                throw new Exception('Prepare failed: '.$con->error);
            }
            
            $stmt->bind_param("s", $email);
            $stmt->execute();
            $result = $stmt->get_result();
            
            if ($result->num_rows === 0) {
                $response['message'] = 'Email not found';
            } else {
                $user = $result->fetch_assoc();
                
                if ($password === $user['password']) {
                    $_SESSION['uid'] = $user['user_id'];
                    $response['status'] = 'success';
                    $response['message'] = 'Login successful';

                    // Merge cart items associated with IP to user_id
                    $merge = $con->prepare("UPDATE cart SET user_id = ? WHERE ip_add = ? AND user_id IS NULL");
                    if ($merge) {
                        $merge->bind_param("is", $_SESSION['uid'], $_SERVER['REMOTE_ADDR']);
                        $merge->execute();
                        $merge->close();
                        $response['debug'][] = 'Cart merged successfully';
                    } else {
                        error_log('Cart merge prepare failed: '.$con->error);
                        $response['debug'][] = 'Cart merge prepare failed';
                    }

                } else {
                    $response['message'] = 'Incorrect password';
                }
            }
            $stmt->close();
        }
    }
} catch (Exception $e) {
    error_log($e->getMessage());
    $response['message'] = 'Server error: '.$e->getMessage();
}

echo json_encode($response);
?>
