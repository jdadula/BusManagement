-- phpMyAdmin SQL Dump
-- version 5.2.1
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Generation Time: May 14, 2026 at 03:53 PM
-- Server version: 10.4.32-MariaDB
-- PHP Version: 8.2.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `pd221labdatabase`
--

-- --------------------------------------------------------

--
-- Table structure for table `bus`
--

CREATE TABLE `bus` (
  `Bus_no` varchar(10) NOT NULL,
  `Source` varchar(50) DEFAULT NULL,
  `Destination` varchar(50) DEFAULT NULL,
  `Couch_type` varchar(26) DEFAULT NULL,
  `Fair` decimal(10,2) DEFAULT NULL,
  `SeatCapacity` int(11) DEFAULT NULL,
  `Manufacture_year` int(11) DEFAULT NULL,
  `Status` varchar(20) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus`
--

INSERT INTO `bus` (`Bus_no`, `Source`, `Destination`, `Couch_type`, `Fair`, `SeatCapacity`, `Manufacture_year`, `Status`) VALUES
('BUS53', 'GENSAN', 'DAVAO', 'DELUXE', 250.75, 60, NULL, 'GOOD CONDITION'),
('BUS54', 'DAVAO', 'MARBEL', 'LUXURY', 425.50, 40, NULL, 'GOOD CONDITION'),
('BUS55', 'GENSAN', 'DAVAO', 'DELUXE', 100.00, 60, 2017, 'PERFECT CONDITION'),
('BUS56', 'DAVAO', 'MARBEL', 'LUXURY', 250.00, 40, 2018, 'PERFECT CONDITION'),
('BUS57', 'MARBEL', 'GENSAN', 'LUXURY', 300.00, 40, 2020, 'PERFECT CONDITION'),
('BUS58', 'GENSAN', 'TACURONG', 'SEMI-LUXURY', 250.75, 50, 2023, 'PERFECT CONDITION'),
('BUS59', 'GENSAN', 'DAVAO', 'LUXURY', 400.00, 40, 2025, 'PERFECT CONDITION'),
('BUS60', 'DAVAO', 'Polomolok', 'DELUXE', 130.00, 40, 2021, 'PERFECT CONDITION'),
('BUS61', 'MARBEL', 'GENSAN', 'LUXURY', 250.00, 40, NULL, 'GOOD CONDITION'),
('BUS62', 'GENSAN', 'TACURONG', 'SEMI-LUXURY', 200.00, 50, NULL, 'GOOD CONDITION'),
('BUS63', 'DAVAO', 'GENSAN', 'LUXURY', 400.00, 40, NULL, 'GOOD CONDITION');

-- --------------------------------------------------------

--
-- Table structure for table `bus_company`
--

CREATE TABLE `bus_company` (
  `Company_ID` varchar(10) NOT NULL,
  `Company_name` varchar(50) DEFAULT NULL,
  `Address` varchar(100) DEFAULT NULL,
  `Contacts` varchar(20) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_company`
--

INSERT INTO `bus_company` (`Company_ID`, `Company_name`, `Address`, `Contacts`, `Email`) VALUES
('BZ301', 'Yellow Bus', 'DAVAO City', '801-4539', 'yellowbus@gmail.com'),
('BZ302', 'Husky', 'Kidapawan', '09059094743', 'husky@hotmail.com'),
('BZ303', 'Mindanao Star', 'General Santos City', '09169191234', 'mindanaostar.gmail.com'),
('BZ304', 'Gensan Liners', 'General Santos City', '09329454342', 'Gensan_liners@yahoo.com'),
('BZ305', 'Tuna Smasher', 'General Santos City', '09999194543', 'tunasmasher@tunasmasher.com');

-- --------------------------------------------------------

--
-- Table structure for table `bus_status`
--

CREATE TABLE `bus_status` (
  `bus_status_id` varchar(10) NOT NULL,
  `bus_number` varchar(10) DEFAULT NULL,
  `status_id` varchar(10) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `bus_status`
--

INSERT INTO `bus_status` (`bus_status_id`, `bus_number`, `status_id`) VALUES
('BS001', 'BUS53', 'ST002'),
('BS002', 'BUS54', 'ST002'),
('BS003', 'BUS55', 'ST003'),
('BS004', 'BUS56', 'ST003'),
('BS005', 'BUS57', 'ST003'),
('BS006', 'BUS58', 'ST003'),
('BS007', 'BUS59', 'ST003'),
('BS008', 'BUS60', 'ST003'),
('BS009', 'BUS61', 'ST003'),
('BS010', 'BUS62', 'ST003'),
('BS011', 'BUS63', 'ST003');

-- --------------------------------------------------------

--
-- Table structure for table `company_bus_relation`
--

CREATE TABLE `company_bus_relation` (
  `Company_ID` varchar(10) NOT NULL,
  `Bus_no` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_bus_relation`
--

INSERT INTO `company_bus_relation` (`Company_ID`, `Bus_no`) VALUES
('BZ301', 'BUS53'),
('BZ301', 'BUS59'),
('BZ302', 'BUS54'),
('BZ302', 'BUS60'),
('BZ303', 'BUS55'),
('BZ303', 'BUS61'),
('BZ304', 'BUS56'),
('BZ304', 'BUS62'),
('BZ305', 'BUS57'),
('BZ305', 'BUS63');

-- --------------------------------------------------------

--
-- Table structure for table `company_owner`
--

CREATE TABLE `company_owner` (
  `Owner_ID` varchar(10) NOT NULL,
  `Owner_name` varchar(50) DEFAULT NULL,
  `Contacts` varchar(20) DEFAULT NULL,
  `Email` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_owner`
--

INSERT INTO `company_owner` (`Owner_ID`, `Owner_name`, `Contacts`, `Email`) VALUES
('OWN-0001', 'John Micheal Abando', '09169324512', 'John.abando@gmail.com'),
('OWN-0002', 'Juan Dela Torre', '09329454321', 'DelaTorreJuan@tunasmasher.com'),
('OWN-0003', 'Michelle Ann Rudriguez', '09089123456', 'Rudriquez_michelleann@gmail.com'),
('OWN-0004', 'Farah Basa', '09998765432', 'farahbasa@hotmail.com');

-- --------------------------------------------------------

--
-- Table structure for table `company_owner_relation`
--

CREATE TABLE `company_owner_relation` (
  `Company_ID` varchar(10) NOT NULL,
  `Owner_ID` varchar(10) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `company_owner_relation`
--

INSERT INTO `company_owner_relation` (`Company_ID`, `Owner_ID`) VALUES
('BZ301', 'OWN-0002'),
('BZ302', 'OWN-0001'),
('BZ303', 'OWN-0004'),
('BZ304', 'OWN-0003'),
('BZ305', 'OWN-0004');

-- --------------------------------------------------------

--
-- Table structure for table `drivers`
--

CREATE TABLE `drivers` (
  `DriverID` int(11) NOT NULL,
  `FirstName` varchar(50) NOT NULL,
  `LastName` varchar(50) NOT NULL,
  `LicenseNumber` varchar(30) NOT NULL,
  `ContactNumber` varchar(20) NOT NULL,
  `BusID` varchar(10) DEFAULT NULL,
  `Username` varchar(50) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

-- --------------------------------------------------------

--
-- Table structure for table `status`
--

CREATE TABLE `status` (
  `status_ID` varchar(10) NOT NULL,
  `status` varchar(20) DEFAULT NULL,
  `description` varchar(100) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `status`
--

INSERT INTO `status` (`status_ID`, `status`, `description`) VALUES
('ST001', 'UNDER MAINTENANCE', 'Bus is currently undergoing repair and is not operational'),
('ST002', 'GOOD CONDITION', 'Bus is operational with minor wear but safe to use'),
('ST003', 'PERFECT CONDITION', 'Bus is brand new or fully serviced and in top condition'),
('ST004', 'OUT OF SERVICE', 'Bus has been permanently retired from operation'),
('ST005', 'RESERVED', 'Bus is temporarily reserved for a special trip or event');

-- --------------------------------------------------------

--
-- Table structure for table `tickets`
--

CREATE TABLE `tickets` (
  `TicketID` varchar(10) NOT NULL,
  `PassengerName` varchar(100) NOT NULL,
  `BusID` varchar(10) DEFAULT NULL,
  `Destination` varchar(50) NOT NULL,
  `DepartureDate` date NOT NULL,
  `DepartureTime` time NOT NULL,
  `SeatNumber` int(11) NOT NULL,
  `Price` decimal(10,2) NOT NULL,
  `Status` enum('BOOKED','CANCELLED','SOLD') DEFAULT 'BOOKED',
  `UserID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `tickets`
--

INSERT INTO `tickets` (`TicketID`, `PassengerName`, `BusID`, `Destination`, `DepartureDate`, `DepartureTime`, `SeatNumber`, `Price`, `Status`, `UserID`) VALUES
('TKT-07ABD', 'AkongManghud', 'BUS53', 'DigosCity', '2026-05-10', '20:53:00', 1, 250.75, 'CANCELLED', 12),
('TKT-4B1A9', 'PAsaheroko', 'BUS53', 'DigosCity', '2026-05-09', '19:46:00', 1, 250.75, 'SOLD', 11),
('TKT-506AE', 'Jonnel12', 'BUS54', 'DigosCitysss', '2026-05-09', '13:14:00', 1, 425.50, 'SOLD', 11),
('TKT-526D8', 'Leomord', 'BUS53', 'davao', '2026-05-09', '06:18:00', 1, 250.75, 'CANCELLED', 1),
('TKT-5B48A', 'Jonnel', 'BUS53', 'DigosCity', '2026-05-09', '23:38:00', 1, 250.75, 'SOLD', 1),
('TKT-5DFF3', 'AkongManghudEdit', 'BUS55', 'DigosCity', '2026-05-10', '06:35:00', 1, 100.00, 'CANCELLED', 11),
('TKT-73212', 'asdasd', 'BUS53', 'asd', '2026-04-28', '05:44:00', 1, 250.75, 'CANCELLED', NULL),
('TKT-7D5D1', 'Jonnel', 'BUS53', 'DigosCity', '2026-04-30', '12:32:00', 1, 250.75, 'SOLD', 11),
('TKT-84C39', 'Jonnel1', 'BUS53', 'DigosCity', '2026-05-11', '23:33:00', 2, 250.75, 'SOLD', 11),
('TKT-890EE', 'asdqwe123', 'BUS53', 'DIGOS', '2026-05-09', '18:25:00', 1, 250.75, 'CANCELLED', 10),
('TKT-964ED', 'Sold na', 'BUS53', 'DigosCity', '2026-04-30', '08:07:00', 1, 250.75, 'SOLD', 11),
('TKT-D1A57', 'asd', 'BUS53', 'asd', '2026-05-06', '21:00:00', 2, 250.75, 'CANCELLED', 12),
('TKT-D2694', 'Jonnel', 'BUS53', 'DigosCity', '2026-05-09', '23:39:00', 1, 250.75, 'SOLD', 11),
('TKT-F2BC3', 'cjcall', 'BUS53', 'davao', '2026-05-09', '06:20:00', 1, 250.75, 'CANCELLED', 10);

-- --------------------------------------------------------

--
-- Table structure for table `transactions`
--

CREATE TABLE `transactions` (
  `TransactionID` int(11) NOT NULL,
  `BusID` varchar(10) DEFAULT NULL,
  `TicketID` varchar(10) DEFAULT NULL,
  `TransactionDate` date NOT NULL,
  `Amount` decimal(10,2) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `transactions`
--

INSERT INTO `transactions` (`TransactionID`, `BusID`, `TicketID`, `TransactionDate`, `Amount`) VALUES
(6, 'BUS53', 'TKT-4B1A9', '2026-05-09', 250.75),
(7, 'BUS54', 'TKT-506AE', '2026-05-09', 425.50),
(8, 'BUS53', 'TKT-5B48A', '2026-05-09', 250.75),
(9, 'BUS53', 'TKT-7D5D1', '2026-04-30', 250.75),
(10, 'BUS53', 'TKT-964ED', '2026-04-30', 250.75),
(11, 'BUS53', 'TKT-D2694', '2026-05-09', 250.75);

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `User_ID` int(11) NOT NULL,
  `Username` varchar(50) NOT NULL,
  `Password` varchar(255) NOT NULL,
  `Role` enum('admin','driver','passenger') NOT NULL,
  `is_active` tinyint(1) DEFAULT 1
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`User_ID`, `Username`, `Password`, `Role`, `is_active`) VALUES
(1, 'admin01', 'e86f78a8a3caf0b60d8e74e5942aa6d86dc150cd3c03338aef25b7d2d7e3acc7', 'admin', 1),
(3, 'passenger01', 'b6bc7b58510319a151d168ba3d5aecb3ac0a9708d06dd930f37fbc89b6cdc697', 'passenger', 1),
(4, 'jonnel', '8f56e1b547ec30a1d8940227c5fbaa4a601d76ea1685498b660d9bd19823a63a', 'passenger', 1),
(8, 'jonnel123', '8f56e1b547ec30a1d8940227c5fbaa4a601d76ea1685498b660d9bd19823a63a', 'passenger', 1),
(9, 'jonnel12345', '2e485973d1a645a32a4b26117bd7c24182f360f5f2cef7a3f7b4f2458715b69d', 'passenger', 1),
(10, 'cjcal', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'passenger', 1),
(11, 'Pasahero', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'passenger', 1),
(12, 'admin02', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'admin', 1),
(21, 'akosijonnel', '388fc22c686505ce88b59e2a9b1deef93dc9b855580578ea704aa383d63ee1fd', 'driver', 1),
(22, 'asd', '688787d8ff144c502c7f5cffaafe2cc588d86079f9de88304c26b0cb99ce91c6', 'driver', 1),
(23, 'asd1', 'c31097bc49e7cbdbec19cfa5193ee0ae8a0763e031040912c725f61ad20e7b26', 'driver', 1),
(24, 'akosijonnel1', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'driver', 1),
(25, 'akosijonnel111', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'driver', 1),
(26, 'akosijonnel1111', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'driver', 1),
(27, 'akosijonne11111', '8d969eef6ecad3c29a3a629280e686cf0c3f5d5a86aff3ca12020c923adc6c92', 'driver', 1),
(28, 'drive00', 'a665a45920422f9d417e4867efdc4fb8a04a1f3fff1fa07e998e86f7f7a27ae3', 'driver', 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `bus`
--
ALTER TABLE `bus`
  ADD PRIMARY KEY (`Bus_no`);

--
-- Indexes for table `bus_company`
--
ALTER TABLE `bus_company`
  ADD PRIMARY KEY (`Company_ID`);

--
-- Indexes for table `bus_status`
--
ALTER TABLE `bus_status`
  ADD PRIMARY KEY (`bus_status_id`),
  ADD KEY `bus_number` (`bus_number`),
  ADD KEY `status_id` (`status_id`);

--
-- Indexes for table `company_bus_relation`
--
ALTER TABLE `company_bus_relation`
  ADD PRIMARY KEY (`Company_ID`,`Bus_no`),
  ADD KEY `Bus_no` (`Bus_no`);

--
-- Indexes for table `company_owner`
--
ALTER TABLE `company_owner`
  ADD PRIMARY KEY (`Owner_ID`);

--
-- Indexes for table `company_owner_relation`
--
ALTER TABLE `company_owner_relation`
  ADD PRIMARY KEY (`Company_ID`,`Owner_ID`),
  ADD KEY `Owner_ID` (`Owner_ID`);

--
-- Indexes for table `drivers`
--
ALTER TABLE `drivers`
  ADD PRIMARY KEY (`DriverID`),
  ADD UNIQUE KEY `LicenseNumber` (`LicenseNumber`),
  ADD KEY `BusID` (`BusID`);

--
-- Indexes for table `status`
--
ALTER TABLE `status`
  ADD PRIMARY KEY (`status_ID`);

--
-- Indexes for table `tickets`
--
ALTER TABLE `tickets`
  ADD PRIMARY KEY (`TicketID`),
  ADD KEY `BusID` (`BusID`),
  ADD KEY `UserID` (`UserID`);

--
-- Indexes for table `transactions`
--
ALTER TABLE `transactions`
  ADD PRIMARY KEY (`TransactionID`),
  ADD KEY `BusID` (`BusID`),
  ADD KEY `TicketID` (`TicketID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`User_ID`),
  ADD UNIQUE KEY `Username` (`Username`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `drivers`
--
ALTER TABLE `drivers`
  MODIFY `DriverID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=30;

--
-- AUTO_INCREMENT for table `transactions`
--
ALTER TABLE `transactions`
  MODIFY `TransactionID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=12;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `User_ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=29;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `bus_status`
--
ALTER TABLE `bus_status`
  ADD CONSTRAINT `bus_status_ibfk_1` FOREIGN KEY (`bus_number`) REFERENCES `bus` (`Bus_no`),
  ADD CONSTRAINT `bus_status_ibfk_2` FOREIGN KEY (`status_id`) REFERENCES `status` (`status_ID`);

--
-- Constraints for table `company_bus_relation`
--
ALTER TABLE `company_bus_relation`
  ADD CONSTRAINT `company_bus_relation_ibfk_1` FOREIGN KEY (`Company_ID`) REFERENCES `bus_company` (`Company_ID`),
  ADD CONSTRAINT `company_bus_relation_ibfk_2` FOREIGN KEY (`Bus_no`) REFERENCES `bus` (`Bus_no`);

--
-- Constraints for table `company_owner_relation`
--
ALTER TABLE `company_owner_relation`
  ADD CONSTRAINT `company_owner_relation_ibfk_1` FOREIGN KEY (`Company_ID`) REFERENCES `bus_company` (`Company_ID`),
  ADD CONSTRAINT `company_owner_relation_ibfk_2` FOREIGN KEY (`Owner_ID`) REFERENCES `company_owner` (`Owner_ID`);

--
-- Constraints for table `drivers`
--
ALTER TABLE `drivers`
  ADD CONSTRAINT `drivers_ibfk_1` FOREIGN KEY (`BusID`) REFERENCES `bus` (`Bus_no`);

--
-- Constraints for table `tickets`
--
ALTER TABLE `tickets`
  ADD CONSTRAINT `tickets_ibfk_1` FOREIGN KEY (`BusID`) REFERENCES `bus` (`Bus_no`),
  ADD CONSTRAINT `tickets_ibfk_2` FOREIGN KEY (`UserID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `tickets_ibfk_3` FOREIGN KEY (`UserID`) REFERENCES `users` (`User_ID`),
  ADD CONSTRAINT `tickets_ibfk_4` FOREIGN KEY (`UserID`) REFERENCES `users` (`User_ID`);

--
-- Constraints for table `transactions`
--
ALTER TABLE `transactions`
  ADD CONSTRAINT `transactions_ibfk_1` FOREIGN KEY (`BusID`) REFERENCES `bus` (`Bus_no`),
  ADD CONSTRAINT `transactions_ibfk_2` FOREIGN KEY (`TicketID`) REFERENCES `tickets` (`TicketID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
