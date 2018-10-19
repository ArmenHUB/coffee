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
             $result = addEditVmType($params->vm_type_id, $params->name);
             if ($result == 0) { // reset password ok
                 $answer = ["token" => $income_data->token, "user_id" => $income_data->user_id, "error" => 0, "lang_id" => $income_data->lang_id, "info" => $result];
             } else { // returned error number
                 $answer = ["token" => T_ERROR, "user_id" => 0, "error" => $result, "lang_id" => $income_data->lang_id, "info" => []];
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
 if ($answer['error'] > 0) {
     $answer['error'] = getError($answer['error'], $income_data->lang_id);
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
//function vmTypeInfo($vm_type_id)
//{
//    if (gettype($vm_type_id) != "integer") {
//        return 11;
//        die();
//    }
//    $con = new Z_MySQL();
//    if($vm_type_id == 0){
//        $data = $con->queryNoDML("SELECT `vm_types`.`vm_type_id` AS  vm_type_id, `vm_types`.`name` AS vn_name, `vm_types`.`button_count` AS button_count, `vm_types`.`image` AS image, `ingredients`.`ingredientsID` AS ingredients_id, `ingredientsName`.`text` AS ingredients_name, `ingredients`.`unitVending` AS unit_v, `ingredients`.`unitCollector` AS unit_col  FROM `vm_types` INNER JOIN `vm_type_ingredients` ON `vm_types`.`vm_type_id` = `vm_type_ingredients`.`vm_type_id` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `vm_type_ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID`");
//        if($data){
//            return $data;
//        }
//        else{
//            return 9;
//        }
//    }
//    else{
//        $data = $con->queryNoDML("SELECT `vm_types`.`vm_type_id` AS  vm_type_id, `vm_types`.`name` AS vn_name, `vm_types`.`button_count` AS button_count, `vm_types`.`image` AS image, `ingredients`.`ingredientsID` AS ingredients_id, `ingredientsName`.`text` AS ingredients_name, `ingredients`.`unitVending` AS unit_v, `ingredients`.`unitCollector` AS unit_col  FROM `vm_types` INNER JOIN `vm_type_ingredients` ON `vm_types`.`vm_type_id` = `vm_type_ingredients`.`vm_type_id` INNER JOIN `ingredients` ON `ingredients`.`ingredientsID` = `vm_type_ingredients`.`ingredientsID` INNER JOIN `ingredientsName` ON `ingredientsName`.`ingredientsNameID` = `ingredients`.`ingredientNameID` WHERE `vm_types`.`vm_type_id` = '$vm_type_id'");
//        if($data){
//            return $data;
//        }
//        else{
//            return 9;
//        }
//    }
//}

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
//function addEditVmType($vm_type_id, $name, $image, $button_count, $ingr_list)
//{
//    if (gettype($vm_type_id) != "integer") {
//        return 11;
//        die();
//    }
//    if ($name == "" || $image == "" || $button_count == "" || count($ingr_list) < 0) {
//        return 9;
//        die();
//    }
//
//   $con = new Z_MySQL();
//    if($vm_type_id == 0){
//         foreach ($ingr_list as $key => $value) {
//            $ingredient_name = $value[0];
//            $unitVending = $value[1];
//            $unitCollector = $value[2];
//            $data_ing = $con->queryNoDML("SELECT `ingredientsNameID` FROM `ingredientsName` WHERE `text`= '$ingredient_name'")[0];
//            $ingredient_name_id=$data_ing['ingredientsNameID'];
//            $con->queryDML("INSERT INTO `ingredients` (`ingredientsID`,`ingredientNameID`,`unitVending`,`unitCollector`) VALUES (NULL,'$ingredient_name_id','$unitVending','$unitCollector')");
//         }
//          $image_uploaded = file_upload($image);
//          $con->queryDML("INSERT INTO `vm_types` (`vm_type_id`,`name`,`button_count`,`image`) VALUES (NULL,'$name','$button_count','$image_uploaded')");
//          $data = $con->queryNoDML("SELECT `vm_type_id` FROM `vm_types` WHERE `name`= '$name' AND `button_count`='$button_count' AND `image`='$image_uploaded'")[0];
//           if($data['vm_type_id'] > 0){
//                foreach ($ingr_list as $key => $value) {
//                    $vm_type_id = $data['vm_type_id'];
//                    $ingredient_name = $value[0];
//                    $unitVending = $value[1];
//                    $unitCollector = $value[2];
//                    $data_ing = $con->queryNoDML("SELECT `ingredientsNameID` FROM `ingredientsName` WHERE `text`= '$ingredient_name'")[0];
//                    $ingredient_name_id=  $data_ing['ingredientsNameID'];
//                   $data1 = $con->queryNoDML("SELECT `ingredientsID` FROM `ingredients` WHERE `ingredientNameID`= '$ingredient_name_id' AND `unitVending`= '$unitVending' AND `unitCollector`='$unitCollector'")[0];
//                   if($data1['ingredientsID'] > 0){
//                        $ingredients_id = $data1['ingredientsID'];
//                       $con->queryDML("INSERT INTO `vm_type_ingredients` (`vm_type_id`,`ingredientsID`) VALUES ('$vm_type_id','$ingredients_id')");
//                   }
//                   else{
//                     return 9;
//
//                   }
//                }
//                  return 0;
//           }
//           else{
//             return 9;
//           }
//    }
//    else{
//        $data = $con->queryDML("UPDATE `vm_types` SET `name` = '$name',`button_count` = '$button_count',`image`='$image' WHERE `vm_type_id` = '$vm_type_id'");
//        if($data){
//             $data1 = $con->queryNoDML("SELECT `ingredientsID` FROM `vm_type_ingredients`  WHERE `vm_type_id` = '$vm_type_id'");
//             foreach ($data1 as $key1 => $value) {
//                $ingredient_id= $value['ingredientsID'];
//                foreach ($ingr_list as $key2 => $value) {
//                  if($key1 == $key2){
//                     $ingredient_name = $value[0];
//                     $unitVending = $value[1];
//                     $unitCollector = $value[2];
//                     $data_ing = $con->queryNoDML("SELECT `ingredientsNameID` FROM `ingredientsName` WHERE `text`= '$ingredient_name'")[0];
//                     $ingredient_name_id = $data_ing['ingredientsNameID'];
//                     $data = $con->queryDML("UPDATE `ingredients` SET `ingredientNameID` = '$ingredient_name_id',`unitVending` = '$unitVending',`unitCollector`='$unitCollector' WHERE `ingredientsID` = '$ingredient_id'");
//                  }
//                }
//             }
//            return 0;
//        }
//        else{
//            return 9;
//        }
//    }
//}

 // $ingr_list = array('0' => array("Sugar","70g","70kg"),'1'=> array("Cup","70g","80kg"),'2' => array("Coffee","70g","90kg"));
 // echo addEditVmType(0, 'VM-33', "vm33.jpeg", "33", $ingr_list);




function vmTypeInfo($vm_type_id){
    if (gettype($vm_type_id) != "integer") {
        return 11;
    }
    $con = new Z_MySQL();
    if($vm_type_id == 0){
        $data = $con->queryNoDML("SELECT `vm_type_id`,`name` FROM `vm_types`");
        if($data){
            return $data;
        }
        else{
            return 9;
        }
    }
    else{
        $data = $con->queryNoDML("SELECT `vm_type_id`,`name` FROM `vm_types` WHERE `vm_type_id` = '$vm_type_id'");
        if($data){
            return $data;
        }
        else{
            return 9;
        }
    }
}


function addEditVmType($vm_type_id, $name){
    if (gettype($vm_type_id) != "integer") {
        return 11;
    }
    if ($name == "") {
        return 9;
    }
    $con = new Z_MySQL();
    $arr = [CUP,MONEY_CASH_IN,VENDING_MONEY];
    if($vm_type_id == 0){
        $data = $con->queryDML("INSERT INTO `vm_types` VALUES (NULL,'$name','0','0')");
        $vm_type_id = $con->connection->insert_id;
        for ($i=0; $i < 3; $i++) {
            $con->queryDML("INSERT INTO `ingredients` (`ingredientsID`,`ingredientNameID`,`unitVending`,`unitCollector`) VALUES (NULL,'$arr[$i]','0','0')");
            $ingr_id = $con->connection->insert_id;
            $con->queryDML("INSERT INTO `vm_type_ingredients` (`vm_type_id`,`ingredientsID`) VALUES ('$vm_type_id',' $ingr_id')");
        }
        return 0;
    }
    else{
        $data=$con->queryDML("UPDATE `vm_types` SET `name`='{$name}' WHERE `vm_types`.`vm_type_id` = {$vm_type_id}");
        if($data){
            return 0;
        }
        else{
            return 9;
        }
    }
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
    $con = new Z_MySQL();
    $con->queryDML("DELETE FROM `vm_types` WHERE `vm_type_id` = '$vm_type_id'");
    $con->queryDML("DELETE FROM `ingredients` WHERE `vm_type_id` = '$vm_type_id'");
    return 0;
}

function file_upload($image){
    if($_FILES['file_upload']['error'] > 0){
       return 'An error ocurred when uploading.';
    }

    if(!getimagesize($_FILES['file_upload']['tmp_name'])){
      return 'Please ensure you are uploading an image.';
     }

        // Check filetype
   if($_FILES['file_upload']['type'] == 'image/png' || $_FILES['file_upload']['type'] == 'image/jpg' || $_FILES['file_upload']['type'] == 'image/jpeg' || $_FILES['file_upload']['type'] == 'image/gif'){
        // Check filesize
        if($_FILES['file_upload']['size'] > 5000000){
          return 'File uploaded exceeds maximum upload size.';
        }
       // Check if the file exists
       if(file_exists('image/' . $_FILES['file_upload']['name'])){
         return 'File with that name already exists.';
       }
       // Upload file
      if(!move_uploaded_file($_FILES['file_upload']['tmp_name'], 'images/' . $_FILES['file_upload']['name'])){
        return 'Error uploading file - check destination is writeable.';
      }
        return  $_FILES['file_upload']['name'];
        
   }
   else{
        return 'Unsupported filetype uploaded.';
   }
}
