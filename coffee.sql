-- phpMyAdmin SQL Dump
-- version 4.7.7
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1:3306
-- Generation Time: Sep 27, 2018 at 05:24 PM
-- Server version: 5.6.38
-- PHP Version: 5.5.38

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `Coffee`
--

-- --------------------------------------------------------

--
-- Table structure for table `action_log`
--

CREATE TABLE `action_log` (
  `deviceID` int(11) NOT NULL,
  `ingredientsID` int(11) NOT NULL,
  `count` text NOT NULL,
  `inc_money` text NOT NULL,
  `measurement_unitsID` int(11) NOT NULL,
  `timestamp` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `action_log`
--

INSERT INTO `action_log` (`deviceID`, `ingredientsID`, `count`, `inc_money`, `measurement_unitsID`, `timestamp`) VALUES
(1, 1, '500', '100', 1, '2018-10-12 08:10:11'),
(1, 2, '50', '300', 5, '2018-10-12 10:12:11'),
(1, 1, '500', '100', 1, '2018-10-13 07:10:11'),
(1, 11, '3000', '400', 6, '2018-11-22 07:33:11');

-- --------------------------------------------------------

--
-- Table structure for table `boardDevice`
--

CREATE TABLE `boardDevice` (
  `deviceID` int(11) NOT NULL,
  `boardID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `boardDevice`
--

INSERT INTO `boardDevice` (`deviceID`, `boardID`) VALUES
(1, 1),
(2, 3),
(3, 4),
(18, 0);

-- --------------------------------------------------------

--
-- Table structure for table `boards`
--

CREATE TABLE `boards` (
  `boardID` int(12) NOT NULL,
  `UID` varchar(25) NOT NULL,
  `serialNumber` varchar(25) NOT NULL,
  `lastActivity` timestamp NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `boards`
--

INSERT INTO `boards` (`boardID`, `UID`, `serialNumber`, `lastActivity`) VALUES
(1, '991901212499190121111256', '5555555555555555', '2018-09-11 08:44:40'),
(4, '365687345367576', '2435464546757515', '2018-09-11 08:44:40'),
(5, '555587345367576', '8547464546757511', '2018-09-11 08:48:40'),
(6, '151111111555555555555555', '4191510991959198', '2018-09-24 12:15:35');

-- --------------------------------------------------------

--
-- Table structure for table `deviceInfo`
--

CREATE TABLE `deviceInfo` (
  `deviceID` int(11) NOT NULL,
  `deviceParamNameID` int(11) NOT NULL,
  `deviceParamValueID` int(11) NOT NULL,
  `deviceTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deviceInfo`
--

INSERT INTO `deviceInfo` (`deviceID`, `deviceParamNameID`, `deviceParamValueID`, `deviceTypeID`) VALUES
(1, 1, 1, 2),
(1, 3, 3, 2),
(1, 4, 4, 2),
(1, 5, 5, 2),
(1, 7, 7, 2),
(2, 7, 8, 2),
(3, 7, 9, 14),
(3, 3, 10, 14),
(1, 8, 11, 2),
(3, 4, 13, 14),
(3, 8, 12, 14),
(3, 5, 14, 14),
(1, 9, 15, 2),
(3, 1, 19, 14),
(3, 9, 17, 14),
(2, 5, 18, 2),
(18, 3, 20, 2),
(18, 1, 21, 2),
(18, 4, 22, 2),
(18, 7, 23, 2),
(18, 5, 14, 2),
(18, 9, 17, 2);

-- --------------------------------------------------------

--
-- Table structure for table `deviceParamNames`
--

CREATE TABLE `deviceParamNames` (
  `deviceParamNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deviceParamNames`
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
-- Table structure for table `deviceParamValues`
--

CREATE TABLE `deviceParamValues` (
  `deviceParamValueID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deviceParamValues`
--

INSERT INTO `deviceParamValues` (`deviceParamValueID`, `text`) VALUES
(1, '12.120.242'),
(3, 'Apple A12'),
(4, 'Sebastia Street'),
(5, '1'),
(7, '2005-07-13 08:00:99'),
(8, '2021-05-17 09:00:00'),
(9, '2009-07-19 09:33:00'),
(10, 'Samsung'),
(11, '4500'),
(12, '6500'),
(13, 'Ara ter Sargsyan 20/1'),
(14, '2'),
(15, 'images/loacation_icon_active.png'),
(16, 'images/loacation_icon_warning.png'),
(17, 'images/loacation_icon_error.png'),
(18, '2'),
(19, '12.16.12.16'),
(20, ' Motorolla'),
(21, ' 12.15.12.15'),
(22, ' Abovyan 75'),
(23, ' 2018/12/12');

-- --------------------------------------------------------

--
-- Table structure for table `deviceTypes`
--

CREATE TABLE `deviceTypes` (
  `deviceTypeID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deviceTypes`
--

INSERT INTO `deviceTypes` (`deviceTypeID`, `langID`, `text`, `image`) VALUES
(2, 1, 'iphone', '1'),
(5, 1, 'S8+', '1');

-- --------------------------------------------------------

--
-- Table structure for table `deviceUsers`
--

CREATE TABLE `deviceUsers` (
  `deviceID` int(11) NOT NULL,
  `userID` int(11) NOT NULL,
  `deviceTypeID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `deviceUsers`
--

INSERT INTO `deviceUsers` (`deviceID`, `userID`, `deviceTypeID`) VALUES
(1, 1, 2),
(2, 3, 2),
(3, 1, 2),
(18, 1, 2);

-- --------------------------------------------------------

--
-- Table structure for table `errors`
--

CREATE TABLE `errors` (
  `errorID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `errors`
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
(10, 'Unknown device ARM', 3),
(11, 'Unknown vm type', 1),
(11, 'Unknown  vm type Rus', 2),
(11, 'Unknown  vm type ARM', 3);

-- --------------------------------------------------------

--
-- Table structure for table `ingredients`
--

CREATE TABLE `ingredients` (
  `ingredientsID` int(11) NOT NULL,
  `ingredientNameID` int(11) NOT NULL,
  `unitVending` varchar(50) NOT NULL,
  `unitCollector` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ingredients`
--

INSERT INTO `ingredients` (`ingredientsID`, `ingredientNameID`, `unitVending`, `unitCollector`) VALUES
(1, 2, '12', '11'),
(2, 3, '8', '9'),
(3, 2, '5', '10'),
(4, 3, '55', '55'),
(11, 5, '22', '33'),
(12, 3, '0', '0'),
(13, 6, '0', '0'),
(14, 7, '0', '0'),
(15, 3, '0', '0'),
(16, 6, '0', '0'),
(17, 7, '0', '0'),
(18, 3, '0', '0'),
(19, 6, '0', '0'),
(20, 7, '0', '0'),
(21, 3, '0', '0'),
(22, 5, '0', '0'),
(23, 6, '0', '0');

-- --------------------------------------------------------

--
-- Table structure for table `ingredientsName`
--

CREATE TABLE `ingredientsName` (
  `ingredientsNameID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `ingredientsName`
--

INSERT INTO `ingredientsName` (`ingredientsNameID`, `text`, `langID`) VALUES
(1, 'Sugar', 1),
(2, 'Coffee', 1),
(3, 'Cup', 1),
(4, 'Milk', 1),
(5, 'Money', 1),
(6, 'Money_cash_in', 1),
(7, 'spent_money', 1);

-- --------------------------------------------------------

--
-- Table structure for table `languages`
--

CREATE TABLE `languages` (
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `loggedUsers`
--

CREATE TABLE `loggedUsers` (
  `userID` int(11) NOT NULL,
  `lastAction` timestamp NOT NULL,
  `token` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `loggedUsers`
--

INSERT INTO `loggedUsers` (`userID`, `lastAction`, `token`) VALUES
(1, '2018-09-27 14:20:09', 'bScEt0VrWE');

-- --------------------------------------------------------

--
-- Table structure for table `measurement_units`
--

CREATE TABLE `measurement_units` (
  `measurement_unitsID` int(11) NOT NULL,
  `text` text NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `measurement_units`
--

INSERT INTO `measurement_units` (`measurement_unitsID`, `text`, `langID`) VALUES
(1, 'gram', 1),
(2, 'kilogram', 1),
(3, 'package', 1),
(4, 'liter', 1),
(5, 'thing', 1),
(6, 'dram', 1);

-- --------------------------------------------------------

--
-- Table structure for table `menu`
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
-- Dumping data for table `menu`
--

INSERT INTO `menu` (`menuID`, `ischildID`, `userTypeID`, `langID`, `text`, `alias`) VALUES
(1, 0, 1, 1, 'Owner List', 'owner_list'),
(2, 1, 1, 1, 'Devices', 'admin_devices'),
(3, 0, 1, 1, 'VM Types', 'vm_type'),
(4, 0, 1, 1, 'Logs', 'logs_admin'),
(5, 0, 2, 1, 'Activate New Device', 'activate_new_device'),
(6, 1, 2, 1, 'Devices', 'user_devices'),
(7, 1, 2, 1, 'Incasation', 'incasation'),
(8, 1, 2, 1, 'Statistics', 'statistics'),
(9, 0, 2, 1, 'Device Register', 'device_register'),
(10, 0, 2, 1, 'Collectors', 'collectors'),
(11, 0, 2, 1, 'Recipe', 'recipe'),
(12, 0, 2, 1, 'Logs', 'logs_owner');

-- --------------------------------------------------------

--
-- Table structure for table `recipeDevice`
--

CREATE TABLE `recipeDevice` (
  `deviceID` int(11) NOT NULL,
  `recipeID` int(11) NOT NULL,
  `buttonID` int(11) NOT NULL,
  `price` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `recipeDevice`
--

INSERT INTO `recipeDevice` (`deviceID`, `recipeID`, `buttonID`, `price`) VALUES
(1, 2, 21, '5150'),
(2, 11, 11, '11'),
(3, 2, 34, '1550');

-- --------------------------------------------------------

--
-- Table structure for table `recipeNames`
--

CREATE TABLE `recipeNames` (
  `recipeNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `static`
--

CREATE TABLE `static` (
  `staticID` int(11) NOT NULL,
  `alias` varchar(50) NOT NULL,
  `text` varchar(50) NOT NULL,
  `langID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userInfo`
--

CREATE TABLE `userInfo` (
  `userID` int(11) NOT NULL,
  `userParamNameID` int(11) NOT NULL,
  `userParamValueID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userParamNames`
--

CREATE TABLE `userParamNames` (
  `userParamNameID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `userParamValues`
--

CREATE TABLE `userParamValues` (
  `userParamValueID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Table structure for table `users`
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
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `username`, `password`, `host`, `userTypeID`, `email`, `name`) VALUES
(1, 'Owner', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 2, 'arm@mail.rukkk', 'Armen'),
(3, 'Admin', '098f6bcd4621d373cade4e832627b4f6', 'coffeev2', 1, 'ars@mail.ru', 'Gago'),
(4, 'Collector', 'd41d8cd98f00b204e9800998ecf8427e', 'coffeev2', 3, 'ars@mail.ru1111', 'Gago5411111222'),
(14, 'Arsen12', 'd41d8cd98f00b204e9800998ecf8427es', 'coffeev2', 2, 'ars@23@nm.ru', 'Arsen');

-- --------------------------------------------------------

--
-- Table structure for table `userTypes`
--

CREATE TABLE `userTypes` (
  `userTypeID` int(11) NOT NULL,
  `langID` int(11) NOT NULL,
  `text` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `userTypes`
--

INSERT INTO `userTypes` (`userTypeID`, `langID`, `text`) VALUES
(1, 1, 'admin'),
(2, 1, 'owner'),
(3, 1, 'collector');

-- --------------------------------------------------------

--
-- Table structure for table `vm_types`
--

CREATE TABLE `vm_types` (
  `vm_type_id` int(11) NOT NULL,
  `name` varchar(50) NOT NULL,
  `button_count` int(11) NOT NULL,
  `image` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vm_types`
--

INSERT INTO `vm_types` (`vm_type_id`, `name`, `button_count`, `image`) VALUES
(1, 'VM7565', 12, 'vm.png'),
(2, 'VM85555555', 14, 'vm85.png'),
(3, '10', 15, 'vm.jpg'),
(14, 'test 1222', 0, '0'),
(17, 'f', 0, '0');

-- --------------------------------------------------------

--
-- Table structure for table `vm_type_ingredients`
--

CREATE TABLE `vm_type_ingredients` (
  `vm_type_id` int(11) NOT NULL,
  `ingredientsID` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Dumping data for table `vm_type_ingredients`
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
(17, 23);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `boardDevice`
--
ALTER TABLE `boardDevice`
  ADD PRIMARY KEY (`deviceID`,`boardID`);

--
-- Indexes for table `boards`
--
ALTER TABLE `boards`
  ADD PRIMARY KEY (`boardID`),
  ADD UNIQUE KEY `UID` (`UID`);

--
-- Indexes for table `deviceParamNames`
--
ALTER TABLE `deviceParamNames`
  ADD PRIMARY KEY (`deviceParamNameID`,`langID`);

--
-- Indexes for table `deviceParamValues`
--
ALTER TABLE `deviceParamValues`
  ADD PRIMARY KEY (`deviceParamValueID`);

--
-- Indexes for table `deviceTypes`
--
ALTER TABLE `deviceTypes`
  ADD PRIMARY KEY (`deviceTypeID`);

--
-- Indexes for table `deviceUsers`
--
ALTER TABLE `deviceUsers`
  ADD PRIMARY KEY (`deviceID`,`userID`);

--
-- Indexes for table `errors`
--
ALTER TABLE `errors`
  ADD PRIMARY KEY (`errorID`,`langID`);

--
-- Indexes for table `ingredients`
--
ALTER TABLE `ingredients`
  ADD PRIMARY KEY (`ingredientsID`);

--
-- Indexes for table `ingredientsName`
--
ALTER TABLE `ingredientsName`
  ADD PRIMARY KEY (`ingredientsNameID`);

--
-- Indexes for table `languages`
--
ALTER TABLE `languages`
  ADD PRIMARY KEY (`langID`);

--
-- Indexes for table `loggedUsers`
--
ALTER TABLE `loggedUsers`
  ADD PRIMARY KEY (`userID`);

--
-- Indexes for table `measurement_units`
--
ALTER TABLE `measurement_units`
  ADD PRIMARY KEY (`measurement_unitsID`);

--
-- Indexes for table `menu`
--
ALTER TABLE `menu`
  ADD PRIMARY KEY (`menuID`);

--
-- Indexes for table `recipeDevice`
--
ALTER TABLE `recipeDevice`
  ADD PRIMARY KEY (`deviceID`);

--
-- Indexes for table `recipeNames`
--
ALTER TABLE `recipeNames`
  ADD PRIMARY KEY (`recipeNameID`);

--
-- Indexes for table `static`
--
ALTER TABLE `static`
  ADD PRIMARY KEY (`staticID`,`alias`);

--
-- Indexes for table `userParamNames`
--
ALTER TABLE `userParamNames`
  ADD PRIMARY KEY (`userParamNameID`,`langID`);

--
-- Indexes for table `userParamValues`
--
ALTER TABLE `userParamValues`
  ADD PRIMARY KEY (`userParamValueID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- Indexes for table `userTypes`
--
ALTER TABLE `userTypes`
  ADD PRIMARY KEY (`userTypeID`,`langID`);

--
-- Indexes for table `vm_types`
--
ALTER TABLE `vm_types`
  ADD PRIMARY KEY (`vm_type_id`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `boards`
--
ALTER TABLE `boards`
  MODIFY `boardID` int(12) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `deviceParamNames`
--
ALTER TABLE `deviceParamNames`
  MODIFY `deviceParamNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=10;

--
-- AUTO_INCREMENT for table `deviceParamValues`
--
ALTER TABLE `deviceParamValues`
  MODIFY `deviceParamValueID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `deviceTypes`
--
ALTER TABLE `deviceTypes`
  MODIFY `deviceTypeID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `deviceUsers`
--
ALTER TABLE `deviceUsers`
  MODIFY `deviceID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=19;

--
-- AUTO_INCREMENT for table `ingredients`
--
ALTER TABLE `ingredients`
  MODIFY `ingredientsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=24;

--
-- AUTO_INCREMENT for table `ingredientsName`
--
ALTER TABLE `ingredientsName`
  MODIFY `ingredientsNameID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `measurement_units`
--
ALTER TABLE `measurement_units`
  MODIFY `measurement_unitsID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=7;

--
-- AUTO_INCREMENT for table `menu`
--
ALTER TABLE `menu`
  MODIFY `menuID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `recipeNames`
--
ALTER TABLE `recipeNames`
  MODIFY `recipeNameID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=15;

--
-- AUTO_INCREMENT for table `vm_types`
--
ALTER TABLE `vm_types`
  MODIFY `vm_type_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=18;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
