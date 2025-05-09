<?php
header('Content-Type: application/json');
include '../db.php';

$query = "SELECT p.*, c.cat_title, 
          (SELECT ROUND(AVG(rating),1) FROM reviews WHERE product_id = p.product_id) AS avg_rating
          FROM products p
          JOIN categories c ON p.product_cat = c.cat_id
          WHERE p.product_quantity > 0";

$result = mysqli_query($con, $query);

$products = [];
while($row = mysqli_fetch_assoc($result)) {
    $products[] = [
        'product_id' => $row['product_id'],
        'product_title' => $row['product_title'],
        'product_price' => $row['product_price'],
        'product_image' => $row['product_image'],
        'cat_title' => $row['cat_title'],
        'avg_rating' => $row['avg_rating']
    ];
}

echo json_encode($products);
?>