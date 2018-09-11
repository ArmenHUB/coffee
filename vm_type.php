<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
require_once "be_mail.php";

//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);
$params = $income_data->params;
$is_logged_normaly = true;
/*$answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
if (checkUser($income_data->user_id, $income_data->token)) {
    $is_logged_normaly = true;
}*/
if ($is_logged_normaly || $params->command === "login") {
    switch ($params->command) {
        case "vm_type_list":
            $result = vmTypesList();
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "vm_type_info":
            $result = vmTypeInfo($params->vm_type_id);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "vm_type_add_edit":
            $result = addEditVmType($params->vm_type_id, $params->name, $params->image, $params->button_count, $params->ingr_list);
            if (gettype($result) == 'integer') { // return error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            } else {
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            }
            break;
        case "vm_type_remove":
            $result = removeVmType($params->vm_type_id);
            if ($result == 0) { // reset password ok
                $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
            } else { // returned error number
                $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
            }
            break;
    }
}
/*if ($answer['error'] > 0) {
    $answer['error'] = errorGet($answer['error'], $income_data->lang_id);
}*/
echo json_encode($answer);
/**
 * @return array
 */
function vmTypesList()
{
    $con = new Z_MySQL();
    $data = $con->queryNoDML("SELECT `vm_type_id`,`name` FROM `vm_types`");
    if($data){
        return $data;
    }
    else{
        return 9;
    }
    // return [
    //     ["vm_type_id" => "1", "name" => "T-521"],
    //     ["vm_type_id" => "2", "name" => "M-610"],
    //     ["vm_type_id" => "3", "name" => "D-100"]
    // ];
}

/**
 * @param $vm_type_id
 * @return array|int
 */
function vmTypeInfo($vm_type_id)
{
    if (gettype($vm_type_id) != "integer") {
        return 11;
    }
    return ["vm_type_id" => 1, "vm_name" => "T-521", "image" => "img/vm_type1.png", "button_count" => "6", "ingredients" => [
        ["ingr_id" => "1", "ingr_name" => "Sugar", "unit_v" => "g", "unit_col" => "kg"],
        ["ingr_id" => "2", "ingr_name" => "Coffee", "unit_v" => "g", "unit_col" => "kg"],
        ["ingr_id" => "3", "ingr_name" => "Cup", "unit_v" => "thing", "unit_col" => "thing"],
    ]];
}

/**
 * @param $vm_type_id
 * @param $name
 * @param $image
 * @param $button_count
 * @param $ingr_list
 * @return int
 */
/*function addEditVmType($vm_type_id, $name, $image, $button_count, $ingr_list)*/
function addEditVmType($vm_type_id, $name, $image, $button_count, $ingr_list)
{
    if (gettype($vm_type_id) != "integer") {
        return 11;
    }
/*    if ($name == "" || $image == "" || $button_count == "" || count($ingr_list) > 0) {
        return 9;
    }*/
    return 0;
}

/**
 * @param $vm_type_id
 * @return int
 */
function removeVmType($vm_type_id)
{
    if (gettype($vm_type_id) != "integer") {
        return 11;
    }
    return 0;
}
