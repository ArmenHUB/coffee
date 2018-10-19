<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
//1
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

$answer = $income_data;
$params = $income_data->params;
$is_logged_normaly = false;
$answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
if (checkUser($income_data->user_id, $income_data->token)) {
    $is_logged_normaly = true;
}
if ($is_logged_normaly) {
switch ($params->command) {

    case "device_list":
        $result = getDeviceList($income_data->user_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_info":
        $result = deviceInfo($params->device_id);
        if (gettype($result) == 'integer') { // return error number
            $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
        } else {
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
    case "device_add_edit":
        $data = $params->data;
        $result = addEditDevice($income_data->user_id,$income_data->lang_id,$params->device_id, $params->device_name, $params->address, $params->vm_type_id, $params->coordinates, $params->expiration_date,$params->serial_number);
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
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
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
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
        }
        break;
}
}
if ($answer['error'] > 0) {
    $answer['error'] = getError($answer['error'], $income_data->lang_id);
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
 * @param $owner_id
 * @return array|int
 */
function getDeviceList($user_id)
{
    if (gettype((int)$user_id) != "integer") {
        return 7;
    }
    $arr1 = array();
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `userTypeID` FROM `users` WHERE `userID` = '$user_id'")[0];
    if($data['userTypeID'] == 2){
        $data1 = $con->queryNoDML("SELECT `deviceID` FROM deviceUsers WHERE `userID` = '$user_id'");
        $arr_param_name_id = [NAME,ADDRESS,SUM,STATUS,LOCATION,MAP_ICON];
        if($data1){
            foreach ($data1 as $key => $value) {
                $device_id = $value['deviceID'];
                $data2 = $con->queryNoDML("SELECT `deviceInfo`.`deviceID` AS DeviceID,`deviceParamNames`.`text` AS DeviceParamName,`deviceParamValues`.`text` AS DeviceParamValue,`vm_types`.`name` AS Model,`deviceInfo`.`vm_type_id` AS DeviceTypeID FROM `deviceInfo` INNER JOIN `deviceParamNames` ON `deviceInfo`.`deviceParamNameID` = `deviceParamNames`.`deviceParamNameID` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` INNER JOIN `vm_types` ON `vm_types`.`vm_type_id` = `deviceInfo`.`vm_type_id`  WHERE   `deviceParamNames`.`deviceParamNameID` IN ($arr_param_name_id[0],$arr_param_name_id[1],$arr_param_name_id[2],$arr_param_name_id[3],$arr_param_name_id[4],$arr_param_name_id[5]) AND `deviceInfo`.`deviceID` = '$device_id'");
                if($data2){
                    $arr = array();
                    foreach ($data2 as $key1 => $value1) {
                        $device_param_name = $value1['DeviceParamName'];
                        $arr['device_id'] = $value1['DeviceID'];
                        $arr['device_type_id'] = $value1['DeviceTypeID'];
                        $arr['device_model'] = $value1['Model'];
                        $arr[$device_param_name] = $value1['DeviceParamValue'];
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

function deviceInfo($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `deviceID` FROM `deviceInfo` WHERE `deviceID` = '$device_id'")[0];
    $arr_param_name_id = [NAME,ADDRESS,SUM,STATUS,LOCATION,MAP_ICON];
    if($data['deviceID'] > 0){
        $data1 = $con->queryNoDML("SELECT `deviceInfo`.`deviceID` AS DeviceID,`vm_types`.`name` AS DeviceTypeName,`deviceInfo`.`vm_type_id` AS Vm_type_id,`deviceParamNames`.`text` AS DeviceParamsNames,`deviceParamValues`.`text` AS DeviceParamsValues FROM `deviceInfo` INNER JOIN `deviceParamNames` ON `deviceInfo`.`deviceParamNameID` = `deviceParamNames`.`deviceParamNameID` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` INNER JOIN `vm_types` ON `vm_types`.`vm_type_id` = `deviceInfo`.`vm_type_id` WHERE  `deviceInfo`.`deviceID` = '$device_id'  AND `deviceParamNames`.`deviceParamNameID` IN ($arr_param_name_id[4],$arr_param_name_id[0],$arr_param_name_id[1],$arr_param_name_id[3])");
        if($data1){
            $arr = array();
            foreach ($data1 as $key1 => $value1) {
                $device_param_name = $value1['DeviceParamsNames'];
                $arr['vm_type_id'] = $value1['Vm_type_id'];
                $arr['device_id'] = $value1['DeviceID'];
                $arr['device_model'] =  $value1['DeviceTypeName'];
                $arr[$device_param_name] =  $value1['DeviceParamsValues'];
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
    }
    if ($name == "" || $address == "" || $device_type_id == "" || $location == ""){
        return 9;
    }
    $con = new Z_MySQL();
    $arr_param_name_id = [NAME,LOCATION,ADDRESS,EXPIRATION_DATE,STATUS,MAP_ICON];
    if($expiration_date !== ""){
        if($device_id == 0){
            $location_xy = implode("-",$location);
            $status = STATUS_VALUE;
            $map_icon = MAP_ICON_VALUE;
            $serial_number_string = implode("",$serial_number);
            $data2 = array();
            $data1= $con->queryDML("INSERT INTO `deviceUsers` (`userID`,`vm_type_id`) VALUES ('$user_id','$device_type_id')");
            if($data1){
                $device_id = $con->connection->insert_id;
                // Add boardDevice
                $data_board = $con->queryNoDML("SELECT `boardID` FROM `boards` WHERE `serialNumber` = '$serial_number_string'")[0];
                $board_id = $data_board['boardID'];
                $con->queryDML("INSERT INTO `boardDevice` (`deviceID`, `boardID`) VALUES ('$device_id', '$board_id')");
                //Add device values
                $arr_dev_val = array($name,$location_xy,$address,$expiration_date);
                $arr_dev_val_1 = array($status,$map_icon);
                for ($i=0; $i < count($arr_dev_val); $i++) {
                    $con->queryDML("INSERT INTO `deviceParamValues` (`deviceParamValueID`, `text`) VALUES (NULL, ' $arr_dev_val[$i]')");
                    $device_param_value_id = $con->connection->insert_id;
                    $data2[$i] =  $device_param_value_id;
                }
                for ($i=0; $i < count($arr_dev_val_1); $i++) {
                    $device_param_value_id = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceParamValues` WHERE `text` = '$arr_dev_val_1[$i]'")[0]['deviceParamValueID'];
                    if($i == 0){
                        $data2[4] = $device_param_value_id;
                    }
                    else{
                        $data2[5] = $device_param_value_id;
                    }
                }
                for ($i=0; $i < 6; $i++) {
                    $data4=$con->queryDML("INSERT INTO `deviceInfo` (`deviceID`,`deviceParamNameID`,`deviceParamValueID`,`vm_type_id`) VALUES ('$device_id','$arr_param_name_id[$i]','$data2[$i]','$device_type_id')");
                }

                if($data4){
                    return 0;
                }
                else{
                    return 4;
                }
            }
        }
    }
    else{
        $data = $con->queryNoDML("SELECT `deviceID` FROM `deviceInfo` WHERE `deviceID` = '$device_id'");
        $location_xy = implode("-",$location);
        if($data){
            $arr_param_name_id = [NAME,LOCATION,ADDRESS];
            $data1 = $con->queryNoDML("UPDATE `deviceInfo` SET `vm_type_id` = '$device_type_id' WHERE `deviceID` = '$device_id'");
            $data2 = $con->queryNoDML("UPDATE `deviceUsers` SET `vm_type_id` = '$device_type_id' WHERE `deviceID` = '$device_id'");
            //if($data1 && $data2){
            $array_values = array($name,$location_xy,$address);
            for ($i=0; $i < 3; $i++) {
                $data3 = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceInfo` WHERE `deviceID` = '$device_id' AND `deviceParamNameID` = '$arr_param_name_id[$i]'")[0]['deviceParamValueID'];
                $con->queryDML("UPDATE `deviceParamValues` SET `text` = '$array_values[$i]' WHERE `deviceParamValueID` = '$data3'");
            }
            return 0;
            // }
            // else{
            //    return 44;
            // }
        }
        else{
            return 4;
        }
    }
}




function deviceListStatusExpiration($owner_id)
{
    if (gettype($owner_id) != "integer") {
        return 10;
    }
    $con = new Z_MySQL();
    $arr_param_name_id = [EXPIRATION_DATE,STATUS];
    $arr = array();
    if($owner_id == 0){
        $data = $con->queryNoDML("SELECT `boardDevice`.`deviceID` AS device_id,`boards`.`UID` AS UID,`boards`.`serialNumber` AS serialNumber,`boards`.`lastActivity` AS lastActivity FROM `boardDevice` RIGHT JOIN `boards` ON `boardDevice`.`boardID` = `boards`.`boardID`");
        $arr1 = array();
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                if (empty($value['device_id'])) {
                    $arr1['device_id'] = "NULL";
                    $arr1['UID'] = $value['UID'];
                    $arr1['serial_number'] = $value['serialNumber'];
                    $last_activity = $value['lastActivity'];
                    if($last_activity == "0000-00-00 00:00:00"){
                        $last_activity_1 = "NULL";
                    }
                    else{
                        $last_activity_1 =  $last_activity;
                    }
                    $arr1['last_activity'] = $last_activity_1;
                    $arr1['expiration_date'] = 'NULL';
                    $arr1['status'] = 2;
                    $arr1['owner_name'] = 'NULL';
                    array_push($arr, $arr1);
                } else {
                    $device_id = $value['device_id'];
                    $u_id = $value['UID'];
                    $serial_number = $value['serialNumber'];
                    $last_activity = $value['lastActivity'];
                    $data1 = $con->queryNoDML("SELECT `deviceParamNames`.`text` AS Device_param_name,`deviceParamValues`.`text` AS Device_param_value, `users`.`name` AS Name FROM `deviceInfo` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` INNER JOIN `deviceUsers` ON `deviceUsers`.`deviceID` = `deviceInfo`.`deviceID` INNER JOIN `users` ON `users`.`userID` = `deviceUsers`.`userID` INNER JOIN `deviceParamNames` ON `deviceParamNames`.`deviceParamNameID` = `deviceInfo`.`deviceParamNameID` WHERE `deviceInfo`.`deviceID` = '$device_id'  AND `deviceInfo`.`deviceParamNameID` IN($arr_param_name_id[0],$arr_param_name_id[1]) AND `users`.`userTypeID` = '2'");
                    foreach ($data1 as $key1 => $value1) {
                        $arr1['device_id'] = $device_id;
                        $arr1['UID'] = $u_id;
                        $arr1['serial_number'] = $serial_number;
                        if($last_activity == "0000-00-00 00:00:00"){
                            $last_activity_1 = "NULL";
                        }
                        else{
                            $last_activity_1 =  $last_activity;
                        }
                        $arr1['last_activity'] = $last_activity_1;
                        $device_param_name = $value1['Device_param_name'];
                        $arr1[$device_param_name] = $value1['Device_param_value'];
                        $arr1['owner_name'] = $value1['Name'];
                    }
                    array_push($arr, $arr1);
                }
            }
        }
        return $arr;
    }
    else if($owner_id == -1){
        $arr1 = array();
        $data = $con->queryNoDML("SELECT `UID`,`serialNumber` FROM `boards` WHERE `boardID` NOT IN (SELECT `boards`.`boardID` AS boardID FROM `boards` INNER JOIN `boardDevice` ON `boards`.`boardID` = `boardDevice`.`boardID`)");
        if($data){
            if(!empty($data)) {
                foreach ($data as $key => $value) {
                    $arr1['device_id'] = "NULL";
                    $arr1['UID'] = $value['UID'];
                    $arr1['serial_number'] = $value['serialNumber'];
                    $arr1['last_activity'] = 'NULL';
                    $arr1['expiration_date'] = 'NULL';
                    $arr1['status'] = 2;
                    $arr1['owner_name'] = 'NULL';
                    array_push($arr, $arr1);
                }
            }
            return $arr;
        }
        else{
            return 7;
        }
    }
    else{
        $data = $con->queryNoDML("SELECT `boardDevice`.`deviceID` AS device_id,`boards`.`UID` AS UID,`boards`.`serialNumber` AS serialNumber,`boards`.`lastActivity` AS lastActivity FROM `boardDevice` INNER JOIN `boards` ON `boardDevice`.`boardID` = `boards`.`boardID` INNER JOIN `deviceUsers` ON `deviceUsers`.`deviceID` = `boardDevice`.`deviceID` WHERE `deviceUsers`.`userID` = '$owner_id'");
        $arr1 = array();
        if(!empty($data)) {
            foreach ($data as $key => $value) {
                $device_id = $value['device_id'];
                $u_id = $value['UID'];
                $serial_number = $value['serialNumber'];
                $last_activity = $value['lastActivity'];
                $data1 = $con->queryNoDML("SELECT `deviceParamNames`.`text` AS Device_param_name,`deviceParamValues`.`text` AS Device_param_value, `users`.`name` AS Name FROM `deviceInfo` INNER JOIN `deviceParamValues` ON `deviceInfo`.`deviceParamValueID` = `deviceParamValues`.`deviceParamValueID` INNER JOIN `deviceUsers` ON `deviceUsers`.`deviceID` = `deviceInfo`.`deviceID` INNER JOIN `users` ON `users`.`userID` = `deviceUsers`.`userID` INNER JOIN `deviceParamNames` ON `deviceParamNames`.`deviceParamNameID` = `deviceInfo`.`deviceParamNameID` WHERE `deviceInfo`.`deviceID` = '$device_id' AND `deviceInfo`.`deviceParamNameID` IN($arr_param_name_id[0],$arr_param_name_id[1]) AND `users`.`userID` = '$owner_id'");
                foreach ($data1 as $key1 => $value1) {
                    $arr1['device_id'] = $device_id;
                    $arr1['UID'] = $u_id;
                    $arr1['serial_number'] = $serial_number;
                    $arr1['last_activity'] = $last_activity;
                    $device_param_name = $value1['Device_param_name'];
                    $arr1[$device_param_name] = $value1['Device_param_value'];
                    $arr1['owner_name'] = $value1['Name'];
                }
                array_push($arr, $arr1);
            }
        }
        return $arr;
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
    $status = STATUS;
    $map_icon = MAP_ICON;
    $data = $con->queryNoDML("SELECT `deviceParamValueID` FROM `deviceInfo` WHERE  `deviceID`= '$device_id' AND `deviceParamNameID` NOT IN ($status,$map_icon)");
    if($data){
        $con->queryDML("DELETE FROM `deviceInfo` WHERE `deviceID`= '$device_id'");
        $con->queryDML("DELETE FROM `deviceUsers` WHERE `deviceID`= '$device_id'");
        $con->queryDML("DELETE FROM `boardDevice` WHERE `deviceID`= '$device_id'");
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
