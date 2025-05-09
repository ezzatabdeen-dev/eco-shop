<?php
header("Content-Type: application/json");
require_once __DIR__.'/../db.php';

$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Only POST requests allowed");
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['email'])) {
        throw new Exception("Email is required");
    }

    $email = filter_var($data['email'], FILTER_VALIDATE_EMAIL);
    if (!$email) {
        throw new Exception("Invalid email format");
    }

    $check_query = "SELECT email_id FROM email_info WHERE email = ?";
    $check_stmt = $con->prepare($check_query);
    $check_stmt->bind_param("s", $email);
    $check_stmt->execute();
    $check_result = $check_stmt->get_result();
    
    if ($check_result->num_rows > 0) {
        throw new Exception("This email is already subscribed");
    }

    $insert_query = "INSERT INTO email_info (email) VALUES (?)";
    $insert_stmt = $con->prepare($insert_query);
    $insert_stmt->bind_param("s", $email);
    
    if (!$insert_stmt->execute()) {
        throw new Exception("Subscription failed: " . $con->error);
    }

    $response = [
        'status' => 'success',
        'message' => 'Subscription successful',
        'inserted_id' => $insert_stmt->insert_id
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>