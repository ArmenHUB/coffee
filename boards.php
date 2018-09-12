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
        case "board_add":
            $result = addBoard($params->UID);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "check_serial_number":
            $result = checkSerialNumber($params->serial_number);
            if($result == 0){ // reset password ok
             $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }else{ // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } 
            break;
        case "expiration_date_edit":
            $result = expirationDateEdit($params->device_id,$params->expiration_date);
            if($result == 0){ // reset password ok
             $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }else{ // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } 
            break;
    }
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
/**
 * @param $UID
 * @return array|int
 */
function addBoard($UID)
{
    if (strlen($UID) < 24) {
        return 8;
        die();
    }
    $con = new Z_MySQL();
    $serial_number = random_serial_number();
    $lastActivity = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
    $data = $con->queryDML("INSERT INTO `boards` (`boardID`,`UID`,`serialNumber`,`lastActivity`) VALUES (NULL,'$UID','$serial_number','$lastActivity')");
    if($data){
        $piece_1 = substr($serial_number, 0, 4);   
        $piece_2 = substr($serial_number, 4, 4); 
        $piece_3 = substr($serial_number, 8, 4); 
        $piece_4 = substr($serial_number, 12, 4);
        $serial_number_array=array($piece_1,$piece_2,$piece_3,$piece_4);
        return $serial_number_array;
    }
    else{
       return 9;
    }
}
/**
 * @brief create session key
 * @return string -  random generated number
 */
function random_serial_number() {
    $result = '';
    for($i = 0; $i < 16; $i++) {
        $result .= mt_rand(0, 9);
    }
    return $result;
}
/**
 * @param $serial_number
 * @return int
 */
//GET ARRAY $serial_number = array("3435","4645","4675","7515");
function checkSerialNumber($serial_number){
    if (empty($serial_number)){
        return 9;
        die();
    }
    $con = new Z_MySQL();
    $serial_number_string = implode("",$serial_number);
    $data = $con->queryNoDML("SELECT `serialNumber` FROM `boards` WHERE `serialNumber` = '$serial_number_string'")[0];
    if($data['serialNumber'] > 0){
        return 0;
    }
    else{
        return 9;
    }
}
/**
 * @param $device_id
 * @param $expiration_date
 * @return int
 */
function expirationDateEdit($device_id,$expiration_date){
    if(gettype($device_id) != "integer") {
        return 10;
        die();
    }
    if($expiration_date == ""){
        return 9;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `deviceParamNameID` FROM `deviceParamNames` WHERE `text` = 'expiration Date'")[0];
    if($data){
      $device_param_name_id = $data['deviceParamNameID'];  
    }
    else{
        return 9;
    }    
    $data1 = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceInfo` WHERE `deviceParamNameID` = '$device_param_name_id' AND `deviceID` = '$device_id'")[0];
    if($data1){
      $device_param_value_id = $data1['deviceParamValueID'];
    }
    else{
        return 9;
    }
    $data2 = $con->queryDML("UPDATE `deviceParamValues` SET `text` = '$expiration_date' WHERE `deviceParamValueID` = '$device_param_value_id'");
    if($data2){
        return 0;
    }
    else{
        return 9;
    }
}
