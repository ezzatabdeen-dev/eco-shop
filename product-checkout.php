<?php
session_start();
require_once __DIR__.'/./db.php';

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

if ($result->num_rows === 0) {
    header("Location: cart.php");
    exit();
}

$grand_total = 0;
$cart_items = [];
$item_count = 0;

while ($row = $result->fetch_assoc()) {
    $subtotal = $row['product_price'] * $row['qty'];
    $grand_total += $subtotal;
    $item_count += $row['qty'];
    $cart_items[] = $row;
}

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $full_name = $_POST['full_name'] ?? '';
    $email = $_POST['email'] ?? '';
    $country = $_POST['country'] ?? '';
    $state = $_POST['state'] ?? '';
    $postal_code = $_POST['postal_code'] ?? '';
    $payment_method = $_POST['payment_method'] ?? '';
    
    
    $order_query = "INSERT INTO orders (user_id, total_amount, payment_method, full_name, email, country, state, postal_code, status) 
                    VALUES (?, ?, ?, ?, ?, ?, ?, ?, 'pending')";
    
    $stmt = $con->prepare($order_query);
    $stmt->bind_param("idssssss", $user_id, $grand_total, $payment_method, $full_name, $email, $country, $state, $postal_code);
    $stmt->execute();
    $order_id = $con->insert_id;
    
    foreach ($cart_items as $item) {
        $order_item_query = "INSERT INTO order_items (order_id, product_id, quantity, price) 
                            VALUES (?, ?, ?, ?)";
        $stmt = $con->prepare($order_item_query);
        $stmt->bind_param("iiid", $order_id, $item['p_id'], $item['qty'], $item['product_price']);
        $stmt->execute();
    }
    
    $clear_cart_query = "DELETE FROM cart WHERE user_id = ? OR ip_add = ?";
    $stmt = $con->prepare($clear_cart_query);
    $stmt->bind_param("is", $user_id, $ip_address);
    $stmt->execute();
    
    header("Location: order-confirmation.php?id=" . $order_id);
    exit();
}
?>

<!DOCTYPE html>
<html lang="en" dir="ltr">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Checkout Page</title>
    <link type='text/css' rel='stylesheet' href='./assets/css/gloable.css'/>
    <link type='text/css' rel='stylesheet' href='./assets/css/checkoutPage.css'/>
    <link type='text/css' rel='stylesheet' href='./sections/header/header.css' />
    <link type='text/css' rel='stylesheet' href='./sections/footer/footer.css' />
</head>
<body style="background-color: #fff">

    <!-- include header -->
    <?php include "./sections/header/header.php"; ?>

    <div class="container">
        <div class="checkout-container">
            <div class="left-section">
                <form id="checkout-form" method="POST">
                    <div class="billing-section">
                        <h2 class="text-3">Billing details</h2>
                        
                        <div class="form-group">
                            <h3 class="text-2">First & last name</h3>
                            <input type="text" name="full_name" placeholder="Enter your full name" required>
                        </div>
                        
                        <div class="form-group">
                            <h3 class="text-2">Email address</h3>
                            <input type="email" name="email" placeholder="Please enter a valid email address" required>
                        </div>
                        
                        <div class="form-group">
                            <h3 class="text-2">Country</h3>
                            <select name="country" required>
                                <option value="">Select Country</option>
                                <option value="United States of America">United States of America</option>
                                <option value="United Kingdom">United Kingdom</option>
                                <option value="Canada">Canada</option>
                                <option value="Australia">Australia</option>
                            </select>
                        </div>
                        
                        <div class="form-group">
                            <h3 class="text-2">State / Province</h3>
                            <input type="text" name="state" placeholder="Enter your state or province" required>
                        </div>
                        
                        <div class="form-group">
                            <h3 class="text-2">Postal code</h3>
                            <input type="text" name="postal_code" placeholder="Enter your postal code" required>
                        </div>
                    </div>
                    
                    <div class="payment-section">
                        <h2 class="text-3">Payment method</h2>
                        
                        <div class="payment-option active">
                            <h3 class="text-2">
                                <input type="radio" name="payment_method" value="credit_card" id="credit_card" checked>
                                <label for="credit_card">Credit card</label>
                            </h3>
                            <div class="form-group">
                                <input type="text" class="card-number" placeholder="4123 4067 123 9819" name="card_number">
                            </div>
                            
                            <div class="form-group">
                                <h3>Expiration date</h3>
                                <div class="expiry-cvv">
                                    <div>
                                        <select name="exp_month">
                                            <option value="">Month</option>
                                            <option value="01">01</option>
                                            <option value="02">02</option>
                                            <option value="03">03</option>
                                            <option value="04">04</option>
                                            <option value="05">05</option>
                                            <option value="06">06</option>
                                            <option value="07">07</option>
                                            <option value="08">08</option>
                                            <option value="09">09</option>
                                            <option value="10">10</option>
                                            <option value="11">11</option>
                                            <option value="12">12</option>
                                        </select>
                                    </div>
                                    <div>
                                        <select name="exp_year">
                                            <option value="">Year</option>
                                            <option value="2023">2023</option>
                                            <option value="2024">2024</option>
                                            <option value="2025">2025</option>
                                            <option value="2026">2026</option>
                                            <option value="2027">2027</option>
                                        </select>
                                    </div>
                                    <div>
                                        <input type="text" name="cvv" placeholder="CVV" maxlength="3">
                                    </div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="divider"></div>
                        
                        <div class="payment-option">
                            <h3>
                                <input type="radio" name="payment_method" value="paypal" id="paypal">
                                <label for="paypal">PayPal</label>
                            </h3>
                            <p>You will be redirected to PayPal website to complete your purchase securely.</p>
                        </div>
                        
                        <button type="submit" class="btn">Complete Order</button>
                        
                        <div class="terms">
                            By clicking the button, you agree to our <a href="#">Terms and Conditions</a>
                        </div>
                    </div>
                </form>
            </div>
            
            <div class="cart-summary">
                <div class="cartSummary-title">
                    <span class="cari-i">
                        <svg id="Layer_1" class="fa-shopping-cart" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 762.47 673.5">
                            <path d="M600.86,489.86a91.82,91.82,0,1,0,91.82,91.82A91.81,91.81,0,0,0,600.86,489.86Zm0,142.93a51.12,51.12,0,1,1,51.11-51.11A51.12,51.12,0,0,1,600.82,632.79Z"></path>
                            <path d="M303.75,489.86a91.82,91.82,0,1,0,91.82,91.82A91.82,91.82,0,0,0,303.75,489.86Zm-.05,142.93a51.12,51.12,0,1,1,51.12-51.11A51.11,51.11,0,0,1,303.7,632.79Z"></path>
                            <path d="M392.07,561.33h66.55a20.52,20.52,0,0,1,20.46,20.46h0a20.52,20.52,0,0,1-20.46,20.46H392.07"></path>
                            <path d="M698.19,451.14H205.93a23.11,23.11,0,0,1-23.09-22c0-.86-.09-1.72-.19-2.57l-1.82-16.36H723.51L721.3,428A23.11,23.11,0,0,1,698.19,451.14Z"></path>
                            <path d="M759.15,153.79H246.94l-3.32-24.38a17.25,17.25,0,0,1,17.25-17.26H745.21a17.26,17.26,0,0,1,17.26,17.26Z"></path>
                            <path d="M271.55,345.56l-31.16-208A20.53,20.53,0,0,1,257.13,114h0a20.53,20.53,0,0,1,23.6,16.74l31.16,208a20.52,20.52,0,0,1-16.74,23.59h0A20.52,20.52,0,0,1,271.55,345.56Z"></path>
                            <path d="M676,451.15l48.69-337.74,22.9.07a17.25,17.25,0,0,1,14.55,19.59l-42.1,303.16a17.24,17.24,0,0,1-19.59,14.54Z"></path>
                            <path d="M184.24,436.27,123.7.12l23.72,0a17.26,17.26,0,0,1,19.33,14.92l60.56,436.35-23.74-.25A17.25,17.25,0,0,1,184.24,436.27Z"></path>
                            <path d="M148.38,40.77H20.26A20.32,20.32,0,0,1,0,20.51H0A20.32,20.32,0,0,1,20.26.25H148.38"></path>
                        </svg>
                    </span>
                    <h2 class="text-3">Cart summary (<?= $item_count ?> items)</h2>
                </div>

                <?php foreach ($cart_items as $item): ?>
                <div class="cart-item">
                    <div class="item-title allChild text-1 text-ellipsis-2"><?= $item['qty'] ?> x <?= htmlspecialchars($item['product_title']) ?></div>
                    <div class="item-price allChild text-1">EG <?= number_format($item['product_price'] * $item['qty'], 2) ?></div>
                </div>
                <?php endforeach; ?>
                
                <div class="divider"></div>
                
                <div class="subtotal text-3">
                    Sub total: EG <?= number_format($grand_total, 2) ?>
                </div>
            </div>
        </div>
    </div>

    <!-- include footer -->
    <?php include "./sections/footer/footer.php"; ?>

    <!-- Add To Cart Script -->
    <script src="./assets/scripts/addToCart.js"></script>
    <script src="./assets/scripts/checkoutPage.js"></script>
</body>
</html>