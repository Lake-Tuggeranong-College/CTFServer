-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Feb 08, 2026 at 11:34 AM
-- Server version: 12.1.2-MariaDB-ubu2404-log
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

--
-- Dumping data for table `Category`
--

INSERT INTO `Category` (`CategoryName`, `id`, `projectID`) VALUES
('Tutorial', 1, 2025),
('Networking', 2, 2025),
('Cryptology', 3, 2025),
('OSINT', 4, 2025),
('Hex', 5, 2025),
('Web', 6, 2025),
('WIP', 8, 2024);

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

--
-- Dumping data for table `Challenges`
--

INSERT INTO `Challenges` (`ID`, `challengeTitle`, `challengeText`, `flag`, `pointsValue`, `moduleName`, `moduleValue`, `dockerChallengeID`, `container`, `Image`, `Enabled`, `categoryID`, `files`) VALUES
(1, 'Traffic Jammed', 'We received a call about the city\'s traffic lights going haywire! This is a nuisance to the city, citizens as well as a major safety hazard. Being the city\'s on-call electrician, we have placed it upon you to rewire the lights. Can you successfully fix the uncoordinated lights and bring peace back to to the road? Your credentials are:', 'CTF{operation_greenwave}', 5, 'TrafficLights', '1', NULL, 3, 'trafficlights4.gif', 1, 2, NULL),
(2, 'Open Sesame', 'A citizen has made an urgent distress call from their home. Initial scans suggest they\'ve lost the key to their garage and are now trapped inside with their car, unable to get to work. As a part of the emergency response team, you have been assigned the task of remotely opening the locked door, without causing any damage to it or the garage, as per the request of the citizen. Will you be able to get it safely unlocked?', 'CTF{Alohomora}', 5, 'GarageDoor', '0', NULL, NULL, 'garagedoor4.gif', 1, 2, NULL),
(3, 'Alarm Anomaly', 'A burglar briefly disarmed the police station’s alarm. The suspect is in custody, but the alarm is still offline. You’ve been called in to bring it back. A suspicious file named Alarm.png was left behind. It looks normal… but is it?\n\nUser: RoboCop\nPassword: TotallySecure01', 'CTF{beep_beep}', 5, 'Alarm', '0', 'alarmAnomaly', 1, 'buzzer.jpg', 1, 3, NULL),
(4, 'Turbine Takeover', 'The city\'s wind turbine has broken down! Being the city\'s main source of power, everyone has entered a state of panic. Fears are growing as the night approaches, threatening to plunge the city into total darkness. As one of the few trained windtechs, and with the clock ticking, you have been assigned to get the turbine operating once again. Can you do it before nightfall arrives? \n\nWhile combing through the turbine’s diagnostic logs, your team uncovered a strange, out-of-place file buried deep in an old backup directory. It wasn’t referenced in any current maintenance:', 'CTF{w1ndm1ll_w1nner}', 5, 'Windmill', '55837', NULL, NULL, 'windmill4.gif', 1, 3, '../../../assets/challengeFiles/control_terminal_backup.zip'),
(5, 'Train Turmoil', 'The CyberCity rail system has gone haywire overnight. A rogue operator locked the train control panel behind a secure container and vanished. The morning commute is in chaos, and the city needs you to get the train back on track.\n\nYour mission: brute force your way into the system, locate the hidden control script, and activate the train. If successful, the train will complete its route and display the flag on the station’s E-Ink board.', 'CTF{Ah_Ch00Ch00}', 5, 'Train', '1', NULL, NULL, NULL, 1, 6, NULL),
(6, 'wee lcd', 'its the lcd', 'CTF{yay_lights}', 5, 'LCD', '0', NULL, NULL, NULL, 1, 6, NULL);

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

--
-- Dumping data for table `ContactUs`
--

INSERT INTO `ContactUs` (`ID`, `Username`, `Email`, `IsRead`) VALUES
(1, 'Oliver', 'test123@gmail.com', 0),
(2, 'Oliver', 'teser1@gmail.com', 0),
(3, 'fef', 'test123', 0),
(4, 'dewf', 'test12', 0),
(5, 'agfadfga', 'ryan.cather@ed.act.edu.au', 0),
(6, 'User21', '27@gmail.com', 1),
(7, 'saxo', 'test.com', 1),
(8, 'Oliver', '123@test.com', 1),
(9, 'no', 'doesthisevenwork@notgmail.com', 1),
(10, 'Problum chiels', 'tjis page isnt working', 0);

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

--
-- Dumping data for table `DockerContainers`
--

INSERT INTO `DockerContainers` (`ID`, `timeInitialised`, `userID`, `challengeID`, `port`) VALUES
(204, '2026-02-07 14:56:32', 6, '3', 17010);

-- --------------------------------------------------------

--
-- Table structure for table `eventLog`
--

CREATE TABLE `eventLog` (
  `id` int(11) NOT NULL,
  `userName` text NOT NULL,
  `eventText` text NOT NULL,
  `datePosted` datetime NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `eventLog`
--

INSERT INTO `eventLog` (`id`, `userName`, `eventText`, `datePosted`) VALUES
(951, 'esp32', 'ESP32 is online.', '2026-02-08 10:08:01'),
(952, 'esp32', 'ESP32 setup complete.', '2026-02-08 10:08:01'),
(953, 'esp32', 'Windmill is online.', '2026-02-08 10:10:32'),
(954, 'esp32', 'Windmill setup complete.', '2026-02-08 10:10:32'),
(955, 'esp32', 'Windmill is online.', '2026-02-08 10:12:51'),
(956, 'esp32', 'Windmill setup complete.', '2026-02-08 10:12:51'),
(957, 'esp32', 'Windmill is online.', '2026-02-08 10:15:54'),
(958, 'esp32', 'Windmill setup complete.', '2026-02-08 10:15:54'),
(959, 'esp32', 'Windmill is online.', '2026-02-08 10:30:28'),
(960, 'esp32', 'Windmill setup complete.', '2026-02-08 10:30:28'),
(961, 'esp32', 'Windmill is online.', '2026-02-08 10:31:03'),
(962, 'esp32', 'Windmill is online.', '2026-02-08 10:38:36');

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

--
-- Dumping data for table `Learn`
--

INSERT INTO `Learn` (`ID`, `Name`, `Icon`, `Text`) VALUES
(1, 'Inspect Element (Fire Department)', 'FireDept.jpg', '<p> All websites are built with something called HTML, HTML is a markdown language, like all other kinds of markdown/programming languages, HTML has the ability to make comments in the code, these comments are not visible on the end product but is visible in the code. </p> <p> Thankfully all browsers have the ability to see the HTML code that made the website </p> <iframe width=\"760\" height=\"515\" src=\"https://www.youtube.com/embed/csy5neBsItY?si=sqIKRd6sElKr-eBP\" title=\"YouTube video player\" frameborder=\"0\" allow=\"accelerometer; autoplay; clipboard-write; encrypted-media; gyroscope; picture-in-picture; web-share\" allowfullscreen></iframe>'),
(5, 'Caesar Cipher (Windmill)', 'Windmill.jpg', '<p> Cryptography is the art of encrypting data. Encryption is making data not readable unless if the recipient of the data knows hows to unencrypt the data. </p> <p> Ceaser Cipher is a type of encryption named after Julius Caesar, who used it for military messages. </p> <p> Try using the website below to encrypt and even decrypt messages. </p> <iframe src=\"https://cryptii.com/pipes/caesar-cipher\" width=\"1500\" height=\"515\"=></iframe>'),
(7, 'Hex Data (Train Timer)', 'TrainLCD.jpg', '<p> Hex/Hexadecimal is the human friendly version of <a href=\"https://en.wikipedia.org/wiki/Binary_code\" target=\"_blank\"> binary data </a> This data is represented with the symbols of 0-9 (representing data values between 0 to 9) and A-F (representing data values between 10 to 15) </p> <p> While all files have hex values, not all of the hex values in the file may be used by the program using the file. </p> <p> Using the online hex editor below, download and open the image from the challenge and see if you can spot the hidden data </p> <iframe src=\"https://hexed.it/\" width=\"1500\" height=\"515\"=></iframe>');

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

--
-- Dumping data for table `ProjectChallenges`
--

INSERT INTO `ProjectChallenges` (`id`, `challenge_id`, `project_id`) VALUES
(1, 1, 2025),
(40, 2, 2024),
(41, 2, 2025),
(42, 3, 2025),
(43, 4, 2025);

-- --------------------------------------------------------

--
-- Table structure for table `Projects`
--

CREATE TABLE `Projects` (
  `project_id` int(11) NOT NULL,
  `project_name` text NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `Projects`
--

INSERT INTO `Projects` (`project_id`, `project_name`) VALUES
(2024, '2024 - Biolab'),
(2025, '2025 - Nuclear Disaster');

-- --------------------------------------------------------

--
-- Table structure for table `UserChallenges`
--

CREATE TABLE `UserChallenges` (
  `id` int(11) NOT NULL,
  `userID` int(11) DEFAULT NULL,
  `challengeID` int(11) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `UserChallenges`
--

INSERT INTO `UserChallenges` (`id`, `userID`, `challengeID`) VALUES
(266, 31, 3),
(267, 133, 3),
(268, 136, 4),
(269, 141, 4),
(270, 125, 3),
(271, 133, 4),
(272, 141, 3),
(273, 157, 3),
(274, 84, 3),
(275, 84, 4);

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
-- Dumping data for table `Users`
--

INSERT INTO `Users` (`ID`, `Username`, `user_email`, `profile_picture`, `HashedPassword`, `AccessLevel`, `Enabled`, `Score`) VALUES
(159, 'rhys', '0328198@schoolsnet.act.edu.au', NULL, '$2y$10$p8OCJxBjhAuvWroRM37GW.t19c2i.sQzMeSzZz3MfPN8LxhsYolrK', 2, 1, 1009),
(161, 'BigMan', '0502741@schoolsnet.act.edu.au', NULL, '$2y$10$XqHW2.B8YMqYJOMdUd8EaOdrNZsZuSjg4L1FInFO8gyJBuSNbz99.', 2, 1, 10829),
(162, 'Voitek', '0363074@schoolsnet.act.edu.au', NULL, '$2y$10$yJbi4Txx1yQS4y5GE6n22O/fKRE/cH7QYroYSvW.0WaLAK1O2K6NW', 2, 1, 0),
(163, 'Reign-Dears', 'siennajdears@gmail.com.au', NULL, '$2y$10$MH8/SsBnbtU59YluldcM2.2i8EPWJzmoQRFy4hpi.e7KBAODNzamy', 2, 1, 5),
(164, '1020500', '1020500@schoolsnet.act.edu.au', NULL, '$2y$10$Qm3gdY7lMun2hT1Exk8TSeFFzXUzqBu8I6bJ.4y/VTg5/kqgZuPZ6', 2, 1, -94),
(165, 'Isaac', '0257743@schoolsnet.act.edu.au', NULL, '$2y$10$5yIndwTrdDuDCMV2KT50hO6IivQ8Ynu0NBiGoWooA16s7EeY0shHW', 2, 1, 776),
(166, 'Jamez', '0706720@schoolsnet.act.edu', NULL, '$2y$10$bz3uL.MC58bbr01LaCHjVOjs8Ej2GUNx0U5gc7nl0POHwS4gEF9XC', 2, 1, 124),
(167, 'Qianwen', 'qianwen@qianwen.com', NULL, '$2y$10$0ljmzulPjXYNLLdBO4uGhev0cC156cMNUAT29DDzVHy/VVndTcc1m', 2, 1, 684),
(168, 'Register', 'Register@Register.com', NULL, '$2y$10$UWmMxSFwOHzQyJ6NzhTPku4rwsijYdrlBdMVilo2NgbaIvhzVn/S.', 2, 1, 8),
(169, 'caelenbyrnes', '0628050@schoolsnet.act.edu.au', NULL, '$2y$10$khqLCcLoTmyWMMv3QiZ.4OrYUk75vj0fKSNkgmw60c6R56LhxD4Da', 2, 1, 67),
(170, 'Snowy', '0852604@schoolsnet.act.edu.au', NULL, '$2y$10$8jtqTMLf.i/C58oy02utDetbJ4xORLWxoAoNYpUep4AFFHWSv9eNm', 2, 1, 0),
(172, 'Dion', '0363074@schoolsnet.act.edu.au', NULL, '$2y$10$M6aghVx3Yh4FiTc6qYdbvultXPPTjAPQKVKCWxZKxTaPQ24qsM1ia', 2, 1, 7),
(173, 'ZakS', '0592695@schoolsnet.act.edu.au', NULL, '$2y$10$uwM9nunL.GYFk2JCkOutWOJdqWx/Nbqkgm73.J6IaZ6pX0HUz6zhO', 2, 1, 5),
(174, 'Clive', '1020511@schoolsnet.act.edu.au', NULL, '$2y$10$yOeIT7p9V0Z8SWo2k.h7N..HUlcIEvmEpJIkGVNQ69IY1udn3X4.e', 2, 1, 5),
(175, 'James', '0852520@schoolsnet.act.edu.au', NULL, '$2y$10$n07gztYujgQ9Iu8n6eRsI.awFRa/hLEy9svtsqd4V427Z1p853mvi', 2, 1, 5),
(176, 'John C. Smith', '9949747@schoolsnet.act.edu.au', NULL, '$2y$10$xgZtX5S9gXQsNve/g/JKh.Sj2ULvJWYhLknY5Pv5MCqQeSKyBjXH2', 2, 1, -1999332),
(177, 'Ryan', 'ryan.cather@ed.act.edu.au', NULL, '$2y$10$S7b/D8EVzd8j4joq1NwkwOFff7fgAHiqmoD1fj6I/DaVOYqSrZYy6', 2, 1, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `Challenges`
--
ALTER TABLE `Challenges`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=102;

--
-- AUTO_INCREMENT for table `ContactUs`
--
ALTER TABLE `ContactUs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `DockerContainers`
--
ALTER TABLE `DockerContainers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=205;

--
-- AUTO_INCREMENT for table `eventLog`
--
ALTER TABLE `eventLog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=963;

--
-- AUTO_INCREMENT for table `Learn`
--
ALTER TABLE `Learn`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ModuleData`
--
ALTER TABLE `ModuleData`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT;

--
-- AUTO_INCREMENT for table `ProjectChallenges`
--
ALTER TABLE `ProjectChallenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=48;

--
-- AUTO_INCREMENT for table `UserChallenges`
--
ALTER TABLE `UserChallenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=276;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

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
