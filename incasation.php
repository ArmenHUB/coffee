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
            $result = getIncasasationInfo($params->device_id, $params->$user_type_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
    }
}
if ($answer['error'] > 0) {
   $answer['error'] = errorGet($answer['error'], $income_data->lang_id);
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
        die();
    }
    if (gettype($user_type_id) != "integer") {
        return 7;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `action_log`.`ingredientsID` AS ingredientsID,`ingredientsName`.`text` AS ingr_name, SUM(`action_log`.`count`) AS measurement, `measurement_units`.`text` AS type FROM `action_log` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `action_log`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` INNER JOIN `measurement_units` ON `measurement_units`.`measurement_unitsID` = `action_log`.`measurement_unitsID` where `action_log`.`deviceID` = '$device_id' GROUP BY `ingredientsName`.`text`");

    if($data){
        return $data;
    }
    else{
        return 9;
    }
     // return [
     //      "ingr_list" => [
     //      ["ingr_id" => "1", "ingr_name" => "cup", "measurement" => "10", "type" => "t"],
     //      ["ingr_id" => "2", "ingr_name" => "sugar", "measurement" => "1", "type" => "p"],
     //      ["ingr_id" => "3", "ingr_name" => "coffee", "measurement" => "2", "type" => "kg"],
     //      ["ingr_id" => "4", "ingr_name" => "money", "measurement" => "2000", "type" => "money"],
     //        ]
     // ];
}
