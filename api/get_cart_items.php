<?php
header("Content-Type: application/json");
header("Access-Control-Allow-Origin: *");
require_once __DIR__.'/../db.php';

session_start();

$response = ['status' => 'error', 'items' => []];

try {
    $user_id = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
    $ip_address = $_SERVER['REMOTE_ADDR'];
    
    $query = "SELECT c.*, p.product_title as name, p.product_price as price, p.product_image as image 
              FROM cart c
              JOIN products p ON c.p_id = p.product_id
              WHERE c.user_id = ? OR (c.ip_add = ? AND c.user_id IS NULL)";
    
    $stmt = $con->prepare($query);
    $stmt->bind_param("is", $user_id, $ip_address);
    $stmt->execute();
    $result = $stmt->get_result();
    
    $items = [];
    $total = 0;
    
    while ($row = $result->fetch_assoc()) {
        $items[] = [
            'id' => $row['id'],
            'product_id' => $row['p_id'],
            'name' => $row['name'],
            'price' => $row['price'],
            'quantity' => $row['qty'],
            'image' => $row['image'],
            'subtotal' => $row['price'] * $row['qty']
        ];
        $total += $row['price'] * $row['qty'];
    }
    
    $response = [
        'status' => 'success',
        'items' => $items,
        'total' => $total,
        'count' => count($items)
    ];
    
} catch (Exception $e) {
    $response['message'] = $e->getMessage();
}

echo json_encode($response);
?>