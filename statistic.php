<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
require_once "be_mail.php";

//get send data //

// $all_data = file_get_contents('php://input');
// $income_data = json_decode($all_data);
// $params = $income_data->params;
// $is_logged_normaly = false;
// $answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
// if (checkUser($income_data->user_id, $income_data->token)) {
//     $is_logged_normaly = true;
// }
// if ($is_logged_normaly) {
//     switch ($params->command) {
//         case "ingridient":
//             $result = getIngridientTable($params->device_id, $params->scale, $params->date_rage);
//             if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//             break;
//         case "enchashement":
//             $result = getEnchashementTable($params->device_id, $params->scale, $params->date_rage);
//             if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//             break;
//         case "vending":
//             $result = getVendingTable($params->device_id, $params->scale, $params->date_rage);
//             if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $result["token"], "user_id" => $result["token"], "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//             break;
//     }
// }
// if ($answer['error'] > 0) {
//    $answer['error'] = errorGet($answer['error'], $income_data->lang_id);
// }
// echo json_encode($answer);

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

function getEnchashementTable($device_id, $scale, $date_rage)
{
   if (gettype($device_id) != "integer") {
        return 10;
        die();
    }
   if ($scale == "" || $date_rage == "") {
       return 9;
       die();
   }
   $con = new Z_MySQL();
      $datetime = explode(" - ",$date_rage);
      $datetime_1 = $datetime[0];
      $datetime_2 = $datetime[1];
    switch ($scale) {
        case 0: // all
           $data = $con->queryNoDML("SELECT `action_log`.`timestamp` AS 'Date',`action_log`.`ingredientsID`,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute, `ingredientsName`.`text` AS ingr_name, sum(`count`) AS CashOut FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$deviceID'  GROUP BY  `timestamp`,`ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`), minute(`timestamp`)");
           return $data;   
        break;
        case 1: // hour
           $data = $con->queryNoDML("SELECT `action_log`.`timestamp` AS 'Date',`action_log`.`ingredientsID`,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour,`ingredientsName`.`text` AS ingr_name, sum(`count`) AS CashOut FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$deviceID'  GROUP BY  `timestamp`,`ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`)");
           return $data;            
        break;
        case 2: // day
           $data = $con->queryNoDML("SELECT `action_log`.`timestamp` AS 'Date',`action_log`.`ingredientsID`,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day,`ingredientsName`.`text` AS ingr_name, sum(`count`) AS CashOut FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$deviceID'  GROUP BY  `timestamp`,`ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`)");
           return $data;            
        break;
        case 3: // month
           $data = $con->queryNoDML("SELECT `action_log`.`timestamp` AS 'Date',`action_log`.`ingredientsID`,year(`timestamp`) AS Year, month(`timestamp`) AS Month,`ingredientsName`.`text` AS ingr_name, sum(`count`) AS CashOut FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '$datetime_1' AND '$datetime_2' AND `action_log`.`deviceID` = '$deviceID'  GROUP BY  `timestamp`,`ingredientsID`, year(`timestamp`), month(`timestamp`)");
           return $data; 
        break; 
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


function getVendingTable($device_id, $scale, $date_rage)
{
   if (gettype($device_id) != "integer") {
       return 10;
       die();
   }
   if ($scale == "" || $date_rage == "") {
       return 9;
       die();
   }
   return [
       "headers" => ["Datetime", "Cup", "V Sum", "In Sum"],
       "body" => [
           ["date" => "19.05.18, 14:20:54", "cup" => "3", "v_sum" => "3000", "in_sum" => "1500"],
           ["date" => "01.03.18, 11:20:54", "cup" =>  "1", "v_sum" =>  "1500", "in_sum" => "2500"],
           ["date" => "13.07.18, 14:20:54", "cup" =>  "3", "v_sum" =>  "3000", "in_sum" => "1500"]
       ]
   ];
}

// SELECT  `action_log`.`timestamp` AS Date,`action_log`.`ingredientsID`,year(`timestamp`) AS Year, month(`timestamp`) AS Month, day(`timestamp`) AS Day, hour(`timestamp`) AS Hour, minute(`timestamp`) AS Minute, `ingredientsName`.`text` AS ingr_name, sum(`count`) AS CashOut FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `timestamp` BETWEEN '2018-10-12 12:09:11' AND '2018-10-13 11:11:11' AND `action_log`.`deviceID` = '1'  GROUP BY  `timestamp`,`ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`), hour(`timestamp`), minute(`timestamp`)



// SELECT * FROM `action_log`
// WHERE timestamp BETWEEN '2018-10-12 12:11:11' AND '2018-10-13 11:20:11';

// INSERT INTO `action_log`
// VALUES ('1', '11', '3000','6','2018-11-22 11:33:11');

// SELECT SUM(`ingredientsID`)
// FROM `action_log`

// UPDATE `action_log`
// SET count = '3000'
// WHERE `ingredientsID` = 2;

// SELECT `action_log`.`timestamp` AS 'date', `ingredientsName`.`text` AS ingr_name,`action_log`.`measurement_unitsID` AS measurement_unitsID,`action_log`.`count`  AS cash_out FROM `action_log` INNER JOIN `ingredients` ON `action_log`.`ingredientsID` = `ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE timestamp BETWEEN '2018-10-12 12:09:11' AND '2018-10-13 11:11:11'

// SELECT `ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`), sum(`count`) FROM `action_log` GROUP BY `ingredientsID`, year(`timestamp`), month(`timestamp`), day(`timestamp`)
