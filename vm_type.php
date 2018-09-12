<?php
require_once "z_config.php";
require_once "z_mysql.php";
require_once "errors.php";
require_once "be_mail.php";

//get send data //
// $all_data = file_get_contents('php://input');
// $income_data = json_decode($all_data);
// $params = $income_data->params;
// $is_logged_normaly = true;
/*$answer = ["token" => T_LOGOUT, "user_id" => 0, "error" => 3, "lang_id" => $income_data->lang_id, "info" => []];
if (checkUser($income_data->user_id, $income_data->token)) {
    $is_logged_normaly = true;
}*/
// if ($is_logged_normaly || $params->command === "login") {
//     switch ($params->command) {
//         case "vm_type_list":
//             $result = vmTypesList();
//             if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//             break;
//         case "vm_type_info":
//             $result = vmTypeInfo($params->vm_type_id);
//             if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//             break;
//         case "vm_type_add_edit":
//             $result = addEditVmType($params->vm_type_id, $params->name, $params->image, $params->button_count, $params->ingr_list);
//             if (gettype($result) == 'integer') { // return error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             } else {
//                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             }
//             break;
//         case "vm_type_remove":
//             $result = removeVmType($params->vm_type_id);
//             if ($result == 0) { // reset password ok
//                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
//             } else { // returned error number
//                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
//             }
//             break;
//     }
// }
// if ($answer['error'] > 0) {
//     $answer['error'] = errorGet($answer['error'], $income_data->lang_id);
// }
// echo json_encode($answer);
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
        die();
    }
    $con = new Z_MySQL();
    if($vm_type_id == 0){
        $data = $con->queryNoDML("SELECT `vm_types`.`vm_type_id` AS  vm_type_id, `vm_types`.`name` AS vn_name, `vm_types`.`button_count` AS button_count, `vm_types`.`image` AS image, `ingredients`.`ingredientsID` AS ingredients_id, `ingredientsName`.`text` AS ingredients_name, `ingredients`.`unitVending` AS unit_v, `ingredients`.`unitCollector` AS unit_col  FROM `vm_types` INNER JOIN `vm_type_ingredients` ON `vm_types`.`vm_type_id` = `vm_type_ingredients`.`vm_type_id` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `vm_type_ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID`");
        if($data){
            return $data;
        }
        else{
            return 9;
        }
    }
    else{
        $data = $con->queryNoDML("SELECT `vm_types`.`vm_type_id` AS  vm_type_id, `vm_types`.`name` AS vn_name, `vm_types`.`button_count` AS button_count, `vm_types`.`image` AS image, `ingredients`.`ingredientsID` AS ingredients_id, `ingredientsName`.`text` AS ingredients_name, `ingredients`.`unitVending` AS unit_v, `ingredients`.`unitCollector` AS unit_col  FROM `vm_types` INNER JOIN `vm_type_ingredients` ON `vm_types`.`vm_type_id` = `vm_type_ingredients`.`vm_type_id` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `vm_type_ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `vm_types`.`vm_type_id` = '$vm_type_id'");
        if($data){
            return $data;
        }
        else{
            return 9;
        }        
    }
    
    // return ["vm_type_id" => 1, "vm_name" => "T-521", "image" => "img/vm_type1.png", "button_count" => "6", "ingredients" => [
    //     ["ingr_id" => "1", "ingr_name" => "Sugar", "unit_v" => "g", "unit_col" => "kg"],
    //     ["ingr_id" => "2", "ingr_name" => "Coffee", "unit_v" => "g", "unit_col" => "kg"],
    //     ["ingr_id" => "3", "ingr_name" => "Cup", "unit_v" => "thing", "unit_col" => "thing"],
    // ]];
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
//$ingr_list = array('0' => array("Cofee","10g","20kg"),'1'=> array("Sugar","12g","21kg"));
function addEditVmType($vm_type_id, $name, $image, $button_count, $ingr_list)
{
    if (gettype($vm_type_id) != "integer") {
        return 11;
        die();
    }
    if ($name == "" || $image == "" || $button_count == "" || count($ingr_list) < 0) {
        return 9;
        die();
    }
   $con = new Z_MySQL();
    if($vm_type_id == 0){
         foreach ($ingr_list as $key => $value) {
            $ingredient_name = $value[0];
            $unitVending = $value[1];
            $unitCollector = $value[2];
            $data_ing = $con->queryNoDML("SELECT `ingredientsNameID` FROM `ingredientsName` WHERE `text`= '$ingredient_name'")[0];
            $ingredient_name_id=  $data_ing['ingredientsNameID'];
            $con->queryDML("INSERT INTO `ingredients` (`ingredientsID`,`ingredientNameID`,`unitVending`,`unitCollector`) VALUES (NULL,'$ingredient_name_id','$unitVending','$unitCollector')"); 

         }
          $con->queryDML("INSERT INTO `vm_types` (`vm_type_id`,`name`,`button_count`,`image`) VALUES (NULL,'$name','$button_count','$image')");
          $data = $con->queryNoDML("SELECT `vm_type_id` FROM `vm_types` WHERE `name`= '$name' AND `button_count`='$button_count' AND `image`='$image'")[0];
           if($data['vm_type_id'] > 0){
                foreach ($ingr_list as $key => $value) {
                    $vm_type_id = $data['vm_type_id'];
                    $ingredient_name = $value[0];
                    $unitVending = $value[1];
                    $unitCollector = $value[2];
                    $data_ing = $con->queryNoDML("SELECT `ingredientsNameID` FROM `ingredientsName` WHERE `text`= '$ingredient_name'")[0];
                    $ingredient_name_id=  $data_ing['ingredientsNameID'];
                   $data1 = $con->queryNoDML("SELECT `ingredientsID` FROM `ingredients` WHERE `ingredientNameID`= '$ingredient_name_id' AND `unitVending`= '$unitVending' AND `unitCollector`='$unitCollector'")[0];
                   if($data1['ingredientsID'] > 0){
                        $ingredients_id = $data1['ingredientsID'];
                       $con->queryDML("INSERT INTO `vm_type_ingredients` (`vm_type_id`,`ingredientsID`) VALUES ('$vm_type_id','$ingredients_id')");                       
                   }
                   else{
                     return 9;

                   }                   
                }
                   return 0;
           }
           else{
             return 9;
           }
    }
    else{

        // $data = $con->queryDML("UPDATE `vm_types` SET `name` = '$name',`button_count` = '$button_count',`image`='$image' WHERE `vm_type_id` = '$vm_type_id'");
        // if($data){
        //      $data1 = $con->queryNoDML("SELECT `ingredientsID` FROM `vm_type_ingredients`  WHERE `vm_type_id` = '$vm_type_id'");
        //      foreach ($data1 as $key => $value) {
        //         $ingredient_id= $value['ingredientsID'];
        //        foreach ($ingr_list as $key => $value) {
        //             $ingredient_name = $value[0];
        //             $unitVending = $value[1];
        //             $unitCollector = $value[2];
        //             $data_ing = $con->queryNoDML("SELECT `ingredientsNameID` FROM `ingredientsName` WHERE `text`= '$ingredient_name'")[0];
        //             $ingredient_name_id=  $data_ing['ingredientsNameID'];                    
        //         }

                 
        //      }
        // }
        // else{
        //     return 9;
        // }    
    }   
}
//$ingr_list = array('0' => array("Coffee","300g","100kg"),'1'=> array("Sugar","320g","120kg"),'2' => array("Cup","330g","130kg"));


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
