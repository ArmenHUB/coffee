<?php
require_once "z_mysql.php";
require_once "errors.php";
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

// $answer = $income_data;
// $params = $income_data->params;

switch ($params->command) {
    case "device_info":
         $user_id = $params->user_id;
         $info = device_info($user_id);
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

function device_info($user_id){
	$con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT deviceID,deviceTypeID FROM `deviceUsers` WHERE `userID` = '$user_id'")[0];
    if($data){
    $deviceID = $data['deviceID'];
    $deviceTypeID = $data['deviceTypeID'];
    $data1 = $con->queryNoDML("SELECT deviceTypes.text AS DeviceType,deviceParamNames.text As ParamName, deviceParamValues.text As ParamValue FROM `deviceInfo` INNER JOIN  deviceParamNames ON deviceInfo.deviceParamNameID =  deviceParamNames.deviceParamNameID INNER JOIN deviceParamValues ON deviceInfo.deviceParamValueID  = deviceParamValues.deviceParamValueID INNER JOIN deviceTypes ON deviceTypes.deviceTypeID = '$deviceTypeID' WHERE deviceInfo.deviceID = '$deviceID'");
    return $data1;    	
    }
}
function device_list(){
	$con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT deviceTypes.text AS DeviceType,deviceParamNames.text As ParamName, deviceParamValues.text As ParamValue FROM `deviceInfo` INNER JOIN  deviceParamNames ON deviceInfo.deviceParamNameID =  deviceParamNames.deviceParamNameID INNER JOIN deviceParamValues ON deviceInfo.deviceParamValueID  = deviceParamValues.deviceParamValueID INNER JOIN deviceTypes");
    return $data;	
}
function device_register($device_type,$device_name,$location){
   $con = new Z_MySQL();
    if ($con->queryDML("INSERT INTO `deviceType`(`deviceTypeID`, `text`, `langID`) VALUES ('1', '$device_type', '1')")) {
       if ($con->queryDML("INSERT INTO `devices`(`deviceID`, `deviceTypeID`, `deviceName`, `deviceStatusID`, `location`) VALUES ('1', '1', '$device_name', '1','$location')")) {
          INSERT INTO deviceParamValues ('3','$location')
          INSERT INTO deviceInfo ('3','1','3')
       }   
    } 
}
SELECT `deviceParamValues`.`text` FROM `deviceParamValues` INNER JOIN `deviceInfo` ON `deviceParamValues`.`deviceParamValueID` = `deviceInfo`.`deviceParamValueID` WHERE `deviceInfo`.`deviceID` = '1';
