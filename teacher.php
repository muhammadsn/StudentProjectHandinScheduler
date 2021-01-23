<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Classes/Teacher.php";
$teacher = new Teacher();
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
        <h3 class="panel_title">پروژه های من</h3>
        <div class="box_title_icon "><i class="zmdi zmdi-file-text"></i></div>
        <div class="panel_login_info">خوش آمدید&nbsp;
            <span class="panel_login_userid"><?php echo $teacher->user_fullname; ?></span>
            <div class="panel_login_logout"><i class="zmdi zmdi-power"></i><a href="logout.php">خروج از سیستم</a> </div>
        </div>
    </div>
    <div class="box_content">
        <div class="form_wrap_tf2">

            <div class="panel_item_title panel_item_title_1" id="form_item_1">
                <span class="form_desc form_desc_id" for="sid">پروژه های شما</span>
            </div>

            <a href="project-editor.php"><div class="btn btn_proj_tf1"><i class='zmdi zmdi-plus'></i>&nbsp;ایجاد پروژه جدید</div></a>

        </div>
        <div class="row table_wrap">
            <div class="form_desc form_desc_info text">
                در این جدول پروژه های فعال شما نمایش داده میشوند
            </div>
            <table class="col-12">
                <tr  class="table_head">
                    <th style="width:30%">عنوان پروژه</th>
                    <th style="width:25%">زمان ایجاد پروژه</th>
                    <th style="width:15%">تعداد روز تحویل</th>
                    <th style="width:15%">تعداد دانشجویان</th>
                    <th style="width:15%">ابزار ها</th>
                </tr>

                <?php
                $projects = $teacher->get_projects_list();
                if ($projects){
                    while ($project = $projects->fetch_assoc()){
                        if ($teacher->is_project_active($project['project_id']) === 1){
                            echo "<tr class='table_row'>";
                            if ($project['project_is_private'] === '1'){
                                echo "<td><i class='zmdi zmdi-lock' title='دسترسی به این پروژه محدود شده است'></i>".$project['project_name']."</td>";
                            }
                            else{
                                echo "<td>".$project['project_name']."</td>";
                            }
                            echo "<td>".date('Y/m/d - H:i:s',strtotime($project['project_create_time']))."</td>";
                            echo "<td>".$teacher->get_project_submission_dates($project['project_id'])['count']."</td>";
                            echo "<td>".$teacher->get_project_student_list($project['project_id'])['count']."</td>";
                            echo "<td>";
                            echo "<a href='delete-project.php?id=".$project['project_id']."' class='table_tool delete' title='حذف این پروژه'><i class='zmdi zmdi-close'></i></a>&nbsp;&nbsp;&nbsp;";
                            echo "<a href='project-editor.php?id=".$project['project_id']."' class='table_tool' title='ویرایش این پروژه'><i class='zmdi zmdi-edit'></i></a>&nbsp;&nbsp;&nbsp;";
                            echo "<a href='project-schedule.php?id=".$project['project_id']."' class='table_tool' title='نمایش زمانبندی این پروژه'><i class='zmdi zmdi-calendar-note'></i>&nbsp;&nbsp;&nbsp;";
                            if (intval($teacher->get_project_student_list($project['project_id'])['count']) > 0 ) {
                                echo "<a href='project-students.php?id=" . $project['project_id'] . "' class='table_tool' title='نمایش دانشجویان این پروژه'><i class=\"zmdi zmdi-pin-account\"></i></a>";
                            }
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                }
                else{
                    echo "<tr class='table_row'><td colspan='5'>شما هیچ پروژه فعالی ندارید!</td></tr>";
                }
                ?>
            </table>
        </div>
        <div style="display: block;height: 30px;"></div>
        <div class="row table_wrap">
            <div class="form_desc form_desc_info text">
                در این جدول پروژه های تمام شده شما نمایش داده میشوند
            </div>
            <table class="col-12">
                <tr  class="table_head_old">
                    <th style="width:30%">عنوان پروژه</th>
                    <th style="width:25%">زمان پایان پروژه</th>
                    <th style="width:15%">تعداد روز تحویل</th>
                    <th style="width:15%">تعداد دانشجویان</th>
                    <th style="width:15%">ابزار ها</th>
                </tr>

                <?php
                $projects = $teacher->get_projects_list();
                if ($projects){
                    while ($project = $projects->fetch_assoc()){
                        if ($teacher->is_project_active($project['project_id'])===0){
                            echo "<tr class='table_row'>";
                            if ($project['project_is_private'] === '1'){
                                echo "<td><i class='zmdi zmdi-lock' title='دسترسی به این پروژه محدود شده است'></i>".$project['project_name']."</td>";
                            }
                            else{
                                echo "<td>".$project['project_name']."</td>";
                            }
                            echo "<td>".date('Y/m/d - H:i:s',strtotime($project['project_create_time']))."</td>";
                            echo "<td>".$teacher->get_project_submission_dates($project['project_id'])['count']."</td>";
                            echo "<td>".$teacher->get_project_student_list($project['project_id'])['count']."</td>";
                            echo "<td>";
                            echo "<a href='delete-project.php?id=".$project['project_id']."' class='table_tool delete' title='حذف این پروژه'><i class='zmdi zmdi-close'></i></a>";
                            echo "</td>";
                            echo "</tr>";
                        }
                    }
                }
                else{
                    echo "<tr  class='table_row'><td colspan='5'>شما هیچ پروژه تمام شده ای ندارید!</td></tr>";
                }
                ?>
            </table>
        </div>

    </div>
    <div class="form_submit disabled_except_signup"></div>
</div>


</body>
</html>
