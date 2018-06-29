<?php
    require_once __DIR__ . '/../required/db_connect.php';
    $username = "username";
    $password = "password";
    if($stmt = $mysqli->prepare("INSERT INTO webuser(pname, password)VALUES(?,?)")){
        $hashedPassword = password_hash($password, PASSWORD_DEFAULT);
        $stmt->bind_param('ss',$username, $hashedPassword);
        $stmt->execute();
    }
?>