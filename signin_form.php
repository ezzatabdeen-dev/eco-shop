<?php
session_start();
error_reporting(E_ALL);
ini_set('display_errors', 1);

// If already logged in
if (isset($_SESSION['user_id'])) {
    $redirect_url = $_SESSION['redirect_url'] ?? 'index.php';
    unset($_SESSION['redirect_url']);
    header("Location: " . $redirect_url);
    exit();
}

// Login processing
if ($_SERVER['REQUEST_METHOD'] == 'POST') {
    require 'config.php';
    
    $email = filter_var($_POST['email'], FILTER_SANITIZE_EMAIL);
    $password = $_POST['password'];
    
    $stmt = $con->prepare("SELECT user_id, password FROM user_info WHERE email = ?");
    $stmt->bind_param("s", $email);
    $stmt->execute();
    $result = $stmt->get_result();
    
    if ($result->num_rows === 1) {
        $user = $result->fetch_assoc();
        
        // Password verification
        if (password_verify($password, $user['password'])) {
            $_SESSION['user_id'] = $user['user_id'];
            
            $redirect_url = $_SESSION['redirect_url'] ?? 'index.php';
            unset($_SESSION['redirect_url']);
            
            header("Location: " . $redirect_url);
            exit();
        }
    }
    
    $error = "Incorrect email or password";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="icon" type="image/png" href="./assets/public_img/favicon.png">
    <meta name="theme-color" content="#ff7226">
    <title>Login Page</title>
    <link rel="stylesheet" href="./assets/css/userLogin.css">
    <link rel="stylesheet" href="./assets/css/gloable.css">
</head>
<body>
    <div class="container signInContainer">
        <div class="wraperPrand">
            <img src="./assets/public_img/OnlineShopLogo.svg" alt="prand" width="200" height="100">
        </div>
        <div class="wraperUserLogin">
            <div class="wraperForm">
                <form id="loginForm" class="login100-form validate-form">
                    <h2 class="formTitle text-6">login</h2>
                    <input type="email" name="email" id="email" placeholder="Email" required />
                    <input class="input100" type="password" name="password" id="password" placeholder="Password" required />

                    <div class="wraperAgreeChcer">
                        <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me" />
                        <label class="label-checkbox100" for="ckb1">
                            <span>I agree to the <a href="#" class="txt2 hov1">Terms of User</a></span>
                        </label>
                    </div>

                    <button class="form-submit" type="submit">Sign in</button>

                    <div class="anutherLink">
                        <a href="signup_form.php" class="text-2">Sign up</a>
                        <a href="index.php" class="text-2">Continue as guest</a>
                    </div>

                    <div class="alert alert-danger" id="errorMsg" style="display: none;">
                        <h4 id="e_msg"></h4>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Login Script -->
    <script src="./assets/scripts/login.js"></script>
</body>
</html>