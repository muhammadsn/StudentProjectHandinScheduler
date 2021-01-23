<?php
require_once "System.php";

class Project extends system
{
    public $project_id;
    public $project_creator_id;
    public $project_name;
    public $project_is_private;
    public $project_pass;
    public $project_reg_due;
    public $project_sbmt_duration;
    public $project_desc;
    public $project_is_active;
    public $project_create_time;

    public function __construct()
    {
        parent::__construct();
    }

    public function create_project($is_edit, $pcid, $pname,$pregdue, $psdur, $pdesc, $pip=0,$ppass="",$edit_id=0){
        if ($is_edit){
            $query1 = "DELETE FROM registerations WHERE project_id='$edit_id'";
            $query2 = "DELETE FROM schedules WHERE project_id='$edit_id'";
            $query3 = "DELETE FROM projects WHERE project_id='$edit_id'";
            if (!$this->db_action($query1)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
            if (!$this->db_action($query2)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
            if (!$this->db_action($query3)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
        }

        $query = "SELECT project_id AS 'lastid' FROM projects ORDER BY project_id DESC LIMIT 1";
        $result = $this->db_read($query);
        $pid = 1;
        if ($result->num_rows > 0){
            $pid = intval($result->fetch_assoc()['lastid']) +1;
        }
        $pctime = date("Y-m-d H:i:s");;
        $query = "INSERT INTO projects(project_id, project_creator_id, project_name,project_is_private, project_pass, project_register_end_time, project_submission_duration, project_desc, project_create_time) 
                  VALUES ('$pid','$pcid','$pname','$pip', '$ppass', '$pregdue', '$psdur', '$pdesc', '$pctime' )";
        if (!$this->db_action($query)){
            $this->set_message("خطا در ثبت فرم! لطفاً همه موارد را وارد کنید");
            return false;
        }
        else{
            $this->set_message("پروژه با موفقیت ثبت شد");
            return $pid;
        }
    }

    public function save_project_time_schedule($times, $pid){
        foreach ($times as $time_slot){
            $query = "INSERT INTO schedules(project_id, sched_date, sched_start_time, sched_end_time) VALUES ('$pid', '$time_slot[0]','$time_slot[1]', '$time_slot[2]')";
            if (!$this->db_action($query)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
        }
        return true;
    }

    public function fetch_project_info($pid){
        $query = "SELECT * FROM projects WHERE project_id='$pid'";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $result = $result->fetch_assoc();
            $this->project_id=$result["project_id"];
            $this->project_creator_id=$result["project_creator_id"];
            $this->project_name=$result["project_name"];
            $this->project_is_private=$result["project_is_private"];
            $this->project_pass=$result["project_pass"];
            $this->project_reg_due=$result["project_register_end_time"];
            $this->project_sbmt_duration=$result["project_submission_duration"];
            $this->project_desc=$result["project_desc"];
            $this->project_is_active=$result["is_active"];
            return true;
        }
        else{
            $this->set_message("مشکلی در سیستم پیش آمده است");
            return false;
        }
    }

    public function fetch_project_sched_info($pid){
        $query = "SELECT * FROM (SELECT * FROM schedules WHERE CONCAT(sched_date,' ',sched_end_time) >= NOW()) as s WHERE project_id='$pid' ORDER BY sched_date ASC, sched_start_time ASC, sched_end_time ASC";
        $result = $this->db_read($query);
        if ($result->num_rows > 0) {
            return $result;
        }
        else{
            return false;
        }
    }

    public function fetch_project_student_sched($sid){
        $query = "SELECT r.sched_id, sched_date, sched_start_time, sched_end_time, r.teacher_confirm FROM schedules s JOIN registerations r on s.sched_id = r.sched_id WHERE r.user_id ='$sid' AND r.project_id = '$this->project_id' ORDER BY sched_date ASC, sched_start_time ASC, sched_end_time ASC ";
        $result = $this->db_read($query);
        if ($result->num_rows > 0){
            return $result;
        }
        else{
            return false;
        }
    }

    public function can_register(){
        $prd = date("Y-m-d H:i:s", strtotime($this->project_reg_due));
        if ($prd >= date("Y-m-d H:i:s")){
            return true;
        }
        else{
            return false;
        }
    }

    public function validate_password($pass){
        if ($this->project_pass == $pass){
            return true;
        }
        else{
            return false;
        }
    }

}