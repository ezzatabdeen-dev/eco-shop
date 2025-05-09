<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

include("../../db.php");

if (!$con) {
    die("Database connection failed: " . mysqli_connect_error());
}

if(isset($_POST['btn_save'])) {
    // Validate all required fields
    $required_fields = ['product_name', 'details', 'price', 'product_type', 'brand', 'tags', 'quantity', 'picture'];
    foreach($required_fields as $field) {
        if(empty($_POST[$field]) && $field != 'picture') {
            $_SESSION['error'] = "$field field is required";
            header("Location: add_products.php");
            exit();
        }
    }

    $product_name = mysqli_real_escape_string($con, $_POST['product_name']);
    $details = mysqli_real_escape_string($con, $_POST['details']);
    $price = floatval($_POST['price']);
    $product_type = intval($_POST['product_type']);
    $brand = intval($_POST['brand']);
    $tags = mysqli_real_escape_string($con, $_POST['tags']);
    $quantity = intval($_POST['quantity']);

    // Process image
    $picture_tmp_name = $_FILES['picture']['tmp_name'];
    $picture_size = $_FILES['picture']['size'];
    $picture_name = $_FILES['picture']['name'];

    // Validate file type
    $allowed_types = ['image/jpeg', 'image/jpg', 'image/png', 'image/gif'];
    $finfo = finfo_open(FILEINFO_MIME_TYPE);
    $mime_type = finfo_file($finfo, $picture_tmp_name);
    finfo_close($finfo);

    if(!in_array($mime_type, $allowed_types)) {
        $_SESSION['error'] = "File type not allowed. Please upload an image in JPEG, JPG, PNG or GIF format";
        header("Location: add_products.php");
        exit();
    }

    // Validate file size
    if($picture_size > 5000000) {
        $_SESSION['error'] = "Image size is too large. Maximum allowed size is 5MB";
        header("Location: add_products.php");
        exit();
    }

    // Create unique name for the image
    $file_extension = pathinfo($picture_name, PATHINFO_EXTENSION);
    $pic_name = time() . "_" . uniqid() . "." . $file_extension;
    $upload_dir = realpath(dirname(__FILE__) . '/../../assets/products_images/') . '/';
    $target_path = $upload_dir . $pic_name;

    if (!file_exists($upload_dir)) {
        if (!mkdir($upload_dir, 0777, true)) {
            $_SESSION['error'] = "Failed to create images folder: " . $upload_dir;
            header("Location: add_products.php");
            exit();
        }
    }

    if (!is_writable($upload_dir)) {
        $_SESSION['error'] = "No write permissions for folder: " . $upload_dir;
        header("Location: add_products.php");
        exit();
    }

    error_log("Upload path: " . $target_path);
    error_log("Folder permissions: " . decoct(fileperms($upload_dir)));

    if(!move_uploaded_file($picture_tmp_name, $target_path)) {
        $error = error_get_last();
        $_SESSION['error'] = "Error uploading image: " . $error['message'];
        header("Location: add_products.php");
        exit();
    }

    $insert_query = "INSERT INTO products 
                    (product_cat, product_brand, product_title, product_price, product_desc, product_image, product_keywords, product_quantity) 
                    VALUES 
                    ('$product_type', '$brand', '$product_name', '$price', '$details', '$pic_name', '$tags', '$quantity')";
    
    if(mysqli_query($con, $insert_query)) {
        $_SESSION['success'] = "Product added successfully!";
        header("Location: products_list.php");
        exit();
    } else {
        $_SESSION['error'] = "Error adding product: " . mysqli_error($con);
        if(file_exists($target_path)) {
            unlink($target_path);
        }
        header("Location: add_products.php");
        exit();
    }
}

include "sidenav.php";
include "topheader.php";
?>

<div class="content">
    <div class="container-fluid">
        <?php
        if(isset($_SESSION['error'])) {
            echo '<div class="alert alert-danger">' . $_SESSION['error'] . '</div>';
            unset($_SESSION['error']);
        }
        if(isset($_SESSION['success'])) {
            echo '<div class="alert alert-success">' . $_SESSION['success'] . '</div>';
            unset($_SESSION['success']);
        }
        ?>
        
        <form action="" method="post" enctype="multipart/form-data">
            <div class="row">
                <div class="col-md-7">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h5 class="title">Add Product</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="product_name">Product Title</label>
                                        <input type="text" id="product_name" required name="product_name" class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-4">
                                    <div>
                                        <label for="picture">Add Image</label>
                                        <input type="file" name="picture" required class="btn btn-fill btn-success" id="picture">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="details">Description</label>
                                        <textarea rows="4" cols="80" id="details" required name="details" class="form-control"></textarea>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="price">Pricing</label>
                                        <input type="number" step="0.01" id="price" name="price" required class="form-control">
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="quantity">Quantity</label>
                                        <input type="number" id="quantity" name="quantity" required class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="col-md-5">
                    <div class="card">
                        <div class="card-header card-header-primary">
                            <h5 class="title">Categories</h5>
                        </div>
                        <div class="card-body">
                            <div class="row">
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Product Category</label>
                                        <select id="product_type" name="product_type" required class="form-control">
                                            <option value="" style='color: #000'>Select Category</option>
                                            <?php
                                            $categories = mysqli_query($con, "SELECT * FROM categories ORDER BY cat_id ASC");
                                            while($cat = mysqli_fetch_assoc($categories)) {
                                                echo "<option value='{$cat['cat_id']}' style='color: #000'>{$cat['cat_title']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label for="">Product Brand</label>
                                        <select id="brand" name="brand" required class="form-control">
                                            <option value="" style='color: #000'>Select Brand</option>
                                            <?php
                                            $brands = mysqli_query($con, "SELECT * FROM brands ORDER BY brand_id ASC");
                                            while($brand = mysqli_fetch_assoc($brands)) {
                                                echo "<option value='{$brand['brand_id']}' style='color: #000'>{$brand['brand_title']}</option>";
                                            }
                                            ?>
                                        </select>
                                    </div>
                                </div>
                                <div class="col-md-12">
                                    <div class="form-group">
                                        <label>Product Keywords</label>
                                        <input type="text" id="tags" name="tags" required class="form-control">
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="card-footer">
                            <button type="submit" id="btn_save" name="btn_save" class="btn btn-fill btn-primary">Add Product</button>
                        </div>
                    </div>
                </div>
            </div>
        </form>
    </div>
</div>
<?php
include "footer.php";
?>