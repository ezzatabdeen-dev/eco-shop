<?php include("./server/server.php"); ?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Admin Login</title>
    <!-- Main css -->
    <link rel="stylesheet" href="./assets/css/adminLogin.css">
    <link rel="stylesheet" href="../assets/css/gloable.css">
</head>
<body>
    <!-- Sing in  Form -->
    <div class="container signInContainer">
            <div class="wraperPrand">
                <img src="../assets/public_img/OnlineShopLogo.svg" alt="prand" width="200" height="100">
            </div>
            <div class="signinWraper">
                <div class="wraperForm">
                    <h2 class="formTitle text-6">admin login</h2>
                    <form  id="login-form" action="login.php" method="post">
                        <div class="alert alert-danger">
                            <span id="e_msg text-2"><?php include('./server/errors.php'); ?></span>
                        </div>
                        <input type="text" name="admin_username" id="your_name" placeholder="Email"/>
                        <input type="password" name="password" id="your_pass" placeholder="Password"/>
                        <input type="submit" name="login_admin" id="signin" class="form-submit" value="Log in"/>
                    </form>
                </div>
                <div class="backToHome">
                    <a href="../index.php" class="signup-image-link text-2">Back To Home</a>
                </div>
            </div>
        </div>
    </div>
    <!-- JS -->
    <script src="vendor/jquery/jquery.min.js"></script>
    <script src="js/main.js"></script>
</body>
</html>