<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "Classes/Teacher.php";
require_once "Classes/Project.php";

$teacher = new Teacher();
$project = new Project();
$status = false;

if (isset($_COOKIE['ps_token'])){
    if ($teacher->is_not_logged_in($_COOKIE['ps_token'], '1')){
        header('Location: login.php');
        die();
    }
    else{
        if(!$teacher->fetch_user_data($_COOKIE['ps_token'])){
            header('Location: login.php');
            die();
        }
    }
}
else{
    header('Location: login.php');
    die();
}

if (isset($_REQUEST['id'])){
    if($project->fetch_project_info($_REQUEST['id'])){
        $teacher->delete_project($_REQUEST['id']);
        $teacher->set_message("پروژه با موفقیت حذف شد");
    }
    else{
        $teacher->set_message("حذف پروژه با خطا مواجه شد");
    }
}
else{
    $teacher->set_message("چیزی برای حذف وجود ندارد");
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

<div class="box_teacher_form_1 col-lg-10 col-xl-10 col-md-10 col-sm-12 offset-lg-1 offset-xl-1 offset-md-1" id="main_box">
    <div class="box_title_teacher_form_1">
        <h3 class="panel_title">حذف <?php echo $project->project_name;?> </h3>
        <div class="box_title_icon "><i class="zmdi zmdi-delete"></i></div>
        <div class="panel_login_info">خوش آمدید&nbsp;
            <span class="panel_login_userid"><?php echo $teacher->user_fullname; ?></span>
            <div class="panel_login_logout"><i class="zmdi zmdi-power"></i><a href="logout.php">خروج از سیستم</a> </div>
        </div>
    </div>
    <div class="box_content">
        <div class="form_wrap_tf2">

            <div class="panel_item_title panel_item_title_1" id="form_item_1">
                <span class="form_desc form_desc_id" for="sid">حذف پروژه <?php echo $project->project_name;?> </span>
            </div>

            <a href="teacher.php"><div class="btn btn_proj_tf1"><i class='zmdi zmdi-chevron-left'></i>&nbsp;بازگشت به پروژه ها</div></a>

        </div>

        <?php
        if ($teacher->has_message()){
            echo "<div class='form_message2'>";
            echo $teacher->get_message();
            echo "</div>";
        }
        ?>

    </div>
    <div class="form_submit disabled_except_signup"></div>
</div>


</body>
</html>

