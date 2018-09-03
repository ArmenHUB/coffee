-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1:3306
-- Время создания: Сен 03 2018 г., 17:44
-- Версия сервера: 5.6.38
-- Версия PHP: 5.5.38

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
  `deviceParamValueID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `deviceInfo`
--

INSERT INTO `deviceInfo` (`deviceID`, `deviceParamNameID`, `deviceParamValueID`) VALUES
(1, 1, 1),
(1, 2, 2);

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
(2, 1, 'cup');

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
(1, 'Erevan'),
(2, '1275');

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
(2, 1, 'apple', '1');

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
-- Структура таблицы `recipeDevice`
--

CREATE TABLE `recipeDevice` (
  `recipeID` int(11) NOT NULL,
  `deviceID` int(11) NOT NULL,
  `buttonID` int(11) NOT NULL,
  `price` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
  `userTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `host`, `userTypeID`) VALUES
(1, 'Armen', 'a123', 'arm.ru', 1),
(2, 'Arsen', 'a321', 'ars.com', 2),
(3, 'Arman', 'a456', 'arman.net', 3);

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
  ADD PRIMARY KEY (`deviceTypeID`,`langID`);

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
  ADD PRIMARY KEY (`userID`);

--
-- Индексы таблицы `userTypes`
--
ALTER TABLE `userTypes`
  ADD PRIMARY KEY (`userTypeID`,`langID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
