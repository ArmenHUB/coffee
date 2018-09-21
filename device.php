<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
//get send data //
// $all_data = file_get_contents('php://input');
// $income_data = json_decode($all_data);

// $answer = $income_data;
// $params = $income_data->params;
// $is_logged_normaly = false;
// $answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
// if (checkUser($income_data->user_id, $income_data->token)) {
//     $is_logged_normaly = true;
// }
if ($is_logged_normaly) {
switch ($params->command) {

    case "device_list":
        $result = getDeviceList($params->owner_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $result["token"], "user_id" => $result["user_id"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_info":
        $result = deviceInfo($params->device_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $result["token"], "user_id" => $result["user_id"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_add_edit":
        $data = $params->data;
        $result = addEditDevice($income_data->user_id,$income_data->lang_id,$params->device_id, $data->name, $data->address, $data->model, $data->location, $data->expiration_date,$data->serial_number);
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
            $answer = ["token" => $result["token"], "user_id" => $result["user_id"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
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
            $answer = ["token" => $result["token"], "user_id" => $result["user_id"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
}
}
// if ($answer['error'] > 0) {
//     $answer['error'] = getError($answer['error'], $income_data->lang_id);
// }
// echo json_encode($answer);

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
 * @param $owner_id
 * @return array|int
 */
function getDeviceList($owner_id)
{
    if (gettype($owner_id) != "integer") {
        return 7;
        die();
    }
    $arr1 = array();
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `userTypeID` FROM `users` WHERE `userID` = '$owner_id'")[0];
    if($data['userTypeID'] == 2){
       $data1 = $con->queryNoDML("SELECT `deviceID` FROM deviceUsers WHERE `userID` = '$owner_id'");
       if($data1){
         foreach ($data1 as $key => $value) {
             $device_id = $value['deviceID'];
 $data2 = $con->queryNoDML("SELECT `deviceInfo`.`deviceID` AS DeviceID,`deviceParamNames`.`text` AS DeviceParamName,`deviceParamValues`.`text` AS DeviceParamValue,`deviceTypes`.`text` AS Model,`deviceTypes`.`deviceTypeID` AS DeviceTypeID FROM `deviceInfo` INNER JOIN `deviceParamNames` ON `deviceInfo`.`deviceParamNameID` = `deviceParamNames`.`deviceParamNameID` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` INNER JOIN `deviceTypes` ON `deviceTypes`.`deviceTypeID` = `deviceInfo`.`deviceTypeID`  WHERE   `deviceParamNames`.`text` IN ('name','address','sum','status','location','map_icon') AND `deviceInfo`.`deviceID` = '$device_id'");
                   if($data2){ 
                         $arr = array();              
                     foreach ($data2 as $key1 => $value1) {
                       $device_id=$value1['DeviceID'];
                       $device_model = $value1['Model'];
                       $device_type_id = $value1['DeviceTypeID'];
                       $device_param_name = $value1['DeviceParamName'];
                       $device_param_value = $value1['DeviceParamValue'];
                       $arr['device_id'] = $device_id;
                       $arr['device_type_id'] =  $device_type_id;
                       $arr['device_model'] = $device_model;
                          $arr[$device_param_name] = $device_param_value;                         
                       }
                      array_push($arr1,$arr);                 
                    }
                   else{
                        return 7;
                    }
         }
     return $arr1;
    }
    else{
        return 7;
    }
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
                          $arr = array();              
                     foreach ($data1 as $key1 => $value1) {
                       $device_id=$value1['DeviceID'];
                       $device_model = $value1['DeviceTypeName'];
                       $device_param_name = $value1['DeviceParamsNames'];
                       $device_param_value = $value1['DeviceParamsValues'];
                       $arr['device_id'] = $device_id;
                       $arr['device_model'] = $device_model;
                          $arr[$device_param_name] = $device_param_value;                         
                       }
                      return $arr;
          }
          else{
            return 7;
          }
    }
    else{
        return 7;
    }    
}
deviceInfo(1);
/**
 * @param $device_id
 * @param $name
 * @param $address
 * @param $model
 * @param $coordinates
 * @param $expiration_date
 * @return int
 */
function addEditDevice($user_id,$lang_id,$device_id, $name, $address, $device_type_id, $location, $expiration_date,$serial_number)
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
                 // Add boardDevice
                 $data_board = $con->queryNoDML("SELECT `boardID` FROM `boards` WHERE `serialNumber` = '$serial_number'")[0];
                 $board_id = $data_board['boardID'];
                 $con->queryDML("INSERT INTO `boardDevice` (`deviceID`, `boardID`) VALUES ('$deviceID', '$board_id')");

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
function deviceListStatusExpiration($owner_id)
{
    if (gettype($owner_id) != "integer") {
        return 10;
        die();
    }
    $con = new Z_MySQL();
    if($owner_id == 0){
       $data = $con->queryNoDML("SELECT `deviceParamNameID` FROM `deviceParamNames` WHERE `text` = 'expiration Date'")[0];
       $device_param_name_id = $data['deviceParamNameID'];
       $data1 = $con->queryNoDML("SELECT `boards`.`UID` AS UID,`boards`.`serialNumber` AS serialNumber,`boards`.`lastActivity` AS lastActivity,`deviceParamValues`.`text` AS expirationDate,`users`.`name` AS Name FROM `boards` INNER JOIN `boardDevice` ON `boardDevice`.`boardID` = `boards`.`boardID` INNER JOIN `deviceUsers` ON `deviceUsers`.`deviceID` = `boardDevice`.`deviceID` INNER JOIN `users` ON `users`.`userID` = `deviceUsers`.`userID` INNER JOIN `deviceInfo` ON `deviceInfo`.`deviceID` = `deviceUsers`.`deviceID` INNER JOIN `deviceParamValues` ON `deviceParamValues`.`deviceParamValueID` = `deviceInfo`.`deviceParamValueID` WHERE `users`.`userTypeID` = '2' AND `deviceInfo`.`deviceParamNameID` = '$device_param_name_id'");
       if($data1){
         return $data1;
       }
       else{
         return  7;
       }   
    }
    else{
       $data = $con->queryNoDML("SELECT `deviceParamNameID` FROM `deviceParamNames` WHERE `text` = 'expiration Date'")[0];
       $device_param_name_id = $data['deviceParamNameID'];
       $data1 = $con->queryNoDML("SELECT `boards`.`UID` AS UID,`boards`.`serialNumber` AS serialNumber,`boards`.`lastActivity` AS lastActivity,`deviceParamValues`.`text` AS expirationDate,`users`.`name` AS Name FROM `boards` INNER JOIN `boardDevice` ON `boardDevice`.`boardID` = `boards`.`boardID` INNER JOIN `deviceUsers` ON `deviceUsers`.`deviceID` = `boardDevice`.`deviceID` INNER JOIN `users` ON `users`.`userID` = `deviceUsers`.`userID` INNER JOIN `deviceInfo` ON `deviceInfo`.`deviceID` = `deviceUsers`.`deviceID` INNER JOIN `deviceParamValues` ON `deviceParamValues`.`deviceParamValueID` = `deviceInfo`.`deviceParamValueID` WHERE `users`.`userTypeID` = '2' AND `deviceInfo`.`deviceParamNameID` = '$device_param_name_id' AND `users`.`userID` = '$owner_id'");
       if($data1){
          return $data1;
       }
       else{
         return 7;
       }
    }
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

function addEditDeviceRecipe($device_id, $button_id, $price, $recipe_id)
{
    if (gettype($device_id) != "integer" || gettype($button_id) != "integer" || gettype($recipe_id) != "integer" || $recipe_id == "0" || $device_id == "0") {
        return 10;
        die();
    }
    if ($price == "") {
        return 9;
        die();
    }
      $con = new Z_MySQL();
    // if($recipe_id == 0){
    //   $data=$con->queryDML("INSERT INTO `recipeDevice` (`recipeID`,`deviceID`,`buttonID`,`price`) VALUES (NULL,'$device_id','$button_id',' $price')");
    //   if($data){
    //      return 0;
    //   }
    //   else{
    //      return 4;
    //   }
    // }
    // else{
    //   $data=$con->queryDML("UPDATE `recipeDevice` SET `deviceID` = '$device_id', `buttonID` = '$button_id', `price` = '$price' WHERE `recipeID` = '$recipe_id'");
    //   if($data){
    //      return 0;
    //   }
    //   else{
    //      return 4;
    //   }
    // }
    $data = $con->queryDML("INSERT INTO `recipeDevice` (`recipeID`,`deviceID`,`buttonID`,`price`) VALUES ('$recipe_id','$device_id','$button_id','$price') ON DUPLICATE KEY UPDATE `recipeID` = '$recipe_id', `buttonID` = '$button_id',`price` = '$price'");
    if($data){
        return 0;
    }
    else{
        return 4;
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
