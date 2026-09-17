-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Sep 17, 2026 at 10:38 PM
-- Server version: 12.0.2-MariaDB-ubu2404-log
-- PHP Version: 8.3.26

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Database: `CyberCity`
--

-- --------------------------------------------------------

--
-- Table structure for table `Category`
--

CREATE TABLE `Category` (
  `CategoryName` text NOT NULL,
  `id` int(11) NOT NULL,
  `projectID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Challenges`
--

CREATE TABLE `Challenges` (
  `ID` int(11) NOT NULL,
  `challengeTitle` text DEFAULT NULL,
  `challengeText` text DEFAULT NULL,
  `flag` text NOT NULL,
  `pointsValue` int(11) NOT NULL,
  `moduleName` varchar(255) DEFAULT NULL,
  `moduleValue` varchar(255) DEFAULT NULL,
  `dockerChallengeID` varchar(255) DEFAULT NULL,
  `container` int(11) DEFAULT NULL,
  `Image` text DEFAULT NULL,
  `Enabled` tinyint(1) DEFAULT 1,
  `categoryID` int(11) DEFAULT NULL,
  `files` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ContactUs`
--

CREATE TABLE `ContactUs` (
  `ID` int(11) NOT NULL,
  `Username` text NOT NULL,
  `Email` text NOT NULL,
  `IsRead` tinyint(1) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `DockerContainers`
--

CREATE TABLE `DockerContainers` (
  `ID` int(11) NOT NULL,
  `timeInitialised` timestamp NOT NULL,
  `userID` int(11) NOT NULL,
  `challengeID` text NOT NULL,
  `port` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `eventLog`
--

CREATE TABLE `eventLog` (
  `id` int(11) NOT NULL,
  `moduleName` text NOT NULL,
  `eventText` text NOT NULL,
  `datePosted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Learn`
--

CREATE TABLE `Learn` (
  `ID` int(11) NOT NULL,
  `Name` text NOT NULL,
  `Icon` text NOT NULL,
  `Text` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ModuleData`
--

CREATE TABLE `ModuleData` (
  `id` int(11) NOT NULL,
  `ModuleID` int(11) DEFAULT NULL,
  `DateTime` datetime DEFAULT NULL,
  `Data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `ProjectChallenges`
--

CREATE TABLE `ProjectChallenges` (
  `id` int(11) NOT NULL,
  `challenge_id` int(11) NOT NULL,
  `project_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `project_id` int(11) NOT NULL,
  `project_name` text NOT NULL,
  `project_title` text NOT NULL,
  `project_description` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `UserChallenges`
--

CREATE TABLE `UserChallenges` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `challengeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

-- --------------------------------------------------------

--
-- Table structure for table `Users`
--

CREATE TABLE `Users` (
  `ID` int(11) NOT NULL,
  `Username` text NOT NULL,
  `user_email` varchar(255) NOT NULL,
  `profile_picture` longblob DEFAULT NULL,
  `HashedPassword` text NOT NULL,
  `AccessLevel` int(11) NOT NULL,
  `Enabled` tinyint(1) NOT NULL,
  `Score` int(11) NOT NULL DEFAULT 0
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Challenges`
--
ALTER TABLE `Challenges`
  ADD PRIMARY KEY (`ID`),
  ADD KEY `xChallenges_Category_id_fk` (`categoryID`);

--
-- Indexes for table `ContactUs`
--
ALTER TABLE `ContactUs`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `DockerContainers`
--
ALTER TABLE `DockerContainers`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `eventLog`
--
ALTER TABLE `eventLog`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Learn`
--
ALTER TABLE `Learn`
  ADD PRIMARY KEY (`ID`);

--
-- Indexes for table `ModuleData`
--
ALTER TABLE `ModuleData`
  ADD PRIMARY KEY (`id`),
  ADD KEY `ModuleData_RegisteredModules_ID_fk` (`ModuleID`);

--
-- Indexes for table `ProjectChallenges`
--
ALTER TABLE `ProjectChallenges`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `Projects`
--
ALTER TABLE `Projects`
  ADD PRIMARY KEY (`project_id`);

--
-- Indexes for table `UserChallenges`
--
ALTER TABLE `UserChallenges`
  ADD PRIMARY KEY (`id`),
  ADD KEY `UserChallenges_Challenges_ID_fk` (`challengeID`),
  ADD KEY `UserChallenges_Users_ID_fk` (`userID`);

--
-- Indexes for table `Users`
--
ALTER TABLE `Users`
  ADD PRIMARY KEY (`ID`);

--
-- AUTO_INCREMENT for dumped tables
--

--
-- AUTO_INCREMENT for table `Category`
--
ALTER TABLE `Category`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Challenges`
--
ALTER TABLE `Challenges`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ContactUs`
--
ALTER TABLE `ContactUs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `DockerContainers`
--
ALTER TABLE `DockerContainers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `eventLog`
--
ALTER TABLE `eventLog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Learn`
--
ALTER TABLE `Learn`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ModuleData`
--
ALTER TABLE `ModuleData`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ProjectChallenges`
--
ALTER TABLE `ProjectChallenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `UserChallenges`
--
ALTER TABLE `UserChallenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT;

--
-- Constraints for dumped tables
--

--
-- Constraints for table `Challenges`
--
ALTER TABLE `Challenges`
  ADD CONSTRAINT `xChallenges_Category_id_fk` FOREIGN KEY (`categoryID`) REFERENCES `Category` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
