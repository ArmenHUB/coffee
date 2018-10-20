<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";

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
        case "incasation":
            $result = getIncasasationInfo($params->device_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "incasation_edit":
            $result = getIncasasationEdit($params->device_id, $params->data);
            if ($result == 0) { // reset password ok
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
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
 * @param $collector_id
 * @return array|int
 */
function getDeviceList($collector_id)
{
    if (gettype((int)$collector_id) != "integer") {
        return 7;
    }
    $con = new Z_MySQL();
    $user_id = $con->queryNoDML("SELECT `owner_id` FROM `Owner_collectors` WHERE `collector_id` = '$collector_id'")[0]['owner_id'];
    $arr1 = array();
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
//$collector_id = '19';
//print_r(getDeviceList($collector_id));

function getIncasasationInfo($device_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
    }

    $con = new Z_MySQL();
    $cup = CUP;
    $data = $con->queryNoDML("SELECT `action_log`.`ingredientsID` AS ingredientsID,`ingredientsName`.`text` AS ingr_name, SUM(`action_log`.`count`) AS measurement, `measurement_units`.`text` AS type, `action_log`.`measurement_unitsID` AS Measurement_UnitID FROM `action_log` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `action_log`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` INNER JOIN `measurement_units` ON `measurement_units`.`measurement_unitsID` = `action_log`.`measurement_unitsID` where `action_log`.`deviceID` = '$device_id' AND  `action_log`.`type` != 'incasation' AND `ingredients`.`ingredientNameID` IN ('$cup') GROUP BY `ingredientsName`.`text`");
    if($data){
        return $data;
    }
    else{
        return 9;
    }
}

function getIncasasationEdit($device_id, $data){
    if (gettype($device_id) != "integer") {
        return 10;
    }
    if (empty($data)) {
        return 10;
    }
    $con = new Z_MySQL();
    $money_cash_in = MONEY_CASH_IN;
    $ingr_name = INC_MONEY;
    $array = json_decode(json_encode($data), true);
    for ($i=0; $i < count($array); $i++){
        $name = $array[$i]['name'];
        $ingr_id = $array[$i]['ingr_id'];
        $count = $array[$i]['value'];
        $measurement_unit_id = $array[$i]['measurement_unit_id'];
        $type = "cash";
        if($name == "Cup" && $count !== "0"){
            $data_insert = $con->queryDML("INSERT INTO `action_log` (`deviceID`,`ingredientsID`,`count`,`measurement_unitsID`,`type`) VALUES ('$device_id','$ingr_id','$count','$measurement_unit_id','$type')");
        }

        if($name == "Cash Out" && $count == "0"){
           return 0;
        }
        else if($name == "Cash Out" && $count == "1"){
            $data = $con->queryNoDML("SELECT `action_log`.`ingredientsID` AS ingr_id,`ingredientsName`.`text` AS ingr_name, SUM(`action_log`.`count`) AS measurement, `measurement_units`.`text` AS type, `action_log`.`measurement_unitsID` AS Measurement_UnitID FROM `action_log` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `action_log`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` INNER JOIN `measurement_units` ON `measurement_units`.`measurement_unitsID` = `action_log`.`measurement_unitsID` where `action_log`.`deviceID` = '$device_id' AND `action_log`.`type` != 'incasation' AND `ingredients`.`ingredientNameID` IN ('$money_cash_in') GROUP BY `ingredientsName`.`text`");
            if($data){
                $data_ingr = $con->queryDML("INSERT INTO `ingredients` (`ingredientNameID`,`unitVending`,`unitCollector`) VALUES ('$ingr_name','0','0')");
                $ingr_id_1 = $con->connection->insert_id;
                $count_1 = $data[0]['measurement'];
                $measurement_unit_id_1 = $data[0]['Measurement_UnitID'];
                $type_1 = "incasation";
                $data_insert_1 = $con->queryDML("INSERT INTO `action_log` (`deviceID`,`ingredientsID`,`count`,`measurement_unitsID`,`type`) VALUES ('$device_id','$ingr_id_1','-$count_1','$measurement_unit_id_1','$type_1')");
                return 0;
            }
            else{
                return 9;
            }
        }
    }


}
//delete from `action_log` Where `count` IN ('150','-5000','0','-4500')
//$device_id = 1;
//$data = array("2");
//print_r(getIncasasationEdit($device_id, $data));
