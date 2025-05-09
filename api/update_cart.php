<?php
header("Content-Type: application/json");
require_once __DIR__.'/../db.php';

session_start();

$response = ['status' => 'error'];

try {
    // Get data sent as JSON
    $json = file_get_contents('php://input');
    $data = json_decode($json, true);
    
    if (!isset($data['cart_id']) || !isset($data['quantity'])) {
        throw new Exception("Invalid request data");
    }
    
    $cart_id = (int)$data['cart_id'];
    $quantity = (int)$data['quantity'];
    
    if ($quantity < 1) {
        throw new Exception("Invalid quantity");
    }
    
    $user_id = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Check if the item is in the cart first.
    $check = $con->prepare("SELECT id FROM cart 
                           WHERE id = ? 
                           AND (user_id = ? OR (ip_add = ? AND user_id IS NULL))");
    $check->bind_param("iis", $cart_id, $user_id, $ip_address);
    $check->execute();
    
    if ($check->get_result()->num_rows === 0) {
        throw new Exception("Item not found or not authorized");
    }
    
    // Update quantity
    $update = $con->prepare("UPDATE cart SET qty = ? 
                            WHERE id = ? 
                            AND (user_id = ? OR (ip_add = ? AND user_id IS NULL))");
    $update->bind_param("iiis", $quantity, $cart_id, $user_id, $ip_address);
    $update->execute();
    
    $response['status'] = 'success';
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>