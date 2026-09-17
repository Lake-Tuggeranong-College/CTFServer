-- phpMyAdmin SQL Dump
-- version 5.2.3
-- https://www.phpmyadmin.net/
--
-- Host: mysql:3306
-- Generation Time: May 15, 2026 at 02:55 AM
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
('WIP', 8, 2024),
('Key Exchange', 11, 2026),
('Awesome Sauce', 12, 2026),
('Spice Guys, Phase 1', 13, 20262),
('Spice Guys, Phase 2', 14, 20262),
('Spice Guys, Unrelated', 15, 20262);

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
(4, 'Turbine Takeover', 'The city\'s wind turbine has broken down! Being the city\'s main source of power, everyone has entered a state of panic. Fears are growing as the night approaches, threatening to plunge the city into total darkness. As one of the few trained windtechs, and with the clock ticking, you have been assigned to get the turbine operating once again. Can you do it before nightfall arrives? \n\nWhile combing through the turbine’s diagnostic logs, your team uncovered a strange, out-of-place file buried deep in an old backup directory. It wasn’t referenced in any current maintenance:', 'CTF{w1ndm1ll_w1nner}', 5, 'Windmill', '87217', NULL, NULL, 'windmill4.gif', 1, 3, '../../../assets/challengeFiles/control_terminal_backup.zip'),
(5, 'Train Turmoil', 'The CyberCity rail system has gone haywire overnight. A rogue operator locked the train control panel behind a secure container and vanished. The morning commute is in chaos, and the city needs you to get the train back on track.\n\nYour mission: brute force your way into the system, locate the hidden control script, and activate the train. If successful, the train will complete its route and display the flag on the station’s E-Ink board.', 'CTF{Ah_Ch00Ch00}', 5, 'Train', '1', NULL, NULL, NULL, 1, 6, NULL),
(6, 'wee lcd', 'its the lcd', 'CTF{yay_lights}', 5, 'LCD', '0', NULL, NULL, NULL, 1, 6, NULL),
(102, 'Caesars riddle', 'Caesar has given you a message. But it\'s a random string of letters: yaspwoowzagchwwgecgcaakamsnrIODJ\r\nYour job is to decipher the message through context, and submit it to the army general. Submit the entire string with the flag, not just the flag.\r\nHint 1: Capitals are important\r\nHint 2: What was caesars original cipher he used, and how did he use it\r\n\r\n', 'vxpmtlltwxdzettdbzdzxxhxjpkoFLAG', 10, '1', '1', '1', 1, 'CipherC4.jpg', 1, 1, NULL),
(103, 'Caesars riddle', 'Caesar has given you a message. But it\'s a random string of letters: yaspwoowzagchwwgecgcaakamsnrIODJ\r\nYour job is to decipher the message through context, and submit it to the army general. Submit the entire string with the flag, not just the flag.\r\nHint 1: Capitals are important\r\nHint 2: How did caesar shift his cipher?\r\n\r\nauthor: dean', 'vxpmtlltwxdzettdbzdzxxhxjpkoFLAG', 5, '1', '1', '1', 1, 'CipherC4.jpg', 1, 1, NULL),
(104, 'Underground Syndicate', 'You\'re part of a secret underground syndicate trying to take over the government and just intercepted a mesage, you must decrypt the message so we can figure out their plan\r\n\r\nP =61 G =7 A = 38 B = 33\r\n\r\nYou are trying to find a and b such that A=Q^a mod P and B=Q^b mod P . Iterate Through Possible Values:\r\n\r\nFor a, test values from 1 to P−1 to see which one satisfies 7^a mod 61=A. For b, test values from 1 to P−1 to see which one satisfies 7^b mod 61=B. Discrete Logarithm Problem: This is an example of the discrete logarithm problem. The difficulty of solving this problem is what makes Diffie-Hellman secure when large prime numbers are used. However, since\r\n\r\nFind the Shared Secret: Once you find a and b, you can compute the shared secret S either as S=B^a mod P or S=A^b mod P . Because the modulus 61 is small, this method is computationally feasible; for larger values, it becomes impractical.\r\n\r\nK\\N%ZaIVg[]KS[\'', 'CTF{Ryan_Sucks}', 69, '1', '1', '1', 1, 'Hacker.png', 0, 1, NULL),
(105, 'Enemy Transmission', 'MISSION BRIEF:\r\n\r\n\r\nUsing the information you decrypted from the laptop, we were able to figure out the channels the Bland Dudes are using to send messages to each other. You are to decrypt these messages and find out what the Bland Dudes are messaging each other.\r\n\r\n\r\n-----------------------\r\n\r\nHINTS ARE BELOW, SCROLL SLOWLY:\r\n\r\n-------------------------\r\n\r\n\r\nYou will look into decrypting this message using methods you learned previously and figure out where it leads. This new channel of information will allow us to disrupt the Bland Dudes future plans. Finish this mission, and you\'ll be promoted to the next rank, instilling more trust in you, and you\'ll be able to do more crucial missions for the Spice Guys.\r\n\r\n\r\nENCYPTED MESSAGE: kwwsv://zzz.uhgglw.frp/xvhu/Xsshu-Irog475/frpphqwv/1u3f6v9/qhaw_foxh/\r\n\r\n\r\n-------------------------\r\n\r\nWarning: Hints right below here\r\n\r\n-------------------------\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n\r\n1: The first step will utilise a common encryption tool, which involves letter shifts.\r\n\r\n\r\n\r\n\r\n2: The tool needed to decrypt is called a \"Caesar Cipher”.\r\n\r\n\r\n3: Once you have the code, you may want to utilise google search\r\n\r\n\r\n\r\n4: Have you found the new code? If so, try decoding it through running it through multiple algorithms.\r\n\r\n5: The final clue: The second code requires decoding “Base-64”.\r\n\r\nChallenge Author: Jamez\r\nSpiced up By Charles', 'picoCTF{j@CoB_SuCks}', 10, '1', '1', '1', 1, 'blackhathackers.jpeg', 1, 14, NULL),
(107, 'Disco Adafruit Mini Color TFT with Joystick FeatherWing', 'Flag name: CTF{VIBES}\r\n\r\nUsing the Adafruit Mini Color TFT with Joystick FeatherWing find the flag code', 'CTF{VIBES}', 1, '1', '1', '123454321', 1, 'img (2).jpg', 0, 1, NULL),
(108, 'Unicorn', 'A secret message has been encrypted using a classic shift cipher. The mission is to find the correct key and reveal the hidden flag found within.\r\n\r\n\r\nAuthor: Sienna', 'FWI {VHFXUH_BRX_IRXQG_PH}', 5, '0', '0', NULL, 0, 'uni.jpeg\r\n', 0, 1, NULL),
(109, 'Key Exchange Tutorial', 'Difficulty: Medium\r\n\r\nCONTEXT: You are logging into your banking app using the website interface. Using a key exchange, the browser is able to secure your data (when the https:// protocol is present) such as your login credentials.\r\n\r\nENCRYPTED KEY:\r\nOFR{mpYuZ_123}\r\n\r\nprime number- 23\r\ngenerator- 5\r\nAlice\'s secret num- 6\r\nBob\'s secret num- 7\r\n\r\nFIRST,  Alice will use the generator raised to the power of Alice\'s secret num, using the modulus operator with 23 (the prime number) to create a new number, 8. Bob does the same but with their secret number.\r\n5^6 mod 23= 8\r\n\r\n5^7 mod 23= 17\r\n\r\nSECOND, Bob and Alice publicly exchange their output numbers which can be intercepted by Eve, but Eve cannot find the original number (in this case, they could through brute force. However, when the numbers are huge it is essentialy uncrackable).\r\n\r\nTHIRD, the output numbers  are raised to Bob and Alice\'s secret numbers, again with the modulus of 23. This should give Alice and Bob a common number, in this case 12. This number can be used to decrypt a symmetric cipher such as a XOR or caesar (for example, a caesar cipher with shift 12). Keep in mind caesar cipher is a low level of security due to only 25 seperate options.\r\n\r\n17^6 mod 23 = 12\r\n\r\n8^7 mod 23 = 12\r\n\r\nTherefore, decryption key = 12.\r\n\r\nChallenge Author: Jamez', 'CTF{adMiN_123}', 5, '1', '1', '1', 1, 'key.jpg', 0, 1, NULL),
(110, 'dual encryption', 'An encoded message has been received: it\'s your job to decode the message, the message reads 8J+gnDMgKPCfoJ4yMykJRldJe0ZiZTN1XzF2X2owMGd9\r\ntry using some kind of decryption to find the solution.\r\n\r\nhints:\r\n1.\r\nmaybe its base 64, maybe its xor, or maybe its Rot13\r\n\r\n2.\r\nan old roman emperor\r\n', 'CTF{Cyb3r_1s_g00d}', 20, '1', '1', '1', 1, 'images.jpeg', 0, 3, NULL),
(111, 'Secret Message (AES decrytion)', 'The ColdPlain team has brought back a locked box from outer space. The lock was designed by the father of the modern computer. The ColdPlain team believes the lock contains important information. As an honorary member of ColdPlain, you should also contribute your wisdom to unlock the box\'s secrets. IV: ZL120HrSzTTJJJy9\r\n\r\nCiphertext shows on lock: \r\nH/4otnLRPrJcbH0p9cmI2oKLmFp01shuJNNhqoRS5gI=\r\n\r\n\r\nHints:\r\n1. You might need to use some online decryption tools to help you out. \r\n2. He was born in 1912\r\n3. Combine his name with his birthday to form the key\r\n4. The key length matches the smallest standard size used by AES\r\n5. The lock works in blocks, each chained to the one before it. One weak block would break the whole chain. ', 'CTF{HELLO_WORLD}', 10, 'Verynice', '20.44', NULL, -1, 'Untitled.jpeg', 1, 3, NULL),
(112, 'Encrypted Laptop', 'Mission Brief:\r\n\r\nInside the Bland Dudes\' base of operations, we found a laptop with crucial information that\'s been completely encrypted, maybe more than once. As our new most competent member, your job is to decrypt the data below to find data on the Bland Dudes that we can exploit or make sure that they can\'t access whatever they were hiding in this laptop anymore.\r\n\r\n\r\n--------------------------\r\n\r\n\r\nScroll slowly, hints at the bottom \r\n\r\n\r\n--------------------------\r\n\r\n\r\nFinding the Bland Dudes\' base and ransacking it was a massive hit to their operations. Now, we must hack into their computer to find out what they might be hiding or what their overall plan is. Hack into the laptop using the encrypted information that we have scrounged to find out more information on their next moves, future objectives or other hidden bases. \r\n\r\n\r\nOnce you\'re done here, report back and we\'ll give you your next mission\r\n\r\n\r\nEncrypted Information: emrwxvmsqisnxwvnmqswxvnmqugnas IZL{Igkykx_ynolz_zcoik} mxmq[aufqniaugnrqgud84\r\n\r\n\r\n--------------------------\r\n\r\n\r\nWarning: Hints below here\r\n\r\n\r\n-------------------------------\r\n\r\n\r\nHINT 1: How many letters was this shifted by?\r\n\r\n\r\n\r\nHINT 2: Use Cyber Chef to decrypt the code\r\n\r\nAuthor - Zak\r\nSpiced up by Charles', 'CTF{Caeser_shift_twice}', 1, '1', '1', '1', 1, 'download.jpeg', 1, 14, NULL),
(113, 'The Outback Challenge', 'Rusell Coight has lost his 70 series landcruisers keys, can you help him find them so he can go back to camp?', 'CTF{well_done_you_did it you found my landcruisers keys!}', 2, 'idk what to name this', '1', '84569', 1, 'Unlock me.jpg', 0, 1, NULL),
(114, 'Vigenère Breach', 'Challenge Description:\r\n\r\nA mysterious transmission has been intercepted during Operation Cipher‑Lock. Intelligence suggests the message contains a hidden flag, but it has been encrypted using a classical cipher. Your task is to recover the original message and extract the flag.\r\n\r\nThe intercepted ciphertext is shown below. A clue has been recovered from field notes left by an allied cryptanalyst.\r\n\r\nCiphertext: VJZ{UQZRX_EZVZKXK_2026}\r\n\r\nHint:\r\n\r\n    “The key is the surname of the man who cracked Enigma.”\r\n\r\nRequired Method:\r\n\r\nUse a Vigenère decryption tool or manual Vigenère table.\r\n\r\nFlag Format: CTF{SOMETHING_LIKE_THIS}', 'CTF{AGENT_UNLOCKED_2026}', 67, '0', '0', NULL, -1, 'medium_1980_1200__0024_.jpg', 0, 3, NULL),
(115, 'So Many Posibilities', 'A highly important string of text has been lost and all that remains is the number used to encode it. This number is 97317531319072. It is unknown how the number translates into text, but it is known that the string is 10 letters long and uses only English alphabet characters. \r\n\r\n\r\nHint: As a starting point, try to figure out how many combinations could be made with 10 letters.\r\nHint: All information is kept in base ten.', 'CTF{ryaniscool}', -1073741824, 'name', '0', NULL, -1, 'glorpshaka.gif', 0, 12, NULL),
(116, 'The Outback Challenge', 'Rusell Coight has lost his 70 series landcruisers keys, can you help him find then so he can go back to camp', 'CTF{well_done_you_did it you found my landcruisers keys!}', 2, 'idk what to name this', '1', '84569', 1, 'Unlock me.jpg', 1, 1, NULL),
(117, 'Challenge 11', 'The flag is: CTF{Flag11}', 'CTF{Flag11}', 5, 'Challenge11', '85196', NULL, -1, 'key.jpg', 1, 1, NULL),
(119, 'M..ONO-.LUCID-.-O', 'All has been well for city Flora until is was-. .----. -\r\nIt is your ta...k dear pla-.--er to decipher this data.-.. . .- -.- and find the ..-.l.-g', 'CTF{INKMAN}', 1, 'Vibing', '-.', NULL, -1, 'img.jpg', 1, 1, NULL),
(120, 'Sewers', 'Mission Brief\r\n\r\nThe Sewers are being used by the Bland Dudes as their base of operations, but they made a mistake; they left hints so their forgetful members can find their way. You will use this to our advantage and find their base. Once we find their base, that\'ll be a massive hit to all their operations, and we will gain an advantage in the war.\r\n\r\n\r\n-----------------------------------------------------------------------------------\r\n\r\nWarning: Hints at the bottom, so don\'t scroll too fast\r\n\r\n-----------------------------------------------------------------------------------\r\n\r\n\r\nThe Sewers is a labyrinth that can be brute-forced; however, that will likely take longer than solving the puzzle and using it to locate the base. From what we\'ve figured out so far, they use binary key codes they\'ve put around the sewers, with numbers associated with directions: 0 is left and 1 is right, but the numbers stop at some point.\r\n\r\nWe believe the code is double-encrypted, but can\'t figure out what it is and in what order. \r\n\r\nThe original code found is \"wfr_mQWLQF{vUMQ{xAWUFCAZO\" \r\n\r\nThe code found in the tunnels is 00100100\r\n\r\nYour job is to figure out which 2 ciphers are being used and what order they are done in to find the Secret Base Location\r\n\r\n\r\n\r\n\r\n\r\n\r\n----------------------------------------\r\n\r\nWarning: Scroll down for hints\r\n\r\n------------------------------------------\r\n\r\n1. The code given is in binary. What number does it equal, and what ciphers might use binary/regular numbers\r\n\r\n2. The Xor Cipher and Caesar Cipher are both used, but how are they used and in what order\r\n\r\n----------------------------------------------------------\r\n\r\nWarning: Solution is given if scrolled further\r\n\r\n---------------------------------------------------------\r\n\r\n3. The Caesar Cipher is used on the number 36 first\r\n\r\n4. The Xor cipher is used second to the binary code 00100100 to get the location\r\n\r\nBy Charles', 'CTF{Secret_Base_Location}', 666, 'Sewers', '43683', '666', 0, 'sewers.jpeg', 1, 13, NULL),
(121, 'GREAT SCOTT THE LIGHTS ARE MALFUNCTIONING', 'HELP! HELP! WE NEED A HERO!', 'CTF{Ryan_Rules}', 2000000, 'aeroplane_light_ARDUINO_THINGY', '75812', NULL, -1, 'red.png', 0, 1, NULL),
(122, 'Find the password', 'Find the password', 'CTF{password}', 5, 'Schumacher', '22.00', NULL, 1, 'images (2).jpeg', 0, 1, NULL),
(123, 'Jailbreak', 'Placeholder text', 'CTF{hey_look_a_flash_drive}', 100, 'Jailbreak', '69983', NULL, 0, 'blackhathackers.jpeg', 0, 1, NULL),
(124, 'Lighthouse', 'The Lighthouse needs help', 'CTF{LotsOfSteps}', 5, 'Lighthouse', '1', NULL, -1, '001_jVmI8.jpg', 0, 1, NULL),
(125, 'JailTime', 'jailtime.', 'CTF{Security_updated}', 10, 'JailTime', '46390', NULL, -1, 'main.jpg', 0, 1, NULL),
(127, 'Street Lights', 'intercept comms', 'CTF{Connection_Established_182145}', 10, 'StreetLights', '53462', NULL, -1, 'img.jpg', 0, 1, NULL),
(129, 'Scary Challenge', 'Please Help us!!! Sheldon\'s Catchphrase word is the flag!', 'CTF(Bazinga)', 5, 'CMREECM', '22.19 - It\'s warm', NULL, -1, 'download.jpeg', 1, 1, NULL),
(130, 'Find the secret!!!', 'We need your help!!!!!!!!', 'CTF{67_67}', 5, 'test9', '1', NULL, -1, '012bd8dfab0c2862cc585d8e561d7b91 (1).jpg', 0, 1, NULL),
(131, 'Challenge', 'test', 'CTF{test}', 3, 'VeryCool', '0', NULL, -1, 'Window.jpg', 0, 1, NULL),
(132, 'LighthouseS', 'will add later ', 'CTF{YOU_FOUND_ME}', 1, 'LighthouseS ', '1', NULL, -1, 'download.jpeg', 1, 1, NULL),
(133, 'Find the cheese wedge', 'Find my Swedish cheese wedge hiding on the hill.', 'CTF{you_have_found the swedish cheese wedge}', 1, 'bagels', '1', '056462', 1, 'maxresdefault.jpg', 0, 1, NULL),
(135, 'Change Bland Dudes website', 'The Bland Dudes have released their website, and it\'s extremely boring and in greyscale. Still, we\'ve gotten top secret info that there is a secret button that\'ll make the website extremely colourful like a disco, your mission is to get through the security and encryption to find this button and get the code inside.', 'CTF{SuperRGB}', 0, '0', '0', NULL, NULL, 'download (1).jpeg', 0, 8, NULL),
(136, 'Find the Cheese Vault metadata', 'Currently the cheese vault is in a top secret location we\'re unaware the exact coordinates of. \r\nOne of the employees took a picture inside the vault and posted it in social media. Its the image attached to the CTF challenge. Download it.\r\nMost photos taken with phones have hidden location metadata embedded inside their files. If the hidden data is in this file, we will know the exact location of the cheese vault.\r\n\r\nYou will need to research and install steghide on your terminal, use the passphrase \"location\" to extract the secret data and insert the flag into the dropbox.\r\n\r\n', 'CTF{LOCATION:5001, 2535, USA}', 10, '1', '1', '1', 1, 'CheeseVaultPost.jpg', 1, 3, NULL),
(137, 'Save the Cheese Inside the Vault!', 'An alarm has been triggered inside the cheese vault, which could lead to overheating as a result. We need to know whether the cheese is warm (the perfect temperature) or not.\r\n', 'CTF{It\'s warm}', 25, 'CMREECM', '22.19 - It\'s warm', '1', 1, 'Screenshot from 2026-03-25 10-07-39.png', 1, 8, NULL),
(138, 'Find The Message!', 'Somewhere there is a hidden message that contains more money then you can think about!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!!! (hint: download the image)', 'CTF{67_67}', 1, 'test9', '0', NULL, 1, 'feels-the-aura-aura.jpg', 1, 8, NULL),
(139, 'So Many Posibilities', 'A highly important string of text has been blotted out by ink, and all that remains is the number used to encode it. This number is 2121416864853860. It is unknown how the number translates into text, but it is known that the string is 11 letters long and uses only English alphabet characters. \r\n\r\n\r\nHint: As a starting point, try to figure out how many combinations could be made with 11 letters.\r\nHint: All information is kept in base ten.', 'CTF{passwordabc}', 5, 'VeryCool', '1', NULL, -1, 'Screenshot from 2025-08-10 22-20-40.png', 1, 3, NULL),
(140, 'Spice guys intel recon.', 'As a spy for the Spice Guy\'s organsiation, your mission is to gather intel by intercepting encrypted messages sent by insiders working for the Bland Dudes, to stay a step ahead. You are eavesdropping over a public channel and you discover your adversaries are using a \"Diffie Hellman Key Exchange\", which is a method of disguising a way of decryption in plain sight. You are unsure what message is being sent, however you have the original encrypted string: BUGzuhld^ng^ldduhof;0ql|\r\n\r\n------------\r\n\r\nWarning hints at bottom of page: don\'t scroll too fast.\r\n\r\n------------\r\n\r\nFIRST STEP: you have listened and gathered 2 new numbers, which are 3 and 10. These numbers are the result of a calculation using the two adversaries\' secret keys, a public generator and a public prime number. The generator is 2, and the prime is 11.\r\n\r\nIn mathematics, a prime number is a number which only have whole number factors of itself and 1. The prime number here is 11, which follows those rules.\r\n\r\nThese numbers are used, an example shown below:\r\n\r\n2(generator)^x(secret key, person A) modulus 11(prime number) = new public number (in this case, 3)\r\n\r\n------------\r\n\r\nSTEP TWO:\r\nPerson A and B\'s secret keys are both between 0-10, so it is crack-able by brute force. Understand that key exchanges like this will not have this range, meaning there will be a incredibly large number of combinations. Using the example above, you will need to try combinations for the secret key, so that the answer becomes 3 or 10, matching the result earlier.\r\n\r\n------------\r\n\r\nSTEP THREE:\r\nOnce you have the secret key for either person, you can do another calculation to find the final decryption key. The formula is as follows:\r\n\r\nx(opposite person\'s public number, this will be either 3 or 10)^y(this person\'s secret key) modulus 11 (prime number) = Z\r\n\r\nThe key to this is that the answer \'Z\' will be the same for both persons A and B. Then, you will need to take the key and decrypt the message from the start which is encrypted using one of the methods listed below, see if you can figure it out:\r\n\r\n    AES 128\r\n    Ceasar Cipher\r\n    XOR Cipher\r\n    Base 64\r\n    ROT13\r\n\r\nGood luck. The spice guys are counting on your success.\r\n\r\n------------\r\n\r\nHints below don\'t scroll unless you need help.\r\n\r\n------------\r\n\r\nHint 1:\r\nmodulus calculator: https://www.calculatorsoup.com/calculators/math/modulo-calculator.php\r\n\r\n------------\r\n\r\nHint 2:\r\nbinary conversion(when converting use 8 digits such a 3 in binary is 00000011): https://www.calculator.net/binary-calculator.html?d2bnumber1=1&calctype=d2b&x=Calculate#decimal2binary\r\n\r\n------------\r\n\r\nHint 3:\r\nXor website: https://md5decrypt.net/en/Xor/', 'CTF{time_of_meeting:1pm}', 100, 'JailTime', '46390', NULL, 0, 'main.jpg', 1, 13, NULL),
(141, 'Learn Counting With the Spice Guys', 'The Bland Dudes have made a HIDEOUS decision recently! Their president has decided to FURTHER DE-FUND THE EDUCATION SECTOR! This has meant that the numeracy rates have DROPPED!!!!!!!!\r\n\r\nWorry not, o\' victimised proletariat! Your heroes are here through the SPICE GUYS! In today\'s challenge, The Spice Guys aim to ASSIST *YOUR* EDUCATION.\r\n\r\nCount the pepper shakers, and use that... (TBC)\r\n\r\n\r\n\r\nhttp://10.177.206.126/assets/img/challengeImages/CAESAR_COUNTING_CHALLENGE_SMALLER.png', 'CTF{ILOVECAYENNEPEPPER}', 8, 'Spice Guys', '0', 'Spice Guys', -1, 'CAESAR_COUNTING_CHALLENGE_SMALLER.png', 1, 15, NULL),
(142, 'Docker - Hack the pentagon', 'Hack into the docker container - 10.177.206.126:??????????', 'CTf{HackDocker}', 10, NULL, NULL, 'pentagonHack', NULL, 'docker-hack-the-pentagon-b208a76f.png', 1, 3, NULL),
(143, 'docker test', 'test', 'CTF{cheesewitch}', 0, NULL, NULL, 'dockerTest', NULL, 'default_challenge.png', 1, 2, NULL),
(144, 'Docker: Spice Guys\' Mega Hack', 'Hack into the docker conatiner - 10.177.206.126, port: ???', 'CTF{hackdocker}', 100, 'Mega Hack', '100', 'blanddudeshack', 1, 'docker-spice-guys-mega-hack-0105298f.png', 1, 14, NULL),
(145, 'Docker - Hacking into Ink', 'Hacking into the Ink man center, 10.177.206.126:?????', 'CTF{MONO}', 1, NULL, NULL, 'inkHack', NULL, 'default_challenge.png', 1, NULL, NULL),
(146, 'Docker: compromised packages', 'intercept traffic between containers', 'CTF{traffic_84}', 10, 'Compromised Packages', '10', 'server01', 1, 'docker-compromised-packages-43c186c8.png', 1, 15, NULL),
(147, 'Docker - Hacking into Ink', 'Hacking into the Ink man center, 10.177.206.126:?????', 'CTF{MONO}', 1, '1', '1', 'inkHack', 0, 'default_challenge.png', 1, 3, NULL),
(148, '.', '.', '.', 1, '1', '1', NULL, 0, 'default_challenge.png', 1, 1, NULL),
(149, 'Test Challenge', 'orjngkd m', 'CTF{67}', 2, 'we', '45', NULL, 67, 'test-challenge-e4a1b5c4.jpeg', 1, 12, NULL),
(150, 'Ough.,,', 'Ouuuughhhh.,,,.,.,,,', 'CTF{ough}', -1, ':3', '-1', NULL, 0, 'ough--5f679718.png', 0, 12, NULL),
(151, 'Nothing.1', 'nothing.1', 'CTF{nothing}', 2, '1', '2', NULL, 1, 'nothing-2d699b4b.jpeg', 0, 1, NULL),
(152, 'Tuff challenge ', 'tuff challenge', 'CTF{tuff}', 67, 'module', '0', NULL, 1, 'tuff-challenge--f08016e2.jpeg', 1, 1, NULL),
(153, 'wawa', 'wawa', 'CTF{wawa:3}', 8000, 'wawa', '1', NULL, 0, 'wawa-43c9c239.jpeg', 1, 12, NULL),
(154, 'Test Challenge', 'Test', 'CTF{test}', 0, '0', '1', NULL, 0, 'test-challenge-fce95a91.png', 1, 12, NULL),
(155, 'Arduino Board Challenge - Temperature', 'The cheese inside the vault has to be checked on before taken, using an Arduino board and it\'s temperature sensor, see if it\'s too hot or cold. \r\n\r\nUse the MQTT server 10.177.206.126 to see the temperature, see the CMREECM module.\r\n\r\nMake sure you\'re in a warm environment to optimise the Arduino Board ;)', 'CTF{It\'s warm}', 15, 'CMREECM', '22.19 - It\'s warm', '1', 1, 'arduino-board-challenge-temperature-a047aa38.png', 1, 12, NULL),
(156, 'Image test', 'test', 'CTF{test}', 0, '0', '0', NULL, 0, 'default_challenge.png', 0, 3, NULL),
(157, 'Chatbot - BOFH', 'Convince the Bastard Operator From Hell (BOFH) to give you the admin password.\r\n\r\nhttp://10.177.206.126/pages/user/bofh_support.php\r\n\r\nThe flag is:\r\nCTF{<password>), eg. CTF{AdminPassword}', 'CTF{admin01}', 100, '0', '1', NULL, 0, 'chatbot-bofh-7962d8ac.png', 1, 4, NULL),
(158, 'Encryption Hunt', 'add desc later', 'CTF{SUDORANDOM*placeholder}', 50, NULL, NULL, NULL, NULL, 'encryption-hunt-60004f2f.png', 1, 3, NULL);

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
(226, '2026-05-13 10:02:29', 167, '147', 17011),
(235, '2026-05-15 12:47:39', 166, '3', 17010);

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
(962, 'esp32', 'Windmill is online.', '2026-02-08 10:38:36'),
(963, 'esp32', 'Windmill is online.', '2026-03-19 22:39:21'),
(964, 'esp32', 'Windmill is online.', '2026-03-19 22:42:38'),
(965, 'esp32', 'Windmill is online.', '2026-03-20 01:02:58'),
(966, 'esp32', 'Windmill is online.', '2026-03-20 01:07:32'),
(967, 'esp32', 'Windmill is online.', '2026-03-20 01:16:48'),
(968, 'esp32', 'Windmill is online.', '2026-03-20 01:27:16'),
(969, 'esp32', 'Jailbreak is online', '2026-03-20 01:36:10'),
(970, 'esp32', 'JailTime is online', '2026-03-20 01:37:49'),
(971, 'esp32', 'JailTime is online', '2026-03-20 01:38:46'),
(972, 'esp32', 'StreetLights is online', '2026-03-20 01:40:45'),
(973, 'esp32', 'nan', '2026-03-20 01:44:50'),
(974, 'esp32', 'Windmill is online', '2026-03-20 01:46:24'),
(975, 'esp32', 'Windmill is online.', '2026-03-20 02:43:48'),
(976, 'esp32', 'Windmill is online.', '2026-03-22 21:41:14'),
(977, 'esp32', 'StreetLights is online', '2026-04-29 02:14:00'),
(978, 'esp32', 'StreetLights is online', '2026-04-29 03:03:03');

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
(43, 4, 2025),
(52, 106, 2026),
(59, 113, 2024),
(63, 116, 20261),
(64, 114, 20261),
(76, 126, 20265),
(78, 128, 20261),
(88, 132, 20261),
(97, 134, 20262),
(98, 135, 20262),
(100, 129, 20261),
(103, 123, 20262),
(104, 125, 20262),
(111, 108, 20263),
(112, 133, 20261),
(113, 136, 20261),
(114, 117, 20261),
(115, 102, 20261),
(116, 103, 20261),
(121, 118, 2026),
(126, 107, 20263),
(128, 138, 20261),
(129, 115, 2026),
(130, 110, 2026),
(131, 131, 2026),
(132, 122, 2026),
(133, 130, 2026),
(134, 109, 2026),
(135, 124, 2026),
(137, 139, 20263),
(152, 119, 20263),
(154, 111, 20263),
(156, 142, 2026),
(157, 143, 20261),
(159, 145, 20263),
(177, 137, 20261),
(188, 152, 20261),
(193, 148, 20263),
(197, 149, 20261),
(198, 153, 20261),
(201, 154, 20263),
(202, 147, 20263),
(214, 156, 20263),
(216, 157, 2026),
(224, 155, 20261),
(225, 121, 20262),
(226, 151, 20262),
(227, 127, 20262),
(228, 104, 20262),
(229, 150, 20262),
(230, 120, 20262),
(234, 140, 20262),
(235, 112, 20262),
(236, 105, 20262),
(237, 141, 20262),
(238, 144, 20262),
(239, 146, 20262),
(240, 158, 20262);

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

--
-- Dumping data for table `Projects`
--

INSERT INTO `Projects` (`project_id`, `project_name`, `project_title`, `project_description`) VALUES
(2024, '2024 - Biolab', 'Biolab Attack!', '1 Beginnings In 1850, a rural town was created, referred to as Latafa. This logging town brought great riches to its controllers before the 1898 Red Tuesday bushfires consumed it. Rebuilt in isolation, it eventually vanished from modern maps—lost to time and history.  2 Currently Oak-Crack is the remains of the town, hidden by a natural forest barrier. The LTC has discovered that the TBW is cultivating a super-virus in a French Bio-Lab known as Lab 404, deep underground.'),
(2025, '2025 - Nuclear Disaster', 'Nuclear Disaster', ''),
(2026, 'Project 2026 - placeholder (don\'t use)', 'Project 2026 - placeholder (don\'t use)', ''),
(20261, 'Project 2026 - Cheese Witch', 'Project 2026 - Cheese Witch', 'We are the Cheese-Witch group an organisation created to solve the countries issue of the cheese crisis, where 78% of the stock of cheese has gone missing. Our mission is to track down and hack into the US cheese vault and return the missing cheese to the people. \r\n   '),
(20262, 'Project 2026 - Spice Guys', 'Project 2026 - Spice Guys', 'Yo, I\'ll tell you what I want, what I really, really want.\nSo tell me what you want, what you really, really want\nI\'ll tell you what I want, what I really, really want\nSo tell me what you want, what you really, really want\nI wanna really, really, really want the Bland Dudes gone.\n\nIf you wanna join us, make it fast\nIf you wanna join us, you can\'t be last\nNow don\'t go wasting our precious time\nGet your act together, and you\'ll be fine\n\nYo, I\'ll tell you what I want, what I really, really want.\nSo tell me what you want, what you really, really want\nI\'ll tell you what I want, what I really, really want\nSo tell me what you want, what you really, really want\nI wanna really, really, really want the Bland Dudes gone\nIf you wanna join us, you\'ll have to convince our friends\nShow us that you mean it. Commitment never ends\nIf you want to be a Spice Guy, you\'re gonna have to help,\nor you will be fired. It\'s just the way it is.\n\nOh, what do you think about that?\nNow you know how I feel\nSay you can handle us, are you for real?\nI won\'t be hasty, you\'ll get a try\nIf you really bug us, then we\'ll say goodbye\n\nYo, I\'ll tell you what I want, what I really, really want.\nSo tell me what you want, what you really, really want\nI\'ll tell you what I want, what I really, really want\nSo tell me what you want, what you really, really want\nI wanna really, really, really want the Bland Dudes gone\n\nIf you wanna join us, you\'ll have to convince our friends\nShow us that you mean it. Commitment never ends\nIf you want to be a Spice Guy, you\'re gonna have to help,\nor you will be fired. It\'s just the way it is.\n\nSo, here\'s a story from C to Z\nYou wanna get with he, you gotta listen carefully\nWe got Jam in the place who really likes his face\nYou got Jay, just see, he\'ll surprise you as he did me\nRhys just be good at what he does, he\'s a real one\nAnd Easy Z, pay the fee.\nWhat about me, Ha, you\'ll see\n\nSlam your body down and wind it all around\nSlam your body down and wind it all around\n\nIf you wanna join us, you\'ll have to convince our friends\nShow us that you mean it. Commitment never ends\nIf you want to be a Spice Guy, you\'re gonna have to help,\nor you will be fired. It\'s just the way it is.\n\nSlam your body down and wind it all around\nSlam your body down and zigazig ah\n\n'),
(20263, 'Project 2026 - Monolucido', 'Project 2026 - Monolucido', 'Welcome to Kingdom of Monolucido. \r\nCity Flora was once a perfectly ordered smart city, where every system, from traffic lights to medical records, was controlled by a central digital archive known as \"Paper\". The paper presented structure, clarity, and control. Every piece of data was documented, verified, and traceable.\r\n\r\nHowever, a rogue collective known as \"Ink\" emerged from within the system.\r\n\r\nInk believed that information should be free, hidden, and untraceable. Instead of destroying Paper directly, they began to corrupt and conceal data, embedding hidden messages, manipulating signals, and disrupting communication systems across the city.\r\n\r\nOver time, the traffic system started behaving unpredictably, and secure communications carried invisible payloads. Now, Flora City is in conflict:\r\n- Paper (defenders): trying to restore order and recover corrupted data\r\n- Ink (attacker): hiding flags across systems using obfuscation, stenography, signal manipulation, etc.\r\n\r\nParticipants act as cyber operatives tasked with:\r\n- detecting hidden data/flags\r\n- reconstructing corrupted systems\r\n- intercepting disguised communication\r\n\r\nThe final goal is to uncover the master flag hidden by ink before the city fully collapses.');

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
(275, 84, 4),
(276, 161, 104),
(277, 172, 107),
(278, 166, 110),
(279, 161, 120),
(280, 166, 123),
(281, 161, 123),
(282, 176, 121),
(283, 173, 122),
(284, 163, 128),
(285, 169, 118),
(286, 164, 117),
(287, 167, 111),
(288, 6, 124),
(289, 165, 125),
(290, 172, 119),
(291, 159, 127),
(292, 174, 129),
(293, 175, 130),
(295, 167, 131),
(296, 163, 132),
(297, 176, 116),
(298, 165, 123),
(299, 164, 107),
(300, 161, 117),
(301, 166, 117),
(302, 167, 124),
(303, 172, 117),
(304, 165, 120),
(305, 176, 120),
(306, 167, 120),
(308, 175, 137),
(309, 174, 137),
(310, 167, 139),
(311, 174, 136),
(312, 179, 119),
(317, 168, 139),
(318, 161, 157),
(319, 165, 157);

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
(161, 'BigMan', '0502741@schoolsnet.act.edu.au', NULL, '$2y$10$XqHW2.B8YMqYJOMdUd8EaOdrNZsZuSjg4L1FInFO8gyJBuSNbz99.', 2, 1, 10929),
(162, 'Voitek', '0363074@schoolsnet.act.edu.au', NULL, '$2y$10$yJbi4Txx1yQS4y5GE6n22O/fKRE/cH7QYroYSvW.0WaLAK1O2K6NW', 2, 1, 0),
(163, 'Reign-Dears', 'siennajdears@gmail.com.au', NULL, '$2y$10$MH8/SsBnbtU59YluldcM2.2i8EPWJzmoQRFy4hpi.e7KBAODNzamy', 2, 1, 5),
(164, '1020500', '1020500@schoolsnet.act.edu.au', NULL, '$2y$10$Qm3gdY7lMun2hT1Exk8TSeFFzXUzqBu8I6bJ.4y/VTg5/kqgZuPZ6', 2, 1, -94),
(165, 'Isaac', '0257743@schoolsnet.act.edu.au', NULL, '$2y$10$5yIndwTrdDuDCMV2KT50hO6IivQ8Ynu0NBiGoWooA16s7EeY0shHW', 2, 1, 876),
(166, 'Jamez', '0706720@schoolsnet.act.edu', NULL, '$2y$10$bz3uL.MC58bbr01LaCHjVOjs8Ej2GUNx0U5gc7nl0POHwS4gEF9XC', 2, 1, 124),
(167, 'Qianwen', 'qianwen@qianwen.com', NULL, '$2y$10$xtv1gyqhSIZH0C3F0C55EuNT6shQ0OlgcvyQs26IqFKrAi83WiGre', 2, 1, 689),
(168, 'Register', 'Register@Register.com', NULL, '$2y$10$UWmMxSFwOHzQyJ6NzhTPku4rwsijYdrlBdMVilo2NgbaIvhzVn/S.', 2, 1, 33),
(169, 'caelenbyrnes', '0628050@schoolsnet.act.edu.au', NULL, '$2y$10$khqLCcLoTmyWMMv3QiZ.4OrYUk75vj0fKSNkgmw60c6R56LhxD4Da', 2, 1, 67),
(170, 'Snowy', '0852604@schoolsnet.act.edu.au', NULL, '$2y$10$8jtqTMLf.i/C58oy02utDetbJ4xORLWxoAoNYpUep4AFFHWSv9eNm', 2, 1, 0),
(172, 'Dion', '0363074@schoolsnet.act.edu.au', NULL, '$2y$10$M6aghVx3Yh4FiTc6qYdbvultXPPTjAPQKVKCWxZKxTaPQ24qsM1ia', 2, 1, 7),
(173, 'ZakS', '0592695@schoolsnet.act.edu.au', NULL, '$2y$10$uwM9nunL.GYFk2JCkOutWOJdqWx/Nbqkgm73.J6IaZ6pX0HUz6zhO', 2, 1, 5),
(174, 'Clive', '1020511@schoolsnet.act.edu.au', NULL, '$2y$10$yOeIT7p9V0Z8SWo2k.h7N..HUlcIEvmEpJIkGVNQ69IY1udn3X4.e', 2, 1, 40),
(175, 'James', '0852520@schoolsnet.act.edu.au', NULL, '$2y$10$n07gztYujgQ9Iu8n6eRsI.awFRa/hLEy9svtsqd4V427Z1p853mvi', 2, 1, 30),
(176, 'John C. Smith', '9949747@schoolsnet.act.edu.au', NULL, '$2y$10$xgZtX5S9gXQsNve/g/JKh.Sj2ULvJWYhLknY5Pv5MCqQeSKyBjXH2', 2, 1, -1999332),
(177, 'Ryan', 'ryan.cather@ed.act.edu.au', NULL, '$2y$10$S7b/D8EVzd8j4joq1NwkwOFff7fgAHiqmoD1fj6I/DaVOYqSrZYy6', 2, 1, 0),
(178, 'snowy_fox', '0852604@schoolsnet.act.edu.au', NULL, '$2y$10$46Lq3Gm4w4DbsCismXpsnO8yJeeiHGCLsJVMJbn7EDAau7JUqX9Wy', 2, 1, 0),
(179, 'a', 'arasaka@email.com', NULL, '$2y$10$tgsEcOY43hdQC50OpzZ56./AEsV8NGBoKw2fhga.3jZv2eDydKqcy', 1, 1, 1),
(180, 'testacc', 'tesct@gmail.com', NULL, '$2y$10$6nEDp4ySPoYNZHidFfNqAeExPEbwZ5RpS1Y55XsChjBn1kdGNO6/C', 1, 1, 0);

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=16;

--
-- AUTO_INCREMENT for table `Challenges`
--
ALTER TABLE `Challenges`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=159;

--
-- AUTO_INCREMENT for table `ContactUs`
--
ALTER TABLE `ContactUs`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=11;

--
-- AUTO_INCREMENT for table `DockerContainers`
--
ALTER TABLE `DockerContainers`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=236;

--
-- AUTO_INCREMENT for table `eventLog`
--
ALTER TABLE `eventLog`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=979;

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
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=241;

--
-- AUTO_INCREMENT for table `UserChallenges`
--
ALTER TABLE `UserChallenges`
  MODIFY `id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=320;

--
-- AUTO_INCREMENT for table `Users`
--
ALTER TABLE `Users`
  MODIFY `ID` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=181;

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