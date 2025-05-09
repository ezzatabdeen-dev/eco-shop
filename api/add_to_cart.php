<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__.'/../db.php';

session_start();

$response = ['status' => 'error', 'message' => ''];

try {
    if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
        throw new Exception("Only POST requests allowed");
    }

    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!$data || !isset($data['product_id'])) {
        throw new Exception("Invalid input data");
    }

    $product_id = (int)$data['product_id'];
    $quantity = isset($data['quantity']) ? (int)$data['quantity'] : 1;
    $user_id = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'];

    // Check product availability only (no stock)
    $product_stmt = $con->prepare("SELECT product_id FROM products WHERE product_id = ?");
    $product_stmt->bind_param("i", $product_id);
    $product_stmt->execute();
    
    if (!$product_stmt->get_result()->num_rows) {
        throw new Exception("Product does not exist");
    }

    // Add/update cart
    $check_stmt = $con->prepare("SELECT id FROM cart 
                               WHERE p_id = ? 
                               AND (user_id = ? OR (ip_add = ? AND user_id IS NULL))");
    $check_stmt->bind_param("iis", $product_id, $user_id, $ip_address);
    $check_stmt->execute();
    
    if ($check_stmt->get_result()->num_rows > 0) {
        $update = $con->prepare("UPDATE cart SET qty = qty + ? 
                               WHERE p_id = ? 
                               AND (user_id = ? OR (ip_add = ? AND user_id IS NULL))");
        $update->bind_param("iisi", $quantity, $product_id, $user_id, $ip_address);
        $success = $update->execute();
    } else {
        $insert = $con->prepare("INSERT INTO cart (p_id, ip_add, user_id, qty) 
                               VALUES (?, ?, ?, ?)");
        $insert->bind_param("isii", $product_id, $ip_address, $user_id, $quantity);
        $success = $insert->execute();
    }

    if (!$success) {
        throw new Exception("Database operation failed: " . $con->error);
    }

    // Calculate the new number
    $count_stmt = $con->prepare("SELECT SUM(qty) AS total FROM cart 
                               WHERE user_id = ? OR (ip_add = ? AND user_id IS NULL)");
    $count_stmt->bind_param("is", $user_id, $ip_address);
    $count_stmt->execute();
    $total = $count_stmt->get_result()->fetch_assoc()['total'] ?? 0;

    $response = [
        'status' => 'success',
        'cart_count' => (int)$total,
        'product_id' => $product_id
    ];

} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>