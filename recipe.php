<?php

require_once "z_mysql.php";
require_once "errors.php";
//get send data //
$all_data = file_get_contents('php://input');
$income_data = json_decode($all_data);

$answer = $income_data;
$params = $income_data->params;

switch ($params->command) {
    case "recipes_list":
        $info = recipes_list();
        $answer = ["user_id" => $income_data->user_id, "token" => "0", "error" => "0", "info" => $info];
        break;
}
if ($answer['error'] > 0) {
    $answer['error'] = getError($answer['error'], $income_data->lang_id);
}
echo json_encode($answer);

function recipes_list(){
	$con = new Z_MySQL();
	$data = $con->queryNoDML("SELECT text FROM recipeNames");
	return $data;
}
