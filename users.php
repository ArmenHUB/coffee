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
if ($is_logged_normaly || $params->command === "login") {
    switch ($params->command) {
        case "login":
            $result = login($params->username, md5($params->password), $params->host);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else { // return correct answer - array
                $answer = ["token" => $result["token"], "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "logout":
            $result = logout($income_data->user_id);
            if ($result == 0) { // correctly logout
                $answer = ["token" => T_LOGOUT, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => []];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => $income_data->user_id, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
        case "user_list":
            $result = userList($params->user_type_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "user_add_edit":
            $result = addEditUser($params->user_id, $params->username, $params->name,$params->password, $params->host, $params->user_type_id, $params->mail);
            if($result == 0){ // correctly added or edited
                $answer = ["token" => $income_data->token, "user_id" => $params->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }else{ // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
        case "user_remove":
            $result = removeUser($income_data->user_id);
            if ($result == 0) { // correctly removed
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
        case "check_password":
            $result = checkPassword($income_data->user_id, md5($params->password));
            if ($result == 0) { // correct password
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
        case "reset_password":
            $result = resetPassword($params->mail);
            if ($result == 0) { // reset password ok
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
        case "change_password":
            $result = changePassword($income_data->user_id, md5($params->password));
            if ($result == 0) { // reset password ok
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
        case "get_menu":
            $result = getMenu($params->user_type_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "user_info":
            $result = userInfo($params->user_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "collector_list":
            $result = collectorList($params->user_type_id, $income_data->host);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $result["token"], "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
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
 * @return int
 */
function logout($user_id)
{
    $con = new Z_MySQL();
    if ($con->queryDML("DELETE FROM `loggedUsers` WHERE `loggedUsers`.`userID` = {$user_id}")) {
        return 0;
    }
    return 7;
}

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
 * @brief create session key
 * @return string -  random generated string
 */
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

/**
 * @param $username
 * @param $password
 * @param $host
 * @return array|int
 */
function login($username, $password, $host)
{
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE `username` = '{$username}' AND `password` = '{$password}' AND `host` = '{$host}'")[0];
    if ((int)$data["userID"] > 0) {
        $user_id = (int)$data["userID"];
        $usertype = (int)$data["userTypeID"];
        $token = createToken();
        $cur_time = $con->queryNoDML("SELECT CURRENT_TIMESTAMP() AS 'time'")[0]["time"];
        if ($con->queryDML("INSERT INTO `loggedUsers`(`userID`, `lastAction`, `token`) VALUES ('{$user_id}', '$cur_time', '$token')")) {
            return ["token" => $token, "user_id" => $user_id, "user_type_id" => $usertype];
        } else {
            return 5;
        }
    }
    return 2;

}

/**
 * @param $user_type_id
 * @return array|int
 */
function userList($user_type_id)// GET usertypeid, RETURN array userList
{
    if ($user_type_id < 1 && $user_type_id > 3) {
        return 8;
    } else {
        $con = new Z_MySQL();
        if ($user_type_id == 0) {
            $data = $con->queryNoDML("SELECT `userID`,`name` FROM `users`");
            if ($data) {
                return $data;
            } else {
                return 4;
            }
        } else {
            $data = $con->queryNoDML("SELECT `userID`,`name` FROM `users` WHERE `userTypeID` = '{$user_type_id}'");
            if ($data) {
                return $data;
            } else {
                return 4;
            }
        }
    }
}

/**
 * @param $user_id
 * @param $username
 * @param $name
 * @param $password
 * @param $host
 * @param $user_type_id
 * @param $mail
 * @return int
 */
function addEditUser($user_id, $username, $name, $password, $host, $user_type_id, $mail)
{
    if (gettype($user_id) != "integer") {
        return 7;
    }
    if ($username == "" || $name == "" ||  $host == "" || $mail == "") {//@TODO CHECK SAME USERNAME  //@todo check host
        return 9;
    }
    if ($user_type_id < 1 && $user_type_id > 3) {
        return 8;
    }
    $con = new Z_MySQL();
    if ($user_id == 0) {
        $data1 = $con->queryNoDML("SELECT `userID` FROM `users` WHERE  `username` = '$username' OR `host` = '$host' OR `email` = '$mail'")[0];
        if ($data1['userID'] > 0) {
            return 4;
        } else {
            $password_random = createToken();
            $data = $con->queryDML("INSERT INTO `users` (`username`,`password`,`host`,`userTypeID`,`email`,`name`) VALUES ('$username','$password_random','$host','$user_type_id','$mail','$name')");
            if ($data) {
                return 0;
            } else {
                return 4;
            }
        }
    } else {
        $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id'");
        if ($data) {
            $con->queryDML("UPDATE `users` SET  `username` = '{$username}', `password` = '{$password}', `host` = '{$host}', `userTypeID` = '{$user_type_id}', `email` = '{$mail}', `name`='{$name}' WHERE  `userID` = '{$user_id}'");
            return 0;
        } else {
            return 4;
        }
    }
}

/**
 * @param $user_id
 * @param $password
 * @return int
 */
function checkPassword($user_id, $password)
{
    if (gettype($user_id) != "integer") {
        return 7;
        die();
    }
    if ($password == "") {
        return 9;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id' AND `password`='$password'");
    if ($data) {
        return 0;
    } else {
        return 7;
    }
}

/**
 * @param $user_id
 * @param $password
 * @return int
 */
function changePassword($user_id, $password)
{
    if (gettype($user_id) != "integer") {
        return 7;
        die();
    }
    if ($password == "") {
        return 9;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id'");
    if ($data) {
        $con->queryDML("UPDATE `users` SET `password`='{$password}' WHERE `users`.`userID` = '{$user_id}'");
        return 0;
    } else {
        return 7;
    }
}

/**
 * @param $user_id
 * @return int
 */
function removeUser($user_id)
{
    if (gettype($user_id) != "integer") {
        return 7;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT * FROM `users` WHERE  `userID`= '$user_id'");
    if ($data) {
        $con->queryDML("DELETE FROM users WHERE `userID`= '$user_id'");
        return 0;
    } else {
        return 7;
    }
}

/**
 * @param $mail
 * @return int
 */
function resetPassword($mail)
{
    if ($mail == "") {
        return 9;
    }

}

/**
 * @param $user_type_id
 * @return array|int
 */
function getMenu($user_type_id)
{
    if ($user_type_id < 1 && $user_type_id > 3) {
        return 8;
        die();
    }
    $con = new Z_MySQL();
    if ($user_type_id == "1") {
        $data = $con->queryNoDML("SELECT * FROM `menu` WHERE  `userTypeID`= '1'");
        if ($data) {
            return $data;
        } else {
            return 8;
        }
    } else if ($user_type_id == "2") {
        $data = $con->queryNoDML("SELECT * FROM `menu` WHERE  `userTypeID`= '2'");
        if ($data) {
            return $data;
        } else {
            return 8;
        }
    }

}

/**
 * @param $user_id
 * @return array|int
 */
function userInfo($user_id)
{
    if (gettype($user_id) != "integer") {
        return 7;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `name`,`host`,`email`,`username`  FROM `users` WHERE  `userID`= '$user_id'")[0];
    if ($data) {
        return $data;
    } else {
        return 7;
    }
}

function collectorList($user_type_id, $host)
{
    if ($user_type_id < 1 && $user_type_id > 3) {
        return 8;
        die();
    }
    if ($host == "") {//@TODO CHECK SAME USERNAME  //@todo check host
        return 9;
        die();
    }
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `userID`,`name` FROM `users` WHERE  `userTypeID`= '$user_type_id' AND `host` = '$host'");
    if ($data) {
        return $data;
    } else {
        return 8;
    }
}
