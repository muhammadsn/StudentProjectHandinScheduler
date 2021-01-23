<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Classes/Student.php";

$student=new Student();

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
$project_list = $student->fetch_all_projects(7);
if (isset($_REQUEST['search'])){
    $project_list = $student->fetch_searched_projects($_REQUEST['search']);
}
$my_project_list = $student->get_student_project_list();
//echo '<pre>';
//var_dump($my_project_list);
//echo '</pre>';
//die();
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
        <h3 class="panel_title">پروژه ها</h3>
        <div class="box_title_icon "><i class="zmdi zmdi-file-text"></i></div>
        <div class="panel_login_info">خوش آمدید&nbsp;
            <span class="panel_login_userid"><?php echo $student->user_fullname; ?></span>
            <div class="panel_login_logout"><i class="zmdi zmdi-power"></i><a href="logout.php">خروج از سیستم</a> </div>
        </div>
    </div>
    <div class="box_content">

        <div class="row">
            <div class="col-6">
                <div class="panel_item_title panel_item_title_2" id="form_item_1">
                    <span class="form_desc form_desc_id" for="sid">پروژه های من</span>
                </div>
                <div class="form_item form_table_sf1" id="form_item_6">
                    <div class="row table_wrap">
                        <table class="col-12 table_sf1">
                            <tr  class="table_head">
                                <th style="width:30%">عنوان پروژه</th>
                                <th style="width:25%">زمان شروع تحویل شما</th>
                                <th style="width:15%">وضعیت</th>
                                <th style="width:15%">ابزار ها</th>
                            </tr>
                            <?php
                            if ($my_project_list){
                                foreach ($my_project_list as $project) {
                                    echo '<tr class="table_row">';
                                    echo "<td>".$project['project_name']."</td>";

                                    if ($project['teacher_confirm'] == 0){
                                        echo "<td>نامشخص</td>";
                                        echo "<td style='background-color: lightgoldenrodyellow' '>منتظر تأیید</td>";
                                        echo '<td>'
                                            .'<a href="project-unregister.php?id='.$project["project_id"].'" class="table_tool delete" title="لغو ثبت نام"><i class="zmdi zmdi-close"></i></a>&nbsp;&nbsp;&nbsp;'
                                            .'<a href="project-register.php?id='.$project["project_id"].'" class="table_tool" title="ویرایش زمانبندی"><i class="zmdi zmdi-edit"></i></a>'
                                            .'</td>';
                                    }
                                    elseif ($project['teacher_confirm'] == 1){
                                        echo '<td>'.date('Y/m/d - H:i:s',strtotime($project["sched_date"]." ".$project["sched_start_time"])).'</td>';
                                        echo "<td style='background-color: #d1efd8' '>تأیید شده</td>";
                                        echo '<td><a href="project-result.php?id='.$project["project_id"].'" class="table_tool" title="مشاهده نتیجه درخواست"><i class="zmdi zmdi-calendar-check"></i></a></td>';
                                    }

                                    echo '</tr>';
                                }
                            }
                            else{
                                echo "<tr  class='table_row'><td colspan='4'>شما پروژه فعالی ندارید</td></tr>";
                            }
                            ?>

                        </table>
                    </div>
                </div>

            </div>







            <div class="col-6">
                <div class="panel_item_title panel_item_title_2" id="form_item_1">
                    <span class="form_desc form_desc_id" for="sid">آخرین پروژه های ایجاد شده</span>
                </div>

                <form id="searchform" action="student.php" method="get">

                    <div class="row" style="margin-top: 70px;">
                        <div class="col-3">
                            <div id="searchbtn" class="btn btn_search_sf1">جستجو</div>
                        </div>
                        <div class="col-9">
                            <div class="form_item form-group" id="form_item_2">
                                <input type="text" class="input form-control" name="search" id="search"  placeholder="نام پروژه یا استاد را وارد نمایید">
                            </div>
                        </div>
                    </div>
                </form>

                <div class="form_item form_table_sf1" id="form_item_6">
                    <div class="row table_wrap">
                        <table class="col-12 table_sf1">
                            <tr  class="table_head">
                                <th style="width:35%">عنوان پروژه</th>
                                <th style="width:30%">استاد</th>
                                <th style="width:30%">مهلت ثبت نام</th>
                                <th style="width:5%">انتخاب</th>
                            </tr>

                            <?php
                            if ($project_list){
                                while ($project = $project_list->fetch_assoc()) {
                                    echo '<tr class="table_row">';
                                    if ($project['project_is_private'] === '1'){
                                        echo "<td><i class='zmdi zmdi-lock' title='دسترسی به این پروژه محدود شده است'></i>".$project['project_name']."</td>";
                                    }
                                    else{
                                        echo "<td>".$project['project_name']."</td>";
                                    }
                                    echo '<td>'.$project["full_name"].'</td>';
                                    echo '<td>'.date('Y/m/d - H:i:s',strtotime($project["project_register_end_time"])).'</td>';
                                    if (!$student->has_project($project["project_id"])) {
                                        echo '<td><a href="project-register.php?id=' . $project["project_id"] . '" class="table_tool" title="ثبت نام در این پروژه"><i class="zmdi zmdi-check"></i></a></td>';
                                    }
                                    else{
                                        echo '<td>&nbsp;</td>';
                                    }
                                    echo '</tr>';
                                }
                            }
                            else{
                                echo "<tr  class='table_row'><td colspan='4'>پروژه ای پیدا نشد</td></tr>";
                            }
                            ?>

                        </table>
                    </div>
                </div>


            </div>

        </div>
    </div>
    <div class="form_submit disabled_except_signup" ></div>
</div>


</body>

</html>