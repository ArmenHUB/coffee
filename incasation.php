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
        case "incasation":
            $result = getIncasasationInfo($params->device_id, $params->user_type_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "incasation_edit":
            $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => '33'];
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

function getIncasasationInfo($device_id, $user_type_id)
{
    if (gettype($device_id) != "integer") {
        return 10;
    }
    if (gettype($user_type_id) != "integer") {
        return 7;
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `action_log`.`ingredientsID` AS ingredientsID,`ingredientsName`.`text` AS ingr_name, SUM(`action_log`.`count`) AS measurement, `measurement_units`.`text` AS type, `action_log`.`measurement_unitsID` AS Measurement_UnitID FROM `action_log` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `action_log`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` INNER JOIN `measurement_units` ON `measurement_units`.`measurement_unitsID` = `action_log`.`measurement_unitsID` where `action_log`.`deviceID` = '$device_id' AND  `action_log`.`type` != 'incasation'  GROUP BY `ingredientsName`.`text`");
    if($data){
        return $data;
    }
    else{
        return 9;
    }
    // return [
    //      "ingr_list" => [
    //      ["ingr_id" => "1", "ingr_name" => "cup", "measurement" => "10", "type" => "t"],
    //      ["ingr_id" => "4", "ingr_name" => "money", "measurement" => "2000", "type" => "money"],
    //        ]
    // ];
}
function getIncasasationEdit($device_id, $data){
    if (gettype($device_id) != "integer") {
        return 10;
    }
    if (empty($data)) {
        return 10;
    }
    $con = new Z_MySQL();
    $array = json_decode(json_encode($data), true);
    for ($i=0; $i < count($array); $i++){
        $ingr_id = $array[$i]['ingr_id'];
        $count = $array[$i]['value'];
        $spent_money = $array[$i]['spent_money'];
        $measurement_unit_id = $array[$i]['measurement_unit_id'];
        $type = "correction";
        $data_insert = $con->queryDML("INSERT INTO `action_log` (`deviceID`,`ingredientsID`,`count`,`spent_money`,`measurement_unitsID`,`type`) VALUES ('$device_id','$ingr_id','$count','$spent_money','$measurement_unit_id','$type')");
    }
    return 0;
}


// UPDATE `action_log` SET `ingredientsID` = '11' WHERE `timestamp` = '2018-06-12 21:22:11'

//Case When amount > 0 then amount else 0 end

// SELECT `action_log`.`ingredientsID` AS ingredientsID,`ingredientsName`.`text` AS ingr_name, SUM(Case When `action_log`.`count` > 0 then `action_log`.`count` else 0 end) AS measurement, `measurement_units`.`text` AS type FROM `action_log` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `action_log`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` INNER JOIN `measurement_units` ON `measurement_units`.`measurement_unitsID` = `action_log`.`measurement_unitsID` where `action_log`.`deviceID` = '1' GROUP BY `ingredientsName`.`text`

//SET GLOBAL sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''));


//$data = array('0' => ['ingr_id'=> '1', 'name'=> 'cup', 'value' => '2000'],'1' => ['ingr_id'=> '11', 'name'=> 'Cash in', 'value' => '7000']);
//for ($i = 0; $i < count($data); $i++){
//    $ingr_id = $data[$i]['ingr_id'];
//    $count = $data[$i]['value'];
//    $spent_money = $data[$i]['spent_money'];
//    $measurement_unit_id = $data[$i]['measurement_unit_id'];
//}
