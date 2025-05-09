<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");

error_reporting(E_ALL);
ini_set('display_errors', 1);
ini_set('log_errors', 1);
ini_set('error_log', __DIR__.'/signup_errors.log');

require_once __DIR__.'/../db.php';

session_start();

$response = [
    'status' => 'error',
    'message' => 'Invalid request',
    'debug' => []
];

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    $response['debug']['received_data'] = $data;

    if ($_SERVER['REQUEST_METHOD'] === 'POST' && $data) {
        if ($con->connect_error) {
            throw new Exception("Database connection failed");
        }

        $required = ['f_name', 'l_name', 'email', 'mobile', 'password', 'address1'];
        foreach ($required as $field) {
            if (empty($data[$field])) {
                throw new Exception("Field {$field} is required");
            }
        }

        $f_name = $con->real_escape_string($data['f_name']);
        $l_name = $con->real_escape_string($data['l_name']);
        $email = $con->real_escape_string($data['email']);
        $mobile = $con->real_escape_string($data['mobile']);
        $password = $con->real_escape_string($data['password']);
        $address1 = $con->real_escape_string($data['address1']);
        $address2 = isset($data['address2']) ? $con->real_escape_string($data['address2']) : '';

        if (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
            throw new Exception("Invalid email format");
        }

        $check_query = "SELECT user_id FROM user_info WHERE email = '{$email}'";
        $result = $con->query($check_query);
        if ($result->num_rows > 0) {
            throw new Exception("Email already registered");
        }

        $insert_query = "INSERT INTO user_info 
            (first_name, last_name, email, password, mobile, address1, address2) 
            VALUES ('{$f_name}', '{$l_name}', '{$email}', '{$password}', '{$mobile}', '{$address1}', '{$address2}')";

        if ($con->query($insert_query)) {
            $user_id = $con->insert_id;
            $_SESSION['uid'] = $user_id;
            
            $response['status'] = 'success';
            $response['message'] = 'Registration successful';
            $response['user'] = [
                'id' => $user_id,
                'name' => $f_name . ' ' . $l_name,
                'email' => $email
            ];
        } else {
            throw new Exception("Database error: " . $con->error);
        }
    }
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
    error_log($e->getMessage());
}

echo json_encode($response);
?>