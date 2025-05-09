<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__.'/../db.php';

session_start();

$response = ['status' => 'error', 'count' => 0];

try {
    $input = file_get_contents('php://input');
    $data = json_decode($input, true);
    
    if (!isset($data['id'])) {
        throw new Exception("Invalid request");
    }
    
    $id = (int)$data['id'];
    $user_id = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    // Delete item
    $delete = $con->prepare("DELETE FROM cart 
                            WHERE id = ? 
                            AND (user_id = ? OR (ip_add = ? AND user_id IS NULL))");
    $delete->bind_param("iis", $id, $user_id, $ip_address);
    $delete->execute();
    
    if ($delete->affected_rows === 0) {
        throw new Exception("Item not found or not authorized");
    }
    
    // Calculate the new number
    $count_stmt = $con->prepare("SELECT COUNT(*) AS total FROM cart 
                               WHERE user_id = ? OR (ip_add = ? AND user_id IS NULL)");
    $count_stmt->bind_param("is", $user_id, $ip_address);
    $count_stmt->execute();
    $count = $count_stmt->get_result()->fetch_assoc()['total'] ?? 0;
    
    $response = [
        'status' => 'success',
        'count' => (int)$count,
        'message' => 'Item removed successfully'
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>