<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__.'/../db.php';

error_reporting(E_ALL);
ini_set('display_errors', 1);

session_start();

$response = [
    'status' => 'error',
    'message' => 'Not logged in',
    'user' => null
];

try {
    if (isset($_SESSION['uid'])) {
        $user_id = $_SESSION['uid'];
        
        $stmt = $con->prepare("SELECT user_id, first_name, last_name, email FROM user_info WHERE user_id = ?");
        $stmt->bind_param("i", $user_id);
        $stmt->execute();
        $result = $stmt->get_result();
        
        if ($result->num_rows > 0) {
            $user = $result->fetch_assoc();
            $response['status'] = 'success';
            $response['message'] = 'User found';
            $response['user'] = [
                'id' => $user['user_id'],
                'name' => $user['first_name'].' '.$user['last_name'],
                'email' => $user['email']
            ];
        } else {
            $response['message'] = 'User not found in database';
        }
        
        $stmt->close();
    }
} catch (Exception $e) {
    $response['message'] = 'Error: '.$e->getMessage();
}

echo json_encode($response);
?>