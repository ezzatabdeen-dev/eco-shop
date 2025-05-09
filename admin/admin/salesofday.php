<?php
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
session_start();

// Check if user is logged in
if(!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

include("./includes/db.php");

// Process order deletion
if(isset($_GET['action']) && $_GET['action'] == 'delete' && isset($_GET['order_id'])) {
    $order_id = intval($_GET['order_id']);
    
    try {
        $stmt = $con->prepare("DELETE FROM orders WHERE order_id = ?");
        $stmt->bind_param("i", $order_id);
        $stmt->execute();
        $stmt->close();
        
        $_SESSION['message'] = "Order deleted successfully";
        header("Location: ".$_SERVER['PHP_SELF']);
        exit();
    } catch(Exception $e) {
        die("Database error: " . $e->getMessage());
    }
}

// Pagination
$page = isset($_GET['page']) ? max(1, intval($_GET['page'])) : 1;
$limit = 10;
$offset = ($page - 1) * $limit;

include "sidenav.php";
include "topheader.php";
?>

<div class="content">
    <div class="container-fluid">
        <?php if(isset($_SESSION['message'])): ?>
            <div class="alert alert-info">
                <?php echo $_SESSION['message']; unset($_SESSION['message']); ?>
            </div>
        <?php endif; ?>
        
        <div class="col-md-14">
            <div class="card">
                <div class="card-header card-header-primary">
                    <h4 class="card-title">Orders Management - Page <?php echo $page; ?></h4>
                </div>
                <div class="card-body">
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead class="text-primary">
                                <tr>
                                    <th>Order ID</th>
                                    <th>Products</th>
                                    <th>Email</th>
                                    <th>Address</th>
                                    <th>Amount</th>
                                    <th>Quantity</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                <?php
                                $query = "SELECT * FROM orders_info LIMIT ?, ?";
                                $stmt = $con->prepare($query);
                                $stmt->bind_param("ii", $offset, $limit);
                                $stmt->execute();
                                $result = $stmt->get_result();
                                
                                if($result->num_rows > 0) {
                                    while($row = $result->fetch_assoc()) {
                                        $order_id = htmlspecialchars($row['order_id']);
                                        $email = htmlspecialchars($row['email']);
                                        $address = htmlspecialchars($row['address']);
                                        $total_amount = htmlspecialchars($row['total_amt']);
                                        $qty = htmlspecialchars($row['prod_count']);
                                ?>
                                <tr>
                                    <td><?php echo $order_id; ?></td>
                                    <td>
                                        <?php
                                        $products_query = "SELECT p.product_title 
                                                         FROM order_products op
                                                         JOIN products p ON op.product_id = p.product_id
                                                         WHERE op.order_id = ?";
                                        $products_stmt = $con->prepare($products_query);
                                        $products_stmt->bind_param("i", $order_id);
                                        $products_stmt->execute();
                                        $products_result = $products_stmt->get_result();
                                        
                                        while($product = $products_result->fetch_assoc()) {
                                            echo htmlspecialchars($product['product_title']) . "<br>";
                                        }
                                        $products_stmt->close();
                                        ?>
                                    </td>
                                    <td><?php echo $email; ?></td>
                                    <td><?php echo $address; ?></td>
                                    <td><?php echo $total_amount; ?></td>
                                    <td><?php echo $qty; ?></td>
                                    <td>
                                        <a href="?action=delete&order_id=<?php echo $order_id; ?>" 
                                           class="btn btn-danger btn-sm"
                                           onclick="return confirm('Are you sure you want to delete this order?')">
                                            Delete
                                        </a>
                                    </td>
                                </tr>
                                <?php
                                    }
                                } else {
                                    echo '<tr><td colspan="7" class="text-center">No orders available</td></tr>';
                                }
                                $stmt->close();
                                ?>
                            </tbody>
                        </table>
                        
                        <!-- Pagination -->
                        <div class="pagination">
                            <?php
                            $count_query = "SELECT COUNT(*) as total FROM orders_info";
                            $count_result = $con->query($count_query);
                            $total_records = $count_result->fetch_assoc()['total'];
                            $total_pages = ceil($total_records / $limit);
                            
                            for($i = 1; $i <= $total_pages; $i++) {
                                echo "<a href='?page=$i' class='btn btn-primary".($page==$i?" active":"")."'>$i</a> ";
                            }
                            ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
include "footer.php";
?>