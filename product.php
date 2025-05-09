<?php
// Start session and include database connection
session_start();
include "db.php";

// Get product ID from URL with validation
$product_id = isset($_GET['p']) ? intval($_GET['p']) : 0;

// Initialize variables
$product_data = [];
$related_products = [];

// Fetch product details
if ($product_id > 0) {
    $product_query = "SELECT * FROM products AS P 
                     JOIN categories AS C ON P.product_cat = C.cat_id 
                     WHERE P.product_id = '$product_id'";
    $product_result = mysqli_query($con, $product_query);
    
    if ($product_result && mysqli_num_rows($product_result) > 0) {
        $product_data = mysqli_fetch_assoc($product_result);
        $_SESSION['product_id'] = $product_id;
        
        // Fetch related products from the same category (excluding current product)
        $category_id = $product_data['product_cat'];
        $related_query = "SELECT * FROM products AS P
                         JOIN categories AS C ON P.product_cat = C.cat_id
                         WHERE P.product_cat = '$category_id' 
                         AND P.product_id != '$product_id'
                         ORDER BY RAND() LIMIT 8";
        
        $related_result = mysqli_query($con, $related_query);
        
        if ($related_result) {
            while($row = mysqli_fetch_assoc($related_result)) {
                $related_products[] = $row;
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8" />
    <meta http-equiv="X-UA-Compatible" content="IE=edge" />
    <meta name="viewport" content="width=device-width, initial-scale=1" />
    <link rel="icon" type="image/png" href="./assets/public_img/favicon.png" />
    <meta name="theme-color" content="#ff7226" />
    <title><?= !empty($product_data) ? $product_data['product_title'] : 'Product Details' ?></title>

    <link rel="stylesheet" href="./assets/css/gloable.css" />
    <link rel="stylesheet" href="./assets/css/productViewer.css" />
    <link rel="stylesheet" href="./sections/header/header.css" />
    <link rel="stylesheet" href="./sections/footer/footer.css" />
  </head>
  <body>
    <!-- Include Header -->
    <?php include "./sections/header/header.php"; ?>

    <!-- MAIN PRODUCT DETAILS SECTION -->
    <div class="container">
      <div class="viewerProductWraper">
        <div class="viewerProductItem">
          <?php if (!empty($product_data)): ?>
            <div class="product-img">
              <img src="./assets/products_images/<?= $product_data['product_image'] ?>" alt="<?= $product_data['product_title'] ?>" />
            </div>

            <div class="product-body">
              <div class="product-details">
                <div class="productHeader">
                    <h2 class="product-name text-6"><?= $product_data['product_title'] ?></h2>
                    <p class="text-4" style="font-wight: 600"><?= $product_data['product_desc'] ?></p>
                </div>
                <div id="rating_reviews"></div>
                <div class="product-price">
                  <h3 class="text-3">Price: 
                    EG <?= $product_data['product_price'] ?>
                    <del style="color: red" class="product-old-price">EG <?= $product_data['product_price'] * 1.2 ?></del>
                  </h3>
                  <p class="text-3">Quantity available
                  <span class="product-available text-3"><?= $product_data['product_quantity'] ?> In Stock</span>
                  </p>
                </div>

                <div class="add-to-cart">
                  <div class="quantity-label">
                    <h3 class="quantityTitle-lable">Quantity</h3>
                    <div class="input-number">
                      <input id="inputNumber" type="number" value="1" />
                      <span class="quantity-up">+</span>
                      <span class="quantity-down">-</span>
                    </div>
                  </div>

                    <button class="addproductCar add-to-cart-btn text-1" data-product-id="<?= $product_data['product_id'] ?>" id="product">
                        <span>
                            <svg id="Layer_1" class="fa-shopping-cart" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 762.47 673.5" width="50" height="50">
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
                        ADD TO CART
                    </button>
                </div>

                <script src="./assets/scripts/productViewer.js"></script>

                <div class="product-category">
                  <p>
                    <span>Category:</span>
                    <?= $product_data['cat_title'] ?>
                  </p>
                </div>

                <ul class="shareProduct">
                    <a href="#">
                        <li>
                            <svg aria-hidden="true" focusable="false" class="icon icon-facebook" viewBox="0 0 18 18" width="20" height="20">
                                <path fill="currentColor" d="M16.42.61c.27 0 .5.1.69.28.19.2.28.42.28.7v15.44c0 .27-.1.5-.28.69a.94.94 0 01-.7.28h-4.39v-6.7h2.25l.31-2.65h-2.56v-1.7c0-.4.1-.72.28-.93.18-.2.5-.32 1-.32h1.37V3.35c-.6-.06-1.27-.1-2.01-.1-1.01 0-1.83.3-2.45.9-.62.6-.93 1.44-.93 2.53v1.97H7.04v2.65h2.24V18H.98c-.28 0-.5-.1-.7-.28a.94.94 0 01-.28-.7V1.59c0-.27.1-.5.28-.69a.94.94 0 01.7-.28h15.44z"></path>
                            </svg>
                        </li>
                    </a>
                    <a href="#">
                        <li>
                            <svg aria-hidden="true" focusable="false" class="icon icon-instagram" viewBox="0 0 18 18" width="20" height="20">
                                <path fill="currentColor" d="M8.77 1.58c2.34 0 2.62.01 3.54.05.86.04 1.32.18 1.63.3.41.17.7.35 1.01.66.3.3.5.6.65 1 .12.32.27.78.3 1.64.05.92.06 1.2.06 3.54s-.01 2.62-.05 3.54a4.79 4.79 0 01-.3 1.63c-.17.41-.35.7-.66 1.01-.3.3-.6.5-1.01.66-.31.12-.77.26-1.63.3-.92.04-1.2.05-3.54.05s-2.62 0-3.55-.05a4.79 4.79 0 01-1.62-.3c-.42-.16-.7-.35-1.01-.66-.31-.3-.5-.6-.66-1a4.87 4.87 0 01-.3-1.64c-.04-.92-.05-1.2-.05-3.54s0-2.62.05-3.54c.04-.86.18-1.32.3-1.63.16-.41.35-.7.66-1.01.3-.3.6-.5 1-.65.32-.12.78-.27 1.63-.3.93-.05 1.2-.06 3.55-.06zm0-1.58C6.39 0 6.09.01 5.15.05c-.93.04-1.57.2-2.13.4-.57.23-1.06.54-1.55 1.02C1 1.96.7 2.45.46 3.02c-.22.56-.37 1.2-.4 2.13C0 6.1 0 6.4 0 8.77s.01 2.68.05 3.61c.04.94.2 1.57.4 2.13.23.58.54 1.07 1.02 1.56.49.48.98.78 1.55 1.01.56.22 1.2.37 2.13.4.94.05 1.24.06 3.62.06 2.39 0 2.68-.01 3.62-.05.93-.04 1.57-.2 2.13-.41a4.27 4.27 0 001.55-1.01c.49-.49.79-.98 1.01-1.56.22-.55.37-1.19.41-2.13.04-.93.05-1.23.05-3.61 0-2.39 0-2.68-.05-3.62a6.47 6.47 0 00-.4-2.13 4.27 4.27 0 00-1.02-1.55A4.35 4.35 0 0014.52.46a6.43 6.43 0 00-2.13-.41A69 69 0 008.77 0z"></path>
                                <path fill="currentColor" d="M8.8 4a4.5 4.5 0 100 9 4.5 4.5 0 000-9zm0 7.43a2.92 2.92 0 110-5.85 2.92 2.92 0 010 5.85zM13.43 5a1.05 1.05 0 100-2.1 1.05 1.05 0 000 2.1z"></path>
                            </svg>
                        </li>
                    </a>
                    <a href="#">
                        <li>
                            <svg aria-hidden="true" focusable="false" class="icon icon-youtube" viewBox="0 0 100 70" width="20" height="20">
                                <path d="M98 11c2 7.7 2 24 2 24s0 16.3-2 24a12.5 12.5 0 01-9 9c-7.7 2-39 2-39 2s-31.3 0-39-2a12.5 12.5 0 01-9-9c-2-7.7-2-24-2-24s0-16.3 2-24c1.2-4.4 4.6-7.8 9-9 7.7-2 39-2 39-2s31.3 0 39 2c4.4 1.2 7.8 4.6 9 9zM40 50l26-15-26-15v30z" fill="currentColor"></path>
                            </svg>
                        </li>
                    </a>
                    <a href="#">
                        <li>
                            <svg aria-hidden="true" class="icon icon-twitter" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="20" height="20">
                                <path d="M389.2 48h70.6L305.6 224.2 487 464H345L233.7 318.6 106.5 464H35.8L200.7 275.5 26.8 48H172.4L272.9 180.9 389.2 48zM364.4 421.8h39.1L151.1 88h-42L364.4 421.8z" fill="currentColor"></path>
                            </svg>
                        </li>
                    </a>
                </ul>
              </div>

              <!-- Product Rating -->
              <div class="rating-content">
                <p class="is-rating">This is a cool white shirt that can be worn in any occasion</p>

                <div class="ratingFormWraper">
                  <div id="review_action" pid="<?= $product_id ?>"></div>
                  <div class="row">
                    <div class="col-md-6">
                      <div id="review-form">
                        <form class="review-form" onsubmit="return false" id="review_form" required>
                          <input class="input" type="text" name="name" placeholder="Your Name" required />
                          <input class="input" type="email" name="email" placeholder="Your Email" required />
                          <input name="product_id" value="<?= $product_id ?>" hidden required />
                          <textarea class="input" name="review" placeholder="Your Review"></textarea>
                          <div class="rating-StarWraper">
                            <h3>Your Rating</h3>
                            <div class="stars">
                              <?php for ($i = 5; $i >= 1; $i--): ?>
                                <input id="star<?= $i ?>" name="rating" value="<?= $i ?>" type="radio" required />
                                <label for="star<?= $i ?>">
                                    <svg xmlns="http://www.w3.org/2000/svg" width="16" height="16" fill="currentColor" class="bi bi-star-fill" viewBox="0 0 16 16">
                                        <path d="M3.612 15.443c-.386.198-.824-.149-.746-.592l.83-4.73L.173 6.765c-.329-.314-.158-.888.283-.95l4.898-.696L7.538.792c.197-.39.73-.39.927 0l2.184 4.327 4.898.696c.441.062.612.636.282.95l-3.522 3.356.83 4.73c.078.443-.36.79-.746.592L8 13.187l-4.389 2.256z"/>
                                    </svg>
                                </label>
                              <?php endfor; ?>
                            </div>
                          </div>
                          <button class="primary-btn" name="review_submit">Add Yor Rating</button>
                        </form>
                      </div>
                    </div>
                  </div>
                </div>
              </div>
            </div>
          <?php else: ?>
            <div class="col-md-12">
              <p>Product not found.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- RELATED PRODUCTS SECTION -->
    <div class="container">
      <div class="relatedProductsSection">
        <div class="relatedProductsHeader">
          <h3 class="relatedProductsTitle">Related Products</h3>
        </div>

        <div class="relatedProductsItems">
          <?php if (!empty($related_products)): ?>
            <?php foreach ($related_products as $product): ?>
              <div class="relatedProductsItem">
                <a href="product.php?p=<?= $product['product_id'] ?>">
                  <div class="product-img">
                    <img src="./assets/products_images/<?= $product['product_image'] ?>" alt="<?= $product['product_title'] ?>" />
                  </div>
                </a>

                <div class="product-discount">
                  <span class="sale">-30%</span>
                  <span class="new">NEW</span>
                </div>

                <div class="product-body">
                  <p class="product-category"><?= $product['cat_title'] ?></p>
                  <h3 class="product-name header-cart-item-name text-3 text-ellipsis-2">
                    <a href="product.php?p=<?= $product['product_id'] ?>" ><?= $product['product_title'] ?></a>
                  </h3>
                  <h4 class="product-price header-cart-item-info text-2">
                    EG <?= $product['product_price'] ?>
                    <del style="color: red" class="product-old-price text-1">EG <?= $product_data['product_price'] * 1.2 ?></del>
                  </h4>

                  <div class="product-rating">
                    <?php
                    $rating_query = "SELECT ROUND(AVG(rating),1) AS avg_rating FROM reviews WHERE product_id='{$product['product_id']}'";
                    $run_review_query = mysqli_query($con, $rating_query);
                    $review_row = mysqli_fetch_array($run_review_query);
                    $avg_count = isset($review_row["avg_rating"]) ? round($review_row["avg_rating"]) : 0;
                    
                    if ($avg_count > 0) {
                        for ($i = 1; $i <= 5; $i++) {
                            echo $i <= $avg_count 
                                ? '<i class="fa fa-star"></i>' 
                                : '<i class="fa fa-star-o empty"></i>';
                        }
                    } else {
                        echo "No ratings yet";
                    }?>
                  </div>

                  <div class="product-label">
                    <button data-product-id="<?= $product['product_id'] ?>" id="wishlist" class="addproductCar add-to-wishlist">
                      <span>
                        <svg id="Layer_1" class="fa-shopping-cart" data-name="Layer 1" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 762.47 673.5" width="50" height="50">
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
                    </button>
                  </div>
                </div>
              </div>
            <?php endforeach; ?>
          <?php else: ?>
            <div class="col-md-12">
              <p>No related products found.</p>
            </div>
          <?php endif; ?>
        </div>
      </div>
    </div>

    <!-- FOOTER SECTION -->
    <?php include "./sections/footer/footer.php"; ?>

    <!-- Add To Cart Acript -->
    <script src="./assets/scripts/addToCart.js"></script>
  </body>
</html>