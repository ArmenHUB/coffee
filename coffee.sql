-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Окт 19 2018 г., 17:57
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
  `type` text NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `action_log`
--

INSERT INTO `action_log` (`deviceID`, `ingredientsID`, `count`, `measurement_unitsID`, `type`, `timestamp`) VALUES
(1, 1, '500', 5, 'cash', '2018-10-01 12:31:19'),
(1, 1, '500', 5, 'cash', '2018-10-01 12:31:55'),
(1, 11, '3000', 6, 'cash', '2018-10-01 12:32:21'),
(1, 11, '2000', 6, 'cash', '2018-10-01 12:32:42'),
(1, 15, '-300', 6, 'incasation', '2018-10-01 12:31:19'),
(1, 15, '-450', 6, 'incasation', '2018-10-01 12:31:55'),
(3, 4, '700', 5, 'cash', '2018-10-01 11:31:45'),
(3, 17, '-200', 6, 'incasation', '2018-10-01 11:31:45'),
(1, 18, '999', 6, 'vending', '2018-09-05 07:00:44'),
(1, 18, '1320', 6, 'vending', '2018-09-05 07:00:59'),
(3, 20, '1500', 6, 'vending', '2018-08-11 10:51:19'),
(3, 21, '4500', 6, 'cash', '2018-04-19 14:51:29');

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
(3, 4),
(4, 20);

-- --------------------------------------------------------

--
-- Структура таблицы `boards`
--

CREATE TABLE `boards` (
  `boardID` int(12) NOT NULL,
  `UID` varchar(25) NOT NULL,
  `serialNumber` varchar(25) NOT NULL,
  `lastActivity` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `boards`
--

INSERT INTO `boards` (`boardID`, `UID`, `serialNumber`, `lastActivity`) VALUES
(1, '991901212499190121111256', '5555555555555555', '2018-09-11 12:44:40'),
(4, '365687345367576', '4444444444444444', '2018-09-11 12:44:40'),
(5, '555587345367576', '8547464546757511', '0000-00-00 00:00:00'),
(6, '151111111555555555555555', '4191510991959198', '0000-00-00 00:00:00'),
(16, '564565765675765675675765', '9742453754875395', '0000-00-00 00:00:00'),
(17, '454545454545454545554545', '5636089479130356', '0000-00-00 00:00:00'),
(20, '777777777777777777777777', '9000900090009000', '2018-10-24 16:15:35');

-- --------------------------------------------------------

--
-- Структура таблицы `deviceInfo`
--

CREATE TABLE `deviceInfo` (
  `deviceID` int(11) NOT NULL,
  `deviceParamNameID` int(11) NOT NULL,
  `deviceParamValueID` int(11) NOT NULL,
  `vm_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceInfo`
--

INSERT INTO `deviceInfo` (`deviceID`, `deviceParamNameID`, `deviceParamValueID`, `vm_type_id`) VALUES
(1, 1, 1, 2),
(1, 3, 3, 2),
(1, 4, 4, 2),
(1, 5, 5, 2),
(1, 7, 7, 2),
(2, 7, 8, 2),
(3, 7, 9, 17),
(3, 3, 10, 17),
(1, 8, 11, 2),
(3, 4, 13, 17),
(3, 8, 12, 17),
(3, 5, 14, 17),
(1, 9, 16, 2),
(3, 1, 19, 17),
(3, 9, 17, 17),
(2, 5, 18, 2),
(4, 3, 20, 3),
(4, 1, 21, 3),
(4, 4, 22, 3),
(4, 7, 23, 3),
(4, 5, 14, 3),
(4, 9, 17, 3);

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
(7, 1, 'expiration_date'),
(8, 1, 'sum'),
(9, 1, 'map_icon');

-- --------------------------------------------------------

--
-- Структура таблицы `deviceParamValues`
--

CREATE TABLE `deviceParamValues` (
  `deviceParamValueID` int(255) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceParamValues`
--

INSERT INTO `deviceParamValues` (`deviceParamValueID`, `text`) VALUES
(1, '40.186755340918175-44.5271944763183'),
(3, 'Apple A12'),
(4, 'Sebastia Street10'),
(5, '1'),
(7, '2020/9/22'),
(8, '2021-05-17 09:00:00'),
(9, '2009-07-19 09:33:00'),
(10, 'SamsungS9'),
(11, '4500'),
(12, '6500'),
(13, 'Ara ter Sargsyan 30/1'),
(14, '2'),
(15, 'images/loacation_icon_active.png'),
(16, 'images/loacation_icon_warning.png'),
(17, 'images/loacation_icon_error.png'),
(18, '1'),
(19, '-12.16.12.16'),
(20, ' OOP'),
(21, ' 40.18078821360807-44.51114413757318'),
(22, ' Baxramyan 13'),
(23, ' 11/19/2018');

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
  `vm_type_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceUsers`
--

INSERT INTO `deviceUsers` (`deviceID`, `userID`, `vm_type_id`) VALUES
(1, 1, 2),
(2, 3, 2),
(3, 1, 17),
(4, 14, 3);

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
(5, 'The user is currently in the system rus', 2),
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
(9, 'Some fiel is empty ARM', 3),
(10, 'Unknown device ARM', 1),
(10, 'Unknown device Rus', 2),
(10, 'Unknown device ENG', 3),
(11, 'Unknown vm type', 1),
(11, 'Unknown  vm type Rus', 2),
(11, 'Unknown  vm type ARM', 3);

-- --------------------------------------------------------

--
-- Структура таблицы `eventType`
--

CREATE TABLE `eventType` (
  `eventTypeID` int(11) NOT NULL,
  `text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `eventType`
--

INSERT INTO `eventType` (`eventTypeID`, `text`) VALUES
(1, 'info'),
(2, 'warning'),
(3, 'error');

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
(1, 3, '0', '0'),
(3, 2, '0', '0'),
(4, 3, '0', '0'),
(8, 1, '0', '0'),
(9, 3, '0', '0'),
(10, 2, '0', '0'),
(11, 5, '0', '0'),
(15, 6, '0', '0'),
(16, 5, '0', '0'),
(17, 6, '0', '0'),
(18, 7, '0', '0'),
(20, 7, '0', '0'),
(21, 5, '0', '0'),
(22, 3, '0', '0'),
(23, 5, '0', '0'),
(24, 7, '0', '0'),
(25, 3, '0', '0'),
(26, 5, '0', '0'),
(27, 7, '0', '0');

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
(4, 'Milk', 1),
(5, 'Money_cash_in', 1),
(6, 'inc_money', 1),
(7, 'Vending_money', 1);

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
-- Структура таблицы `logs`
--

CREATE TABLE `logs` (
  `id` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL,
  `eventTypeID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `module` text NOT NULL,
  `event` text NOT NULL,
  `deviceID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `logs`
--

INSERT INTO `logs` (`id`, `timestamp`, `eventTypeID`, `userID`, `module`, `event`, `deviceID`) VALUES
(1, '2018-04-12 13:12:44', 1, 1, 'text/id', 'incasation', 1),
(2, '2018-06-02 07:12:12', 2, 1, 'text/id', 'cash_in', 1),
(3, '2018-07-25 12:52:01', 3, 1, 'text/id', 'cash_out', 1),
(4, '2018-02-12 13:12:44', 1, 1, 'text/id', 'incasation', 3),
(5, '2018-03-02 07:12:12', 2, 1, 'text/id', 'cash_in', 3),
(6, '2018-05-11 12:52:01', 3, 1, 'text/id', 'cash_out', 3),
(7, '2018-01-28 12:12:44', 1, 1, 'text/id', 'test', 0),
(8, '2018-05-29 07:12:12', 2, 1, 'text/id', 'test 1', 0),
(9, '2018-09-01 08:22:01', 3, 1, 'text/id', 'test 2', 0),
(10, '2018-01-11 15:12:00', 1, 14, 'text/id', 'test 1', 0),
(11, '2018-06-27 10:12:39', 2, 14, 'text/id', 'test 4', 0),
(12, '2018-11-07 13:24:09', 3, 14, 'text/id', 'test 7', 0);

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
(5, 'thing', 1),
(6, 'money', 1);

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
(2, 1, 1, 1, 'Devices', 'admin_devices'),
(3, 0, 1, 1, 'VM Types', 'vm_type'),
(4, 0, 1, 1, 'Logs', 'logs_admin'),
(5, 0, 2, 1, 'Device Register', 'activate_new_device'),
(6, 0, 2, 1, 'Devices', 'user_devices'),
(7, 1, 2, 1, 'Incasation', 'incasation'),
(8, 0, 2, 1, 'Statistics', 'statistics'),
(10, 0, 2, 1, 'Collectors', 'collectors'),
(12, 0, 2, 1, 'Logs', 'logs_owner');

-- --------------------------------------------------------

--
-- Структура таблицы `Owner_collectors`
--

CREATE TABLE `Owner_collectors` (
  `owner_id` int(11) NOT NULL,
  `collector_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `Owner_collectors`
--

INSERT INTO `Owner_collectors` (`owner_id`, `collector_id`) VALUES
(1, 19),
(1, 4);

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

--
-- Дамп данных таблицы `userInfo`
--

INSERT INTO `userInfo` (`userID`, `userParamNameID`, `userParamValueID`) VALUES
(1, 1, 1),
(3, 1, 2),
(4, 1, 3);

-- --------------------------------------------------------

--
-- Структура таблицы `userParamNames`
--

CREATE TABLE `userParamNames` (
  `userParamNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `userParamNames`
--

INSERT INTO `userParamNames` (`userParamNameID`, `langID`, `text`) VALUES
(1, 1, 'surname');

-- --------------------------------------------------------

--
-- Структура таблицы `userParamValues`
--

CREATE TABLE `userParamValues` (
  `userParamValueID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `userParamValues`
--

INSERT INTO `userParamValues` (`userParamValueID`, `text`) VALUES
(1, 'Manukyan'),
(2, 'Gevorgyan'),
(3, 'Vardanyan');

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
(1, 'Owner', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 2, 'arm@mail.rukkk', 'Armen'),
(3, 'Admin', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 1, 'ars@mail.ru', 'Gago'),
(4, 'Collector', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 3, 'ars@mail.ru1111', 'Gago5411111222'),
(14, 'Arsen12', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 2, 'ars@23@nm.ru', 'Arsen'),
(15, 'Abo12', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 2, 'arm@z-sof.net', 'Abo'),
(19, 'kalo', 'AoOWYC4Jrr', 'coffeev2', 3, 'lolo@mail.ru', 'lolo');

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
(1, 'VM7565', 12, 'vm.png'),
(2, 'VM85555555', 14, 'vm85.png'),
(3, '10', 15, 'vm.jpg'),
(14, 'test 1222', 0, '0'),
(17, 'f', 0, '0');

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
(14, 12),
(14, 13),
(14, 14),
(15, 15),
(15, 16),
(15, 17),
(16, 18),
(16, 19),
(16, 20),
(17, 21),
(17, 22),
(17, 23),
(18, 24),
(18, 25),
(18, 26),
(19, 22),
(19, 23),
(19, 24),
(20, 25),
(20, 26),
(20, 27);

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
-- Индексы таблицы `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`errorID`,`langID`);

--
-- Индексы таблицы `eventType`
--
ALTER TABLE `eventType`
  ADD PRIMARY KEY (`eventTypeID`);

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
-- Индексы таблицы `logs`
--
ALTER TABLE `logs`
  ADD PRIMARY KEY (`id`);

--
-- Индексы таблицы `measurement_units`
--
ALTER TABLE `measurement_units`
  ADD PRIMARY KEY (`measurement_unitsID`);

--
-- Индексы таблицы `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menuID`);

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
  MODIFY `boardID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `deviceParamNames`
--
ALTER TABLE `deviceParamNames`
  MODIFY `deviceParamNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `deviceParamValues`
--
ALTER TABLE `deviceParamValues`
  MODIFY `deviceParamValueID` int(255) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=40;

--
-- AUTO_INCREMENT для таблицы `deviceTypes`
--
ALTER TABLE `deviceTypes`
  MODIFY `deviceTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `deviceUsers`
--
ALTER TABLE `deviceUsers`
  MODIFY `deviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- AUTO_INCREMENT для таблицы `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredientsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=28;

--
-- AUTO_INCREMENT для таблицы `ingredientsName`
--
ALTER TABLE `ingredientsName`
  MODIFY `ingredientsNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `logs`
--
ALTER TABLE `logs`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `measurement_units`
--
ALTER TABLE `measurement_units`
  MODIFY `measurement_unitsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT для таблицы `menu`
--
ALTER TABLE `menu`
  MODIFY `menuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT для таблицы `recipeNames`
--
ALTER TABLE `recipeNames`
  MODIFY `recipeNameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT для таблицы `userParamValues`
--
ALTER TABLE `userParamValues`
  MODIFY `userParamValueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;

--
-- AUTO_INCREMENT для таблицы `vm_types`
--
ALTER TABLE `vm_types`
  MODIFY `vm_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=21;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
