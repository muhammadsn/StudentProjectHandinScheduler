<?php
require_once "System.php";
class User extends System
{
    public $user_id;
    public $user_fullname;
    public $user_password;
    public $user_email;
    public $user_token;
    public $user_type;

    public function __construct()
    {
        parent::__construct();
    }

    public function login($id, $password){
        $query = "SELECT * FROM users WHERE user_id=$id";
        $result = $this->db_read($query);
        if ($result){
            $user = $result->fetch_assoc();
            if ($user['pass'] === $this->hash($password)){
                $this->user_id = $user['user_id'];
                $this->user_fullname = $user['full_name'];
                $this->user_type = $user['type'];
                $this->user_id = $user['user_id'];
                $this->user_token = $this->generate_token($this->user_id);
                setcookie("ps_token", $this->user_token);
                if ($this->user_type === "0"){
                    header("Location: student.php");
                    die();
                }
                else if ($this->user_type === "1"){
                    header("Location: teacher.php");
                    die();
                }
            }
            else{
                $this->set_message("نام کاربری یا گذرواژه نادرست است");
            }
        }
        else{
            $this->set_message("نام کاربری یا گذرواژه نادرست است");
        }
    }

    public function signup($id, $fullname, $email, $password){
        if (!$this->is_user($id, $email)){
            $pass = $this->hash($password);
            $query = "INSERT INTO users (user_id, pass, email, full_name) VALUES ('$id', '$pass', '$email', '$fullname')";
            if (!$this->db_action($query)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
            $token = $this->generate_token($id);
            setcookie("ps_token", $token);
            header("Location: student.php");
            die();
        }
        else if($this->is_user($id, $email) === 1){
            $this->set_message("این نام کاربری قبلاً ثبت شده است");
            return false;
        }
        else if($this->is_user($id, $email) === 2){
            $this->set_message("این ایمیل قبلاً ثبت شده است");
            return false;
        }
    }

    public function logout($token){
        $query = "SELECT * FROM users WHERE token='$token'";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0){
            $query = "UPDATE users SET token='' WHERE token='$token'";
            if (!$this->db_action($query)){
                $this->set_message("مشکلی در ارتباط با سرور پیش آمده است.");
                return false;
            }
            setcookie('ps_token', '', time() - 3600 );
            header('Location: login.php');
            die();
        }
        else{
            header('Location: login.php');
            die();
        }
    }

    public function fetch_user_data($token){
        $query = "SELECT user_id, full_name,type FROM users WHERE token='$token'";
        $result = $this->db_read($query);
        if ($result && $result->num_rows > 0) {
            $userinfo = $result->fetch_assoc();
            $this->user_id = $userinfo['user_id'];
            $this->user_fullname = $userinfo['full_name'];
            $this->user_type = $userinfo['type'];
            return true;
        }
        else{
            return false;
        }
    }

}