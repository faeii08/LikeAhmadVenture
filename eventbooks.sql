-- phpMyAdmin SQL Dump
-- version 5.2.0
-- https://www.phpmyadmin.net/
--
-- Host: localhost:3306
-- Generation Time: Jan 13, 2025 at 01:12 PM
-- Server version: 8.0.30
-- PHP Version: 8.1.10

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `eventbook`
--

-- --------------------------------------------------------

--
-- Table structure for table `events`
--

CREATE TABLE `events` (
  `eventID` int NOT NULL,
  `eventName` varchar(255) NOT NULL,
  `img` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `events`
--

INSERT INTO `events` (`eventID`, `eventName`, `img`) VALUES
(3, 'preSchool', 'images/GettyImages-1461629187 (1).jpg'),
(5, 'preSchool', 'images/GettyImages-1461629187 (1).jpg'),
(6, 'preSchool', 'images/GettyImages-1461629187 (1).jpg'),
(7, 'high', 'images/GettyImages-1461629187 (1).jpg');

-- --------------------------------------------------------

--
-- Table structure for table `event_details`
--

CREATE TABLE `event_details` (
  `eventDetailID` int NOT NULL,
  `eventID` int DEFAULT NULL,
  `description` text,
  `eventDate` date DEFAULT NULL,
  `price` float NOT NULL,
  `totalParticipant` int NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `event_details`
--

INSERT INTO `event_details` (`eventDetailID`, `eventID`, `description`, `eventDate`, `price`, `totalParticipant`) VALUES
(5, 6, 'membaca', '2025-01-08', 12, 12);

-- --------------------------------------------------------

--
-- Table structure for table `participants`
--

CREATE TABLE `participants` (
  `participantID` int NOT NULL,
  `name` varchar(100) NOT NULL,
  `age` int NOT NULL,
  `userID` int DEFAULT NULL,
  `eventDetailID` int DEFAULT NULL,
  `amount` float DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `participants`
--

INSERT INTO `participants` (`participantID`, `name`, `age`, `userID`, `eventDetailID`, `amount`) VALUES
(22, 'maulana', 13, 4, 5, 12);

-- --------------------------------------------------------

--
-- Table structure for table `payments`
--

CREATE TABLE `payments` (
  `paymentID` int NOT NULL,
  `participantID` int DEFAULT NULL,
  `amount` decimal(10,2) DEFAULT NULL,
  `date` date DEFAULT NULL,
  `payment_method` varchar(50) DEFAULT NULL,
  `status` varchar(50) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `payments`
--

INSERT INTO `payments` (`paymentID`, `participantID`, `amount`, `date`, `payment_method`, `status`) VALUES
(12, 22, '12.00', '2025-01-13', 'fpx', 'paid');

-- --------------------------------------------------------

--
-- Table structure for table `report`
--

CREATE TABLE `report` (
  `reportID` varchar(255) NOT NULL,
  `eventID` varchar(255) NOT NULL,
  `summary` text,
  `dateGenerated` date NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `users`
--

CREATE TABLE `users` (
  `userID` int NOT NULL,
  `name` varchar(255) NOT NULL,
  `username` varchar(50) NOT NULL,
  `email` varchar(100) NOT NULL,
  `address` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `role` varchar(255) NOT NULL,
  `phone` varchar(255) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `users`
--

INSERT INTO `users` (`userID`, `name`, `username`, `email`, `address`, `password`, `role`, `phone`) VALUES
(1, 'wushu', 'wushu', 'wushu@gmail.com', 'no 34, jalan sentosa', '$2y$10$MXxYE8aH3RBbFMHRxWKYAetvf5XOggh8QMc2xmBttjTaFmW0xbT9G', 'Participant', '1637829'),
(2, 'wushu', 'wushu', 'wusha@gmail.com', 'no 34, jalan sentosa', '$2y$10$RkPgxT4l1d7haJTkvPoesuv0E3oxfPI47Zxxa7tqsTc2umxioFdqu', 'Participant', '112433'),
(3, 'wushu', 'wushu', 'wushi@gmail.com', 'no 34, jalan sentosa', '$2y$10$RNibZPHH6deFeBWLfSV/UejI3onfoKDKzItvqEHikBSOGjDuelAg.', 'Participant', '13782982'),
(4, 'malll', 'mal', 'mall@gmail.com', 'no 34, jalan bunga ros', '$2y$10$SqRI.rY.wnX7vRrRv5DqdumqPi/wBHaY4DgOGX891lulWiWnX.Xwi', 'Participant', '11982342'),
(8, 'syuhada', 'syu', 'syuhada00@gmail.com', 'no 56 taman baru lama, jalan lama', '$2y$10$O2rHO76gsMSnLyLhpZOCheZWb46gZi6FfV0I4hwazFI311fKXUqvC', 'Organizer', '012773399');

--
-- Indexes for dumped tables
--

--
-- Indexes for table `events`
--
ALTER TABLE `events`
  ADD PRIMARY KEY (`eventID`);

--
-- Indexes for table `event_details`
--
ALTER TABLE `event_details`
  ADD PRIMARY KEY (`eventDetailID`),
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `participants`
--
ALTER TABLE `participants`
  ADD PRIMARY KEY (`participantID`),
  ADD KEY `userID` (`userID`),
  ADD KEY `fk_eventDetailID` (`eventDetailID`);

--
-- Indexes for table `payments`
--
ALTER TABLE `payments`
  ADD PRIMARY KEY (`paymentID`),
  ADD KEY `participantID` (`participantID`);

--
-- Indexes for table `report`
--
ALTER TABLE `report`
  ADD PRIMARY KEY (`reportID`),
  ADD KEY `eventID` (`eventID`);

--
-- Indexes for table `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`userID`),
  ADD UNIQUE KEY `email` (`email`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `events`
--
ALTER TABLE `events`
  MODIFY `eventID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `event_details`
--
ALTER TABLE `event_details`
  MODIFY `eventDetailID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

--
-- AUTO_INCREMENT for table `participants`
--
ALTER TABLE `participants`
  MODIFY `participantID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=23;

--
-- AUTO_INCREMENT for table `payments`
--
ALTER TABLE `payments`
  MODIFY `paymentID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=13;

--
-- AUTO_INCREMENT for table `users`
--
ALTER TABLE `users`
  MODIFY `userID` int NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=9;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `event_details`
--
ALTER TABLE `event_details`
  ADD CONSTRAINT `event_details_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `events` (`eventID`);

--
-- Constraints for table `participants`
--
ALTER TABLE `participants`
  ADD CONSTRAINT `fk_eventDetailID` FOREIGN KEY (`eventDetailID`) REFERENCES `event_details` (`eventDetailID`),
  ADD CONSTRAINT `participants_ibfk_1` FOREIGN KEY (`userID`) REFERENCES `users` (`userID`) ON DELETE CASCADE;

--
-- Constraints for table `payments`
--
ALTER TABLE `payments`
  ADD CONSTRAINT `payments_ibfk_1` FOREIGN KEY (`participantID`) REFERENCES `participants` (`participantID`);

--
-- Constraints for table `report`
--
ALTER TABLE `report`
  ADD CONSTRAINT `report_ibfk_1` FOREIGN KEY (`eventID`) REFERENCES `event` (`eventID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
