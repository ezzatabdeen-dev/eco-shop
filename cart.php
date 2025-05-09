<?php
session_start();
require_once __DIR__.'/./db.php';
?>

<!DOCTYPE html>
<html lang="en">
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/public_img/favicon.png">
    <meta name="theme-color" content="#ff7226">
	<title>Online Shop | Cart</title>
    <link type='text/css' rel='stylesheet' href='./assets/css/gloable.css'/>
    <link type='text/css' rel='stylesheet' href='./assets/css/cartPage.css'/>
    <link type='text/css' rel='stylesheet' href='./sections/header/header.css' />
    <link type='text/css' rel='stylesheet' href='./sections/footer/footer.css' />
</head>
<body>

    <!-- include header -->
    <?php include "./sections/header/header.php"; ?>

    <div class="container">
        <div class="cart-wraper">
            <div class="cart-header-section">
                <h1 class="text-5">Shopping Cart</h1>
            </div>

            <?php
            // Fetch basket contents from database
            $user_id = isset($_SESSION['uid']) ? (int)$_SESSION['uid'] : null;
            $ip_address = $_SERVER['REMOTE_ADDR'];

            $query = "SELECT c.*, p.product_title, p.product_price, p.product_image 
                    FROM cart c
                    JOIN products p ON c.p_id = p.product_id
                    WHERE c.user_id = ? OR (c.ip_add = ? AND c.user_id IS NULL)";

            $stmt = $con->prepare($query);
            $stmt->bind_param("is", $user_id, $ip_address);
            $stmt->execute();
            $result = $stmt->get_result();

            if ($result->num_rows > 0):
            ?>

            <div class="cart-items-wraperSection">
                <?php 
                $grand_total = 0;
                while ($row = $result->fetch_assoc()): 
                    $subtotal = $row['product_price'] * $row['qty'];
                    $grand_total += $subtotal;
                ?>
                <div class="cart-items-section" data-cart-id="<?= $row['id'] ?>">
                    <div class="cart-items-section-leftItem">
                        <img src="./assets/products_images/<?= htmlspecialchars($row['product_image']) ?>" 
                            alt="<?= htmlspecialchars($row['product_title']) ?>">
                        <div class="product-details">
                            <h3 class="text-3 text-ellipsis-1"><?= htmlspecialchars($row['product_title']) ?></h3>
                            <div class="product-price">
                                <span class="price-lable text-1" style="font-weight: bold">Price:</span>
                                <span class="price text-1">EG <?= number_format($row['product_price'], 2) ?></span>
                            </div>
                            <div class="product-price">
                                <span class="price-lable text-1" style="font-weight: bold">Subtotal:</span>
                                <span class="price text-1 product-subtotal">EG <?= number_format($subtotal, 2) ?></span>
                            </div>
                        </div>
                    </div>
                    
                    <div class="cart-items-section-rightItem">
                        <div class="product-quantity">
                            <label for="<?= $row['id'] ?>" class="text-1">Quantity:</label>
                            <input type="number" class="quantity-input" data-cart-id="<?= $row['id'] ?>" value="<?= $row['qty'] ?>" min="1">
                        </div>
                        <button class="remove-btn" data-cart-id="<?= $row['id'] ?>">Remove</button>
                    </div>
                </div>
                <?php endwhile; ?>
            </div>

            <div class="checkout-section">
                <div class="total-price text-3">
                    <strong class="text-3">Total:</strong> EG <?= number_format($grand_total, 2) ?>
                </div>
                <a href="./product-checkout.php" class="checkout-btn text-1">Proceed to Checkout</a>
            </div>

        </div>
        
        <div>
            <?php else: ?>
                <div class="empty-cart">
                    <p class="text-5">Your cart is empty.</p>
                    <a href="index.php" class="continue-shopping text-1">Continue Shopping</a>
                </div>
            <?php endif; ?>
        </div>
    </div>

    <!-- Cart Script -->
    <script src="./assets/scripts/cartPage.js"></script>
    <!-- Add To Cart Script -->
    <script src="./assets/scripts/addToCart.js"></script>
    <!-- include footer -->
    <?php 
        include "./sections/footer/footer.php";
    ?>
</body>
</html>