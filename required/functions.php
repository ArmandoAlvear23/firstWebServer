<?php
require_once 'db_connect.php';

//Function to create a secure session
function secure_session_start(){
    $session_name = 'secure_session_id';
    $secure = TRUE;
    $httpsonly = TRUE;
    if(ini_set('session.use_only_cookies',1)==FALSE){
        header("Location: ../error.php?err=Cannot exclusively use cookies (ini_set)");
        exit();
    }
    $cookieParams = session_get_cookie_params();
    session_set_cookie_params($cookieParams["lifetime"], $cookieParams["path"], $cookieParams["domain"], $secure, $httpsonly);
    session_name($session_name);
    session_start();
    session_regenerate_id();
}

//Function to login
function login($username, $password, $mysqli){
    if($stmt = $mysqli->prepare("SELECT pid,pname,password FROM webuser where pname = ? LIMIT 1")){
        $stmt->bind_param('s',$username);
        $stmt->execute();
        $stmt->store_result();
        $stmt->bind_result($db_pid,$db_pname,$db_password);
        $stmt->fetch();
        if($stmt->num_rows==1){
            if(password_verify($password,$db_password)){
                
                if($stmt=$mysqli->prepare("SELECT status FROM device WHERE devnum=1")){
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($db_status);
                    $stmt->fetch();
                    if($db_status==1){
                        $user_browser=$_SERVER['HTTP_USER_AGENT'];
                        $_SESSION['user_id'] = $db_pid;
                        $_SESSION['username']= $username;
                        $_SESSION['login_string']=hash('sha512',$db_password.$user_browser);
                        return true;
                    }else {return false;}
                }else {return false;}
            }else {return false;}
        }else {return false;}
    }else {return false;}
}

//Function to check if user is logged in
function login_check($mysqli){
    if(isset($_SESSION['user_id'], $_SESSION['username'], $_SESSION['login_string'])){
        $user_id = $_SESSION['user_id'];
        $username=$_SESSION['username'];
        $login_string=$_SESSION['login_string'];
        $user_browser= $_SERVER['HTTP_USER_AGENT'];
        if($stmt=$mysqli->prepare("SELECT password FROM webuser WHERE pid = ? LIMIT 1")){
            $stmt->bind_param('i', $user_id);
            $stmt->execute();
            $stmt->store_result();
            $stmt->bind_result($password);
            $stmt->fetch();
            if($stmt->num_rows==1){
                $login_check=hash('sha512',$password.$user_browser);
                if(hash_equals($login_check,$login_string)){
                    return true;
                }else {return false;}
            }else {return false;}
        }else {return false;}
    }else {return false;}
}
?>