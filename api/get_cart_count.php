<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__.'/../db.php';

session_start();

$response = ['status' => 'error', 'count' => 0];

try {
    $user_id = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $stmt = $con->prepare("SELECT SUM(qty) AS total FROM cart 
                          WHERE user_id = ? OR (ip_add = ? AND user_id IS NULL)");
    $stmt->bind_param("is", $user_id, $ip_address);
    $stmt->execute();
    $result = $stmt->get_result()->fetch_assoc();
    
    $response = [
        'status' => 'success',
        'count' => (int)$result['total'] ?? 0
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>