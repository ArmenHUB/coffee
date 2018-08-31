<?php
require_once "z_mysql.php";
require_once "errors.php";
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

$answer = $income_data;
$params = $income_data->params;

switch ($params->command) {
    case "login":
        $username = $params->username;
        $password = $params->password;
        $host = $params->host;
        $answer = checkLogin($username, $password, $host);
        break;
     case "logout":
         $user_id = $params->user_id;
         $answer = logout($user_id);
        break;
     case "admin_device_tree":
         $answer = owners_list();
        break;
     case "Create company":
         $host = $params->host;
         $mail = $params->mail;
         $company_name = $params->company_name;
         $answer = add_owner($host,$mail,$company_name);
       break;   

}
if ($answer['error'] > 0) {
    $answer['error'] = getError($answer['error'], $income_data->lang_id);
}
echo json_encode($answer);

function checkLogin($username, $password, $host)
{
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `login` WHERE `username` = '{$username}' AND `password` = '{$password}' AND `host` = '{$host}'")[0];
    if($data["user_id"] > 0) {
        $user_id = $data["user_id"];
        $usertype = $data["userType"];
        $host = $data["host"];
        $token = createToken();
        $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
        if ($con->queryDML("INSERT INTO `loggedUsers`(`user_id`, `lastAction`, `token`) VALUES ({$user_id}, '$cur_time', '$token')")) {
            return ["token" => $token, "user_id" => $data["user_id"], "error" => 0, "info" => ["token" => $token, "user_id" => $data["user_id"], "host" => $host], "userType" => $usertype];

        }    
    }
    return ["token" => 0, "user_id" => 0, "error" => 2, "info" => []];
}

function logout($user_id){
    $con = new Z_MySQL();
     if($con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`user_id` = {$user_id}")){
         return ["token" => -1, "user_id" => 0, "error" => 0, "info" => ["token" => -1, "user_id" => 0]];
     }
}

function owners_list(){
   $con = new Z_MySQL();
   $data=$con->queryNoDML("SELECT username,user_id FROM `login` WHERE userType = 'owner'")[0];
   if($data["user_id"] > 0) {
       $token = createToken();
       $username = $data["username"];
       return ["user_id" => $data["user_id"], "token" => $token, "error" => "0","info" => ["token" => $token, "user_id" => $data["user_id"], "username" => $username]];
      
   }
   return ["token" => 0, "user_id" => 0, "error" => 2, "info" => []];
}

function add_owner($host,$mail,$company_name){
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `userValueID` FROM `userValue` WHERE `text` = '{$host}' OR `text` = '{$mail}' OR `text` = '{$company_name}'")[0];
    if($data["userValueID"] < 0) {
        $con->queryDML("INSERT INTO `userValue`(`text`, `langID`) VALUES ('$host', '1')");
        $con->queryDML("INSERT INTO `userValue`(`text`, `langID`) VALUES ('$mail', '1')");
        $con->queryDML("INSERT INTO `userValue`(`text`, `langID`) VALUES ('$company_name', '1')");
    }
    return ["token" => 0, "user_id" => 0, "error" => 2, "info" => []];
}
function createToken()
{
    $alphabet = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ1234567890';
    $pass = array();
    $alphaLength = strlen($alphabet) - 1;
    for ($i = 0; $i < 10; $i++) {
        $n = rand(0, $alphaLength);
        $pass[] = $alphabet[$n];
    }
    return implode($pass);
}
