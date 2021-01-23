<?php
require_once "User.php";

class Teacher extends User
{
    public function __construct()
    {
        parent::__construct();
    }

    public function get_projects_list(){
        $query = "SELECT * FROM projects WHERE project_creator_id='$this->user_id' ORDER BY project_create_time";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            return $result;
        }
        else{
            return false;
        }
    }

    public function get_project_student_list($project_id){
        $query1 = "SELECT COUNT(DISTINCT user_id) AS 'stdcnt' FROM registerations WHERE project_id='$project_id'";
        $result1 = $this->db_read($query1);
        $query2 = "SELECT DISTINCT u.user_id, full_name,  email,project_id FROM registerations AS r JOIN users AS u ON r.user_id = u.user_id WHERE project_id='$project_id' ";
        $result2 = $this->db_read($query2);
        $student_count = $result1->fetch_assoc()['stdcnt'];
        $student_list = array("count" => $student_count, "student" => array());
        if ($result2 && $result2->num_rows > 0){
            while ($item = $result2->fetch_assoc()){
                $student = array(
                    "s_id" => $item['user_id'],
                    "s_fn" => $item['full_name'],
                    "s_em" => $item['email']
                );
                array_push($student_list['student'], $student);
            }
        }
        return $student_list;
    }

    public function get_project_submission_dates($project_id){
        $query1 = "SELECT COUNT(sched_id) AS 'datecnt' FROM schedules WHERE project_id='$project_id'";
        $result1 = $this->db_read($query1);
        $query2 = "SELECT * FROM schedules WHERE project_id='$project_id' ";
        $result2 = $this->db_read($query2);
        $date_count = $result1->fetch_assoc()['datecnt'];
        $date_list = array("count" => $date_count, "student" => array());
        if ($result2 && $result2->num_rows > 0){
            while ($item = $result2->fetch_assoc()){
                $student = array(
                    "s_id" => $item['sched_id'],
                    "p_id" => $item['project_id'],
                    "s_dt" => $item['sched_date'],
                    "s_st" => $item['sched_start_time'],
                    "s_et" => $item['sched_end_time']
                );
                array_push($date_list['student'], $student);
            }
        }
        return $date_list;

    }

    public function is_project_active($project_id){
        $query = "SELECT * FROM schedules WHERE project_id='$project_id' ORDER BY sched_date DESC , sched_end_time DESC LIMIT 1";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $result = $result->fetch_assoc();
            $project_datetime =$result['sched_date']." ".$result['sched_start_time'];
            if (strtotime($project_datetime) < time()){
                $query = "UPDATE projects SET is_active=0 WHERE project_id='$project_id'";
                if (!$this->db_action($query)){
                    $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                    return -1;
                }
                return 0;
            }
            else{
                return 1;
            }
        }
    }

    public function get_student_info($sid){
        $query = "SELECT user_id, full_name, email FROM users WHERE user_id='$sid' ";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $result=$result->fetch_assoc();
            return $result;
        }
        else{
            return false;
        }
    }

    public function delete_project($pid){
        $query1 = "DELETE FROM registerations WHERE project_id='$pid'";
        $query2 = "DELETE FROM schedules WHERE project_id='$pid'";
        $query3 = "DELETE FROM projects WHERE project_id='$pid'";

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
        return true;
    }

    public function set_student_time($pid, $sid, $times){
        $query1 = "UPDATE registerations SET teacher_confirm = 0 WHERE (project_id = '$pid') AND (user_id = '$sid') ";
        if (!$this->db_action($query1)){
            $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
            return false;
        }
        $sched_item = current($times);
        $sched_id = key($times);
        $query2 = "UPDATE registerations SET teacher_confirm = 1 WHERE (project_id = '$pid') AND (user_id = '$sid') AND (sched_id = $sched_id)";
        if (!$this->db_action($query2)){
            $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
            return false;
        }
        $this->set_message("درخواست شما در سیستم ثبت شد");
        return true;

    }

}