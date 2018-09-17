-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 17 2018 г., 17:43
-- Версия сервера: 5.7.20
-- Версия PHP: 7.2.0

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `Coffee`
--

-- --------------------------------------------------------

--
-- Структура таблицы `action_log`
--

CREATE TABLE `action_log` (
  `deviceID` int(11) NOT NULL,
  `ingredientsID` int(11) NOT NULL,
  `count` text NOT NULL,
  `measurement_unitsID` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `action_log`
--

INSERT INTO `action_log` (`deviceID`, `ingredientsID`, `count`, `measurement_unitsID`, `timestamp`) VALUES
(1, 1, '0', 1, '2018-10-12 08:10:11'),
(1, 2, '0', 5, '2018-10-12 10:12:11');

-- --------------------------------------------------------

--
-- Структура таблицы `boardDevice`
--

CREATE TABLE `boardDevice` (
  `deviceID` int(11) NOT NULL,
  `boardID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `boardDevice`
--

INSERT INTO `boardDevice` (`deviceID`, `boardID`) VALUES
(1, 1),
(2, 3),
(3, 4);

-- --------------------------------------------------------

--
-- Структура таблицы `boards`
--

CREATE TABLE `boards` (
  `boardID` int(12) NOT NULL,
  `UID` varchar(25) NOT NULL,
  `serialNumber` varchar(25) NOT NULL,
  `lastActivity` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `boards`
--

INSERT INTO `boards` (`boardID`, `UID`, `serialNumber`, `lastActivity`) VALUES
(3, '991901212499190121111256', '0879858196537515', '2018-09-11 08:44:40'),
(4, '365687345367576', '2435464546757515', '2018-09-11 08:44:40');

-- --------------------------------------------------------

--
-- Структура таблицы `deviceInfo`
--

CREATE TABLE `deviceInfo` (
  `deviceID` int(11) NOT NULL,
  `deviceParamNameID` int(11) NOT NULL,
  `deviceParamValueID` int(11) NOT NULL,
  `deviceTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceInfo`
--

INSERT INTO `deviceInfo` (`deviceID`, `deviceParamNameID`, `deviceParamValueID`, `deviceTypeID`) VALUES
(1, 1, 1, 2),
(1, 3, 3, 2),
(1, 4, 4, 2),
(1, 5, 5, 2),
(1, 7, 7, 2),
(2, 7, 8, 2),
(3, 7, 9, 2),
(3, 3, 10, 2),
(1, 8, 11, 2),
(3, 4, 13, 2),
(3, 8, 12, 2),
(3, 5, 14, 2),
(1, 9, 15, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `deviceParamNames`
--

CREATE TABLE `deviceParamNames` (
  `deviceParamNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceParamNames`
--

INSERT INTO `deviceParamNames` (`deviceParamNameID`, `langID`, `text`) VALUES
(1, 1, 'location'),
(3, 1, 'name'),
(4, 1, 'address'),
(5, 1, 'status'),
(7, 1, 'expiration Date'),
(8, 1, 'sum'),
(9, 1, 'map_icon');

-- --------------------------------------------------------

--
-- Структура таблицы `deviceParamValues`
--

CREATE TABLE `deviceParamValues` (
  `deviceParamValueID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceParamValues`
--

INSERT INTO `deviceParamValues` (`deviceParamValueID`, `text`) VALUES
(1, '56.190333'),
(3, 'Apple3434'),
(4, 'A.Sargsyan 20/1'),
(5, '0'),
(7, '1999-07-14 09:00:00'),
(8, '2021-05-17 09:00:00'),
(9, '1999-07-14 09:00:00'),
(10, 'Samsung'),
(11, '4500'),
(12, '6500'),
(13, 'Ara ter Sargsyan 20/1'),
(14, '1'),
(15, 'images/loacation_icon_active.png'),
(16, 'images/loacation_icon_warning.png'),
(17, 'images/loacation_icon_error.png'),
(18, '2');

-- --------------------------------------------------------

--
-- Структура таблицы `deviceTypes`
--

CREATE TABLE `deviceTypes` (
  `deviceTypeID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceTypes`
--

INSERT INTO `deviceTypes` (`deviceTypeID`, `langID`, `text`, `image`) VALUES
(2, 1, 'iphone', '1'),
(5, 1, 'S8+', '1');

-- --------------------------------------------------------

--
-- Структура таблицы `deviceUsers`
--

CREATE TABLE `deviceUsers` (
  `deviceID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `deviceTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceUsers`
--

INSERT INTO `deviceUsers` (`deviceID`, `userID`, `deviceTypeID`) VALUES
(1, 1, 2),
(2, 3, 2),
(3, 1, 2);

-- --------------------------------------------------------

--
-- Структура таблицы `errors`
--

CREATE TABLE `errors` (
  `errorID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `errors`
--

INSERT INTO `errors` (`errorID`, `text`, `langID`) VALUES
(0, 'Unknown error', 1),
(0, 'Неизвестная ошибка', 2),
(0, 'Անհայտ սխալ', 3),
(1, 'Invalid characters used', 1),
(1, 'использованы недопустимые символы', 2),
(1, 'Օգտագործված անվավեր նիշերը', 3),
(2, 'Wrong username or password', 1),
(2, 'Неправильный пользователь или пароль', 2),
(2, 'Սխալ անուն կամ գաղտնաբառ', 3),
(3, 'Session expired', 1),
(3, 'Сессия истекла', 2),
(3, 'Սեսսիան ավարտված է', 3),
(4, 'Problem to save data', 1),
(4, 'Проблема с сохранением информации', 2),
(4, 'Խնդիր Տվըալների ներդրման հետ', 3),
(5, 'The user is currently in the system', 1),
(5, 'Проблема с сохранением информации', 2),
(5, 'Տվյալ օգտատերը համակարգում է', 3),
(6, 'Access dinaed', 1),
(6, 'Доступ закрыт', 2),
(6, 'Մուտքն արգելված է', 3),
(7, 'Wrong user', 1),
(7, 'Wrong user RUS', 2),
(7, 'Wrong user ARM', 3),
(8, 'Wrong user type', 1),
(8, 'Wrong user type RUS', 2),
(8, 'Wrong user type ARM', 3),
(9, 'Some fiel is empty', 1),
(9, 'Some fiel is empty RUS', 2),
(9, 'Some fiel is empty ARM', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredientsID` int(11) NOT NULL,
  `ingredientNameID` int(11) NOT NULL,
  `unitVending` varchar(50) NOT NULL,
  `unitCollector` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ingredients`
--

INSERT INTO `ingredients` (`ingredientsID`, `ingredientNameID`, `unitVending`, `unitCollector`) VALUES
(1, 2, '12', '11'),
(2, 3, '8', '9'),
(3, 2, '5', '10'),
(4, 3, '55', '55'),
(8, 1, '70', '70'),
(9, 3, '70', '80'),
(10, 2, '70', '90');

-- --------------------------------------------------------

--
-- Структура таблицы `ingredientsName`
--

CREATE TABLE `ingredientsName` (
  `ingredientsNameID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `ingredientsName`
--

INSERT INTO `ingredientsName` (`ingredientsNameID`, `text`, `langID`) VALUES
(1, 'Sugar', 1),
(2, 'Coffee', 1),
(3, 'Cup', 1),
(4, 'Milk', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `languages`
--

CREATE TABLE `languages` (
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `loggedUsers`
--

CREATE TABLE `loggedUsers` (
  `userID` int(11) NOT NULL,
  `lastAction` timestamp NOT NULL,
  `token` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `measurement_units`
--

CREATE TABLE `measurement_units` (
  `measurement_unitsID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `measurement_units`
--

INSERT INTO `measurement_units` (`measurement_unitsID`, `text`, `langID`) VALUES
(1, 'gram', 1),
(2, 'kilogram', 1),
(3, 'package', 1),
(4, 'liter', 1),
(5, 'thing', 1);

-- --------------------------------------------------------

--
-- Структура таблицы `menu`
--

CREATE TABLE `menu` (
  `menuID` int(11) NOT NULL,
  `ischildID` int(11) NOT NULL,
  `userTypeID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL,
  `alias` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `menu`
--

INSERT INTO `menu` (`menuID`, `ischildID`, `userTypeID`, `langID`, `text`, `alias`) VALUES
(1, 0, 1, 1, 'Owner List', 'owner_list'),
(1, 1, 1, 1, 'Devices', 'admin_devices'),
(1, 0, 1, 1, 'VM Types', 'VM_type'),
(1, 0, 1, 1, 'Logs', 'logs'),
(2, 0, 2, 1, 'Activate New Device', 'activate_new_device'),
(2, 1, 2, 1, 'Devices', 'user_devices'),
(2, 1, 2, 1, 'Incasation', 'incasation'),
(2, 1, 2, 1, 'Statistics', 'statistics'),
(2, 0, 2, 1, 'Device Register', 'device_register'),
(2, 1, 2, 1, 'Collectors', 'collectors'),
(2, 0, 2, 1, 'Recipe', 'recipe'),
(2, 0, 2, 1, 'Logs', 'logs');

-- --------------------------------------------------------

--
-- Структура таблицы `recipeDevice`
--

CREATE TABLE `recipeDevice` (
  `deviceID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `buttonID` int(11) NOT NULL,
  `price` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `recipeDevice`
--

INSERT INTO `recipeDevice` (`deviceID`, `recipeID`, `buttonID`, `price`) VALUES
(1, 2, 21, '5150'),
(2, 11, 11, '11'),
(3, 2, 34, '1550');

-- --------------------------------------------------------

--
-- Структура таблицы `recipeNames`
--

CREATE TABLE `recipeNames` (
  `recipeNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `static`
--

CREATE TABLE `static` (
  `staticID` int(11) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `text` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userInfo`
--

CREATE TABLE `userInfo` (
  `userID` int(11) NOT NULL,
  `userParamNameID` int(11) NOT NULL,
  `userParamValueID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userParamNames`
--

CREATE TABLE `userParamNames` (
  `userParamNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `userParamValues`
--

CREATE TABLE `userParamValues` (
  `userParamValueID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Структура таблицы `users`
--

CREATE TABLE `users` (
  `userID` int(11) NOT NULL,
  `username` varchar(50) NOT NULL,
  `password` text NOT NULL,
  `host` varchar(50) NOT NULL,
  `userTypeID` int(11) NOT NULL,
  `email` varchar(50) NOT NULL,
  `name` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `host`, `userTypeID`, `email`, `name`) VALUES
(1, 'Armm555', '47d1b506e28d729c81702800e086d909', 'coffeenew', 2, 'arm@mail.ru', 'Armen'),
(3, 'Ars12', '47d1b506e28d729c81702800e086d909', 'coffeenew', 2, 'ars@mail.ru', 'Gago'),
(4, 'Ars12', '47d1b506e28d729c81702800e086d909', 'coffeenew', 2, 'ars@mail.ru11', 'Gago');

-- --------------------------------------------------------

--
-- Структура таблицы `userTypes`
--

CREATE TABLE `userTypes` (
  `userTypeID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `userTypes`
--

INSERT INTO `userTypes` (`userTypeID`, `langID`, `text`) VALUES
(1, 1, 'admin'),
(2, 1, 'owner'),
(3, 1, 'collector');

-- --------------------------------------------------------

--
-- Структура таблицы `vm_types`
--

CREATE TABLE `vm_types` (
  `vm_type_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `button_count` int(11) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vm_types`
--

INSERT INTO `vm_types` (`vm_type_id`, `name`, `button_count`, `image`) VALUES
(1, 'VM75', 12, 'vm.png'),
(2, 'VM85', 14, 'vm85.png'),
(3, ' 100', 15, 'vm.jpg'),
(5, 'VM-33', 33, 'vm.png');

-- --------------------------------------------------------

--
-- Структура таблицы `vm_type_ingredients`
--

CREATE TABLE `vm_type_ingredients` (
  `vm_type_id` int(11) NOT NULL,
  `ingredientsID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `vm_type_ingredients`
--

INSERT INTO `vm_type_ingredients` (`vm_type_id`, `ingredientsID`) VALUES
(1, 1),
(1, 2),
(2, 3),
(2, 4),
(5, 8),
(5, 9),
(5, 10);

--
-- Индексы сохранённых таблиц
--

--
-- Индексы таблицы `boardDevice`
--
ALTER TABLE `boardDevice`
  ADD PRIMARY KEY (`deviceID`,`boardID`);

--
-- Индексы таблицы `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`boardID`),
  ADD UNIQUE KEY `UID` (`UID`);

--
-- Индексы таблицы `deviceParamNames`
--
ALTER TABLE `deviceParamNames`
  ADD PRIMARY KEY (`deviceParamNameID`,`langID`);

--
-- Индексы таблицы `deviceParamValues`
--
ALTER TABLE `deviceParamValues`
  ADD PRIMARY KEY (`deviceParamValueID`);

--
-- Индексы таблицы `deviceTypes`
--
ALTER TABLE `deviceTypes`
  ADD PRIMARY KEY (`deviceTypeID`);

--
-- Индексы таблицы `deviceUsers`
--
ALTER TABLE `deviceUsers`
  ADD PRIMARY KEY (`deviceID`,`userID`);

--
-- Индексы таблицы `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredientsID`);

--
-- Индексы таблицы `ingredientsName`
--
ALTER TABLE `ingredientsName`
  ADD PRIMARY KEY (`ingredientsNameID`);

--
-- Индексы таблицы `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`langID`);

--
-- Индексы таблицы `loggedUsers`
--
ALTER TABLE `loggedUsers`
  ADD PRIMARY KEY (`userID`);

--
-- Индексы таблицы `measurement_units`
--
ALTER TABLE `measurement_units`
  ADD PRIMARY KEY (`measurement_unitsID`);

--
-- Индексы таблицы `recipeDevice`
--
ALTER TABLE `recipeDevice`
  ADD PRIMARY KEY (`deviceID`);

--
-- Индексы таблицы `recipeNames`
--
ALTER TABLE `recipeNames`
  ADD PRIMARY KEY (`recipeNameID`);

--
-- Индексы таблицы `static`
--
ALTER TABLE `static`
  ADD PRIMARY KEY (`staticID`,`alias`);

--
-- Индексы таблицы `userParamNames`
--
ALTER TABLE `userParamNames`
  ADD PRIMARY KEY (`userParamNameID`,`langID`);

--
-- Индексы таблицы `userParamValues`
--
ALTER TABLE `userParamValues`
  ADD PRIMARY KEY (`userParamValueID`);

--
-- Индексы таблицы `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Индексы таблицы `userTypes`
--
ALTER TABLE `userTypes`
  ADD PRIMARY KEY (`userTypeID`,`langID`);

--
-- Индексы таблицы `vm_types`
--
ALTER TABLE `vm_types`
  ADD PRIMARY KEY (`vm_type_id`);

--
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `boards`
--
ALTER TABLE `boards`
  MODIFY `boardID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `deviceParamNames`
--
ALTER TABLE `deviceParamNames`
  MODIFY `deviceParamNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `deviceParamValues`
--
ALTER TABLE `deviceParamValues`
  MODIFY `deviceParamValueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT для таблицы `deviceTypes`
--
ALTER TABLE `deviceTypes`
  MODIFY `deviceTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `deviceUsers`
--
ALTER TABLE `deviceUsers`
  MODIFY `deviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredientsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT для таблицы `ingredientsName`
--
ALTER TABLE `ingredientsName`
  MODIFY `ingredientsNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `measurement_units`
--
ALTER TABLE `measurement_units`
  MODIFY `measurement_unitsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `recipeNames`
--
ALTER TABLE `recipeNames`
  MODIFY `recipeNameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `vm_types`
--
ALTER TABLE `vm_types`
  MODIFY `vm_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
