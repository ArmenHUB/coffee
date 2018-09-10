<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

$answer = $income_data;
$params = $income_data->params;

switch ($params->command) {
    case "device_list":
        $result = getDeviceList($params->owner_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_info":
        $result = deviceInfo($params->device_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_add_edit":
        $result = addEditDevice($income_data->user_id,$income_data->lang_id,$params->device_id, $params->name, $params->address, $params->model, $params->location, $params->expiration_date);
        if ($result == 0) { // reset password ok
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        } else { // returned error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        }
        break;
    case "device_list_status_expiration":
        $result = deviceListStatusExpiration($params->owner_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_remove":
        $result = removeDevice($params->device_id);
        if ($result == 0) { // reset password ok
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        } else { // returned error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        }
        break;
    case "device_recipe_add":
        $result = addEditDeviceRecipe($params->device_id, $params->button_id, $params->price, $params->recipe_id);
        if ($result == 0) { // reset password ok
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        } else { // returned error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        }
        break;

    case "get_recipe_by_device_button_id":
        $result = getRecipeByDeviceButtonId($params->device_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
}
if ($answer['error'] > 0) {
    $answer['error'] = getError($answer['error'], $income_data->lang_id);
}
echo json_encode($answer);

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
function addEditDevice($user_id,$lang_id,$device_id, $name, $address, $device_type_id, $location, $expiration_date)
{
    if (gettype($device_id) != "integer") {
        return 10;
        die();
    }
    if ($name == "" || $address == "" || $device_type_id == "" || $location == "" || $expiration_date == ""){
        return 9;
        die();
    }
    $con = new Z_MySQL();
      if($device_id == 0){
            $con->queryDML("INSERT INTO `deviceUsers` (`userID`,`deviceTypeID`) VALUES ('$user_id','$device_type_id')");
             $data1 = $con->queryNoDML("SELECT `deviceID` FROM `deviceUsers` WHERE `userID` = '$user_id'")[0];
             if($data1['deviceID'] > 0){
                $deviceID = $data1['deviceID'];
                $con->queryDML("INSERT INTO `deviceParamValues` (`deviceParamValueID`, `text`) VALUES (NULL, '$name'),(NULL, '$location'),(NULL, '$address'),(NULL, '$expiration_date')");

                $data2 = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceParamValues` WHERE `text` IN ('$name','$location','$address','$expiration_date')");
                // DeviceParamValues
                $val_id1 = $data2[0]['deviceParamValueID'];
                $val_id2 = $data2[1]['deviceParamValueID'];
                $val_id3 = $data2[2]['deviceParamValueID'];
                $val_id4 = $data2[3]['deviceParamValueID'];

                $data3 = $con->queryNoDML("SELECT `deviceParamNameID` FROM `deviceParamNames` WHERE `text` IN ('name','location','address','expiration Date')");
                // DeviceParamNames
                $name_id1 = $data3[0]['deviceParamNameID'];// location
                $name_id2 = $data3[1]['deviceParamNameID'];// name
                $name_id3 = $data3[2]['deviceParamNameID'];// address              
                $name_id4 = $data3[3]['deviceParamNameID'];// expiration date  
               $data4=$con->queryDML("INSERT INTO `deviceInfo` (`deviceID`,`deviceParamNameID`,`deviceParamValueID`,`deviceTypeID`) VALUES ('$deviceID','$name_id2','$val_id1','$device_type_id'), ('$deviceID','$name_id1','$val_id2','$device_type_id'), ('$deviceID','$name_id3','$val_id3','$device_type_id'),('$deviceID','$name_id4','$val_id4','$device_type_id')");  
                   if($data4){
                      return 0;
                   }
                   else{
                      return 4;
                   }              
             }
      }
      else{
         $data = $con->queryNoDML("SELECT `deviceID` FROM `deviceInfo` WHERE `deviceID` = '$device_id'"); 
         if($data){
            $data1 = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceInfo` WHERE `deviceID` = '$device_id'");
            $status = '1';
            $array_values = array($location,$name,$address,$status,$expiration_date);
            $i = 0;
             foreach ($data1 as $key => $value) {
                 $device_param_value_id = $value['deviceParamValueID'];
                 $con->queryDML("UPDATE `deviceParamValues` SET `text` = '$array_values[$i]' WHERE `deviceParamValueID` = '$device_param_value_id'");
                 $i++;
             }
             return 0;
         }
         else{
            return 4;
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
        ["serial_number" => ["9852", "4568", "2356", "7452"], "last_activity" => "2018-09-06 20:59:51", "UID" => "952684265893257145885312", "owner" => "Owner name 3", "expiration_date" => "10.12.18", "status" => "2"]
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
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceInfo` WHERE  `deviceID`= '$device_id'");
    if($data){
       $con->queryDML("DELETE FROM `deviceInfo` WHERE `deviceID`= '$device_id'");
       $con->queryDML("DELETE FROM `deviceUsers` WHERE `deviceID`= '$device_id'"); 
       foreach ($data as $key => $value) {
          $device_param_value_id = $value['deviceParamValueID'];
          $con->queryDML("DELETE FROM `deviceParamValues` WHERE `deviceParamValueID`= '$device_param_value_id'");      
       }
      return 0;           
    }
    else{
        return 7;
    }
}
/**
 * @param $device_id
 * @param $button_id
 * @param $price
 * @param $recipe_id
 * @return int
 */
//????????????????????????????????????????????????????????????????????????
function addEditDeviceRecipe($device_id, $button_id, $price, $recipe_id)
{
    if (gettype($device_id) != "integer" || gettype($button_id) != "integer" || gettype($recipe_id) != "integer") {
        return 10;
        die();
    }
    if ($price == "") {
        return 9;
        die();
    }
    $con = new Z_MySQL();
    if($recipe_id == 0){
      $data=$con->queryDML("INSERT INTO `recipeDevice` (`recipeID`,`deviceID`,`buttonID`,`price`) VALUES (NULL,'$device_id','$button_id',' $price')");
      if($data){
         return 0;
      }
      else{
         return 4;
      }
    }
    else{
      $data=$con->queryDML("UPDATE `recipeDevice` SET `deviceID` = '$device_id', `buttonID` = '$button_id', `price` = '$price' WHERE `recipeID` = '$recipe_id'");
      if($data){
         return 0;
      }
      else{
         return 4;
      }
    }
}
/**
 * @param $device_id
 * @return array|int
 */
function getRecipeByDeviceButtonId($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `recipeID`,`buttonID`,`price` FROM `recipeDevice` WHERE `deviceID` = '$device_id'")[0];
    if($data){
        return $data;
    }
    else{
       return 7;
    }
}
