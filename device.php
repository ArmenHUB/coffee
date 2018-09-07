<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
//get send data //
// $all_data = file_get_contents('php://input');
// $income_data = json_decode($all_data);

// $answer = $income_data;
// $params = $income_data->params;

// switch ($params->command) {
//     case "device_list":
//         $result = getDeviceList($params->owner_id);
//         if (gettype($result) == 'integer') { // return error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         } else {
//             $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         }
//         break;
//     case "device_info":
//         $result = deviceInfo($params->device_id);
//         if (gettype($result) == 'integer') { // return error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         } else {
//             $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         }
//         break;
//     case "device_add_edit":
//         $result = addEditDevice($income_data->user_id,$income_data->lang_id,$params->device_id, $params->name, $params->address, $params->model, $params->location, $params->expiration_date);
//         if ($result == 0) { // reset password ok
//             $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         } else { // returned error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         }
//         break;
//     case "device_list_status_expiration":
//         $result = deviceListStatusExpiration($params->owner_id);
//         if (gettype($result) == 'integer') { // return error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         } else {
//             $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         }
//         break;
//     case "device_remove":
//         $result = removeDevice($params->device_id);
//         if ($result == 0) { // reset password ok
//             $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         } else { // returned error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         }
//         break;
//     case "device_recipe_add":
//         $result = addEditDeviceRecipe($params->device_id, $params->button_id, $params->price, $params->recipe_id);
//         if ($result == 0) { // reset password ok
//             $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         } else { // returned error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         }
//         break;

//     case "get_recipe_by_device_button_id":
//         $result = getRecipeByDeviceButtonId($params->device_id);
//         if (gettype($result) == 'integer') { // return error number
//             $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//         } else {
//             $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//         }
//         break;
// }
// if ($answer['error'] > 0) {
//     $answer['error'] = getError($answer['error'], $income_data->lang_id);
// }
// echo json_encode($answer);

/**
 * @param $owner_id
 * @return array|int
 */
function getDeviceList($owner_id)
{
    if (gettype($owner_id) != "integer") {
        return 7;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `userTypeID` FROM `users` WHERE `userID` = '$owner_id'")[0];
    if($data['userTypeID'] == 2){
       $data1 = $con->queryNoDML("SELECT `deviceID` FROM deviceUsers WHERE `userID` = '$owner_id'")[0];
       if($data1['deviceID'] > 0){
          $device_id = $data1['deviceID'];
          $data2 = $con->queryNoDML("SELECT `deviceInfo`.`deviceID` AS DeviceID,`deviceParamValues`.`text` AS DeviceName FROM `deviceInfo` INNER JOIN `deviceParamNames` ON `deviceInfo`.`deviceParamNameID` = `deviceParamNames`.`deviceParamNameID` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` WHERE  `deviceInfo`.`deviceID` = '$device_id' AND `deviceParamNames`.`text` = 'name'");
          if($data2){
            return $data2;
          }
       }
       else{
         return 7;
       }
    }
    else{
        return 7;
    }
}
/**
 * @param $device_id
 * @return array|int
 */
function deviceInfo($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `deviceID` FROM `deviceInfo` WHERE `deviceID` = '$device_id'")[0];
    if($data['deviceID'] > 0){
          $data1 = $con->queryNoDML("SELECT `deviceInfo`.`deviceID` AS DeviceID,`deviceTypes`.`text` AS DeviceTypeName,`deviceParamNames`.`text` AS DeviceParamsNames,`deviceParamValues`.`text` AS DeviceParamsValues FROM `deviceInfo` INNER JOIN `deviceParamNames` ON `deviceInfo`.`deviceParamNameID` = `deviceParamNames`.`deviceParamNameID` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` INNER JOIN `deviceTypes` ON `deviceTypes`.`deviceTypeID` = `deviceInfo`.`deviceTypeID` WHERE  `deviceInfo`.`deviceID` = '$device_id'  AND `deviceParamNames`.`text` IN ('location','name','address','status')");
          if($data1){
             return $data1;
          }
          else{
            return 7;
          }
    }
    else{
        return 7;
    }    
}
/**
 * @param $device_id
 * @param $name
 * @param $address
 * @param $model
 * @param $coordinates
 * @param $expiration_date
 * @return int
 */
function addEditDevice($user_id,$lang_id,$device_id, $name, $address, $model, $location, $expiration_date)
{
    if (gettype($device_id) != "integer") {
        return 10;
        die();
    }
    if ($name == "" || $address == "" || $model == "" || $location == "" || $expiration_date == ""){
        return 9;
        die();
    }
    $con = new Z_MySQL();
      if($device_id == 0){
         $con->queryDML("INSERT INTO `deviceTypes` (`langID`,`text`,`image`) VALUES ('$lang_id','$model','1')");
         $data = $con->queryNoDML("SELECT `deviceTypeID` FROM `deviceTypes` WHERE `text` = '$model'")[0];
         if($data){
            $deviceTypeID = $data['deviceTypeID'];
            $con->queryDML("INSERT INTO `deviceUsers` (`userID`,`deviceTypeID`) VALUES ('$user_id','$deviceTypeID')");
            
            $con->queryDML("INSERT INTO `deviceParamValues` (`deviceParamValueID`, `text`) VALUES (NULL, '$name'),(NULL, '$address'),(NULL, '$location'),(NULL, '$expiration_date')");
            $con->queryDML("INSERT INTO `deviceInfo` (`userID`,`deviceTypeID`) VALUES ('$user_id','$deviceTypeID')");

         }
          
      }
}

/**
 * @param $device_id
 * @return array|int
 */
function deviceListStatusExpiration($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
    }
    return [
        ["serial_number" => ["1258", "4599", "4777", "6577"], "last_activity" => "2018-09-05 10:20:10", "UID" => "123456789012345678901234", "owner" => "Owner name 1", "expiration_date" => "10.11.18", "status" => "0"],
        ["serial_number" => ["9852", "4568", "2356", "7452"], "last_activity" => "2018-09-06 20:59:51", "UID" => "159753951456852456852125", "owner" => "Owner name 2", "expiration_date" => "10.11.18", "status" => "1"],
        ["serial_number" => ["9852", "4568", "2356", "7452"], "last_activity" => "2018-09-06 20:59:51", "UID" => "952684265893257145885312", "owner" => "Owner name 2", "expiration_date" => "10.12.18", "status" => "2"]
    ];
}

/**
 * @param $device_id
 * @return int
 */
function removeDevice($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
    }
    return 0;
}

/**
 * @param $device_id
 * @param $button_id
 * @param $price
 * @param $recipe_id
 * @return int
 */
function addEditDeviceRecipe($device_id, $button_id, $price, $recipe_id)
{
    if (gettype($device_id) != "integer" || gettype($button_id) != "integer" || gettype($recipe_id) != "integer") {
        return 10;
    }
    if ($price == "") {
        return 9;
    }
    return 0;
}

/**
 * @param $device_id
 * @return array|int
 */
function getRecipeByDeviceButtonId($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
    }
    return ["price" => "150", "button_id" => "1", "recipe_id" => "3"];
}
