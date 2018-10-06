<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
require_once "be_mail.php";
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);
$params = $income_data->params;
$is_logged_normaly = false;
$answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
if (checkUser($income_data->user_id, $income_data->token)) {
    $is_logged_normaly = true;
}
if ($is_logged_normaly) {
    switch ($params->command) {
        case "logs_devices":
            $data = $params->data;
            $result = getDeviceLogsTable($data->device_id,$data->date_range,$data->message_type);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => $income_data->user_id, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "logs_users":
            $data = $params->data;
            $result = getUsersLogsTable($data->user_id,$data->date_range,$data->message_type);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => $income_data->user_id, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
    }
}
if ($answer['error'] > 0) {
    $answer['error'] = Geterror($answer['error'], $income_data->lang_id);
}
echo json_encode($answer);
/**
 * @param $user_id
 * @param $token
 * @return bool
 */
function checkUser($user_id, $token)
{
    $con = new Z_MySQL();
    $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
    $answer = $con->queryNoDML("SELECT `loggedUsers`.`lastAction` AS 'lastAction' FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id} AND `loggedUsers`.`token` = '{$token}'")[0]["lastAction"];
    $cur_date = new DateTime($cur_time);
    $last_date = new DateTime($answer);
    if ($answer != "") {
        if ($last_date->getTimestamp() + LOG_OFF_DELAY > $cur_date->getTimestamp() || LOG_OFF_DELAY === 0) {
            $con->queryDML("UPDATE `loggedUsers` SET `lastAction`='{$cur_time}' WHERE `loggedUsers`.`userID` = {$user_id}");
            return true;
        } else {
            $con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id}");
        }
    }
    return false;
}
//function getVendingTable($device_id, $scale, $date_rage)

function getDeviceLogsTable($device_id,$date_range, $message_type)
{
/*    if (gettype($device_id) != "array") {
        return 10;
    }*/
    $con = new Z_MySQL();
    $arr_send = array();
    $device_param_name_id = NAME;
    $datetime = explode(" - ", $date_range);
    $datetime_1 = $datetime[0];
    $datetime_2 = $datetime[1];
    if($message_type == "0"){
        for ($i = 0; $i < count($device_id); $i++) {
            $device_id_1 = $device_id[$i];
            $data = $con->queryNoDML("SELECT `deviceInfo`.`deviceParamValueID` AS device_param_value FROM `deviceInfo` WHERE `deviceID` = '$device_id_1' AND `deviceParamNameID` = $device_param_name_id");
            if ($data){
                $device_param_value_id = $data[0]['device_param_value'];
                $device_name = $con->queryNoDML("SELECT `deviceParamValues`.`text` AS Device_name FROM `deviceParamValues` WHERE `deviceParamValueID`='$device_param_value_id'")[0]['Device_name'];
                $data1 = $con->queryNoDML("SELECT `logs`.`timestamp` AS 'date',`eventType`.`text` AS message_type,`logs`.`module` AS result, `logs`.`event` AS action FROM `logs` INNER JOIN `eventType` ON `logs`.`eventTypeID` = `eventType`.`eventTypeID` WHERE `logs`.`deviceID` = '$device_id_1' AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2'");
                for ($i1=0; $i1 < count($data1); $i1++) {
                    $data1[$i1]['name'] = $device_name;
                    array_push($arr_send, $data1[$i1]);
                }
            } else {
                return 10;
            }
        }
        return $arr_send;
    }
    else{
        if($message_type == "1"){ // info
            $event_type_id = EVENT_TYPE_INFO;
        }
        else if($message_type == "2"){ // warning
            $event_type_id = EVENT_TYPE_WARNING;
        }
        else if($message_type == "3"){ // error
            $event_type_id = EVENT_TYPE_ERROR;
        }
        else{
            return 0;
        }
        for ($i = 0; $i < count($device_id); $i++) {
            $device_id_1 = $device_id[$i];
            $data = $con->queryNoDML("SELECT `deviceInfo`.`deviceParamValueID` AS device_param_value FROM `deviceInfo` WHERE `deviceID` = '$device_id_1' AND `deviceParamNameID` = $device_param_name_id");
            if ($data){
                $device_param_value_id = $data[0]['device_param_value'];
                $device_name = $con->queryNoDML("SELECT `deviceParamValues`.`text` AS Device_name FROM `deviceParamValues` WHERE `deviceParamValueID`='$device_param_value_id'")[0]['Device_name'];
                $data1 = $con->queryNoDML("SELECT `logs`.`timestamp` AS 'date',`eventType`.`text` AS message_type,`logs`.`module` AS result, `logs`.`event` AS action FROM `logs` INNER JOIN `eventType` ON `logs`.`eventTypeID` = `eventType`.`eventTypeID` WHERE `logs`.`deviceID` = '$device_id_1' AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `eventType`.`eventTypeID` = $event_type_id");
                for ($i1=0; $i1 < count($data1); $i1++) {
                    $data1[$i1]['name'] = $device_name;
                    array_push($arr_send, $data1[$i1]);
                }
            } else {
                return 10;
            }
        }
        return $arr_send;
    }
    // return [
    //     "headers" => ["Datetime", "Message Type", "Username","DeviceID","Result", "Action"],
    //     "body" => [
    //         ["date" => "19-05-18 14:20:54", "message_type" => "test", "name" => "Test name 1", "result"=> "Incasation OK", "action" => "Incasation"],
    //         ["date" => "01.03.18 11:20:54", "message_type" => "test", "name" => "Test name 2", "result"=> "", "action" => "Cash Out"],
    //         ["date" => "13.07.18 14:20:54", "message_type" => "test", "name" => "Test name 3", "result"=> "", "action" => "Cash In "]
    //     ]
    // ];
}
// $device_id = array('3');
// $user_id = '1';
// $date_range = '2018-01-12 17:12:44 - 2018-12-12 17:12:44';
// $message_type = 3;
// $a = getDeviceLogsTable($device_id, $user_id, $date_range, $message_type);
// foreach ($a as $key => $value) {
//     $n = $value['device_name'];
//     echo $value['device_name']."|".$value['date']."|".$value['message_type']."|".$value['Result']."|".$value['event']."|".$value['action']."</br>";
// }
function getUsersLogsTable($user_id,$date_range,$message_type){
    if (gettype($user_id) != "array") {
        return 10;
    }
    $con = new Z_MySQL();
    $arr_send = array();
    $datetime = explode(" - ", $date_range);
    $datetime_1 = $datetime[0];
    $datetime_2 = $datetime[1];
    if($message_type == "0"){
        for ($i = 0; $i < count($user_id); $i++) {
            $user_id_1 = $user_id[$i];
            $data = $con->queryNoDML("SELECT `users`.`name` AS name FROM `users` WHERE `userID` = '$user_id_1'");
            if ($data){
                $name = $data[0]['name'];
                $data1 = $con->queryNoDML("SELECT `logs`.`timestamp` AS 'date',`eventType`.`text` AS message_type,`logs`.`module` AS Result, `logs`.`event` AS action FROM `logs` INNER JOIN `eventType` ON `logs`.`eventTypeID` = `eventType`.`eventTypeID` WHERE `logs`.`userID` = '$user_id_1' AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `deviceID` = '0' ");
                for ($i1=0; $i1 < count($data1); $i1++) {
                    $data1[$i1]['name'] = $name;
                    array_push($arr_send, $data1[$i1]);
                }
            } else {
                return 10;
            }
        }
        return $arr_send;
    }else{
        if($message_type == "1"){ // info
            $event_type_id = EVENT_TYPE_INFO;
        }
        else if($message_type == "2"){ // warning
            $event_type_id = EVENT_TYPE_WARNING;
        }
        else if($message_type == "3"){ // error
            $event_type_id = EVENT_TYPE_ERROR;
        }
        else{
            return 0;
        }
        for ($i = 0; $i < count($user_id); $i++) {
            $user_id_1 = $user_id[$i];
            $data = $con->queryNoDML("SELECT `users`.`name` AS name FROM `users` WHERE `userID` = '$user_id_1'");
            if ($data){
                $name = $data[0]['name'];
                $data1 = $con->queryNoDML("SELECT `logs`.`timestamp` AS 'date',`eventType`.`text` AS message_type,`logs`.`module` AS Result, `logs`.`event` AS action FROM `logs` INNER JOIN `eventType` ON `logs`.`eventTypeID` = `eventType`.`eventTypeID` WHERE `logs`.`userID` = '$user_id_1' AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `deviceID` = '0' AND `eventType`.`eventTypeID` = $event_type_id");
                for ($i1=0; $i1 < count($data1); $i1++) {
                    $data1[$i1]['name'] = $name;
                    array_push($arr_send, $data1[$i1]);
                }
            } else {
                return 10;
            }
        }
        return $arr_send;
    }
}
// $user_id = array('1');
// $date_range = '2018-01-12 17:12:44 - 2018-12-12 17:12:44';
// $message_type = 3;
// $a = getUsersLogsTable($user_id,$date_range,$message_type);
// foreach ($a as $key => $value) {
//     echo $value['name']."|".$value['date']."|".$value['message_type']."|".$value['Result']."|".$value['event']."|".$value['action']."</br>";
// }
