-- Adminer 4.3.1 MySQL dump

SET NAMES utf8;
SET time_zone = '+00:00';
SET foreign_key_checks = 0;
SET sql_mode = 'NO_AUTO_VALUE_ON_ZERO';

SET NAMES utf8mb4;

CREATE DATABASE `project` /*!40100 DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci */;
USE `project`;

DROP TABLE IF EXISTS `articles`;
CREATE TABLE `articles` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `category` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NULL',
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NULL',
  `title` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'NULL',
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  `is_pinned` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `articles` (`id`, `category`, `author`, `title`, `content`, `date`, `is_pinned`) VALUES
(1,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(2,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(3,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(4,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(6,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(7,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(8,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(9,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(13,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(14,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(15,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(16,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(17,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(18,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(19,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(20,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(28,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(29,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(30,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(31,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(32,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(33,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(34,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(35,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(36,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(37,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(38,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(39,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	1),
(40,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(41,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(42,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0),
(43,	'Network',	'test',	'My great article',	'This is my firt post about my great article',	'2017-07-23 23:44:24',	0);

DROP TABLE IF EXISTS `ban`;
CREATE TABLE `ban` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `msg` varchar(255) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `categories`;
CREATE TABLE `categories` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `item_count` tinyint(4) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `categories` (`id`, `name`, `item_count`) VALUES
(1,	'Network',	2),
(2,	'Developpment',	0),
(3,	'Exploits',	0);

DROP TABLE IF EXISTS `friends`;
CREATE TABLE `friends` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` text NOT NULL,
  `validate` tinyint(4) NOT NULL DEFAULT '0',
  `attempts` tinyint(4) NOT NULL DEFAULT '0',
  `viewed` tinyint(4) NOT NULL DEFAULT '0',
  `deleted_by` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `mute`;
CREATE TABLE `mute` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `start` datetime NOT NULL,
  `end` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `private`;
CREATE TABLE `private` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `sender` varchar(255) NOT NULL,
  `contact` varchar(255) NOT NULL,
  `message` text NOT NULL,
  `date` datetime NOT NULL,
  `viewed` tinyint(4) NOT NULL DEFAULT '0',
  `is_typing` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;


DROP TABLE IF EXISTS `replies`;
CREATE TABLE `replies` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `article_id` int(11) NOT NULL,
  `author` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `content` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `date` datetime NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;

INSERT INTO `replies` (`id`, `article_id`, `author`, `content`, `date`) VALUES
(1,	1,	'mark',	'Great post !',	'2017-07-22 23:07:56'),
(2,	1,	'mark',	'Great post !',	'2017-07-22 23:07:56');

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(255) NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `reg_date` date NOT NULL,
  `rank` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=latin1;

INSERT INTO `users` (`id`, `name`, `email`, `password`, `reg_date`, `rank`) VALUES
(2,	'test',	'ea97b75619f5cb2b9df9d184c4541aafe3b87484',	'$2y$10$q5zgDaaEKf9gYufxmU6PEeqCFA5akLqUL8Ca8vBOGV.yFAWDw64U6',	'2017-07-21',	0),
(3,	'____',	'7197f75b9213873c4679bfcb982a00943fc0ffb3',	'$2y$10$uQPTMcD5nh8oXhM0FJwFWeOud2oUs2cbz/9FPDvKCG3CUi.wGQ7t.',	'2017-07-24',	0),
(4,	'$*#@',	'c0556c2efb85e93037681164036c8d2c3fc91ee1',	'$2y$10$OrC74dKcajDe4H1Td.G0heW2nFflgU993a5IpWf3xYylV.1G09XMu',	'2017-07-24',	0);

-- 2017-07-25 07:37:05
