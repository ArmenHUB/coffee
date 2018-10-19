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
            $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`,`timestamp` FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`deviceID` = '$device_id' AND 1 GROUP BY `timestamp`");
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
            else{
                return 9;
            }
        }
        return $arr_send;
    }
    else{
       // UPDATE `action_log` SET `timestamp` = '2018-10-01 15:31:45' WHERE `count` = '700'
        switch ($scale) {
            case "0": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, `timestamp` FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND  `action_log`.`deviceID` = '$device_id1' AND 1 GROUP BY `timestamp`");
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
                    else{
                        return 9;
                    }

                }
                return $arr_send;
                break;
            case "1": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, `timestamp` FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND 1 GROUP BY `timestamp`");
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
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "2": // hour
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, Year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND 1 GROUP BY `timestamp`");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
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
                            $arr['cup'] = $count_ingr[0];
                            $arr['cash_out'] = $count_ingr[1];
                            array_push($arr_send, $arr);
                        }

                    }
                    else{
                        return 9;
                    }

                }
                return $arr_send;
                break;
            case "3": // day
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, Year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND 1 GROUP BY `timestamp`");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $year = $value['Year'];
                            $month = $value['Month'];
                            $day = $value['Day'];
                            $date = $year . "-" . $month . "-" . $day;
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;
                            $arr['cup'] = $count_ingr[0];
                            $arr['cash_out'] = $count_ingr[1];
                            array_push($arr_send, $arr);
                        }
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "4": // month
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, Year(`timestamp`) AS Year, month(`timestamp`) AS Month FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND 1 GROUP BY `timestamp`");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $year = $value['Year'];
                            $month = $value['Month'];
                            $date = $year . "-" . $month;
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $date;
                            $arr['cup'] = $count_ingr[0];
                            $arr['cash_out'] = $count_ingr[1];
                            array_push($arr_send, $arr);
                        }
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "5": // month
                for ($i=0;$i < count($device_id);$i++) {
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `deviceID`,GROUP_CONCAT(`action_log`.`ingredientsID`), GROUP_CONCAT(`count`) AS `count`, Year(`timestamp`) AS Year FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1]) AND `action_log`.`timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND 1 GROUP BY `timestamp`");
                    if($data1) {
                        foreach ($data1 as $key => $value) {
                            $arr = array();
                            $year = $value['Year'];
                            $count = $value['count'];
                            $count_ingr = explode(",", $count);
                            $arr['device_id'] = $value['deviceID'];
                            $arr['date'] = $year;
                            $arr['cup'] = $count_ingr[0];
                            $arr['cash_out'] = $count_ingr[1];
                            array_push($arr_send, $arr);
                        }
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
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
                    $data1 = $con->queryNoDML("SELECT `ingredientsName`.`ingredientsNameID` AS igr_name_id,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute,second(`timestamp`) AS Second, GROUP_CONCAT(`action_log`.`ingredientsID`) AS ingr_name, sum(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND 1 GROUP BY `ingredientsName`.`ingredientsNameID`,year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`),minute(`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $igr_name_id = $value['igr_name_id'];
                            $date = $value['Year']."-".$value['Month']."-".$value['Day']." ".$value['Hour'].":".$value['Minute'].":".$value['Second'];
                            $count = $value['count'];
                            if($igr_name_id == $arr_val[0]){
                                $arr['cup'] = $count;
                                $arr['date'] = $date;
                            }
                            else if($igr_name_id == $arr_val[1]){
                                $arr['in_sum'] = $count;
                            }
                            else if($igr_name_id == $arr_val[2]){
                                $arr['v_sum'] = $count;
                            }
                            $arr['date'] = $date;

                        }
                        array_push($arr_send, $arr);
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "1": // all
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `ingredientsName`.`ingredientsNameID` AS igr_name_id,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute,second(`timestamp`) AS Second, GROUP_CONCAT(`action_log`.`ingredientsID`) AS ingr_name, sum(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND 1 GROUP BY `ingredientsName`.`ingredientsNameID`,year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`),minute(`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $igr_name_id = $value['igr_name_id'];
                            $date = $value['Year']."-".$value['Month']."-".$value['Day']." ".$value['Hour'].":".$value['Minute'].":".$value['Second'];
                            $count = $value['count'];
                            if($igr_name_id == $arr_val[0]){
                                $arr['cup'] = $count;
                                $arr['date'] = $date;
                            }
                            else if($igr_name_id == $arr_val[1]){
                                $arr['in_sum'] = $count;
                            }
                            else if($igr_name_id == $arr_val[2]){
                                $arr['v_sum'] = $count;
                            }
                            $arr['date'] = $date;

                        }
                        array_push($arr_send, $arr);
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "2": // hour
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `ingredientsName`.`ingredientsNameID` AS igr_name_id,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour,  minute(`timestamp`) AS Minute,  GROUP_CONCAT(`action_log`.`ingredientsID`) AS ingr_name, sum(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND 1 GROUP BY `ingredientsName`.`ingredientsNameID`,year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`),minute(`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $igr_name_id = $value['igr_name_id'];
                            $date = $value['Year']."-".$value['Month']."-".$value['Day']." ".$value['Hour'].":".$value['Minute'];
                            $count = $value['count'];
                            if($igr_name_id == $arr_val[0]){
                                $arr['cup'] = $count;
                                $arr['date'] = $date;
                            }
                            else if($igr_name_id == $arr_val[1]){
                                $arr['in_sum'] = $count;
                            }
                            else if($igr_name_id == $arr_val[2]){
                                $arr['v_sum'] = $count;
                            }


                        }
                        array_push($arr_send, $arr);
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "3": // day
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `ingredientsName`.`ingredientsNameID` AS igr_name_id,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, GROUP_CONCAT(`action_log`.`ingredientsID`) AS ingr_name, sum(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND 1 GROUP BY `ingredientsName`.`ingredientsNameID`,year(`timestamp`), month(`timestamp`), day(`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $igr_name_id = $value['igr_name_id'];
                            $date = $value['Year']."-".$value['Month']."-".$value['Day'];
                            $count = $value['count'];
                            if($igr_name_id == $arr_val[0]){
                                $arr['cup'] = $count;
                                $arr['date'] = $date;
                            }
                            else if($igr_name_id == $arr_val[1]){
                                $arr['in_sum'] = $count;
                            }
                            else if($igr_name_id == $arr_val[2]){
                                $arr['v_sum'] = $count;
                            }


                        }
                        array_push($arr_send, $arr);
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "4": // month
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `ingredientsName`.`ingredientsNameID` AS igr_name_id,year(`timestamp`) AS Year, month(`timestamp`) AS Month,  GROUP_CONCAT(`action_log`.`ingredientsID`) AS ingr_name, sum(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND 1 GROUP BY `ingredientsName`.`ingredientsNameID`,year(`timestamp`), month(`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $igr_name_id = $value['igr_name_id'];
                            $date = $value['Year']."-".$value['Month'];
                            $count = $value['count'];
                            if($igr_name_id == $arr_val[0]){
                                $arr['cup'] = $count;
                                $arr['date'] = $date;
                            }
                            else if($igr_name_id == $arr_val[1]){
                                $arr['in_sum'] = $count;
                            }
                            else if($igr_name_id == $arr_val[2]){
                                $arr['v_sum'] = $count;
                            }


                        }
                        array_push($arr_send, $arr);
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
                break;
            case "5": // year
                for ($i=0;$i < count($device_id);$i++){
                    $device_id1 = $device_id[$i];
                    $data1 = $con->queryNoDML("SELECT `ingredientsName`.`ingredientsNameID` AS igr_name_id,year(`timestamp`) AS Year,  GROUP_CONCAT(`action_log`.`ingredientsID`) AS ingr_name, sum(`count`) AS count FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$device_id1' AND `ingredientsName`.`ingredientsNameID` IN ($arr_val[0],$arr_val[1],$arr_val[2]) AND 1 GROUP BY `ingredientsName`.`ingredientsNameID`,year(`timestamp`)");
                    if($data1){
                        $arr = array();
                        foreach ($data1 as $key => $value) {
                            $igr_name_id = $value['igr_name_id'];
                            $date = $value['Year'];
                            $count = $value['count'];
                            if($igr_name_id == $arr_val[0]){
                                $arr['cup'] = $count;
                                $arr['date'] = $date;
                            }
                            else if($igr_name_id == $arr_val[1]){
                                $arr['in_sum'] = $count;
                            }
                            else if($igr_name_id == $arr_val[2]){
                                $arr['v_sum'] = $count;
                            }


                        }
                        array_push($arr_send, $arr);
                    }
                    else{
                        return 9;
                    }
                }
                return $arr_send;
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
