<?php
include_once "Classes/User.php";
$user = new User();

if (isset($_COOKIE['ps_token'])){
    $user->logout($_COOKIE['ps_token']);
}
else{
    header('Location: login.php');
    die();
}