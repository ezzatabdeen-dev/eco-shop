<?php
session_start();
if(isset($_SESSION["uid"])){
    header('Location: index.php');
    exit;
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
    <title>Register Page</title>
    <link rel="stylesheet" type="text/css" href="./assets/css/gloable.css">
    <link rel="stylesheet" type="text/css" href="./assets/css/userSignupForm.css">
</head>
<body>
    <div class="container signInContainer">
        <div class="wraperPrand">
            <img src="./assets/public_img/OnlineShopLogo.svg" alt="prand" width="200" height="100">
        </div>
        <div class="wraperUserSignUp">
            <div class="wraperForm">
                <form id="signup_form" class="login100-form validate-form">
                    <h2 class="formTitle text-6">Sign Up</h2>
                    <input class="input100" type="text" name="f_name" id="f_name" placeholder="First Name" required />
                    <input class="input100" type="text" name="l_name" id="l_name" placeholder="Last Name" required />
                    <input class="input100" type="email" name="email" id="email" placeholder="Email address" required />
                    <input class="input100" type="text" name="mobile" id="mobile" placeholder="Phone" required />
                    <input class="input100" type="password" name="password" id="password" placeholder="Password" required />
                    <input class="input100" type="text" name="address1" id="address1" placeholder="Address" required />
                    <input class="input100" type="text" name="address2" id="address2" placeholder="City" />

                    <div class="wraperAgreeChcer">
                        <input class="input-checkbox100" id="ckb1" type="checkbox" name="remember-me" required />
                        <label class="label-checkbox100" for="ckb1">
                            <span>I agree to the <a href="#" class="txt2 hov1">Terms of User</a></span>
                        </label>
                    </div>

                    <button class="form-submit" type="submit">Create Account</button>

                    <div class="anutherLink">
                        <a href="signin_form.php" class="text-2">Already have an account? Sign in</a>
                        <a href="index.php" class="text-2">Continue as guest</a>
                    </div>

                    <div class="alert alert-danger" id="errorMsg">
                        <h4 id="e_msg"></h4>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Register Script -->
    <script src="./assets/scripts/SignUp.js"> </script>
</body>
</html>