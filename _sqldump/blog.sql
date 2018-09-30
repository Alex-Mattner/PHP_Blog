-- phpMyAdmin SQL Dump
-- version 4.7.0
-- https://www.phpmyadmin.net/
--
-- Host: 127.0.0.1
-- Erstellungszeit: 13. Jul 2018 um 09:58
-- Server-Version: 10.1.25-MariaDB
-- PHP-Version: 7.1.7

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Datenbank: `blog`
--
CREATE DATABASE IF NOT EXISTS `blog` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci;
USE `blog`;

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `blogs`
--

DROP TABLE IF EXISTS `blogs`;
CREATE TABLE `blogs` (
  `blog_id` int(11) NOT NULL,
  `blog_headline` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_image` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `blog_imageAlignment` varchar(10) COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `blog_date` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  `cat_id` int(11) NOT NULL,
  `usr_id` int(11) NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `blogs`
--

INSERT INTO `blogs` (`blog_id`, `blog_headline`, `blog_image`, `blog_imageAlignment`, `blog_content`, `blog_date`, `cat_id`, `usr_id`) VALUES
(1, 'Fehlen im die Eier...', 'uploaded_images/526033rguhnxlzfscmpwqjydkbvaiote536827_avatar2.jpg', 'fleft', 'Erdogan hat keine Eier mehr im Kühlschrank...Türkei hat Notstand ausgerufen...', '2018-07-13 07:47:29', 3, 3),
(2, 'Erdogan hatte noch nie Eier...', NULL, 'fleft', 'aäb cde fgh ijk lmn oöp qrsß tuü vwx yz AÄBC DEF GHI JKL MNO ÖPQ RST UÜV WXYZ !&quot;§ $%&amp; /() =?* \'&lt;&gt; #|; ²³~ @`´ ©«» ¼× {} aäb cde fgh ijk lmn oöp qrsß tuü vwx yz AÄBC DEF GHI JKL MNO ÖPQ RST UÜV WXYZ !&quot;§', '2018-07-13 07:48:49', 3, 3),
(3, 'Ist das wirklich unser TRAPP???', 'uploaded_images/28994wltovbyefigmkduczxrasnhjpq298935_avatar1.jpg', 'fleft', 'Es gibt im Moment in diese Mannschaft, oh, einige Spieler vergessen ihnen Profi was sie sind.', '2018-07-13 07:52:11', 2, 3),
(4, 'Wer steckt unter dem Helm??? Ist das Erdogan???', 'uploaded_images/799638lgmudwqhnvsoitbpkyczeafxjr969890_04_-_Katze.jpg', 'fleft', 'Ich bin dein VATER...', '2018-07-13 07:53:38', 4, 3),
(5, 'Es ist wirklich wahr... Erdogan fehlen die EIER...', 'uploaded_images/491864shucakpzwinxbqytfvordjlemg626881_Buddhisten.jpg', 'fright', 'Seine Putzfrau hat sie schon seit langem weggeschmissen...waren verfault...\r\nReporter berichten...Wer Erdogans Eier findet, wird Eier-Minister...', '2018-07-13 07:55:51', 3, 3);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `categories`
--

DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `cat_id` int(11) NOT NULL,
  `cat_name` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `categories`
--

INSERT INTO `categories` (`cat_id`, `cat_name`) VALUES
(1, 'Krims Krams'),
(2, 'Trappattoni Jung'),
(3, 'Erdogan - weil\'s einfach zum b'),
(4, 'Star Wars - aber ich bin doch ');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `users`
--

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `usr_id` int(11) NOT NULL,
  `usr_firstname` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_lastname` varchar(72) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_city` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `usr_password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

--
-- Daten für Tabelle `users`
--

INSERT INTO `users` (`usr_id`, `usr_firstname`, `usr_lastname`, `usr_email`, `usr_city`, `usr_password`) VALUES
(3, 'Alex', 'Mattner', 'a@b.c', 'Augsburg', '$2y$10$HTcF1gz/Pp6RxDyAd6qhp.Xd93fHkITCLfRQXr/1Hw3UKJXX8z5Yq');

--
-- Indizes der exportierten Tabellen
--

--
-- Indizes für die Tabelle `blogs`
--
ALTER TABLE `blogs`
  ADD PRIMARY KEY (`blog_id`),
  ADD KEY `cat_id` (`cat_id`,`usr_id`),
  ADD KEY `cat_id_2` (`cat_id`),
  ADD KEY `usr_id` (`usr_id`);

--
-- Indizes für die Tabelle `categories`
--
ALTER TABLE `categories`
  ADD PRIMARY KEY (`cat_id`);

--
-- Indizes für die Tabelle `users`
--
ALTER TABLE `users`
  ADD PRIMARY KEY (`usr_id`);

--
-- AUTO_INCREMENT für exportierte Tabellen
--

--
-- AUTO_INCREMENT für Tabelle `blogs`
--
ALTER TABLE `blogs`
  MODIFY `blog_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=6;
--
-- AUTO_INCREMENT für Tabelle `categories`
--
ALTER TABLE `categories`
  MODIFY `cat_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=5;
--
-- AUTO_INCREMENT für Tabelle `users`
--
ALTER TABLE `users`
  MODIFY `usr_id` int(11) NOT NULL AUTO_INCREMENT, AUTO_INCREMENT=4;COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
