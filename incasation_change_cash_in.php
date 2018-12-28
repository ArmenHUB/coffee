SELECT SUM(case when `type` IN ('cash','vending') AND `action_log`.`ingredientsID` = '3' THEN `count` END) cup,
SUM(case when `type` IN ('cash','incasation') AND `action_log`.`ingredientsID` IN ('5','6') THEN `count` END) cash_in,
SUM(case when `type` = 'vending' AND `action_log`.`ingredientsID` = '7' THEN `count` END) vending_money FROM `action_log`
WHERE `deviceID` = '5'
