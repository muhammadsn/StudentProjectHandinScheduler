<?php
error_reporting(E_ALL);
ini_set('display_errors', 1);

include_once "Classes/Teacher.php";
include_once "Classes/Project.php";

$teacher = new Teacher();
$edit_mode = isset($_REQUEST['edit'])?true:false;
$edit_id = isset($_REQUEST['pid'])?$_REQUEST['pid']:0;
$timing = array();
$sched_i = 1;

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


if (!empty($_REQUEST)){

    if (isset($_REQUEST['id'])){
        $project = new Project();
        $edit_mode = $project->fetch_project_info($_REQUEST['id']);
        $edit_id = $project->project_id;
        $timing = $project->fetch_project_sched_info($_REQUEST['id']);
    }
    elseif (isset($_REQUEST['project_name']) && !empty($_REQUEST["project_name"])){
        $project = new Project();
        $pcid = $teacher->user_id;
        $pname = $_REQUEST["project_name"];
        $pregdue = $_REQUEST["submit_end_time"];
        $psdur = $_REQUEST["project_duration"];
        $pdesc = $_REQUEST["project_desc"];
        if (isset($_REQUEST["private_project"]) && $_REQUEST["private_project"] === "on"){
            $pip = 1;
        }
        else{
            $pip = 0;
        }
        $ppass = $_REQUEST["project_password"];

        if ($pid = $project->create_project($edit_mode,$pcid, $pname,$pregdue, $psdur, $pdesc, $pip,$ppass,$edit_id)){
            $time_table = array();
            foreach ($_REQUEST as $r){
                if (is_array($r)){
                    array_push($time_table, $r);
                }
            }
            $project->save_project_time_schedule($time_table, $pid);
        }

    }
    else{
        $teacher->set_message("لطفاً همه موارد را پر کنید");
    }
}


?>


<!DOCTYPE html>
<html lang="en" xmlns="http://www.w3.org/1999/html">

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
        <h3 class="panel_title"><?php if($edit_mode){echo "ویرایش";}else{echo "ایجاد";}?> پروژه </h3>
        <div class="box_title_icon "><i class="zmdi zmdi-file-text"></i></div>
        <div class="panel_login_info">خوش آمدید&nbsp;
            <span class="panel_login_userid"><?php echo $teacher->user_fullname; ?></span>
            <div class="panel_login_logout"><i class="zmdi zmdi-power"></i><a href="logout.php">خروج از سیستم</a> </div>
        </div>
    </div>
    <div class="box_content">
        <form id="project_form" action="project-editor.php<?php if($edit_mode){echo "?pid=".$project->project_id."&edit=1";}?>" method="post">
            <div class="form_wrap_tf2">
                <div class="form_wrap_tchr">
                    <div class="panel_item_title panel_item_title_2" id="form_item_1">
                        <span class="form_desc form_desc_id">با استفاده از فرم زیر پروژه را <?php if($edit_mode){echo "ویرایش";}else{echo "تعریف";}?> نمایید</span>
                    </div>
                    <a href="teacher.php"><div class="btn btn_proj_tf1"><i class='zmdi zmdi-chevron-left'></i>&nbsp;بازگشت به پروژه ها</div></a>
                </div>
                <?php
                if ($teacher->has_message()){
                    echo "<div class='form_message'>";
                    echo $teacher->get_message();
                    echo "</div>";
                }
                ?>
                <div class="row">
                    <div class="col-7">
                        <div class="form_item form-group form-group-time-tf2" id="form_item_5">
                            <div class="row">
                                <div class="col-4">
                                    <label class="form_desc_date form_desc_time_text" for="date">تاریخ ارائه</label>
                                    <input type="date" class="input form-control" id="date">
                                </div>
                                <div class="col-4">
                                    <label class="form_desc_time form_desc_time_text" for="start_time">ساعت شروع</label>
                                    <input type="time" class="input form-control" id="start_time" >
                                </div>
                                <div class="col-4">
                                    <label class="form_desc_time form_desc_time_text" for="end_time">ساعت پایان</label>
                                    <input type="time" class="input form-control" id="end_time">
                                </div>
                            </div>
                            <div id="add_time" class="btn  btn_add_tf2"><i class="zmdi zmdi-long-arrow-down"></i>&nbsp; افزودن زمان جدید</div>
                        </div>
                        <div class="row table_wrap">
                            <table id="time_table" class="col-12">
                                <tr class="table_head">
                                    <th style="width:30%">تاریخ ارائه</th>
                                    <th style="width:25%">ساعت شروع</th>
                                    <th style="width:25%">ساعت پایان</th>
                                    <th style="width:20%">ابزارها</th>
                                </tr>

                                <?php
                                    if ($timing){
                                        while ($time_row = $timing->fetch_assoc()){
                                            $x = "<tr class='table_row del_".$sched_i."'>" .
                                                "<td><input class='table_input' type='text' name='sched_".$sched_i."[]' value='" . $time_row["sched_date"] ."' readonly></td>" .
                                                "<td><input class='table_input' type='text' name='sched_".$sched_i."[]' value='" . $time_row["sched_start_time"] ."' readonly></td>" .
                                                "<td><input class='table_input' type='text' name='sched_".$sched_i."[]' value='" . $time_row["sched_end_time"] ."' readonly></td>" .
                                                "<td><a href='#' id='del_".$sched_i."' class='table_tool delete' onclick=\"delete_row('del_".$sched_i."')\" title='حذف این زمان'><i class='zmdi zmdi-close'></i></a></td></tr>";
                                            echo $x;
                                            $sched_i++;
                                        }
                                    }else{
                                        echo '<tr id="default_row" class="table_row"><td colspan="4">هیچ زمانی اضافه نشده است</td></tr>';
                                    }
                                ?>

                            </table>
                        </div>
                    </div>
                    <div class="col-5">
                        <div class="row">
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-12">
                                        <div class="form_item form-group-accessibility" id="form_item_2">
                                            <label class="form_desc_edit" for="project_desc">عنوان پروژه</label>
                                            <input type="text" class="input form-control" name="project_name" id="project_name" <?php if($edit_mode){echo "value=\"".$project->project_name."\"";}?> placeholder="عنوان پروژه">
                                        </div>

                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="row">
                                    <div class="col-6">
                                        <div class="form_item form-group-accessibility" id="form_item_2">
                                            <label class="form_desc_time" for="project_desc">مدت ارائه هر پروژه</label>
                                            <input type="text" class="input form-control" name="project_duration" id="project_duration" <?php if($edit_mode){echo "value=\"".$project->project_sbmt_duration."\"";}?>  placeholder="زمان هر ارائه به دقیقه">
                                        </div>
                                    </div>
                                    <div class="col-6">
                                        <div class="form_item form-group-accessibility" id="form_item_2">
                                            <label class="form_desc_time" for="project_desc">زمان پایان ثبت نام</label>
                                            <input type="datetime-local" class="input form-control" name="submit_end_time" <?php if($edit_mode){echo "value=\"".substr(date("c", strtotime($project->project_reg_due)), 0, 19)."\"";}?> id="submit_end_time">
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="col-12">
                                <div class="form_item form_group_tf2" id="form_item_6">
                                    <label class="form_desc_edit" for="project_desc">توضیحات پروژه</label>
                                    <textarea class="input form-control" name="project_desc" id="project_desc"  rows="6"><?php if($edit_mode){echo $project->project_desc;}?> </textarea>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-6">
                                <div class="form_item form-group-accessibility" id="form_item_2">
                                    <input type="text" class="input form-control" name="project_password" id="project_password" <?php if($edit_mode){echo "value=\"".$project->project_pass."\"";}?>  placeholder="کد دسترسی به پروژه" >
                                    <?php if($edit_mode && $project->project_is_private==1){echo '<script>$(function() {$("#project_password").show()});</script>';}?>
                                </div>
                            </div>
                            <div class="col-6">
                                <div style="margin-top: 2em;" class="form_desc text text_tf2">
                                    <input type="checkbox" name="private_project" id="checkbox" <?php if($edit_mode && $project->project_is_private==1){echo "checked";}?> >
                                    دسترسی پروژه محدود شود؟
                                </div>
                            </div>
                        </div>

                    </div>
                </div>


                <div class="row">
                    <div class="col-12">
                        <div id="projectsubmit" class="btn  btn_apply_tf2">ثبت پروژه</div>
                    </div>
                </div>

            </div>
        </form>



    </div>
    <div class="form_submit disabled_except_signup" ></div>
</div>


</body>

</html>
