<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
require_once "be_mail.php";

//get send data //
date_default_timezone_set('America/Los_Angeles');
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
         case "ingridient":
             $result = getIngridientTable($params->device_id, $params->scale, $params->date_rage);
             if (gettype($result) == 'integer') { // return error number
                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
             } else {
                 $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
             }
             break;
         case "enchashement":
             $data = $params->data;
             $result = getEnchashementTable($data->device_id,$data->scale, $data->date_range,$income_data->user_id);

             if (gettype($result) == 'integer') { // return error number
                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
             } else {
                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
             }
             break;
         case "vending":
             $data = $params->data;
             $result = getVendingTable($data->device_id,$data->scale, $data->date_range,$income_data->user_id);
             if (gettype($result) == 'integer') { // return error number
                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
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
function getIngridientTable($device_id, $scale, $date_rage)
{
    if (gettype($device_id) != "integer") {
        return 10;
    }
    if ($scale == "" || $date_rage == "") {
        return 9;
    }
    return [
        "headers" => ["Datetime", "Sugar", "Milk", "Coffee", "Money"],
        "body" => [
            ["10.05.18, 14:20:54", "300", "300", "300", "1500"],
            ["25.05.18, 11:20:54", "150", "150", "150", "2500"],
            ["10.05.18, 14:20:54", "300", "300", "300", "1500"]
        ]
    ];
}

function getEnchashementTable($device_id, $scale, $date_range,$user_id)
{
//    if (gettype($device_id) != "array") {
//        return 10;
//    }
    $con = new Z_MySQL();
    $datetime = explode(" - ",$date_range);
    $datetime_1 = $datetime[0];
    $datetime_2 = $datetime[1];
    $arr_val = [CUP,INC_MONEY];
    $arr_send = array();
    if(empty($device_id)){
        $data = $con->queryNoDML("SELECT `deviceID` FROM `deviceUsers` WHERE `userID`='$user_id'");
        for ($i=0; $i < count($data); $i++) {
            $device_id = $data[$i]['deviceID'];
            $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`,`timestamp` FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`deviceID` = '$device_id'  AND  `action_log`.`type` NOT IN ('vending') AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND 1 GROUP BY `timestamp`");
            if($data1){
                foreach ($data1 as $key => $value) {
                    $arr = array();
                    $date = $value['timestamp'];
                    $count = $value['count'];
                    $count_ingr = explode(",",$count);
                    $arr['device_id'] = $value['deviceID'];
                    $arr['date'] = $date;
                    $arr['cup'] = $count_ingr[0];
                    $arr['cash_out'] = $count_ingr[1];
                    array_push($arr_send, $arr);
                }
            }
        }
        if(empty($arr_send)){
            return FALSE;
        }else{
            return $arr_send;
        }
    }
    else{
       // UPDATE `action_log` SET `timestamp` = '2018-10-01 15:31:45' WHERE `count` = '700'
        switch ($scale) {
            case "0": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, `timestamp` FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND  `action_log`.`deviceID` = '$device_id1' AND `action_log`.`type` NOT IN ('vending') AND 1 GROUP BY `timestamp`");
                    if($data1){
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $date = $value['timestamp'];
                            $count = $value['count'];
                            $count_ingr = explode(",",$count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;
                            $arr['cup'] = $count_ingr[0];
                            $arr['cash_out'] = $count_ingr[1];
                            array_push($arr_send, $arr);
                        }
                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "1": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, `timestamp` FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `action_log`.`type` NOT IN ('vending') AND  1 GROUP BY `timestamp`");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $date = $value['timestamp'];
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;
                            $arr['cup'] = $count_ingr[0];
                            $arr['cash_out'] = $count_ingr[1];
                            array_push($arr_send, $arr);
                        }
                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "2": // hour
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`),GROUP_CONCAT(`count`) AS `count`,Year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `action_log`.`type` NOT IN ('vending') AND 1 GROUP BY Year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`)");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $ingr_id = $value['ingredientsID'];
                            $year = $value['Year'];
                            $month = $value['Month'];
                            $day = $value['Day'];
                            $hour = $value['Hour'];
                            $minute = $value['Minute'];
                            $date = $year . "-" . $month . "-" . $day . " " . $hour . ":" . $minute;
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;

                            for($j = 0;$j < count($count_ingr);$j++){
                                if($count_ingr[$j] > 0){
                                    $arr['cup'] += $count_ingr[$j];
                                }else if($count_ingr[$j] < 0){
                                    $arr['cash_out'] += $count_ingr[$j];
                                }
                            }
                            array_push($arr_send, $arr);

                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "3": // day
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`),GROUP_CONCAT(`count`) AS `count`,Year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `action_log`.`type` NOT IN ('vending') AND 1 GROUP BY Year(`timestamp`), month(`timestamp`), day(`timestamp`)");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $ingr_id = $value['ingredientsID'];
                            $year = $value['Year'];
                            $month = $value['Month'];
                            $day = $value['Day'];
                            $hour = $value['Hour'];
                            $minute = $value['Minute'];
                            $date = $year . "-" . $month . "-" . $day;
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;

                            for($j = 0;$j < count($count_ingr);$j++){
                                if($count_ingr[$j] > 0){
                                    $arr['cup'] += $count_ingr[$j];
                                }else if($count_ingr[$j] < 0){
                                    $arr['cash_out'] += $count_ingr[$j];
                                }
                            }
                            array_push($arr_send, $arr);

                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "4": // month
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`),GROUP_CONCAT(`count`) AS `count`,Year(`timestamp`) AS Year, month(`timestamp`) AS Month FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `action_log`.`type` NOT IN ('vending') AND 1 GROUP BY Year(`timestamp`), month(`timestamp`)");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $ingr_id = $value['ingredientsID'];
                            $year = $value['Year'];
                            $month = $value['Month'];
                            $day = $value['Day'];
                            $hour = $value['Hour'];
                            $minute = $value['Minute'];
                            $date = $year . "-" . $month;
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;

                            for($j = 0;$j < count($count_ingr);$j++){
                                if($count_ingr[$j] > 0){
                                    $arr['cup'] += $count_ingr[$j];
                                }else if($count_ingr[$j] < 0){
                                    $arr['cash_out'] += $count_ingr[$j];
                                }
                            }
                            array_push($arr_send, $arr);

                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "5": // month
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`),GROUP_CONCAT(`count`) AS `count`,Year(`timestamp`) AS Year FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `action_log`.`type` NOT IN ('vending') AND 1 GROUP BY Year(`timestamp`)");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $ingr_id = $value['ingredientsID'];
                            $year = $value['Year'];
                            $month = $value['Month'];
                            $day = $value['Day'];
                            $hour = $value['Hour'];
                            $minute = $value['Minute'];
                            $date = $year;
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;

                            for($j = 0;$j < count($count_ingr);$j++){
                                if($count_ingr[$j] > 0){
                                    $arr['cup'] += $count_ingr[$j];
                                }else if($count_ingr[$j] < 0){
                                    $arr['cash_out'] += $count_ingr[$j];
                                }
                            }
                            array_push($arr_send, $arr);

                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
        }
    }
    // return [
    //     "headers" => ["Datetime", "Cup", "Cash Out",],
    //     "body" => [
    //         ["date" => "17.05.18, 14:20:54", "cup" => "1",  "cash_out" => "1500"],
    //         ["date" => "11.05.18, 11:20:54", "cup" => "21", "cash_out" => "2500"],
    //         ["date" =>  "18.05.18, 14:20:54","cup" => "31", "cash_out" => "1500"]
    //     ]
    // ];
}
//UPDATE `action_log` SET `timestamp` = '2018-10-01 16:31:22' WHERE `count` = '700'


//SELECT GROUP_CONCAT(`ingredientsName`.`ingredientsNameID`) AS igr_name_id, `action_log`.`timestamp` AS 'timestamp',GROUP_CONCAT(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '2018-11-30 12:12:12' AND '2018-12-25 12:12:12' AND `action_log`.`deviceID` = '5' AND `ingredientsName`.`ingredientsNameID` IN ('3','5','7') AND (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '3' AND `type` = 'cash') AND 1 GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`), day(`action_log`.`timestamp`), hour(`action_log`.`timestamp`),minute(`action_log`.`timestamp`)

function getVendingTable($device_id, $scale, $date_range,$user_id)
{
    // if (gettype($device_id) != "integer") {
    //     return 10;
    // }
    // if ($scale == "" || $date_rage == "") {
    //     return 9;
    // }
    $con = new Z_MySQL();
    $datetime = explode(" - ",$date_range);
    $datetime_1 = $datetime[0];
    $datetime_2 = $datetime[1];
    $arr_val = [CUP,MONEY_CASH_IN,VENDING_MONEY];
    $arr_send = array();
        switch ($scale) {
            case "0": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT GROUP_CONCAT(`ingredientsName`.`ingredientsNameID`) AS igr_name_id, `action_log`.`timestamp` AS 'timestamp',GROUP_CONCAT(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '$arr_val[0]' AND `type` = 'cash') AND 1 GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`), day(`action_log`.`timestamp`), hour(`action_log`.`timestamp`),minute(`action_log`.`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $date = $value['timestamp'];
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['in_sum'] = $count_ingr[0];
                            $arr['v_sum'] = $count_ingr[1];
                            $arr['cup'] = $count_ingr[2];
                            $arr['date'] = $date;
                            array_push($arr_send, $arr);
                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "1": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT GROUP_CONCAT(`ingredientsName`.`ingredientsNameID`) AS igr_name_id, `action_log`.`timestamp` AS 'timestamp',GROUP_CONCAT(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '$arr_val[0]' AND `type` = 'cash') AND 1 GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`), day(`action_log`.`timestamp`), hour(`action_log`.`timestamp`),minute(`action_log`.`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $date = $value['timestamp'];
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['in_sum'] = $count_ingr[0];
                            $arr['v_sum'] = $count_ingr[1];
                            $arr['cup'] = $count_ingr[2];
                            $arr['date'] = $date;
                            array_push($arr_send, $arr);
                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "2": // hour
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("Select `action_log`.`timestamp` AS 'timestamp', GROUP_CONCAT(`ingredientsID`), SUM(case when `type` = 'cash' THEN `count` END) cash, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[2]' THEN `count` END) vending, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[0]' THEN `count` END) incasation from `action_log` WHERE (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '3' AND `type` = 'cash') AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`), day(`action_log`.`timestamp`), hour(`action_log`.`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $date = $value['timestamp'];
                            $arr['in_sum'] = $value['cash'];
                            $arr['v_sum'] = $value['vending'];
                            $arr['cup'] = $value['incasation'];
                            $arr['date'] = $date;
                            array_push($arr_send, $arr);
                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "3": // day
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("Select `action_log`.`timestamp` AS 'timestamp', GROUP_CONCAT(`ingredientsID`), SUM(case when `type` = 'cash' THEN `count` END) cash, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[2]' THEN `count` END) vending, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[0]' THEN `count` END) incasation from `action_log` WHERE (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '3' AND `type` = 'cash') AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`), day(`action_log`.`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $date = $value['timestamp'];
                            $arr['in_sum'] = $value['cash'];
                            $arr['v_sum'] = $value['vending'];
                            $arr['cup'] = $value['incasation'];
                            $arr['date'] = $date;
                            array_push($arr_send, $arr);
                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "4": // month
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("Select `action_log`.`timestamp` AS 'timestamp', GROUP_CONCAT(`ingredientsID`), SUM(case when `type` = 'cash' THEN `count` END) cash, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[2]' THEN `count` END) vending, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[0]' THEN `count` END) incasation from `action_log` WHERE (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '3' AND `type` = 'cash') AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $date = $value['timestamp'];
                            $arr['in_sum'] = $value['cash'];
                            $arr['v_sum'] = $value['vending'];
                            $arr['cup'] = $value['incasation'];
                            $arr['date'] = $date;
                            array_push($arr_send, $arr);
                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
            case "5": // year
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("Select `action_log`.`timestamp` AS 'timestamp', GROUP_CONCAT(`ingredientsID`), SUM(case when `type` = 'cash' THEN `count` END) cash, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[2]' THEN `count` END) vending, SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = ' $arr_val[0]' THEN `count` END) incasation from `action_log` WHERE (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '3' AND `type` = 'cash') AND `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' GROUP BY year(`action_log`.`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $date = $value['timestamp'];
                            $arr['in_sum'] = $value['cash'];
                            $arr['v_sum'] = $value['vending'];
                            $arr['cup'] = $value['incasation'];
                            $arr['date'] = $date;
                            array_push($arr_send, $arr);
                        }

                    }
                }
                if(empty($arr_send)){
                    return FALSE;
                }else{
                    return $arr_send;
                }
                break;
        }
    // return [
    //     "headers" => ["Datetime", "Cup", "V Sum", "In Sum"],
    //     "body" => [
    //         ["date" => "19.05.18, 14:20:54", "cup" => "3", "v_sum" => "3000", "in_sum" => "1500"],
    //         ["date" => "01.03.18, 11:20:54", "cup" =>  "1", "v_sum" =>  "1500", "in_sum" => "2500"],
    //         ["date" => "13.07.18, 14:20:54", "cup" =>  "3", "v_sum" =>  "3000", "in_sum" => "1500"]
    //     ]
    // ];
}

// SELECT  `action_log`.`timestamp` AS Date,`action_log`.`ingredientsID`,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute, `ingredientsName`.`text` AS ingr_name, sum(`count`) AS CashOut FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '2018-10-12 12:09:11' AND '2018-10-13 11:11:11' AND `action_log`.`deviceID` = '1'  GROUP BY  `timestamp`,`ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`), minute(`timestamp`)



//SELECT GROUP_CONCAT(`ingredientsName`.`ingredientsNameID`) AS igr_name_id, `action_log`.`timestamp` AS 'timestamp',GROUP_CONCAT(`action_log`.`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '2017-12-12 12:12:12' AND '2019-12-12 12:12:12' AND `action_log`.`deviceID` = '5' AND `ingredientsName`.`ingredientsNameID` IN ('3','5','7') AND (`action_log`.`ingredientsID`,`action_log`.`type`) NOT IN (SELECT `action_log`.`ingredientsID`,`action_log`.`type` FROM `action_log` WHERE `ingredientsID` = '3' AND `type` = 'cash') AND 1 GROUP BY year(`action_log`.`timestamp`), month(`action_log`.`timestamp`), day(`action_log`.`timestamp`)
