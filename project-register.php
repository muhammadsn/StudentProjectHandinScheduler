<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Classes/Student.php";
include_once "Classes/Project.php";

$student=new Student();
$project = new Project();
$times = array();
$sched_i = 1;
$has_access = false;

if (isset($_COOKIE['ps_token'])){
    if ($student->is_not_logged_in($_COOKIE['ps_token'], '0')){
        header('Location: login.php');
        die();
    }
    else{
        if(!$student->fetch_user_data($_COOKIE['ps_token'])){
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
    if ($project->fetch_project_info($_REQUEST['id'])) {
        if ($project->can_register()) {
            $times = $project->fetch_project_sched_info($_REQUEST['id']);
        }
        else{
            $student->set_message('مهلت ثبت نام در این پروژه تمام شده است');
        }
    }

}
else{
    $student->set_message('چیزی برای نمایش وجود ندارد');
}

if (isset($_REQUEST['sched_ids'])){
    $student->register_times($_REQUEST['id'], $_REQUEST['sched_ids']);
}
$has_access = $student->has_project($project->project_id)?true:false;
if (isset($_REQUEST['project_pass'])){
    $has_access = $project->validate_password($_REQUEST['project_pass']);
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
        <h3 class="panel_title">انتخاب زمان تحویل پروژه</h3>
        <div class="box_title_icon "><i class="zmdi zmdi-calendar-check"></i></div>
        <div class="panel_login_info">خوش آمدید&nbsp;
            <span class="panel_login_userid"><?php echo $student->user_fullname; ?></span>
            <div class="panel_login_logout"><i class="zmdi zmdi-power"></i><a href="logout.php">خروج از سیستم</a> </div>
        </div>
    </div>
    <div class="box_content">

        <div class="form_wrap_tf2">

            <div class="panel_item_title panel_item_title_1" id="form_item_1">
                <span class="form_desc form_desc_id" for="sid">زمان هایی که برای این تحویل این پروژه در نظر گرفته شده است</span>
            </div>

            <a href="student.php"><div class="btn btn_proj_tf1"><i class='zmdi zmdi-chevron-left'></i>&nbsp;بازگشت به پروژه ها</div></a>

        </div>

        <?php
        if ($student->has_message()){
            echo "<div class='form_message2'>";
            echo $student->get_message();
            echo "</div>";
        }
        ?>

        <?php if (isset($_REQUEST['id']) && $project->project_is_private && $project->project_is_active && $project->can_register() && !$student->has_project($project->project_id) && !$has_access): ?>
            <form id="project_form" action="project-register.php?id=<?php echo $_REQUEST['id']; ?>" method="post">
                <div class="project-info">
                    <div class="row project-pass">

                        <span><i class="zmdi zmdi-lock"></i> دسترسی به این پروژه محدود شده است</span>
                        <div class="col-9">
                            <div class="form_item form-group" id="form_item_2">
                                <input type="password" class="input form-control" name="project_pass" id="project_pass"
                                       style=" height: 36px;" placeholder="رمز عبور را وارد کنید">
                            </div>
                        </div>
                        <div class="col-3">
                            <div id="projectsubmit" class="btn btn_proj_tf1">اعتبار سنجی</div>
                        </div>
                    </div>
                </div>
            </form>
        <?php endif ?>


        <?php if (($times && isset($_REQUEST['id']) && $has_access) || !$project->project_is_private): ?>
            <div class="project-info">
                <div class="project-info-item"><span><i class="zmdi zmdi-edit"></i>عنوان: </span><?php echo $project->project_name; ?></div>
                <div class="project-info-item"><span><i class="zmdi zmdi-timer"></i>مهلت ثبت نام در پروژه: </span><?php echo date('Y/m/d - H:i:s',strtotime($project->project_reg_due)); ?></div>
                <div class="project-info-item"><span><i class="zmdi zmdi-time-countdown"></i>مدت تحویل پروژه هر دانشجو: </span><?php echo $project->project_sbmt_duration; ?> دقیقه</div>
                <div class="project-info-item"><span><i class="zmdi zmdi-file-text"></i>توضیح استاد: </span><?php echo $project->project_desc; ?> </div>
            </div>

        <form id="project_form" action="project-register.php?id=<?php echo $_REQUEST['id'];?>" method="post">
            <div class="row table_wrap">

                <table class="col-12">
                    <tr class="table_head">
                        <th style="width:5%">#</th>
                        <th style="width:45%">تاریخ ارائه</th>
                        <th style="width:20%">ساعت شروع</th>
                        <th style="width:20%">ساعت پایان</th>
                        <th style="width:10%">انتخاب</th>
                    </tr>

                    <?php
                    if ($times && isset($_REQUEST['id'])) {
                        while ($time_row = $times->fetch_assoc()) {
                            $x = "<tr class='table_row del_" . $sched_i . "'>" .
                                "<td><input class='table_input' type='text' value='" . $sched_i . "' readonly></td>" .
                                "<td><input class='table_input' type='text' value='" . $time_row["sched_date"] . "' readonly></td>" .
                                "<td><input class='table_input' type='text' value='" . $time_row["sched_start_time"] . "' readonly></td>" .
                                "<td><input class='table_input' type='text' value='" . $time_row["sched_end_time"] . "' readonly></td>" .
                                "<td><input type='checkbox' name='sched_ids[" . $time_row["sched_id"] . "]' " . ($student->has_project_sched($project->project_id, $time_row["sched_id"])?"checked":"") . "></td>";
                            echo $x;
                            $sched_i++;
                        }
                    } else {
                        echo '<tr id="default_row" class="table_row"><td colspan="5">هیچ زمانی اعلام نشده است</td></tr>';
                    }
                    ?>
                </table>
            </div>
        </form>

            <div class="btn btn_select_sf2" id="projectsubmit">ثبت زمان</div>

        <?php endif ?>


    </div>
    <div class="form_submit disabled_except_signup" ></div>
</div>


</body>

</html>
