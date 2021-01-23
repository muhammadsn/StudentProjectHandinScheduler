<?php
require_once "User.php";

class Student extends User
{

    public function __construct()
    {
        parent::__construct();
    }

    public function fetch_all_projects($count){
        $query = "SELECT p.*, u.full_name FROM users u JOIN projects p on u.user_id = p.project_creator_id WHERE (project_register_end_time >= CURDATE()) AND (is_active = 1) ORDER BY p.project_create_time DESC LIMIT $count";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            return $result;
        }
        else{
            $this->set_message("hello");
            return false;
        }
    }

    public function fetch_searched_projects($needle){
        $needle = "%".$needle."%";
        $query = "SELECT p.*, u.full_name FROM users u JOIN projects p on u.user_id = p.project_creator_id WHERE (project_register_end_time >= CURDATE()) AND (is_active = 1) AND ((project_name LIKE '$needle') OR (full_name LIKE '$needle')) ORDER BY p.project_create_time DESC";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            return $result;
        }
        else{
            return false;
        }
    }

    public function fetch_student_project_info($pid){
        $query0 = "SELECT p.project_id, p.project_name, p.project_register_end_time ,s.sched_id, s.sched_date, s.sched_start_time, r.teacher_confirm FROM projects p  JOIN (
  SELECT  project_id, sched_id, sched_date AS sched_date, sched_start_time FROM (SELECT * FROM schedules WHERE CONCAT(sched_date,' ',sched_end_time) >= NOW()) AS ns
) AS s ON (p.project_id = s.project_id) JOIN registerations r ON (r.sched_id = s.sched_id) WHERE (p.is_active = 1) AND (r.user_id='9427903') AND (p.project_id = '6') ORDER BY  sched_date LIMIT  1";

        $query1 = "SELECT p.project_id, p.project_name, p.project_register_end_time ,s.sched_id, s.sched_date, s.sched_start_time, r.teacher_confirm FROM projects p  JOIN (
  SELECT  project_id, sched_id, MIN(sched_date) AS sched_date, sched_start_time FROM (SELECT * FROM schedules WHERE CONCAT(sched_date,' ',sched_end_time) >= NOW()) AS ns GROUP BY project_id
  ) AS s ON (p.project_id = s.project_id) JOIN registerations r ON (r.sched_id = s.sched_id) WHERE (p.is_active = 1) AND (r.user_id='$this->user_id') AND (p.project_id = '$pid')";

        $query2 = "SELECT p.project_id, p.project_name, p.project_register_end_time ,s.sched_id, s.sched_date, s.sched_start_time, r.teacher_confirm FROM projects p  JOIN (
  SELECT  project_id, sched_id, sched_date, sched_start_time FROM (SELECT * FROM schedules WHERE CONCAT(sched_date,' ',sched_end_time) >= NOW()) AS ns
  ) AS s ON (p.project_id = s.project_id) JOIN registerations r ON (r.sched_id = s.sched_id) WHERE (p.is_active = 1) AND (r.user_id='$this->user_id') AND (p.project_id = '$pid') AND (r.teacher_confirm = 1)";

        $result = $this->db_read($query2);
        if ($result && $result->num_rows > 0){
            return $result;
        }
        else{
            $result = $this->db_read($query0);
            return $result;
        }
    }

    public function get_student_project_list(){
        $query = "SELECT DISTINCT r.project_id FROM registerations r JOIN (SELECT * FROM schedules WHERE CONCAT(sched_date,' ',sched_end_time) >= NOW()) AS s ON (r.sched_id = s.sched_id) WHERE user_id = '$this->user_id'";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $project_list = array();
            while ($r = $result->fetch_assoc()){
                $p = $this->fetch_student_project_info($r['project_id']);
                if ($p) {
                    array_push($project_list, $p->fetch_assoc());
                }
            }
            return $project_list;
        }
        else{
            return false;
        }
    }

    public function has_project($pid){
        $query = "SELECT * FROM registerations WHERE (user_id = '$this->user_id') AND (project_id = '$pid')";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function has_project_sched($pid, $sched_id){
        $query = "SELECT * FROM registerations WHERE (user_id = '$this->user_id') AND (project_id = '$pid') AND (sched_id = $sched_id)";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            return true;
        }
        else{
            return false;
        }
    }

    public function register_times($pid, $times){
        $query1 = "DELETE FROM registerations WHERE (user_id=$this->user_id) AND (project_id = '$pid') ";
        if (!$this->db_action($query1)){
            $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
            return false;
        }
        while ($sched_item = current($times)){
            $sched_id = key($times);
            $query2 = "INSERT INTO registerations(project_id, sched_id, user_id) VALUES ('$pid', $sched_id, '$this->user_id')";
            if (!$this->db_action($query2)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
            next($times);
        }
        $this->set_message("درخواست شما در سیستم ثبت شد");
        return true;

    }
    public function delete_project($pid){
        $query1 = "DELETE FROM registerations WHERE (user_id=$this->user_id) AND (project_id = '$pid') ";
        if (!$this->db_action($query1)){
            $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
            return false;
        }
        return true;
    }

    public function fetch_project_sched_info_confirmed($pid){
        $query = "SELECT * FROM schedules as s JOIN registerations as r on s.sched_id = r.sched_id WHERE (s.project_id='$pid') AND (r.user_id = $this->user_id) AND (r.teacher_confirm = 1)";
        $result = $this->db_read($query);
        if ($result->num_rows > 0) {
            return $result;
        }
        else{
            return false;
        }
    }

}