-- phpMyAdmin SQL Dump
-- version 5.2.2
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: Oct 13, 2025 at 02:25 AM
-- Server version: 12.0.2-MariaDB-ubu2404
-- PHP Version: 8.2.27

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
-- Table structure for table `ChallengeData`
--

CREATE TABLE `ChallengeData` (
  `id` int(11) NOT NULL,
  `moduleID` int(11) DEFAULT NULL,
  `reference` text DEFAULT NULL,
  `data` text DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;

--
-- Dumping data for table `ChallengeData`
--

INSERT INTO `ChallengeData` (`id`, `moduleID`, `reference`, `data`) VALUES
(1, 44, 'Email_1: John.R: \'Don\'t forget to have no repeating charaters Xen\'', 'Email 1'),
(2, 44, 'E_2', 'Email 2'),
(3, 44, 'E_3', 'Email 3'),
(4, 44, 'E_4', 'Email 4'),
(5, 44, 'E_5', 'Email 5');

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
(4, 'Turbine Takeover', 'The city\'s wind turbine has broken down! Being the city\'s main source of power, everyone has entered a state of panic. Fears are growing as the night approaches, threatening to plunge the city into total darkness. As one of the few trained windtechs, and with the clock ticking, you have been assigned to get the turbine operating once again. Can you do it before nightfall arrives? \n\nWhile combing through the turbine’s diagnostic logs, your team uncovered a strange, out-of-place file buried deep in an old backup directory. It wasn’t referenced in any current maintenance:', 'CTF{w1ndm1ll_w1nner}', 5, 'Windmill', '1', NULL, NULL, 'windmill4.gif', 1, 3, '/CyberCity/website/assets/Files/control_terminal_backup.zip'),
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
(1, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2023-12-01 14:00:02'),
(2, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.50', '2023-12-01 14:00:07'),
(3, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:04:35'),
(4, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:04:37'),
(5, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:07:43'),
(6, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2023-12-01 14:55:41'),
(7, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2023-12-04 11:34:03'),
(8, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.51', '2023-12-04 11:34:04'),
(9, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2023-12-04 11:55:39'),
(10, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.51', '2023-12-04 11:55:40'),
(11, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.51', '2023-12-04 13:30:11'),
(12, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-02-07 14:40:08'),
(13, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.50', '2024-02-07 14:40:08'),
(14, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-03-08 11:23:53'),
(15, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-03-08 11:24:01'),
(16, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-03-08 11:35:34'),
(17, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-03-08 12:02:44'),
(18, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2024-04-16 15:04:35'),
(19, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2024-04-18 10:46:54'),
(20, 'Unknown', 'Attempted to access eventLog.php via GET request.', '2024-04-18 10:51:58'),
(21, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:06:22'),
(22, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:22:35'),
(23, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:25:14'),
(24, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:34:41'),
(25, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:34:49'),
(26, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:35:38'),
(27, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:36:39'),
(28, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:36:45'),
(29, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:46:11'),
(30, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:46:12'),
(31, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 09:50:34'),
(32, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 09:50:37'),
(33, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-15 10:19:24'),
(34, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-15 10:19:25'),
(35, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-05-16 09:17:55'),
(36, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.15', '2024-05-16 09:18:20'),
(37, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.42', '2024-06-04 10:29:01'),
(38, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-07-31 14:51:32'),
(39, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.4', '2024-07-31 14:51:34'),
(40, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-08-16 11:19:52'),
(41, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-08-16 11:21:03'),
(42, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-08-16 11:22:49'),
(43, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.7', '2024-08-16 11:22:51'),
(44, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-09-02 09:44:16'),
(45, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.110', '2024-09-02 09:44:16'),
(46, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2024-11-06 13:42:02'),
(47, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.103', '2024-11-06 13:42:03'),
(48, 'Mpuk tl, pm fvb jhu...', 'Monitoring Initialised. Avoid squishy biologicals at all costs.', '2025-03-24 12:46:25'),
(49, 'Mpuk tl, pm fvb jhu...', 'IP: 192.168.1.108', '2025-03-24 12:46:25');

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
(1, 'Tester', '', NULL, '$2y$10$IY.HhoyfwNp8QbIx.YelyO2otFeMu4vjVLOwmLOIVoM0J.ANwMsNm', 1, 0, 0),
(2, 'Oliver', '', NULL, '$2y$10$00IJ8x3VLvaJBztpi05iTOoTY4IPZ/gDYGuthw56AfzJ0Bs.33Xd6', 1, 1, 0),
(3, 'DisabledTest', '', NULL, '$2y$10$Zpvt38iUgYypuZ3pGkqBy.nL0ZRwht73OATIwgC8YjAmxHIrS2dae', 1, 0, 0),
(4, 'test1', '', NULL, '$2y$10$Xkf08qDzwW8PGn9s.7iIDeoSnRgcIAMkJhnE2TYHnRL26O20fKou.', 1, 0, 0),
(5, 'test2', '', NULL, '$2y$10$EXcLN0wu158qQRtt//KA3OtIuLUNo02/F.XYFaxLOTwREbsjDcnoe', 1, 0, 0),
(6, 'ryan.cather@ed.act.edu.au', 'ryan.cather@ed.act.edu.au', NULL, '$2y$10$LYpcgqEzulNLIhn/LxDKiuDCmd4KZQclGdfSrtZ42VouqR7FfAk0S', 2, 1, 75),
(7, 'sax', '', NULL, '$2y$10$sGTeppxswv.By0Uee7E03OniHgXPhrmt12DKNcarsw2suMRlKhPv6', 1, 1, 0),
(8, 'Dylan', '', NULL, '$2y$10$l.hzBaQ.MQnm2E06u83orO3KrGlki22KbCRZpH6IdZomW.i82715y', 1, 1, 0),
(9, 'Admintester', '', NULL, '$2y$10$vksjcXZYce9LAUi./7CCpucBR9rGH06HHqvlGKJ5MRGbMqT/hgqy6', 1, 1, 0),
(11, 'Cburrows', '', NULL, '$2y$10$PEJxEKx3Y443NX.2Qp8/F.VkK.iJfY/xngRnlhSYJF2/SqEKQRvyi', 1, 1, 0),
(15, 'Admin', '', NULL, '$2y$10$DQdudJ0ngTrM9DlxOl2ipehaQJyqKWSb5DJ6zeMOnPStMlz/hgLt.', 1, 1, 0),
(16, 'User', '', NULL, '$2y$10$v5DY/DQxszHAF1NwWwB9NO0vuIYQ6Hl9x2QT.UKuL3xuLPSubbtQG', 1, 0, 0),
(18, 'Dylan2', '', NULL, '$2y$10$fWNhGR9cQ621HRnJZhcQ3OLmad9Cy8kImE9gE4goI/rDWzbfegDua', 1, 0, 0),
(19, '111', '', NULL, '$2y$10$W9iT7pZkVezdCgTS7aaTq.ls7qTjmPv9pKKzdPUEMz7omEyEKeTD.', 1, 0, 0),
(22, 'J0el', '', NULL, '$2y$10$e6brSyWAR/xWUUjnL.j3z.mQyahGfM84FPU1Cro0wzYPqB7ZxKG.a', 1, 1, 0),
(23, 'saxo', '', NULL, '$2y$10$2Zxz0/inz1oU654aPX6VIOKm.siUZXiYRoHn5h/ns6WxiVYsN98Iu', 1, 0, 0),
(24, '10liter', '', NULL, '$2y$10$FNz2nh6RiXkYFDXBhtGlC.khv2eWSGUUWJ7QHOjxYXOPTI8ohITcK', 1, 0, 0),
(25, '1234', '', NULL, '$2y$10$GahotWx253OeBrqSrkOnz.zfwCyF1BAitz2xCCshw6gaUhnpghRhC', 1, 1, 0),
(28, 'ihatemylife', '', NULL, '$2y$10$j/gnckca6Bei2RfbD.p.RORi8JOkvNtsmHZG9iBGdy/4dpdcgkbhK', 1, 0, 0),
(29, 'John', '', NULL, '$2y$10$z8jpokEH6jc9KUBsCi/3h.5YjrGJ3NhBALXjqf6RajGCMJb53X19C', 1, 0, 0),
(30, 'joel', '', NULL, '$2y$10$dnHq/iWYqZXs.841P5srpO4dBUMxq.CiQ31SGOcOo48aUmo9aMeHC', 1, 0, 0),
(31, '123', '', NULL, '$2y$10$hzJa.d4RAGH4tJ9UY/btru3QCLAaurTGBAygEs.KVktkZscvC..XK', 1, 0, 35),
(32, 'Ryan', '', NULL, '$2y$10$v1ytfm4z4COEtIlPAnuehuwiT5kEjQojhcsTNsc9L.SNLI8/kzbn2', 1, 1, 0),
(33, 'User21', '', NULL, '$2y$10$65YkkDHIz8AOkUPiIBNioeIw4JxKSyJiyARyMaWi53z2r0bCSEni.', 1, 0, 0),
(34, 'user5', '', NULL, '$2y$10$DHz8g/e9pSwyRFBir2VAQe9E2sGWEJtLzFgLe/jLdpLL8Bfgl.yui', 1, 1, 0),
(35, 'user22', '', NULL, '$2y$10$DTNG3152M8HTLEJ/KRWEl.CPKaz2dTvVhFiV/48P.XclY5s5ysLaq', 1, 0, 0),
(36, '115', '', NULL, '$2y$10$6Qq7G3mgfGKocnAs8IgxEePmLmDkqoEjjxQf7lhoVi.C6cPUphRhq', 1, 0, 0),
(37, 'User 1', '', NULL, '$2y$10$a2nImwwya3eByKPSNXBvauN2lManMdsiZYc4z49ZqPwncAgO.pKda', 1, 0, 0),
(38, 'Cyber city player', '', NULL, '$2y$10$KNGhVW1ceZNnOYwhE4lrauZ4Vn/RZSZLqzPhJ3cVFzQncn6D/pLu.', 1, 0, 0),
(39, 'qwe', '', NULL, '$2y$10$ghYf4ORuPzXImjf1G85SZeUGp6qRi7d3p/pilm3Ne5MlD1r4mlawO', 1, 0, 0),
(40, 'ajaysayer', '', NULL, '$2y$10$WbunAJfH79uUU68nVa57t.9pzjgRYCBW4TWzjC7rBJId/Xk44d6Fy', 1, 0, 95),
(41, 'FakeJon25', '', NULL, '$2y$10$xBjndd8oV2JcbI339eJDwetexogjFcyJU0/7PsFYYZYg7UM8FVVfK', 1, 0, 1130),
(42, 'Glovania', '9937412@schoolsnet.act.edu.au', NULL, '$2y$10$0jreLrJrGnS2ZD3Yade4zOpTMA8ieRMirXwqzLkK3Uj6aRRzDiVlW', 1, 0, 25),
(43, 'Mcflys', '', NULL, '$2y$10$44AW1lD7.SmlCMlphkcT8Os5y16XEMO1JR93MHOVKUOAmKAJAP./G', 1, 1, 345),
(44, 'Mythie2Oscar', '', NULL, '$2y$10$28cdrcjpXad8Ze0et6OFAeHTRUs1gr/i3bKm/vCohY8tQ87GANKju', 1, 1, 0),
(45, 'lily', '', NULL, '$2y$10$xOsoUxnqxmDfo6wrw0Kv1eITTFfbcjjSSrgDr3sc1K/8e/3vP/4Li', 1, 0, 0),
(46, 'Nicholas', '', NULL, '$2y$10$DwrJfifGBoag1hgg7WxAEetxH6BBK0g/H.xY2tTiTsjWaK8lKnNHy', 1, 1, 0),
(47, 'rileytraise', '', NULL, '$2y$10$N1ZsFRDt3SDKow2gxFWt1O8gGxUz5ub0b1WNHUiDvYxZbTRT3.n.G', 1, 0, 270),
(48, 'Jordan M', '', NULL, '$2y$10$vVP9o9KJmuRsPC.CWlWrvu/jFqkrWS6QPINuGbrVfm63S5DK095FS', 1, 0, 0),
(49, 'ConnorMcHarry', '', NULL, '$2y$10$xdWeT9MdhMnNYkCz6b8LU.XTZgjqu724Hki8FWtK1kXZnE5Y3Pq.6', 1, 1, 0),
(50, 'Hayden Ceely', '', NULL, '$2y$10$paBP/AxwvE.7Q3ivSPTZk.L6iZJ.iENaMIqZiUb/TFcMyMgA3PqJ6', 1, 0, 0),
(51, 'haydon', 'test@gmail.com', NULL, '$2y$10$HDwrD1DcL3SQsz4pfEWaOO825f35ROfv5myUXUoL9ad2BFSfBImuW', 1, 0, 460),
(52, 'Calwell', '', NULL, '$2y$10$Mnwyirph1JMKyDAmZuU0A.Ne0EL0CN1pQ4WqncLq4EoAWzciJJk2e', 1, 1, 480),
(53, 'haydon123', 'test@gmail.com', NULL, '$2y$10$jOTd.MtMquvgsjBJhguKIe3Bb2fD1.qwNF7TsDgsM.avK/052XnDq', 1, 1, 312),
(54, 'Hayden C', '', NULL, '$2y$10$d1LtBu//55NlVt9p.wSsEOfMcHb/uEpJ28gVcGwdSIALvGN5JhpxC', 1, 0, 0),
(55, 'FakeAdmin26', 'Fake@email.com', NULL, '$2y$10$MBwu3WXJKtIAJl5OVSTvSu4mVtfkY0eWdiuItgNzSgo5ajN7eOJmC', 1, 1, 45),
(56, 'Nicholas.', '', NULL, '$2y$10$ML5fj8IEm39LFPhkDuI55e3Pf.sSmZtEP.yce7klXI0SdzsAfrOc2', 1, 0, 0),
(57, '4200184', '', NULL, '$2y$10$7bmRWLQ1DTYftunGoKRGc.TE7kj6J8E5WSBZLKbJiDPjhakh15yKu', 1, 0, 0),
(58, 'Hayden', '', NULL, '$2y$10$zfRAHy7mJUO71sA5icnqueVblSVlgApPGa7jDqkegpYW/tINnw8gG', 1, 0, 0),
(60, 'ajaytest', '', NULL, '$2y$10$/a1.tDj/6/fsrZCa0UVKDeGidkItUJ912hckMMgRpQIDxAmEnQS.q', 1, 0, 0),
(61, 'test123', '', NULL, '$2y$10$vHmpuVpYyDy6XaY9e05NzOEMiO2W2hy.Gt.dYCs2bu9ddTMFaedLK', 1, 0, 0),
(62, '&lt;h1&gt;name&lt;/h1&gt;', '', NULL, '$2y$10$QJCJs4dwrSzlpYPlTPmlJuzq9oa4aI2sNfVn4Dwo9xVBjQUayHeo2', 1, 0, 0),
(64, 'haydon1', '', NULL, '$2y$10$ke5d9KPyhBcM25gJR/EGAuQEbXjme/bEHSoc0QpyDUNelj7M9U/AS', 1, 0, 0),
(65, 'DomPuric', '', NULL, '$2y$10$5oODmNC8QOXb7zu29Fh/A.80rMhJvEN.gxxnnFmRbEtuEqz8EwYVC', 1, 0, 0),
(66, 'lady gaga', '', NULL, '$2y$10$ZDRr52onESVtKJg8sYxbneqWAZEFt9lNasRANT8xxT96YcA0PLwt6', 1, 0, 0),
(67, 'Collo', '', NULL, '$2y$10$FHvy4/Yab6iw47.s8lYajO75YOX796x28qQ0tDNylU7jMAG0cFF76', 1, 0, 0),
(68, 'Anastasia', '', NULL, '$2y$10$kCeGhbA7Z8fDHxDG6cKMc.X4u3vw479.vPf4L.cVD6sZ7wQED90Uu', 1, 0, 0),
(69, 'Alexflis', '', NULL, '$2y$10$ZcrKSZ29oIpAzUT0ftQPcepHgseALc0IT6Z/TinaFalwYuxx2HKX6', 1, 0, 0),
(70, 'Sam', '', NULL, '$2y$10$XZEaMtoi5IYYRsxYnBcE/.VHAB9bXqIdDWuP6U6uWxZji1xvUQyWS', 1, 0, 0),
(71, 'Finlay', '', NULL, '$2y$10$kUFgQG/o3wjnR8gHagNCf.QeuT8EPiZqRyavl3MaK0t2/V6a6kae6', 1, 0, 0),
(72, 'ibrahim', '', NULL, '$2y$10$YmGManeLLr/b5j0tHJ/i/OgC2/9InsuICOS9wENXx4/MgCZ2vKVDC', 1, 0, 0),
(73, 'binxmybeloved', '', NULL, '$2y$10$XH6dRwmhCkmtemifRty9TesccdsKkfQf.0IiZWnNFW4nJqeVS.SVe', 1, 0, 0),
(74, 'H20990', '', NULL, '$2y$10$O1UqrN3JfJANv/C0/0qfmOnXHzzqmnwQPV8FfvgBaAs8SrBL7Gqmm', 1, 0, 0),
(75, '0321841', '', NULL, '$2y$10$g94S.XGfssXYEZSeU/2uPuyJHkoSLfcj5iyZQ9NHyEs5pI5QD9xOq', 1, 0, 0),
(76, '0321856', '', NULL, '$2y$10$usASdYv1jeceS46AoiXhyOlShnta5eQyK3CUf3gdUdBRzBeilaZmW', 1, 0, 0),
(77, 'julian', '', NULL, '$2y$10$ien3u4xz3WlePXkCv/jXquEXo55h5XykNpuqZ51HGB7QNMUx79BxW', 1, 0, 0),
(78, 'AJ', '', NULL, '$2y$10$fEn72IKNWfXnuFJNqjzkD.m54gUymARDl6stFQBbTh/IEM31a3ovq', 1, 0, 0),
(79, 'oofeman', '', NULL, '$2y$10$zPB9VmR4OFRCgO8UxvuczO2h2fClrVTPsEwSWl5K08Vk8itdfQvla', 1, 0, 0),
(80, 'andy', '', NULL, '$2y$10$elKA5R1aP74ORKeMx.27geQmWwXQ2BO9TpF.QUUitqtv0IXWyG5fi', 1, 0, 0),
(81, 'Paul', '', NULL, '$2y$10$kIOEuMWi2/pW9kgqw7RflekETq13P4k5tkntmrV1xhfoZWz4SnECq', 1, 0, 0),
(82, 'JaydenMC', '', NULL, '$2y$10$unp11TL0y6HfxFHhC9W4EOl3j5k8PitE7CmtmcinNpfoFptgUN65G', 1, 0, 0),
(83, 'Hackzer_will', '', NULL, '$2y$10$DeE7mUi.ToYaAjdiwJOfKeuX4adCgcyoRzH3/wIhG8l7.fTbFXs7S', 1, 0, 0),
(84, 'NadalR7', '', NULL, '$2y$10$eDC6i15oe4InaUw/jBOexen6bIwOIyAyEm72s6YRDBWXj7zrp2wPC', 1, 0, 15),
(85, 'Oscar', '', NULL, '$2y$10$xUqv/.QHseXs/bsNJjD5/uhhs91dcdSma5.nchQaoq2LCxtZY5Ene', 1, 0, 0),
(86, 'eddie', '', NULL, '$2y$10$/9zOruKLXdnLPQQjeXeWuOvagUiIeJC57C7/M2zSYbIiUEkgXHoRm', 1, 0, 0),
(87, 'Emmet', '', NULL, '$2y$10$F7HJarpXFb98T89GVRv7r.sERATmgwVqROZ6nt3zji7PGjXdQRPi2', 1, 0, 15),
(88, 'Balls', '', NULL, '$2y$10$rbRLzpl2YM6J14xbjqLvOeNC7619DwqKEQJw7mXcH.ek5Gi7O8XdC', 1, 0, 0),
(89, 'chrometest', '', NULL, '$2y$10$wBA5BHnMNr8VQU9H9NDCw.DLRYBbAqDiR44VnE43hTIQA8MJ0AVni', 1, 0, 0),
(90, 'ProfileTest1', '', NULL, '$2y$10$fjS3kJeJ4KLS1C2AZE4Iru9kFxXpbExV8i3UD4Nwix0C.QAHuF3y2', 1, 0, 0),
(91, 'Kalden2', '', NULL, '$2y$10$IT.xCR0em5CZW2Dx6wVomOHrj2SiAjXMZKYZa8QTbDWAfiFOwQTaW', 1, 0, 0),
(92, 'sigma1', '', NULL, '$2y$10$xnr4YHWy6hffdePKKgoVW.ueOAaGWrZcd/vm.YwL2jw9C86yCDCty', 1, 0, 0),
(95, 'PeetPeet', '', NULL, '$2y$10$BJEW40ExKYwdf1vM9PQqieXOa5VlyRp3o49HJ2kmJ0OqHz3ol22l2', 1, 0, 0),
(96, 'bvb', '', NULL, '$2y$10$jjlRBHk3ps6IjtTMXvKg1eBegLYaxU0RnGdE4Jp45CGmbpiRU3MDS', 1, 0, 0),
(97, 'rjyrjy', '', NULL, '$2y$10$18vqmbu/4tLr/eqbpEJ5qeCyPggdnMVpQcdVqwUHa/noBF2ZvJLGK', 1, 0, 0),
(98, 'new', '', NULL, '$2y$10$z3abATimtE4rLIdQgiowiek88t6WMioXhdEcPm5/ofKRrowuEHQ6S', 1, 0, 0),
(99, 'Humuhumunukunukuapua&#039;a', '', NULL, '$2y$10$i83/9/RomvhCvjlDySHT5OJ9Vf5grVAUSzCgY.rSGHIw/F6DTsdHq', 1, 0, 0),
(100, 'King Kong', '', NULL, '$2y$10$d0VIDVhboY/A0.YVtNWBM.TgOAmSleFoiK0wuoHNqPpU6jfPF3s4m', 1, 0, 115),
(101, 'Alan Wake', '', NULL, '$2y$10$f5EbWQkIQLKoyy4XRRMC9.uadabsZ9TBzpLM1L8kfKe21rwSgmgOm', 1, 0, 55),
(102, 'Alabeasta', '', NULL, '$2y$10$WeSxoWvIZ8dY97z7C8QF..ZvpK8i4Kj3mv8A/YHz7qlJXD5CAD1Gm', 1, 0, 55),
(103, 'his friend', 'abc@gmail.com', NULL, '$2y$10$X18n2KK9CrcjCqVs56lqhuVWPrSJaBiy0qTfed2sbznkf5CmfBBAe', 1, 0, 1000),
(104, 'tjntrjnh', 'cde@gmail.com', NULL, '$2y$10$Z2P6lDbpMw3s2uAlNTLP3eSQyYD9fkP9DL0zCCCkoZymcumj9c6Ii', 1, 0, 0),
(105, 'ytrjhtrjh', 'efg@gmail.com', NULL, '$2y$10$CbZbDlZiY1BNlwJr7C3EyOGO6LQgk5jJQCskFQAiKCG9q9La.7jwm', 1, 0, 15),
(106, 'ytr', 'ytr@gmail.com', NULL, '$2y$10$JuGXNK.h.HCiJ/o5mlv2IecWBA.A6vINLQrlw9Q4AC/b6ljqxJlsm', 1, 0, 0),
(107, 'Jedi', '9582398573295@gmail.com', NULL, '$2y$10$DoazZLgLLq3oRols.sT3BO3Edvo3UPsUebHq.bysU6YzbD0RxU1D.', 1, 1, 25),
(109, 'erbeerb', 'Fake@email.com', NULL, '$2y$10$VHXpNPvAc0IzIeZNwY7PSOlPmjfcShwnSQQlCrYUkjDM6/X7gC8Y2', 1, 0, 0),
(110, 'AmongUs', 'sus69@hotmail.com', NULL, '$2y$10$IqTAK60dYof8rLhCfhAL5eztnveuTbtmPxg6S2K22FkiriYjq0yG.', 1, 0, 0),
(111, 'UpdateTestAccount', '17653981368752@ed.act.edu.au', NULL, '$2y$10$gAs8lOCuGfPEoQqm1a6Y2eFcG6APs/dvlx1zEiqqC4Oyhko1GrEIK', 1, 0, 0),
(112, 'btrugh', 'pp@pp.vom', NULL, '$2y$10$WVWmwVoROXz.KxexOYQhL.Mg3j98HfYXE02xCE.Vr574mWLztkjim', 1, 0, 465),
(113, 'wanniassa', 'wanniassa@fakemail.com', NULL, '$2y$10$3mFfU1CFbznoMo.3qUwU5uFEKaPuDuPIAXT6dbg0ifaKEALbQj3jy', 1, 1, 355),
(114, 'mcKillop', 'mcKillop@fakemail.com', NULL, '$2y$10$Yxb/iXmbC/IsQTAnTfEt.uGdRMRI8nTtCwVU..IacB1USqKmQ1xC6', 1, 1, 405),
(115, 'calwell', 'calwell@fakemail.com', NULL, '$2y$10$FwRinKNyv5eYdKVrCNHoxetjIGzzFEEaTnp5RWXWu0Ohb8/gv4DdC', 1, 1, 0),
(116, 'lanyon', 'lanyon@fakemail.com', NULL, '$2y$10$hZOhO/tI1V0AOYEfadwrgujTo5jezWKWT.6geZnaH7rhqwqAoIgS.', 1, 1, 0),
(117, 'namadgi', 'namadgi@fakemail.com', NULL, '$2y$10$1ehx8Y/5XzDC4l7djg/ZROX7Hbl.jKepX67FSTRIoyhK1pwMAHDPS', 1, 1, 0),
(118, 'chisholm', 'chisholm@fakemail.com', NULL, '$2y$10$aMxgHYwsgOjDMKm2kRluruENIvPj4Q4ZFrI5pqshpKxlR0ldYtrRC', 1, 1, 270),
(119, 'test', 'test@test.com', NULL, '$2y$10$YO3sZTU1Tncwgux3bkPQBepuPPJ24liSuzpqzBgygbfswJ5hcPPl2', 1, 0, 20),
(120, 'laptoptest', 'laptoptest@laptoptest.com', NULL, '$2y$10$/Scee8csZm3NHpj7o2hjE.MIlfeRBd06Ayo3DzwLzMEDwm6uPm.Rm', 1, 0, 0),
(121, 'user45', 'test@email.com', NULL, '$2y$10$6/Y3RrGX8yBBvztdWu7Zc.H3vcUZQJnfL0iUjKRxP/lUUtL41SRd2', 1, 0, 5),
(122, 'Hacker-X', 'fake@gmail.com', NULL, '$2y$10$voCjLeEC9SuTNaVe0LKbxuY5qXTt7/gmUibyZpi1ICPDxTJUmUCR6', 1, 1, 5),
(123, 'bgt', 'gt4@bnhyt', NULL, '$2y$10$jMJztZS./hTuf8rAGZZOtecMP.sLBWG0pVtoGrECeK8C.GsBv8tfq', 1, 0, 25),
(124, 'LTC', 'LTC@gmail.com', NULL, '$2y$10$m/qyGet2R29pE0DyBkTjAeOWIF85rT7KAJyEK2uGpWmg9Yi8/xlma', 1, 0, 180),
(125, 'Alex', '0627778@schoolsnet.act.edu.au', NULL, '$2y$10$1f37IZ/A8MIrFw74a5eNs.p5/M/Rodz6Q6muj3D8XSOGhW2tiv5BW', 2, 1, 10),
(126, 'lachlandr', '0717649@schoolsnet.act.edu.au', NULL, '$2y$10$WkLkhUlCWe9RIFioJ.nxIuTXvGU3rhUZpRerS/J3cFYXf/cINp/jy', 1, 1, 30),
(127, 'PudgyPeter', '1020311@schoolsnet.act.edu.au', NULL, '$2y$10$Hlml.h3TrgWq.qtH5MpMWee9iIPzS6tSyFN59f4BrQfDxBjd2s4pW', 1, 1, 30),
(128, 'Doranako', '0321841@schoolsnet.act.edu.au', NULL, '$2y$10$NBHo4hVBeDpfR9EpKLrwMOQGF8fSSD3oO6dT.FeqHP33BQxCOHLfq', 1, 1, 80),
(129, 'Aidan geer', '0538180@schoolsnet.act.edu.au', NULL, '$2y$10$ttA5SfDoAAgx89Eo6eE8sO1sMXWTpJbT5hDT59Ea6EYmRtkGYUxKu', 2, 1, 80),
(130, 'JesseUgljesic', 'jesseugljesic@gmail.com', NULL, '$2y$10$8zBuyhdV1VKhZL8amrylg.ajxVLdps0bSIfCwF2lHyc9CHY7Aikq6', 2, 1, 30),
(131, 'Nadal', 'nadal.rahman07@gmail.com', NULL, '$2y$10$LHvehsxwhqsgIE/8SzoI0e/IosYDk8J7TETyy2Wuq6N8hygTwY3hy', 1, 1, 30),
(132, 'juju', '0676682@schoolsnet.act.edu.au', NULL, '$2y$10$nlmiVsLHDM5C5H.HvCmmzuUbKUjiSmFYWfF8Lvr44hcuo3Il5b9Ai', 1, 1, 30),
(133, 'Ani', '0852401@schoolsnet.act.edu.au', NULL, '$2y$10$EiaYaQ/GH7s6.VBgTQghgObqWJIr0T7I5QV22kDsjkdjnvSjfAD7a', 1, 1, 45),
(134, 'andy12', '0870510@schoolsnet.act.edu.au', NULL, '$2y$10$KTU.mdyguAihUcpARjPk2OyCEzd9N068.UwYHgnOmDac1Ckbsp0Yu', 2, 1, 30),
(135, 'DiaVolentine', '0321856@schoolsnet.act.edu.au', NULL, '$2y$10$XjcenWeoZUw6Pd2GUspyTuAiWlH14D.0sXrZZlE4TpvQGKTjIq3bG', 1, 1, 30),
(136, 'Finlay.Mckenzie', '2681543@schoolsnet.act.edu.au', NULL, '$2y$10$KVMljBFRjdzOflxmS79TuOWUloo3Q6Gq112FEkBz3nLB0mYt1igb2', 2, 1, 105),
(137, 'IbrahimI', 'Ibrahimi@Ibrahimi.com', NULL, '$2y$10$l5r1dCg0oe7geA8umSIHKeQXP3F4VY4hDpjgZkY3USDEYEYKwnvNm', 2, 1, 55),
(138, 'Tari Ganas', '0676776@schoolsnet.act.edu.au', NULL, '$2y$10$NQaN8mIuU.DsuY2Aehk.KudQJtuYtNJt0UyuxK4Uz3v4Zuhr1/Vy6', 1, 1, 0),
(139, 'Tari', '0676776@schoolsnet.act.edu.au', NULL, '$2y$10$EF7gbsAvIIK53dcs54rWu.qwE.V7sUdw1uG7XeXVtCvnBXabjJ/Z6', 1, 1, 0),
(140, 'LandedTitan220', 'Flisalex823@Gmail.com', NULL, '$2y$10$rQaR1ZoSdtE8rmMZ4g9IY.up3q2AYLIvNXwhWHk.b3f2k/oAYS4/S', 1, 1, 30),
(141, 'Tashi', '8030071@schoolsnet.act.edu.au', NULL, '$2y$10$5FvbSwi9VBHUrLO7aYPqg.u3c7kYlgKW9T9weW2NqC7TaWwlYysc2', 2, 1, 354),
(142, 'Larraine', 'aquino.larraine@gmail.com', NULL, '$2y$10$YvYPaY.zNCmZxCZnUDoZaOI8Zj.fCp7UnKK.4H2HCYmMJsk5eY87y', 1, 1, 0),
(143, 'TippyTetris', 'nadal.rahman07@gmail.com', NULL, '$2y$10$tUUZlKaMLR/9OIF5iUr8X.jCuJ2dZHcR9j.fWSwgZFxXQk5xy66EW', 2, 1, 0),
(144, '0483182', '0483182@schoolsnet.act.edu.au', NULL, '$2y$10$A2I6gccTK/LX5aEkdi4Apux0GHmsZ.LcGmNr83mmJXIJnJvO1vEp.', 1, 1, 0),
(145, 'testa', 'testa@test.com', NULL, '$2y$10$QUoMA6Mkq53gjLJABiSHgure.snqAXFtgGVlFfp/vh5COZRFamiBS', 1, 1, 0),
(146, 'testa2', 'testa2@test.com', NULL, '$2y$10$7Im/7LghZaRJlpEGT1LhfuigjzRPLYb4o1hsEIi3F.OpLRbuAWkg2', 1, 1, 0),
(147, 'Apple', 'apple@apple', NULL, '$2y$10$xH2NpYeUir7HPqxhVOvhqu4dgAN/YR48ybqADAtOVr/xBPr0EJqt.', 1, 1, 0),
(148, 'tester3', 'test@test', NULL, '$2y$10$dAfzEM59T2ebMyxQaUxoOOgTyMkOG97mYFzanN1/NxImttaHDN0ve', 1, 1, 0),
(149, 'testt', 'test@gmail.com', NULL, '$2y$10$5aNyziuqAIsbgmFy8/uDDOE4tcVKmtEo0gTspZPPMWzYpfnn3jGiq', 1, 1, 0),
(150, 'Boranako', '0321841@schoolsnet.act.edu.au', NULL, '$2y$10$BeR9CQ/KA7k9FgcwXgUhXOFHOiEtNbgsIg5rawPqGF9BykVBD3B92', 1, 1, 5),
(151, 'Jesse', '0592547@schoolsnet.act.edu.au', NULL, '$2y$10$Q5rD7cy4fkwEydncrpu/0ucGPfleKQgCYlXaHP29BuLfMVlaaxwAm', 1, 1, 0),
(152, 'larraine2', 'aquino.larraine@gmail.com', NULL, '$2y$10$QBoBlIAin8umProqBN38Pe6qr/gBmYdP4vLkmrQn8vEFTvMRFK.b.', 1, 1, 0),
(153, '5', '5@5', NULL, '$2y$10$0vDRWBEbVOr7dl1zNHiYROGhzwp8itst2GlR4/8hUCYeUMbPg7NNi', 1, 1, 0),
(154, 'TestTurbine', 'apple@apple', NULL, '$2y$10$Y6ddbtsYb7/pPdGbOVD0sOd438KGIw288dkDmy85lGQ2.x74SjQ8a', 1, 1, 5),
(155, 'TestCase', 'apple@apple', NULL, '$2y$10$zHKfWtwmywHm0DrmG0YKx.HuaVSAq6WBHmeKmO8kUvzxw74/LunXG', 1, 1, 15),
(156, 'testeddd', 'apple@apple', NULL, '$2y$10$vB76jh/U3G9u1oaA7wdfh.LiIfxulKzaxD2rcJm/KXXhIZaQn7Hy2', 1, 1, 0),
(157, 'testagain', 'apple@apple', NULL, '$2y$10$RO./sIz8QhyZUXYQU8URfOmagk8eCEyR2IQ0O5Ll6cffN0aAqaw2q', 1, 1, 5),
(158, 'Brahim', 'brahim@brahim.com', NULL, '$2y$10$AKLy0R/db4JtvOfVepIk/Oj1x7knKV3zEQS9y2md6v8tlhdpLm25W', 1, 1, 0);

--
-- Indexes for dumped tables
--

--
-- Indexes for table `Category`
--
ALTER TABLE `Category`
  ADD PRIMARY KEY (`id`);

--
-- Indexes for table `ChallengeData`
--
ALTER TABLE `ChallengeData`
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
-- AUTO_INCREMENT for table `ChallengeData`
--
ALTER TABLE `ChallengeData`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;

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
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=197;

--
-- AUTO_INCREMENT for table `eventLog`
--
ALTER TABLE `eventLog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=50;

--
-- AUTO_INCREMENT for table `Learn`
--
ALTER TABLE `Learn`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8;

--
-- AUTO_INCREMENT for table `ModuleData`
--
ALTER TABLE `ModuleData`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=8996111;

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

--
-- Constraints for table `ModuleData`
--
ALTER TABLE `ModuleData`
  ADD CONSTRAINT `ModuleData_RegisteredModules_ID_fk` FOREIGN KEY (`ModuleID`) REFERENCES `archivedRegisteredModules` (`ID`);

--
-- Constraints for table `UserChallenges`
--
ALTER TABLE `UserChallenges`
  ADD CONSTRAINT `UserChallenges_Users_ID_fk` FOREIGN KEY (`userID`) REFERENCES `Users` (`ID`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
