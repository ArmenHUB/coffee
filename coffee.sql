-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 08 2018 г., 12:32
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
(1, 7, 7, 2);

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
(7, 1, 'expiration Date');

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
(5, '11'),
(7, '2018-09-05 70:20:55');

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
(1, 1, 2);

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
  `recipeID` int(11) NOT NULL,
  `deviceID` int(11) NOT NULL,
  `buttonID` int(11) NOT NULL,
  `price` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `recipeDevice`
--

INSERT INTO `recipeDevice` (`recipeID`, `deviceID`, `buttonID`, `price`) VALUES
(1, 1, 1, '300');

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
(3, 'Ars12', '47d1b506e28d729c81702800e086d909', 'coffeenew', 2, 'ars@mail.ru', 'Arsen');

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

--
-- Индексы сохранённых таблиц
--

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
-- Индексы таблицы `recipeDevice`
--
ALTER TABLE `recipeDevice`
  ADD PRIMARY KEY (`recipeID`);

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
-- AUTO_INCREMENT для сохранённых таблиц
--

--
-- AUTO_INCREMENT для таблицы `deviceParamNames`
--
ALTER TABLE `deviceParamNames`
  MODIFY `deviceParamNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT для таблицы `deviceParamValues`
--
ALTER TABLE `deviceParamValues`
  MODIFY `deviceParamValueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT для таблицы `deviceTypes`
--
ALTER TABLE `deviceTypes`
  MODIFY `deviceTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT для таблицы `deviceUsers`
--
ALTER TABLE `deviceUsers`
  MODIFY `deviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=2;

--
-- AUTO_INCREMENT для таблицы `recipeDevice`
--
ALTER TABLE `recipeDevice`
  MODIFY `recipeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;

--
-- AUTO_INCREMENT для таблицы `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
