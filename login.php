<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Classes/User.php";
$user = new User();

if (isset($_COOKIE['ps_token'])){
    $user->is_logged_in($_COOKIE['ps_token']);
}

if (!empty($_REQUEST)){
    $result = $user->login($_REQUEST['username'], $_REQUEST['password']);
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width = device-width, initial-scale = 1">

    <!--JS-->
    <script src="Vendors/plugins/jquery/jquery-3.3.1.min.js" type="text/javascript"></script>
    <script src="Vendors/plugins/bootstrap/js/bootstrap.js" type="text/javascript"></script>
    <script src="Resources/assets/js/script.js" type="text/javascript"></script>

    <!--CSS-->
    <link href="Vendors/plugins/bootstrap/css/bootstrap.css" rel="stylesheet" type="text/css">
    <link href="Vendors/plugins/material-design-iconic-font/material-design-iconic-font.css" rel="stylesheet" type="text/css">
    <link href="Resources/assets/css/style.css" rel="stylesheet" type="text/css">

    <title>سامانه زمانبندی تحویل پروژه</title>

</head>
<body>
<div class="box_login col-lg-6 col-xl-4 col-md-6 col-sm-12 offset-lg-3 offset-xl-4 offset-md-3" id="main_box">
    <div class="box_title box_title_form">
        <h3 class="form_title">ورود به سامانه</h3>
        <div class="box_title_icon_signup "><i class="zmdi zmdi-account"></i>
        </div>
    </div>
    <div class="box_content_login">
        <div class="form_wrap">

            <?php
                if ($user->has_message()){
                    echo "<div class='form_message'>";
                    echo $user->get_message();
                    echo "</div>";
                }
            ?>

            <form id="login_form"  action="login.php" method="post">

                <div class="form_item form-group" id="form_item_1">
                    <label class="form_desc_id" for="sid">نام کاربری</label>
                    <input type="number" class="input form-control" name="username" id="sid" maxlength="7" minlength="7" required>
                </div>

                <div class="form_item form-group" id="form_item_2">
                    <label class="form_desc_pass" for="password">گذرواژه</label>
                    <input type="password" class="input form-control" name="password" id="password" required>
                </div>

            </form>
        </div>
        <div class="form_desc form_desc_info text-login">
            <a href="signup.php">حساب کاربری ندارم</a>
        </div>
    </div>
    <div class="form_submit disabled" id="submit">
        <span class="form_submit_title">ورود</span>
    </div>
</div>
</body>
</html>