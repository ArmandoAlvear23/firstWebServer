<?php
require_once __DIR__ . '/../../required/db_connect.php';
$input = file_get_contents("php://input");
$error=0; 
$out_json = array(); 
$out_json['success'] = 1;

$SW1_status=0; 
$LED1_status=0;
if ($input) {
	$json = json_decode($input, true);
	if (json_last_error() == JSON_ERROR_NONE) {
		if (isset($json["username"]) && isset($json["password"]) && isset($json["SW1"])&& isset($json["LED1"]) && isset($json["SENDER"])){
			$in_username = $json["username"];
			$in_password = $json["password"];
			$in_SW1 = $json["SW1"];
			$in_LED1 = $json["LED1"];
			$in_sender = $json["SENDER"];
			if($stmt=$mysqli->prepare("SELECT password FROM webuser WHERE pname = ? LIMIT 1")) {
				$stmt->bind_param('s', $in_username);
				$stmt->execute();  
				$stmt->store_result();
				$stmt->bind_result($db_password);
				$stmt->fetch();
				if ($stmt->num_rows == 1) {
					if (password_verify($in_password, $db_password)) {
						$stmt->close();

						#RPi Instructions
						if($in_sender == 'RPI')
						{
							if($stmt = $mysqli->prepare("UPDATE device SET status = ? WHERE devname = 'SW1'"))
							{
								$stmt->bind_param('i', $in_SW1);
								$stmt->execute();
							}else
							{
								$error=0;
							}
							$stmt->close();
						}

						#Android Instructions
						elseif($in_sender == 'ANDROID')
						{
							if($stmt = $mysqli->prepare("UPDATE device SET status = ? WHERE devname = 'LED1'")){
								$stmt->bind_param('i', $in_LED1);
								$stmt->execute();
							} 
							else 
							{
								$error=1;
							}
							$stmt->close();
						}
						else 
						{
							$error=10;
						}

						if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM device WHERE devname='SW1'"))){
							$stmt->execute(); 
							$stmt->bind_result($SW1_status);
							$stmt->fetch();
						}else {$error=2;}
						$stmt->close();
						if (!$error && ($stmt = $mysqli->prepare("SELECT status FROM device WHERE devname = 'LED1'"))){
							$stmt->execute(); 
							$stmt->bind_result($LED1_status);
							$stmt->fetch();
						}else {$error=3;}
						$stmt->close();
					} else {$error=4;}
				} else {$error=5;}
			} else {$error=6;}
		} else {$error=7;}
	} else {$error=8;}
} else {$error=9;}
if ($error){
	$out_json['success'] = 0;
}
$out_json['SW1'] = $SW1_status;
$out_json['LED1'] = $LED1_status;
$out_json['error'] = $error;
echo json_encode($out_json);
?>