<?php
require_once "z_mysql.php";
require_once "errors.php";
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

$answer = $income_data;
$params = $income_data->params;

switch ($params->command) {
    case "device_info":
         $info = device_info();
         $answer = ["user_id" => $income_data->user_id, "token" => "0", "error" => "0", "info" => $info];
        break; 
    case "device_list":
         $info = device_list();
         $answer = ["user_id" => $income_data->user_id, "token" => "0", "error" => "0", "info" => $info];
        break;
    case "device_register":
         $device_type = $params->device_type;
         $device_name = $params->device_name;
         $location = $params->location;
         $answer = device_register($device_type,$device_name,$location);
        break;          
}
if ($answer['error'] > 0) {
    $answer['error'] = getError($answer['error'], $income_data->lang_id);
}
echo json_encode($answer);

function device_info(){
	$con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT devices.deviceID AS deviceID, deviceType.text AS devicetype,  devices.deviceName AS deviceName,  devices.location AS location, devices.button_img AS button_img FROM `devices` INNER JOIN `deviceType` ON devices.deviceTypeID = deviceType.deviceTypeID");
    return $data;	
}
function device_list(){
	$con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT devices.deviceID AS deviceID, deviceType.text AS devicetype,  devices.deviceName AS deviceName,  devices.location AS location, devices.deviceStatusID FROM `devices` INNER JOIN `deviceType` ON devices.deviceTypeID = deviceType.deviceTypeID");
    return $data;	
}
function device_register($device_type,$device_name,$location){
   $con = new Z_MySQL();
   $data = $con->queryNoDML("SELECT deviceID, FROM `devices` WHERE `deviceName` = '{$device_name}' AND `location` = '{$location}'")[0];
   if($data['deviceID'] < 0){
   	  
   }
}
