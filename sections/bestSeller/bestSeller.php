<!-- Best Seller Section Slider -->
<div class="container topSelectedContainer">
    <div class="topSelectedItems">
        <div class="topSelectedHeader">
            <h2 class="topSelectedTitle text-5">Best Seller</h2>
            <div class="topSelectedSliderControler">
                <span class="topSelectedProduct-next"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M310.6 233.4c12.5 12.5 12.5 32.8 0 45.3l-192 192c-12.5 12.5-32.8 12.5-45.3 0s-12.5-32.8 0-45.3L242.7 256 73.4 86.6c-12.5-12.5-12.5-32.8 0-45.3s32.8-12.5 45.3 0l192 192z"/></svg></span>
                <span class="topSelectedProduct-prev"><svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 320 512"><path d="M9.4 233.4c-12.5 12.5-12.5 32.8 0 45.3l192 192c12.5 12.5 32.8 12.5 45.3 0s12.5-32.8 0-45.3L77.3 256 246.6 86.6c12.5-12.5 12.5-32.8 0-45.3s-32.8-12.5-45.3 0l-192 192z"/></svg></span>
            </div>
        </div>
        
        <div class="swiper topSelectedProdutsSlider">
            <div class="swiper-wrapper">

                <?php
                include 'db.php';

                $product_query = "SELECT * FROM products,categories WHERE product_cat=cat_id AND product_quantity > 0";
                $run_query = mysqli_query($con, $product_query);
                
                if(mysqli_num_rows($run_query) > 0){
                    while($row = mysqli_fetch_array($run_query)){
                        $pro_id    = $row['product_id'];
                        $pro_cat   = $row['product_cat'];
                        $pro_brand = $row['product_brand'];
                        $pro_title = $row['product_title'];
                        $pro_price = $row['product_price'];
                        $pro_image = $row['product_image'];
                        $cat_name  = $row["cat_title"];

                        $rating_query = "SELECT ROUND(AVG(rating),1) AS avg_rating FROM reviews WHERE product_id='$pro_id'";
                        $run_review_query = mysqli_query($con, $rating_query);
                        $review_row = mysqli_fetch_array($run_review_query);
                        $avg_count = isset($review_row["avg_rating"]) ? round($review_row["avg_rating"]) : 0;

                        echo "
                        <div class='swiper-slide'>
                            <div class='productSlide'>
                                <a href='product.php?p=$pro_id'>
                                    <div class='product-img'>
                                        <img src='./assets/products_images/$pro_image' style='max-height: 170px;' alt='$pro_id'>
                                    </div>
                                </a>
                                <div class='product-label'>
                                    <button data-product-id='$pro_id' class='addproductCar'>
                                        <span>
                                            <svg id='Layer_1' class='fa-shopping-cart' data-name='Layer 1' xmlns='http://www.w3.org/2000/svg' viewBox='0 0 762.47 673.5'>
                                                <path d='M600.86,489.86a91.82,91.82,0,1,0,91.82,91.82A91.81,91.81,0,0,0,600.86,489.86Zm0,142.93a51.12,51.12,0,1,1,51.11-51.11A51.12,51.12,0,0,1,600.82,632.79Z'></path><path d='M303.75,489.86a91.82,91.82,0,1,0,91.82,91.82A91.82,91.82,0,0,0,303.75,489.86Zm-.05,142.93a51.12,51.12,0,1,1,51.12-51.11A51.11,51.11,0,0,1,303.7,632.79Z'></path><path d='M392.07,561.33h66.55a20.52,20.52,0,0,1,20.46,20.46h0a20.52,20.52,0,0,1-20.46,20.46H392.07'></path><path d='M698.19,451.14H205.93a23.11,23.11,0,0,1-23.09-22c0-.86-.09-1.72-.19-2.57l-1.82-16.36H723.51L721.3,428A23.11,23.11,0,0,1,698.19,451.14Z'></path><path d='M759.15,153.79H246.94l-3.32-24.38a17.25,17.25,0,0,1,17.25-17.26H745.21a17.26,17.26,0,0,1,17.26,17.26Z'></path><path d='M271.55,345.56l-31.16-208A20.53,20.53,0,0,1,257.13,114h0a20.53,20.53,0,0,1,23.6,16.74l31.16,208a20.52,20.52,0,0,1-16.74,23.59h0A20.52,20.52,0,0,1,271.55,345.56Z'></path><path d='M676,451.15l48.69-337.74,22.9.07a17.25,17.25,0,0,1,14.55,19.59l-42.1,303.16a17.24,17.24,0,0,1-19.59,14.54Z'></path><path d='M184.24,436.27,123.7.12l23.72,0a17.26,17.26,0,0,1,19.33,14.92l60.56,436.35-23.74-.25A17.25,17.25,0,0,1,184.24,436.27Z'></path><path d='M148.38,40.77H20.26A20.32,20.32,0,0,1,0,20.51H0A20.32,20.32,0,0,1,20.26.25H148.38'></path>
                                            </svg>
                                        </span>
                                    </button>
                                </div>
                                <div class='product-body'>
                                    <p class='product-category text-1'>$cat_name</p>
                                    <h3 class='product-name header-cart-item-name'>
                                        <a href='product.php?p=$pro_id' class='text-3 text-ellipsis-2'>$pro_title</a>
                                    </h3>
                                    <h4 class='product-price header-cart-item-info text-1'>
                                        EG $pro_price<del class='product-old-price' style='color: red; margin-left: .5rem; font-size: 12px'> EG" . number_format($pro_price * 1.2) . "</del>
                                    </h4>
                                    <div class='product-rating'>";
                                    
                                        if ($avg_count > 0) {
                                            for ($i = 1; $i <= $avg_count; $i++) {
                                                echo '<i class="fa fa-star"></i>';
                                            }
                                            for ($i = $avg_count + 1; $i <= 5; $i++) {
                                                echo '<i class="fa fa-star-o empty starColor"></i>';
                                            }
                                        } else {
                                            echo "<span class='text-1' style='color: rgb(51, 51, 51);'>No ratings yet</span>";
                                        }

                                    echo "
                                    </div>
                                </div>
                            </div>
                        </div>";
                    }
                }
                ?>

            </div>
        </div>
    </div>
</div>