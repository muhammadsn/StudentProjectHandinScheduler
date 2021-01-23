<?php
include_once "Classes/User.php";
$user = new User();
if (isset($_COOKIE['ps_token'])){
    $user->is_logged_in($_COOKIE['ps_token']);
}

if (!empty($_REQUEST)){
    $result = $user->signup($_REQUEST['sid'], $_REQUEST['full_name'], $_REQUEST['email'], $_REQUEST['password']);
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

<div class="box_signup col-lg-6 col-xl-4 col-md-6 col-sm-12 offset-lg-3 offset-xl-4 offset-md-3" id="main_box">
    <div class="box_title box_title_form">
        <h3 class="form_title">عضویت در سامانه</h3>
        <div class="box_title_icon_signup "><i class="zmdi zmdi-accounts-add"></i>
        </div>
    </div>
    <div class="box_content_signup ">

        <div class="form_wrap">

            <?php
            if ($user->has_message()){
                echo "<div class='form_message'>";
                echo $user->get_message();
                echo "</div>";
            }
            ?>

            <form id="login_form" action="signup.php" method="post">

                <div class="form_item form-group" id="form_item_1">
                    <label class="form_desc_id" for="sid">نام کاربری</label>
                    <input type="number" class="input form-control" name="sid" id="sid" maxlength="7" minlength="7" required>
                </div>

                <div class="form_item form-group" id="form_item_1">
                    <label class="form_desc_name" for="name">نام و نام خانوادگی</label>
                    <input type="text" class="input form-control" name="full_name" id="name" required>
                </div>

                <div class="form_item form-group" id="form_item_2">
                    <label class="form_desc_email" for="email">ایمیل</label>
                    <input type="email" class="input form-control" name="email" id="email" required>
                </div>

                <div class="form_item form-group" id="form_item_3">
                    <label class="form_desc_pass" for="password">گذرواژه</label>
                    <input type="password" class="input form-control" name="password" id="password" maxlength="11" minlength="11" required>
                </div>

            </form>
        </div>
        <div class="form_desc form_desc_info text-signup">
            <a href="login.php">قبلا عضو شده ام!</a>
        </div>

    </div>
    <div class="form_submit disabled" id="submit">
        <span class="form_submit_title">ثبت نام</span>
    </div>
</div>


</body>
</html>

