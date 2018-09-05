<?php

require_once "z_mysql.php";
require_once "errors.php";
require_once "be_mail.php";

//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

$answer = $income_data;
$params = $income_data->params;

switch ($params->command) {
    case "login":
        $username = $params->username;
        $password = md5($params->password);
        $host = $params->host;
        $answer = check_user_password($username, $password, $host);
        break;
    case "reset_password":
        $user_id = $income_data->user_id;
        $email = $params->email;
        $answer = reset_password($user_id,$email);
        break;    
     case "logout":
         $user_id = $income_data->user_id;
         $token = $income_data->token;
         $answer = logout($user_id);
        break;
     case "get_user_list":
         $userTypeID = $params->user_type_id;
         $answer = get_user_list($userTypeID);
        break;
     case "check_user":
        if (check_login_timeout($income_data->user_id, $income_data->token)) {
            $answer = ["token" => $params->token, "user_id" => $params->user_id, "error" => 0, "info" => ["token" => $params->token, "user_id" => $params->user_id, "host" => $params->host]];
        } else {
            $answer = ["token" => -1, "user_id" => 0, "error" => 0, "info" => ["token" => -1, "user_id" => 0, "host" => $params->host]];
        }
        break;   
    case "user_add_edit":
        $user_id = $income_data->user_id;
        $userTypeID = $params->user_type_id;
        $username = $params->username;
        $password = md5($params->password);
        $host = $income_data->host;
        $mail = $params->mail;
        $answer = add_edit_user($user_id,$username, $password, $host,$userTypeID,$mail);
        break;
    case "check_password":
        $user_id = $income_data->user_id;
        $password = md5($params->password);
        $answer = check_password($user_id,$password);
        break;
    case "change_password":
        $user_id = $income_data->user_id;
        $new_password = md5($params->password);
        $answer = change_password($user_id,$new_password);
        break; 
    case "remove_user":
        $user_id = $income_data->user_id;
        $answer = remove_user($user_id);
       break;        
}
if ($answer['error'] > 0) {
    $answer['error'] = getError($answer['error'], $income_data->lang_id);
}
echo json_encode($answer);

function check_user_password($username, $password, $host)
{
   $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE `username` = '{$username}' AND `password` = '{$password}' AND `host` = '{$host}'")[0];
    if((int)$data["userID"] > 0) {
        $user_id = (int)$data["userID"];
        $usertype = (int)$data["userTypeID"];
        $host = $data["host"];
        $token = createToken();
        $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
        if ($con->queryDML("INSERT INTO `loggedUsers`(`userID`, `lastAction`, `token`) VALUES ('{$user_id}', '$cur_time', '$token')")) {
            return ["token" => $token, "user_id" =>  $user_id, "error" => 0, "lang_id"=>$income_data->lang_id, "info" => ["token" => $token, "user_id" => $user_id, "userType" => $usertype]];
        }else{          
            return ["token" => $token, "user_id" =>  $user_id, "error" => 5, "lang_id"=>$income_data->lang_id, "info" => ["token" => $token, "user_id" =>  $user_id, "userType" => $usertype]];
        }
    }
     return ["token" => "0", "user_id" => 0, "error" => 2,  "lang_id"=>$income_data->lang_id, "info" => []];

}
function logout($user_id){
    $con = new Z_MySQL();
     if($con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`user_id` = {$user_id}")){
         return ["token" => -1, "user_id" => 0, "error" => 0,  "lang_id"=>$income_data->lang_id, "info" => []];
     }
     return ["token" => 0, "user_id" => 0, "error" => 7,  "lang_id"=>$income_data->lang_id, "info" => []];
}
function get_user_list($userTypeID){
   $con = new Z_MySQL();
   if($userTypeID == 0){
      $data=$con->queryNoDML("SELECT userID,username,host FROM `users`");
        if($data) {
          return ["user_id" => $user_id, "token" => $token, "error" => "0",  "lang_id"=>$income_data->lang_id, "info" => $data];
        }
         return ["token" => 0, "user_id" => 0, "error" => 7,  "lang_id"=>$income_data->lang_id, "info" => []];    
   }
   else{
     $data=$con->queryNoDML("SELECT userID,username,host FROM `users` WHERE `userTypeID` = '{$userTypeID}'")[0];
     if($data) {
       $user_id =  $data["userID"];
       $username = $data["username"];
       $host = $data["host"];
       return ["user_id" => $user_id, "token" => $token, "error" => "0",  "lang_id"=>$income_data->lang_id, "info" => ["host" => $host, "user_id" => $user_id, "username" => $username]];
     }
   return ["token" => 0, "user_id" => 0, "error" => 2,  "lang_id"=>$income_data->lang_id, "info" => []];
   }

}

function add_edit_user($user_id,$username, $password, $host,$userTypeID,$mail){
    $con = new Z_MySQL();  
        if($user_id == 0){
             $data1 = $con->queryNoDML("SELECT `userID` FROM `users` WHERE  `username` = '$username' OR `host` = '$host' OR `email` = '$mail'")[0];
           if($data1['userID'] > 0){
              return ["token" => 0, "user_id" => 0, "error" => 2,  "lang_id"=>$income_data->lang_id, "info" => []];
           }
           else{              
               $data=$con->queryDML("INSERT INTO `users` (`username`,`password`,`host`,`userTypeID`,`email`) VALUES ('$username','$password','$host','$userTypeID','$mail')");
               if($data){
                 return ["token" => 0, "user_id" => 0, "error" => 0,  "lang_id"=>$income_data->lang_id, "info" => []];
               }
               else{
                 return ["token" => 0, "user_id" => 0, "error" => 4,  "lang_id"=>$income_data->lang_id, "info" => []];
               }
           } 
       }
       else{
          $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id'"); 
         if($data){
           $con->queryDML("UPDATE `users` SET  `username` = '{$username}', `password` = '{$password}', `host` = '{$host}', `userTypeID` = '{$userTypeID}', `email` = '{$mail}' WHERE  `userID` = '{$user_id}'");
           return ["token" => 0, "user_id" => 0, "error" => 0,  "lang_id"=>$income_data->lang_id, "info" => []];
         }
         else{
          return ["token" => 0, "user_id" => 0, "error" => 7,  "lang_id"=>$income_data->lang_id, "info" => []];
         }
       }
} 
function check_login_timeout($user_id,$token){
    $con = new Z_MySQL();
    $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
    $answer = $con->queryNoDML("SELECT `loggedUsers`.`lastAction` AS 'lastAction' FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id} AND `loggedUsers`.`token` = '{$token}'")[0]["lastAction"];
    $cur_date = new DateTime($cur_time);
    $last_date = new DateTime($answer);
    if ($answer != "") {
        if ($last_date->getTimestamp() + LOG_OFF_DELAY > $cur_date->getTimestamp() || LOG_OFF_DELAY === 0) {
            $con->queryDML("UPDATE `loggedUsers` SET `lastAction`='{$cur_time}' WHERE `loggedUsers`.`userID` = {$user_id}");
            return true;
        }
        else{
            $con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id}");
        }
        
    }
    return false;
}
function check_password($user_id,$password){
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id' AND `password`='$password'");
    if($data){
      return ["token" => 0, "user_id" => 0, "error" => 0,  "lang_id"=>$income_data->lang_id, "info" => []];
    }
    else{
      return ["token" => 0, "user_id" => 0, "error" => 2,  "lang_id"=>$income_data->lang_id, "info" => []];
    }
}
function change_password($user_id,$new_password){
   $con = new Z_MySQL();
   $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id'");
   if($data){
      $con->queryDML("UPDATE `users` SET `password`='{$new_password}' WHERE `users`.`userID` = '{$user_id}'");
      return ["token" => 0, "user_id" => 0, "error" => 0,  "lang_id"=>$income_data->lang_id, "info" => []];
   }
   else{
      return ["token" => 0, "user_id" => 0, "error" => 7,  "lang_id"=>$income_data->lang_id,     "info" => []];
   }  
}
function remove_user($user_id){
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id'");
   if($data){
      $con->queryDML("DELETE FROM users WHERE `userID`= '$user_id'");
      return ["token" => 0, "user_id" => 0, "error" => 0,  "lang_id"=>$income_data->lang_id, "info" => []];
   }
   else{
      return ["token" => 0, "user_id" => 0, "error" => 7,  "lang_id"=>$income_data->lang_id, "info" => []];
   }
}
function reset_password($user_id,$email){
  
   
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
