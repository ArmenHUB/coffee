-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Хост: 127.0.0.1
-- Время создания: Сен 30 2018 г., 16:59
-- Версия сервера: 10.1.26-MariaDB
-- Версия PHP: 7.0.22

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- База данных: `coffee`
--

-- --------------------------------------------------------

--
-- Структура таблицы `action_log`
--

CREATE TABLE `action_log` (
  `deviceID` int(11) NOT NULL,
  `ingredientsID` int(11) NOT NULL,
  `count` text NOT NULL,
  `spend_money` text NOT NULL,
  `measurement_unitsID` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Дамп данных таблицы `action_log`
--

INSERT INTO `action_log` (`deviceID`, `ingredientsID`, `count`, `spend_money`, `measurement_unitsID`, `timestamp`) VALUES
(1, 1, '500', '100', 1, '2018-10-12 08:10:11'),
(1, 1, '500', '100', 1, '2018-09-30 14:35:10'),
(1, 11, '3000', '400', 6, '2018-11-22 07:33:11'),
(1, 12, '2000', '300', 6, '2018-11-11 09:11:11'),
(1, 13, '-300', '200', 6, '2018-06-12 17:22:11');
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
