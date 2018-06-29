<?php
    require_once __DIR__ . '/../../required/db_connect.php';
    $input = file_get_contents("php://input");
    if($input){
        $inJson = json_decode($input, true);
        if(json_last_error()== JSON_ERROR_NON){
            if(isset($inJson["username"], $inJson["password"])){
                if($stmt = $mysqli->prepare("SELECT password FROM webuser WHERE pname = ? LIMIT 1")){
                    $stmt->bind_param('s', $inJson["username"]);
                    $stmt->execute();
                    $stmt->store_result();
                    $stmt->bind_result($pass);
                    $stmt->fetch();
                    if($stmt->num_rows==1){
                        if(password_verify($inJson["password"], $pass)){
                            $mydata = simplexml_load_file(__DIR__.'/../xfer1.xml');
                            $pname = $mydata->insert->person->pname;
                            $street = $mydata->insert->person->street;
                            $city = $mydata->insert->person->city;
                            if($stmt = $mysqli->prepare("INSERT INTO person(pname,street,city)VALUES(?,?,?)")){
                                $stmt->bind_param('sss',$pname,$street,$city);
                                $stmt->execute();
                                echo 'SUCCESS';
                            }
                            else {echo 'ERROR: INSERT statement failed';}
                        }
                        else {echo 'ERROR: Incorrect password';}
                    }
                    else {echo 'ERROR: Number of rows != 1';}
                }
                else {echo 'ERROR: mysqli>prepare statement failed';}
            }
            else {echo 'ERROR: JSON Request is missing username and/or password keys';}
        }
        else {echo 'ERROR: There is some type of error found in JSONRequest input';}
    }
    else {echo 'ERROR: input is NULL';}
    ?>