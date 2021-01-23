<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

require_once "Classes/Teacher.php";
require_once "Classes/Project.php";

$teacher = new Teacher();
$project = new Project();
$students = array();
$std_i = 1;

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
        $students = $teacher->get_project_student_list($_REQUEST['id'])['student'];
    }
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
        <h3 class="panel_title">دانشجویان <?php echo $project->project_name;?> </h3>
        <div class="box_title_icon "><i class="zmdi zmdi-pin-account"></i></div>
        <div class="panel_login_info">خوش آمدید&nbsp;
            <span class="panel_login_userid"><?php echo $teacher->user_fullname; ?></span>
            <div class="panel_login_logout"><i class="zmdi zmdi-power"></i><a href="logout.php">خروج از سیستم</a> </div>
        </div>
    </div>
    <div class="box_content">
        <div class="form_wrap_tf2">

            <div class="panel_item_title panel_item_title_1" id="form_item_1">
                <span class="form_desc form_desc_id" for="sid">دانشجویانی که برای تحویل این پروژه زمان خود را ثبت کرده اند</span>
            </div>

            <a href="teacher.php"><div class="btn btn_proj_tf1"><i class='zmdi zmdi-chevron-left'></i>&nbsp;بازگشت به پروژه ها</div></a>
            <?php if (isset($_REQUEST['id'])) echo '<a href="project-editor.php?id='.$project->project_id.'"><div class="btn btn_proj_tf1"><i class="zmdi zmdi-edit"></i>&nbsp;ویرایش این پروژه</div></a>';?>

        </div>
        <div class="row table_wrap">

            <table class="col-12">
                <tr class="table_head">
                    <th style="width:5%">#</th>
                    <th style="width:20%">شماره دانشجویی</th>
                    <th style="width:30%">نام و نام خانوادگی</th>
                    <th style="width:30%">ایمیل</th>
                    <th style="width:10%">زمانبندی</th>
                </tr>

                <?php
                if ($students && isset($_REQUEST['id'])){

                    foreach ($students as $std_row){
                        $x = "<tr class='table_row del_".$std_i."'>" .
                            "<td><input class='table_input' type='text' name='std_".$std_i."[]' value='" . $std_i ."' readonly></td>" .
                            "<td><input class='table_input' type='text' name='std_".$std_i."[]' value='" . $std_row["s_id"] ."' readonly></td>" .
                            "<td><input class='table_input' type='text' name='std_".$std_i."[]' value='" . $std_row["s_fn"] ."' readonly></td>" .
                            "<td><input class='table_input' type='text' name='std_".$std_i."[]' value='" . $std_row["s_em"] ."' readonly></td>" .
                            "<td><a href='project-student-schedule.php?sid=".$std_row["s_id"]."&pid=".$project->project_id."' class='table_tool' title='نمایش زمانبندی منتخب این دانشجو'><i class='zmdi zmdi-calendar-note'></i></td>" ;
                        echo $x;
                        $std_i++;
                    }
                }else{
                    echo '<tr id="default_row" class="table_row"><td colspan="5">هیچ دانشجویی ثبت نشده است</td></tr>';
                }
                ?>
            </table>
        </div>


    </div>
    <div class="form_submit disabled_except_signup"></div>
</div>


</body>
</html>

