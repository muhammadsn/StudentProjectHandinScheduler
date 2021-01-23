<?php
$GLOBALS = array(
    'message' => ""
);

class System
{
    private $db_host;
    private $db_name;
    private $db_user;
    private $db_pass;
    private $connection;
    protected $glb;

    public function __construct()
    {
        $this->db_host = "localhost";
        $this->db_name = "ProjectScheduler";
        $this->db_user = "root";
        $this->db_pass = "2g2o1r4t3e9x";
        $this->glb =& $GLOBALS;
    }

    public function set_message($msg){
        $this->glb['message'] = $msg;
        return $this->glb['message'];
    }

    public function has_message(){
        if (!empty($this->glb['message'])){
            return true;
        }
        else{
            return false;
        }
    }

    public function get_message(){
        return $this->glb['message'];
    }

    private function db_connect(){
        $this->connection = new mysqli($this->db_host,$this->db_user, $this->db_pass, $this->db_name);
        if ($this->connection->connect_error){
            die("connection to database failed; ".$this->connection->connect_error);
        }
        else{
            return true;
        }
    }

    protected function db_read($query){
        $this->db_connect();
        $result = $this->connection->query($query);
        $this->connection->close();
        if ($result && $result->num_rows > 0){
            return $result;
        }
        else {
            return false;
        }
    }

    protected function db_action($query){
        $this->db_connect();
        if ($this->connection->query($query) === true){
            $this->connection->close();
            return true;
        }
        else {
            $this->connection->close();
            return false;
        }
    }

    protected function hash($string){
        return md5($string);
    }

    protected function generate_token($user_id, $length = 10 ){
        $characters = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        $charactersLength = strlen($characters);
        $randomString = '';
        for ($i = 0; $i < $length; $i++) {
            $randomString .= $characters[rand(0, $charactersLength - 1)];
        }
        $query = "UPDATE users SET token='$randomString' WHERE user_id=$user_id";
        if (!$this->db_action($query)){
            $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
            return 0;
        }
        return $randomString;
    }

    public function is_logged_in($user_token){
        $query = "SELECT * FROM users WHERE token='$user_token'";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $userinfo = $result->fetch_assoc();
            if ($userinfo['type'] === "0"){
                header("Location: student.php");
                die();
            }
            else if ($userinfo['type'] === "1"){
                header("Location: teacher.php");
                die();
            }
        }
        else{
            return false;
        }
    }

    public function is_not_logged_in($user_token, $type){
        $query = "SELECT * FROM users WHERE token='$user_token'";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $userinfo = $result->fetch_assoc();
            if ($userinfo['type'] == $type){
                return false;
            }
            else if ($userinfo['type'] != $type){
                header("Location: login.php");
                die();
            }
        }
        else{
            return true;
        }
    }

    public function is_user($id, $email){
        $query = "SELECT * FROM users WHERE user_id='$id'";
        $result1 = $this->db_read($query);
        $query = "SELECT * FROM users WHERE email='$email'";
        $result2 = $this->db_read($query);
        if ($result1 && $result1->num_rows > 0){
            return 1;
        }
        else if ($result2 && $result2->num_rows > 0){
            $this->set_message("این ایمیل قبلاً ثبت شده است");
            return 2;
        }
        else{
            return 0;
        }
    }
}