-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  sam. 08 août 2020 à 14:06
-- Version du serveur :  10.4.10-MariaDB
-- Version de PHP :  7.3.12

SET SQL_MODE = "NO_AUTO_VALUE_ON_ZERO";
SET AUTOCOMMIT = 0;
START TRANSACTION;
SET time_zone = "+00:00";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8mb4 */;

--
-- Base de données :  `art_school`
--

-- --------------------------------------------------------

--
-- Structure de la table `as_banishment`
--

DROP TABLE IF EXISTS `as_banishment`;
CREATE TABLE IF NOT EXISTS `as_banishment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `dateBanishment` datetime NOT NULL,
  `dateUnbanishment` datetime NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `as_banishment_ibfk_1` (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_comment`
--

DROP TABLE IF EXISTS `as_comment`;
CREATE TABLE IF NOT EXISTS `as_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPost` int(11) NOT NULL,
  `idAuthor` int(11) NOT NULL,
  `nameAuthor` varchar(355) NOT NULL,
  `profilePictureAuthor` varchar(355) NOT NULL DEFAULT 'public/images/question-mark.png',
  `content` text NOT NULL,
  `datePublication` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idPost` (`idPost`,`idAuthor`),
  KEY `idAuthor` (`idAuthor`)
) ENGINE=InnoDB AUTO_INCREMENT=73 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_comment`
--

INSERT INTO `as_comment` (`id`, `idPost`, `idAuthor`, `nameAuthor`, `profilePictureAuthor`, `content`, `datePublication`) VALUES
(55, 59, 2, 'school 1 admin 1', 'public/images/question-mark.png', 'retry\r\n', '2020-05-27 11:49:15'),
(58, 58, 2, 'school 1 admin 1', 'public/images/question-mark.png', 'try', '2020-06-04 15:03:46'),
(59, 57, 1, 'Julien Chemin', 'public/images/question-mark.png', 'try\r\n', '2020-06-07 01:55:25'),
(60, 57, 1, 'Julien Chemin', 'public/images/question-mark.png', 'tyry\r\n', '2020-06-07 01:55:30'),
(63, 59, 2, 'school 1 admin 1', 'http://localhost/P5_Chemin_Julien/P5_01_Code/public/images/dl/abc10638ccdf9a3e0a4e8a5c30ed13c4.png', 'try', '2020-06-11 18:07:40'),
(71, 59, 6, 'school 1 user 1', 'public/images/question-mark.png', 'tr', '2020-06-12 12:37:49');

-- --------------------------------------------------------

--
-- Structure de la table `as_history`
--

DROP TABLE IF EXISTS `as_history`;
CREATE TABLE IF NOT EXISTS `as_history` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idSchool` int(11) NOT NULL,
  `category` varchar(50) NOT NULL,
  `entry` varchar(255) NOT NULL,
  `dateEntry` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idSchool` (`idSchool`)
) ENGINE=InnoDB AUTO_INCREMENT=260 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_history`
--

INSERT INTO `as_history` (`id`, `idSchool`, `category`, `entry`, `dateEntry`) VALUES
(1, 2, 'account', 'Julien Chemin a passé school 2 moderator 2 au grade : utilisateur', '2020-02-17 18:07:58'),
(2, 2, 'account', 'Julien Chemin a passé school 2 moderator 2 au grade : modérateur', '2020-02-17 18:08:18'),
(3, 2, 'profil', 'Julien Chemin a modifié le code d\'affiliation en \"testeditcode\"', '2020-02-18 21:55:04'),
(4, 2, 'profil', 'Julien Chemin a modifié le code d\'affiliation en \"code 2\"', '2020-02-18 21:55:13'),
(5, 2, 'profil', 'Julien Chemin a modifié le code d\'affiliation en \"testeditcode\"', '2020-02-18 21:55:18'),
(6, 2, 'profil', 'Julien Chemin a modifié le code d\'affiliation en \"code 2\"', '2020-02-18 21:55:32'),
(7, 4, 'activityPeriod', 'Bienvenue sur ArtSchool ! Vous vous êtes inscrit pour une période de 6 mois. L\'abonnement prendra fin le %18/%08/%2020 à 23:08.10', '2020-02-19 00:21:10'),
(8, 4, 'activityPeriod', 'L\'établissement a été désactivé', '2020-02-19 00:29:33'),
(9, 4, 'activityPeriod', 'L\'établissement a été activé', '2020-02-19 00:29:43'),
(10, 4, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au18/11/2020', '2020-02-19 00:31:17'),
(11, 1, 'activityPeriod', 'L\'établissement a été activé', '2020-02-19 20:48:26'),
(12, 5, 'activityPeriod', 'Bienvenue sur ArtSchool ! Vous vous êtes inscrit pour une période de 3 mois, avec 5 compte(s) affiliés a votre établissement. L\'abonnement prendra fin le 26/05/2020', '2020-02-26 20:56:43'),
(13, 5, 'profil', 'Julien Chemin a modifié le nom de votre établissement en : retest', '2020-02-26 20:57:26'),
(14, 5, 'profil', 'Julien Chemin a modifié le code d\'affiliation en : testeditcode', '2020-02-26 21:08:57'),
(15, 5, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à 7', '2020-02-26 21:11:10'),
(16, 5, 'activityPeriod', 'L\'établissement a été désactivé', '2020-02-26 21:11:21'),
(17, 5, 'activityPeriod', 'L\'établissement a été activé', '2020-02-26 21:11:29'),
(18, 5, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 26/08/2020', '2020-02-26 21:11:39'),
(19, 3, 'account', 'Julien Chemin a désactivé le compte de noschool user 1', '2020-02-26 21:26:08'),
(20, 3, 'account', 'Julien Chemin a activé le compte de noschool user 1', '2020-02-26 21:26:25'),
(21, 5, 'account', 'asaas a créé un compte affilié à votre établissement', '2020-02-26 21:30:27'),
(22, 5, 'account', 'Julien Chemin a désactivé le compte de asaas', '2020-02-26 21:31:01'),
(23, 5, 'account', 'Julien Chemin a activé le compte de asaas', '2020-02-26 21:31:08'),
(24, 5, 'account', 'Julien Chemin a passé asaas au grade : modérateur', '2020-02-26 21:31:22'),
(25, 5, 'account', 'Julien Chemin a passé asaas au grade : administrateur', '2020-02-26 21:31:29'),
(26, 5, 'profil', 'Julien Chemin a remplacé l\'administrateur principal par : asaas', '2020-02-26 21:31:45'),
(27, 5, 'profil', 'Julien Chemin a remplacé l\'administrateur principal par : osef', '2020-02-26 21:31:58'),
(28, 5, 'account', 'Julien Chemin a passé asaas au grade : modérateur', '2020-02-26 21:32:13'),
(29, 5, 'account', 'Julien Chemin a supprimé le compte de asaas', '2020-02-26 21:32:29'),
(30, 5, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-02-26 21:33:26'),
(31, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user 1', '2020-03-13 12:38:08'),
(32, 1, 'profil', 'Julien Chemin a modifié le nom de votre établissement en : testedit 1', '2020-04-18 02:39:50'),
(33, 1, 'profil', 'Julien Chemin a modifié le nom de votre établissement en : school 1', '2020-04-18 02:39:59'),
(34, 2, 'profil', 'Julien Chemin a modifié le code d\'affiliation en : code 2 edité', '2020-04-18 02:41:12'),
(35, 2, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 16/04/2021', '2020-04-18 02:49:42'),
(36, 2, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 03:01:43'),
(37, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:12:49'),
(38, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:15:52'),
(39, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:16:03'),
(40, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:16:13'),
(41, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:16:36'),
(42, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:18:14'),
(43, 4, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-04-18 12:49:42'),
(44, 4, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à ', '2020-04-18 13:01:12'),
(45, 4, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à ', '2020-04-18 13:01:21'),
(46, 4, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à ', '2020-04-18 13:01:57'),
(47, 4, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à ', '2020-04-18 13:03:37'),
(48, 4, 'profil', 'Julien Chemin a modifié le code d\'affiliation en : recode test', '2020-04-18 13:04:23'),
(49, 4, 'profil', 'Julien Chemin a modifié le nom de votre établissement en : school test again', '2020-04-18 13:04:35'),
(50, 4, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 18/02/2021', '2020-04-18 13:04:49'),
(51, 4, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à 5', '2020-04-18 13:07:41'),
(52, 2, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à 1', '2020-04-18 13:08:02'),
(53, 1, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à 10', '2020-04-18 13:08:14'),
(54, 2, 'activityPeriod', 'L\'établissement a été désactivé', '2020-04-18 13:09:07'),
(55, 2, 'account', 'Julien Chemin a passé school 2 moderator 1 au grade : administrateur', '2020-04-18 13:10:10'),
(56, 2, 'account', 'Julien Chemin a passé school 2 moderator 1 au grade : modérateur', '2020-04-18 13:10:13'),
(57, 2, 'account', 'Julien Chemin a supprimé le compte de school 2 moderator 2', '2020-04-18 13:11:04'),
(58, 2, 'activityPeriod', 'L\'établissement a été activé', '2020-04-18 13:12:41'),
(59, 2, 'activityPeriod', 'L\'établissement a été désactivé', '2020-04-18 13:12:52'),
(60, 2, 'activityPeriod', 'L\'établissement a été activé', '2020-04-18 13:12:57'),
(61, 2, 'activityPeriod', 'L\'établissement a été désactivé', '2020-04-18 13:14:25'),
(62, 2, 'activityPeriod', 'L\'établissement a été activé', '2020-04-18 13:14:31'),
(63, 1, 'account', 'Julien Chemin a passé school 1 moderator 2 au grade : utilisateur', '2020-04-18 13:15:47'),
(64, 1, 'account', 'Julien Chemin a désactivé le compte de school 1 moderator 2', '2020-04-18 13:15:53'),
(65, 1, 'account', 'Julien Chemin a activé le compte de school 1 moderator 2', '2020-04-18 13:15:58'),
(66, 1, 'account', 'Julien Chemin a passé school 1 moderator 2 au grade : modérateur', '2020-04-18 13:16:02'),
(67, 1, 'account', 'Julien Chemin a activé le compte de school 1 user inactif 1', '2020-04-18 13:16:07'),
(68, 1, 'activityPeriod', 'L\'établissement a été désactivé', '2020-04-18 13:16:59'),
(69, 1, 'activityPeriod', 'L\'établissement a été activé', '2020-04-18 13:18:20'),
(70, 1, 'account', 'Julien Chemin a activé le compte de school 1 user 1', '2020-04-18 13:18:28'),
(71, 1, 'account', 'Julien Chemin a passé school 1 moderator 1 au grade : administrateur', '2020-04-18 13:59:08'),
(72, 1, 'account', 'Julien Chemin a passé school 1 moderator 1 au grade : modérateur', '2020-04-18 13:59:12'),
(73, 1, 'account', 'Julien Chemin a activé le compte de school 1 user inactif 1', '2020-04-18 13:59:18'),
(74, 1, 'account', 'Julien Chemin a désactivé le compte de school 1 user inactif 1', '2020-04-18 13:59:24'),
(75, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user inactif 1', '2020-05-02 22:25:51'),
(76, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user inactif 1', '2020-05-02 22:26:02'),
(77, 2, 'activityPeriod', 'L\'établissement a été désactivé', '2020-05-05 13:10:21'),
(78, 2, 'activityPeriod', 'L\'établissement a été activé', '2020-05-07 12:53:57'),
(79, 2, 'activityPeriod', 'L\'établissement a été désactivé', '2020-05-07 12:54:02'),
(80, 1, 'profil', 'Julien Chemin a modifié le code d\'affiliation en : code school 1', '2020-05-07 13:07:14'),
(81, 1, 'profil', 'school 1 admin 1 a modifié le code d\'affiliation en : code 1', '2020-05-07 13:11:26'),
(82, 1, 'account', 'Julien Chemin a activé le compte de school 1 user inactif 1', '2020-05-11 01:00:35'),
(83, 1, 'account', 'Julien Chemin a désactivé le compte de school 1 user inactif 1', '2020-05-11 01:19:10'),
(84, 1, 'account', 'Julien Chemin a activé le compte de school 1 user inactif 1', '2020-05-11 01:19:50'),
(85, 1, 'account', 'Julien Chemin a désactivé le compte de school 1 user inactif 1', '2020-05-11 01:20:12'),
(86, 1, 'activityPeriod', 'L\'établissement a été désactivé', '2020-05-12 00:10:21'),
(87, 1, 'activityPeriod', 'L\'établissement a été activé', '2020-05-12 00:11:18'),
(88, 1, 'account', 'Julien Chemin a activé le compte de school 1 user 1', '2020-05-12 00:11:45'),
(89, 1, 'account', 'Julien Chemin a passé school 1 moderator 1 au grade : administrateur', '2020-05-12 11:47:29'),
(90, 1, 'account', 'Julien Chemin a passé school 1 moderator 1 au grade : modérateur', '2020-05-12 11:47:34'),
(91, 1, 'account', 'Julien Chemin a passé school 1 moderator 1 au grade : utilisateur', '2020-05-12 11:47:39'),
(92, 1, 'account', 'Julien Chemin a passé school 1 moderator 1 au grade : modérateur', '2020-05-12 11:47:45'),
(93, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user 1', '2020-05-14 14:06:58'),
(94, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user 1', '2020-05-14 14:07:03'),
(95, 1, 'account', 'school 1 admin 1 a passé school 1 user 1 au grade : modérateur', '2020-05-14 14:07:59'),
(96, 1, 'account', 'school 1 admin 1 a passé school 1 user 1 au grade : utilisateur', '2020-05-14 14:08:10'),
(97, 1, 'account', 'school 1 admin 1 a passé school 1 user 1 au grade : modérateur', '2020-05-14 14:08:23'),
(98, 1, 'account', 'school 1 admin 1 a passé school 1 user 1 au grade : utilisateur', '2020-05-14 14:08:27'),
(99, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : administrateur', '2020-05-14 14:08:55'),
(100, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : modérateur', '2020-05-14 14:08:58'),
(101, 1, 'account', 'school 1 admin 1 a créé un compte modérateur : test_-è n,', '2020-05-14 14:09:56'),
(102, 1, 'account', 'school 1 admin 1 a supprimé le compte de test_-è n,', '2020-05-14 14:10:29'),
(103, 1, 'account', 'school 1 admin 1 a créé un compte modérateur : try', '2020-05-14 14:14:22'),
(104, 1, 'account', 'school 1 admin 1 a passé try au grade : utilisateur', '2020-05-14 14:14:28'),
(105, 1, 'account', 'school 1 admin 1 a supprimé le compte de try', '2020-05-14 14:14:38'),
(106, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user inactif 1', '2020-05-14 14:26:16'),
(107, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : utilisateur', '2020-05-14 14:26:20'),
(108, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : modérateur', '2020-05-14 14:26:31'),
(109, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user inactif 1', '2020-05-14 14:26:33'),
(110, 1, 'account', 'testuser a créé un compte affilié à votre établissement', '2020-05-14 14:28:52'),
(111, 1, 'account', 'school 1 admin 1 a passé testuser au grade : modérateur', '2020-05-14 14:33:02'),
(112, 1, 'account', 'Julien Chemin a activé le compte de school 1 user inactif 1', '2020-05-14 14:34:15'),
(113, 1, 'account', 'Julien Chemin a désactivé le compte de school 1 user inactif 1', '2020-05-14 14:34:20'),
(114, 1, 'account', 'Julien Chemin a passé testuser au grade : administrateur', '2020-05-14 14:34:26'),
(115, 1, 'account', 'Julien Chemin a passé testuser au grade : modérateur', '2020-05-14 14:34:38'),
(116, 1, 'account', 'Julien Chemin a passé testuser au grade : utilisateur', '2020-05-14 14:35:07'),
(117, 1, 'profil', 'school 1 admin 1 a modifié le nom de votre établissement en : school 1test', '2020-05-14 15:28:54'),
(118, 1, 'profil', 'school 1 admin 1 a modifié le nom de votre établissement en : school 1', '2020-05-14 15:29:11'),
(119, 1, 'profil', 'school 1 admin 1 a remplacé l\'administrateur principal par : school 1 admin 2', '2020-05-14 15:29:45'),
(120, 1, 'profil', 'school 1 admin 1 a remplacé l\'administrateur principal par : school 1 admin 1', '2020-05-14 15:30:09'),
(121, 1, 'profil', 'school 1 admin 1 a modifié le code d\'affiliation en : code t', '2020-05-14 15:30:15'),
(122, 1, 'profil', 'school 1 admin 1 a modifié le code d\'affiliation en : code 1', '2020-05-14 15:30:20'),
(123, 1, 'profil', 'school 1 admin 1 a modifié le logo de l\'établissement', '2020-05-14 15:32:27'),
(124, 1, 'profil', 'school 1 admin 1 a modifié le logo de l\'établissement', '2020-05-14 15:32:35'),
(125, 1, 'profil', 'school 1 admin 1 a modifié le logo de l\'établissement', '2020-05-14 15:32:41'),
(126, 1, 'profil', 'school 1 admin 1 a modifié le logo de l\'établissement', '2020-05-14 15:32:49'),
(127, 1, 'profil', 'school 1 admin 1 a modifié le logo de l\'établissement', '2020-05-14 15:32:54'),
(128, 1, 'profil', 'school 1 admin 1 a modifié le logo de l\'établissement', '2020-05-14 15:32:59'),
(129, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user inactif 1', '2020-05-20 01:37:27'),
(130, 1, 'account', 'school 1 admin 1 a supprimé le compte de testuser', '2020-05-25 18:03:06'),
(131, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user inactif 1', '2020-05-28 11:55:34'),
(132, 1, 'account', 'retryag a créé un compte affilié à votre établissement', '2020-05-30 12:01:38'),
(133, 5, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-07-07 13:03:38'),
(134, 2, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 16/07/2021', '2020-07-19 23:13:43'),
(135, 2, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 16/10/2021', '2020-07-20 01:19:58'),
(144, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : administrateur', '2020-07-25 13:54:17'),
(145, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : modérateur', '2020-07-25 13:54:20'),
(146, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : utilisateur', '2020-07-25 13:54:23'),
(147, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 moderator 1', '2020-07-25 14:02:28'),
(148, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 moderator 1', '2020-07-25 14:02:34'),
(149, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 moderator 1', '2020-07-25 14:02:40'),
(150, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user inactif 1', '2020-07-25 14:05:32'),
(151, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user inactif 1', '2020-07-25 14:15:36'),
(152, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 moderator 1', '2020-07-25 14:26:19'),
(153, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 moderator 1', '2020-07-25 14:27:49'),
(154, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user 1', '2020-07-25 14:28:02'),
(155, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 moderator 1', '2020-07-25 14:28:04'),
(156, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user 1', '2020-07-25 14:28:07'),
(157, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user inactif 1', '2020-07-25 14:28:09'),
(158, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user inactif 1', '2020-07-25 14:28:16'),
(159, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : modérateur', '2020-07-25 14:28:26'),
(160, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : administrateur', '2020-07-25 14:28:58'),
(161, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : modérateur', '2020-07-25 14:29:00'),
(162, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : utilisateur', '2020-07-25 14:29:02'),
(163, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 moderator 1', '2020-07-25 14:29:17'),
(164, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user inactif 1', '2020-07-25 14:29:22'),
(165, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user inactif 1', '2020-07-25 14:29:24'),
(166, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 moderator 1', '2020-07-25 14:29:26'),
(167, 1, 'account', 'school 1 admin 1 a passé school 1 moderator 1 au grade : modérateur', '2020-07-25 14:29:29'),
(168, 1, 'account', 'school 1 admin 1 a désactivé le compte de school 1 user 1', '2020-07-25 14:30:44'),
(169, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user 1', '2020-07-25 14:30:47'),
(170, 1, 'profil', 'Julien Chemin a modifié l\'adresse mail en : ', '2020-08-03 15:56:05'),
(173, 2, 'profil', 'Le nombre maximum de compte affilié à votre établissement est passé à 5', '2020-08-04 11:18:22');

-- --------------------------------------------------------

--
-- Structure de la table `as_like_post`
--

DROP TABLE IF EXISTS `as_like_post`;
CREATE TABLE IF NOT EXISTS `as_like_post` (
  `idUser` int(11) NOT NULL,
  `idPost` int(11) NOT NULL,
  PRIMARY KEY (`idUser`,`idPost`),
  KEY `idPost` (`idPost`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_like_post`
--

INSERT INTO `as_like_post` (`idUser`, `idPost`) VALUES
(1, 57),
(1, 59),
(1, 73),
(1, 74),
(2, 57),
(2, 58),
(2, 59),
(2, 73),
(2, 74),
(2, 106),
(2, 107),
(2, 108),
(4, 59),
(6, 57),
(6, 107),
(6, 138),
(9, 59),
(11, 136);

-- --------------------------------------------------------

--
-- Structure de la table `as_post`
--

DROP TABLE IF EXISTS `as_post`;
CREATE TABLE IF NOT EXISTS `as_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idAuthor` int(11) NOT NULL,
  `school` varchar(100) NOT NULL,
  `title` varchar(30) DEFAULT NULL,
  `filePath` text DEFAULT NULL,
  `urlVideo` varchar(355) DEFAULT NULL,
  `description` text DEFAULT NULL,
  `datePublication` datetime NOT NULL,
  `isPrivate` tinyint(1) NOT NULL,
  `authorizedGroups` text DEFAULT NULL,
  `postType` varchar(10) NOT NULL,
  `fileType` varchar(10) NOT NULL,
  `onFolder` int(11) DEFAULT NULL,
  `tags` text DEFAULT NULL,
  `nbLike` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idAuthor` (`idAuthor`)
) ENGINE=InnoDB AUTO_INCREMENT=140 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_post`
--

INSERT INTO `as_post` (`id`, `idAuthor`, `school`, `title`, `filePath`, `urlVideo`, `description`, `datePublication`, `isPrivate`, `authorizedGroups`, `postType`, `fileType`, `onFolder`, `tags`, `nbLike`) VALUES
(57, 2, 'school 1', 'private folder', NULL, NULL, NULL, '2020-05-20 00:42:17', 1, NULL, 'schoolPost', 'folder', NULL, NULL, 3),
(58, 2, 'school 1', 'dossier public', NULL, NULL, NULL, '2020-05-20 00:53:44', 0, NULL, 'schoolPost', 'folder', NULL, NULL, 1),
(59, 2, 'school 1', NULL, 'public/images/dl/ae37345f9327c55aeee0bae57301b7ef.jpg', NULL, NULL, '2020-05-20 01:36:28', 1, ',group_1', 'schoolPost', 'image', 57, NULL, 4),
(73, 2, 'school 1', 'test on folder in moz', 'public/images/dl/eb751063869b270c92745a174d2b3cf6.jpg', NULL, '<p>rttyyyq</p>', '2020-06-06 14:55:39', 1, NULL, 'schoolPost', 'image', 57, NULL, 2),
(74, 2, 'school 1', 'fold on fold', 'public/images/dl/91fe17fe055329a63f7f9f04472860d0.png', NULL, '<p>fopld on fold try</p>', '2020-06-06 14:57:10', 1, NULL, 'schoolPost', 'folder', 57, NULL, 2),
(75, 2, 'school 1', 'try', 'public/images/dl/db47319546035e1bb9b2bfb32ee3b305.png', NULL, '<p>vbfxwbgf</p>', '2020-06-08 15:01:29', 1, NULL, 'schoolPost', 'image', 74, NULL, 0),
(106, 6, 'school 1', NULL, 'public/images/dl/9bff7b9fb9878bac52823fcf51bdfe43.jpg', NULL, NULL, '2020-06-17 15:44:17', 0, NULL, 'userPost', 'image', NULL, ',bleu,rouge,rose', 1),
(107, 6, 'school 1', NULL, 'public/images/dl/f6ef284aa880352baf900abd065b926e.jpg', NULL, NULL, '2020-06-17 15:44:39', 0, NULL, 'userPost', 'image', NULL, ',jaune,bleu,rouge', 2),
(108, 6, 'school 1', NULL, 'public/images/dl/6f4084b508ceb582f87d69c9dfbf342d.jpg', NULL, NULL, '2020-06-17 15:45:03', 0, NULL, 'userPost', 'image', NULL, ',orange,blanc,bleu,rouge', 1),
(110, 6, 'school 1', 'fold user 1 on school profil', 'public/images/dl/dd12d1e066650b9f9125bc4c677eef0e.jpg', NULL, '<p>fold user 1 on school profil</p>', '2020-06-23 14:05:08', 1, 'none', 'schoolPost', 'folder', 74, NULL, 0),
(123, 6, 'school 1', NULL, 'public/images/dl/7e3c1de89f0ff9c5be64748c0a172a61.jpg', NULL, NULL, '2020-06-25 20:08:01', 1, 'none', 'schoolPost', 'image', 110, NULL, 0),
(125, 19, 'noSchool', 'non ref img user incatif', 'public/images/dl/f892349d6d90e062f3fdf591bf71bf98.jpg', NULL, NULL, '2020-06-28 13:21:00', 0, NULL, 'userPost', 'image', NULL, NULL, 0),
(128, 6, 'school 1', 'test', 'public/images/dl/b813a1af99c8cdc702848013bde054ab.jpg', NULL, NULL, '2020-06-28 17:32:44', 0, NULL, 'userPost', 'image', NULL, ',bleu,rouge', 0),
(129, 6, 'school 1', 'test video', NULL, 'TdVnV5RRVNg', NULL, '2020-07-02 16:24:47', 0, NULL, 'userPost', 'video', NULL, ',video,dormir chez vous', 0),
(131, 6, 'school 1', NULL, 'public/images/dl/f9d0690c89b13808a28e5b7e8ac1fc45.jpg', NULL, NULL, '2020-07-03 01:13:26', 0, NULL, 'userPost', 'image', NULL, ',rouge rose,rougeatre,rouuuuge,rougir,roger,roller', 0),
(132, 6, 'school 1', 'test vid with  thumbnai', 'public/images/dl/8aaec28d8c77d2b72bc802a80b524fd1.jpg', '2JsYHpiH2xs', NULL, '2020-07-04 18:08:59', 0, NULL, 'userPost', 'video', NULL, ',test,video', 0),
(133, 6, 'school 1', NULL, 'public/images/dl/6ee0f6971096d5191f7c63f01e6f1958.jpg', NULL, NULL, '2020-07-06 10:19:48', 0, NULL, 'userPost', 'image', NULL, ',rouge,rouge rose,rougeatre,rouuuuge', 0),
(135, 6, 'school 1', NULL, 'public/images/dl/0de0765ab01c1219dfdc6e3be58942ed.jpg', NULL, NULL, '2020-07-06 16:00:32', 0, NULL, 'userPost', 'image', NULL, ',rouuuuge,rougeatre,rougir,rouge rose', 0),
(136, 6, 'school 1', NULL, 'public/images/dl/9f4ad7554ad15738409856052f9a18a8.jpg', NULL, NULL, '2020-07-06 16:00:50', 0, NULL, 'userPost', 'image', NULL, ',rouge,bleu,blanc', 1),
(138, 6, 'school 1', NULL, 'public/images/dl/38cc7f6b794a6a04106b3cb4a272ad24.gif', NULL, NULL, '2020-08-08 12:13:19', 1, 'none', 'schoolPost', 'image', 57, NULL, 1);

--
-- Déclencheurs `as_post`
--
DROP TRIGGER IF EXISTS `post_delete`;
DELIMITER $$
CREATE TRIGGER `post_delete` AFTER DELETE ON `as_post` FOR EACH ROW DELETE FROM as_tag_post WHERE OLD.id = as_tag_post.idPost
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `as_profile_content`
--

DROP TABLE IF EXISTS `as_profile_content`;
CREATE TABLE IF NOT EXISTS `as_profile_content` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `userId` int(11) DEFAULT NULL,
  `schoolId` int(11) DEFAULT NULL,
  `tab` varchar(50) NOT NULL,
  `size` varchar(50) NOT NULL,
  `contentOrder` int(11) NOT NULL,
  `align` varchar(50) NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `userId` (`userId`),
  KEY `schoolId` (`schoolId`)
) ENGINE=InnoDB AUTO_INCREMENT=308 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_profile_content`
--

INSERT INTO `as_profile_content` (`id`, `userId`, `schoolId`, `tab`, `size`, `contentOrder`, `align`, `content`) VALUES
(205, NULL, 1, 'profile', 'small', 2, '', '<p>2</p>'),
(210, NULL, 1, 'about', 'medium', 2, 'elemCenter', '<p style=\"text-align: center;\">2</p>'),
(211, NULL, 1, 'about', 'small', 3, 'elemEnd', '<p style=\"text-align: right;\">3</p>'),
(212, NULL, 1, 'about', 'small', 1, '', '<p>1</p>'),
(214, NULL, 1, 'news', 'small', 3, '', '<p>2</p>'),
(218, NULL, 1, 'news', 'medium', 4, 'elemCenter', '<p style=\"text-align: center;\">3</p>'),
(219, NULL, 1, 'news', 'small', 2, '', '<p>1</p>'),
(222, NULL, 1, 'news', 'medium', 1, '', '<p style=\"text-align: center;\">test</p>'),
(231, NULL, 1, 'profile', 'medium', 3, 'elemEnd', '<p style=\"text-align: center;\">3</p>'),
(246, NULL, 2, 'profile', 'small', 1, '', '<p>1</p>'),
(248, NULL, 2, 'profile', 'small', 3, '', '<p>3</p>'),
(250, NULL, 2, 'profile', 'small', 2, '', '<p>2</p>'),
(305, NULL, 1, 'profile', 'small', 1, '', '<p>1</p>\r\n<p>&nbsp;</p>');

--
-- Déclencheurs `as_profile_content`
--
DROP TRIGGER IF EXISTS `delete_profile_content`;
DELIMITER $$
CREATE TRIGGER `delete_profile_content` AFTER DELETE ON `as_profile_content` FOR EACH ROW UPDATE as_profile_content_img SET toDelete = 1 WHERE as_profile_content_img.idProfileContent = OLD.id
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `as_profile_content_img`
--

DROP TABLE IF EXISTS `as_profile_content_img`;
CREATE TABLE IF NOT EXISTS `as_profile_content_img` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idProfileContent` int(11) NOT NULL,
  `filePath` varchar(355) NOT NULL,
  `toDelete` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idProfileContent` (`idProfileContent`)
) ENGINE=InnoDB AUTO_INCREMENT=47 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_report_comment`
--

DROP TABLE IF EXISTS `as_report_comment`;
CREATE TABLE IF NOT EXISTS `as_report_comment` (
  `idComment` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `userName` varchar(355) NOT NULL,
  `dateReport` datetime NOT NULL,
  `content` text DEFAULT NULL,
  KEY `idComment` (`idComment`,`idUser`),
  KEY `idAuthor` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_report_post`
--

DROP TABLE IF EXISTS `as_report_post`;
CREATE TABLE IF NOT EXISTS `as_report_post` (
  `idPost` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `userName` varchar(355) NOT NULL,
  `dateReport` datetime NOT NULL,
  `content` text DEFAULT NULL,
  KEY `idPost` (`idPost`,`idUser`),
  KEY `idAuthor` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_school`
--

DROP TABLE IF EXISTS `as_school`;
CREATE TABLE IF NOT EXISTS `as_school` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idAdmin` int(11) NOT NULL,
  `name` varchar(255) NOT NULL,
  `nameAdmin` varchar(255) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `schoolGroups` text DEFAULT NULL,
  `code` varchar(255) NOT NULL,
  `nbEleve` int(11) DEFAULT 0,
  `nbActiveAccount` int(11) NOT NULL DEFAULT 0,
  `dateInscription` datetime NOT NULL,
  `logo` text NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `profileBannerInfo` text DEFAULT NULL,
  `profilePictureInfo` text DEFAULT NULL,
  `profileTextInfo` text DEFAULT NULL,
  PRIMARY KEY (`id`),
  KEY `idAdmin` (`idAdmin`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_school`
--

INSERT INTO `as_school` (`id`, `idAdmin`, `name`, `nameAdmin`, `mail`, `schoolGroups`, `code`, `nbEleve`, `nbActiveAccount`, `dateInscription`, `logo`, `isActive`, `profileBannerInfo`, `profilePictureInfo`, `profileTextInfo`) VALUES
(1, 2, 'school 1', 'school 1 admin 1', 'julchemin@orange.fr', ',group_1,group_2', 'code 1', 3, 1, '2020-01-16 13:55:33', 'https://images.pexels.com/photos/2040125/pexels-photo-2040125.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940', 1, 'https://user-images.strikinglycdn.com/res/hrscywv4p/image/upload/c_limit,fl_lossy,h_9000,w_1200,f_auto,q_auto/1069337/957406e8-14df-4164-b700-038326d3f938_eyt7ln.png false', 'https://a.wattpad.com/cover/105177276-352-k551553.jpg highPicture bigPicture', 'elemCenter elemCenter'),
(2, 3, 'school 2', 'school 2 admin 1', '', NULL, 'code 2 edité', 5, 0, '2020-01-16 14:32:24', 'https://c.wallhere.com/photos/e8/ac/anime_anime_girls_kawaii_girl_pink_white_dress_Mahou_Shoujo_Madoka_Magica_Kaname_Madoka_pink_hair-1309355.jpg!d', 0, NULL, NULL, NULL),
(3, 12, 'noSchool', 'no school admin 1', '', NULL, 'Ysiiac42kilsfog4ac23b2b', 100000000, 1, '2020-01-24 13:23:47', 'https://cdn.pixabay.com/photo/2014/04/03/11/47/people-312122_960_720.png', 1, NULL, NULL, NULL),
(4, 16, 'school test again', 'school test admin 1', '', NULL, 'recode test', 5, 0, '2020-02-19 00:21:10', 'https://encrypted-tbn0.gstatic.com/images?q=tbn:ANd9GcRwoVnrRrJNmKa3zqCu8PtZQqhCFwKXHuLAGOQ0qZcBjb1y04UhkQ&s', 1, NULL, NULL, NULL),
(5, 17, 'retest', 'osef', '', NULL, 'testeditcode', 8, 0, '2020-02-26 20:56:43', 'public/images/dl/df169c72f9a085766d5a80aaec52bcf6.png', 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `as_school_contract_reminder`
--

DROP TABLE IF EXISTS `as_school_contract_reminder`;
CREATE TABLE IF NOT EXISTS `as_school_contract_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOwner` int(11) NOT NULL,
  `mailToRemind` varchar(255) NOT NULL,
  `remindType` varchar(10) NOT NULL,
  `dateRemind` datetime NOT NULL,
  `dateContractEnd` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idSchool` (`idOwner`)
) ENGINE=InnoDB AUTO_INCREMENT=38 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_tag`
--

DROP TABLE IF EXISTS `as_tag`;
CREATE TABLE IF NOT EXISTS `as_tag` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(355) NOT NULL,
  `tagCount` int(11) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=66 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_tag`
--

INSERT INTO `as_tag` (`id`, `name`, `tagCount`) VALUES
(45, 'rouge', 6),
(46, 'bleu', 5),
(47, 'jaune', 1),
(50, 'rose', 1),
(51, 'orange', 1),
(52, 'blanc', 2),
(56, 'video', 2),
(57, 'dormir chez vous', 1),
(58, 'rouge rose', 3),
(59, 'rougeatre', 3),
(60, 'rouuuuge', 3),
(61, 'rougir', 2),
(62, 'roger', 1),
(63, 'roller', 1),
(64, 'test', 1);

-- --------------------------------------------------------

--
-- Structure de la table `as_tag_post`
--

DROP TABLE IF EXISTS `as_tag_post`;
CREATE TABLE IF NOT EXISTS `as_tag_post` (
  `idPost` int(11) NOT NULL,
  `tagName` varchar(355) NOT NULL,
  KEY `idPost` (`idPost`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_tag_post`
--

INSERT INTO `as_tag_post` (`idPost`, `tagName`) VALUES
(106, 'bleu'),
(106, 'rouge'),
(106, 'rose'),
(107, 'jaune'),
(107, 'bleu'),
(107, 'rouge'),
(108, 'orange'),
(108, 'blanc'),
(108, 'bleu'),
(108, 'rouge'),
(128, 'bleu'),
(128, 'rouge'),
(129, 'video'),
(129, 'dormir chez vous'),
(131, 'rouge rose'),
(131, 'rougeatre'),
(131, 'rouuuuge'),
(131, 'rougir'),
(131, 'roger'),
(131, 'roller'),
(132, 'test'),
(132, 'video'),
(133, 'rouge'),
(133, 'rouge rose'),
(133, 'rougeatre'),
(133, 'rouuuuge'),
(135, 'rouuuuge'),
(135, 'rougeatre'),
(135, 'rougir'),
(135, 'rouge rose'),
(136, 'rouge'),
(136, 'bleu'),
(136, 'blanc');

--
-- Déclencheurs `as_tag_post`
--
DROP TRIGGER IF EXISTS `tag_post_delete`;
DELIMITER $$
CREATE TRIGGER `tag_post_delete` AFTER DELETE ON `as_tag_post` FOR EACH ROW UPDATE as_tag SET tagCount = tagCount - 1 WHERE as_tag.name = OLD.tagName
$$
DELIMITER ;
DROP TRIGGER IF EXISTS `tag_post_new`;
DELIMITER $$
CREATE TRIGGER `tag_post_new` AFTER INSERT ON `as_tag_post` FOR EACH ROW UPDATE as_tag SET tagCount = tagCount + 1 
WHERE NEW.tagName = name
$$
DELIMITER ;

-- --------------------------------------------------------

--
-- Structure de la table `as_user`
--

DROP TABLE IF EXISTS `as_user`;
CREATE TABLE IF NOT EXISTS `as_user` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `school` varchar(255) NOT NULL,
  `schoolGroup` varchar(50) DEFAULT NULL,
  `password` varchar(255) NOT NULL,
  `temporaryPassword` text DEFAULT NULL,
  `beingReset` tinyint(1) NOT NULL DEFAULT 0,
  `nbWarning` int(11) NOT NULL DEFAULT 0,
  `isBan` tinyint(1) NOT NULL DEFAULT 0,
  `isAdmin` tinyint(1) NOT NULL DEFAULT 0,
  `isModerator` tinyint(1) NOT NULL DEFAULT 0,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  `dateDeadline` datetime DEFAULT NULL,
  `profileBannerInfo` text DEFAULT NULL,
  `profilePictureInfo` text DEFAULT NULL,
  `profileTextInfo` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_user`
--

INSERT INTO `as_user` (`id`, `name`, `mail`, `school`, `schoolGroup`, `password`, `temporaryPassword`, `beingReset`, `nbWarning`, `isBan`, `isAdmin`, `isModerator`, `isActive`, `dateDeadline`, `profileBannerInfo`, `profilePictureInfo`, `profileTextInfo`) VALUES
(1, 'Julien Chemin', 'julchemin@orange.fr', 'allSchool', NULL, '$2y$10$kJ1W2sEancIUJPVp3Ecq2ef1Od9BxzF4bpyoyjVkyPuyn72cARy26', '$2y$10$lcWyVNH7ojRtvEjQpBUkMurrgamS1b7XMSiAD9lDQaNUl/eMkfUKu', 0, 0, 0, 1, 0, 1, NULL, NULL, NULL, NULL),
(2, 'school 1 admin 1', 'school1@admin1.fr', 'school 1', NULL, '$2y$10$XUkx5w1ZeA8o2fCZVA4jnugiINucVGgVojX7PFcHiUXZO6JfKbh4O', NULL, 0, 0, 0, 1, 0, 1, NULL, 'public/images/dl/a25885687877726e8f8808880c2db453.jpg false', 'public/images/dl/f60cbcd2d8f00cffa4b9ac781f26eaec.jpg highPicture bigPicture', 'elemCenter elemCenter elemEnd'),
(3, 'school 2 admin 1', 'school2@admin1.fr', 'school 2', NULL, '$2y$10$osVD3E3cOBsNZAKgnyV/8uaL7wI5Y.V/G4GCO89ckBtGJ8nP6yCGu', NULL, 0, 0, 0, 1, 0, 0, NULL, NULL, NULL, NULL),
(4, 'school 1 admin 2', 'school1@admin2.fr', 'school 1', NULL, '$2y$10$AMPHmJItKJDb/w5p93.RaemOPMOJOG1W9uOKpcl87ldcvA.jBH3pa', NULL, 0, 0, 0, 1, 0, 1, NULL, NULL, NULL, NULL),
(5, 'school 1 moderator 1', 'school1@moderator1.fr', 'school 1', NULL, '$2y$10$DKQbFIdAh0ow1kfgDKBFhOiX8Gdy8Hc7ylI0yrGVyC8zC84Rm3l9e', NULL, 0, 0, 0, 0, 1, 1, NULL, NULL, NULL, NULL),
(6, 'school 1 user 1', 'school1@user1.fr', 'school 1', 'group_1', '$2y$10$9hRf3BcZJUr17hCBw133IOi66snjV6Du/bN/FtI5g2SX6hNuUqdJu', NULL, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL, NULL),
(9, 'school 1 user inactif 1', 'school1@userinactif1.fr', 'school 1', 'group_2', '$2y$10$tVn4/UttJTo/ioodoijOceJIq5c6G1qWKXGHwL6Yrw6epGudMWJSG', NULL, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL),
(11, 'noschool user 1', 'noschool@user1.fr', 'noSchool', NULL, '$2y$10$8nJej/tAJSkLXhxSPnvjIe2Kkrq3MYgtyBAdkeCGEt.Dgl5oF28Mq', NULL, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL, NULL),
(12, 'no school admin 1', 'noschool@admin1.fr', 'noSchool', NULL, '$2y$10$GT4./enDDg41G6xCfl2El.lBgJIbCr3gyTRQcJSiDb9rPqVehWKQS', NULL, 0, 0, 0, 1, 0, 1, NULL, NULL, NULL, NULL),
(13, 'school 2 moderator 1', 'school2@moderator1.fr', 'school 2', NULL, '$2y$10$g5M3SrNEpIy8sRmw.jXe7eb22aVE9MTFE6wpqlAjNSMZ7e4xhHXkq', NULL, 0, 0, 0, 0, 1, 0, NULL, NULL, NULL, NULL),
(15, 'school 1 moderator 2', 'school1@moderator2.fr', 'school 1', NULL, '$2y$10$CerSWFudtNUvgOxlDj3gy..fXqZiF2cqvze28LMdP4f2e.kYx1pDm', NULL, 0, 0, 0, 0, 1, 1, NULL, NULL, NULL, NULL),
(16, 'school test admin 1', 'schooltest@admin1.fr', 'school test again', NULL, '$2y$10$lYHijLF9fL1plpl.eOKZOO/RmaJHfBmy5V2nOyubgWYj/TLnwcwyW', NULL, 0, 0, 0, 1, 0, 1, NULL, NULL, NULL, NULL),
(17, 'osef', 'osef@osef.fr', 'retest', NULL, '$2y$10$utsERJZtLXjgvSFR4pVm1O3/kNb20u4arWkYoHFz6wTknV/EQp3lS', NULL, 0, 1, 0, 1, 0, 1, NULL, NULL, NULL, NULL),
(18, 'test', 'tse@tetete.fr', 'noSchool', NULL, '$2y$10$LrLt4zxeHCOg63PXPFk.EOnNNLEmIjItn24J.r2sDIdmHVt3FauKy', NULL, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL, NULL),
(19, 'test2', 'test2@test.fr', 'noSchool', NULL, '$2y$10$0AxewiEBDmHShmbV..NjC.EQjjcwZ72iGfETZP3SVcqdVQ.RjP0Ha', NULL, 0, 0, 0, 0, 0, 1, NULL, NULL, NULL, NULL),
(47, 'tetete', 'julchettmin@ordgddfdgfange.fr', 'noSchool', NULL, '$2y$10$MzadQfEz9fjcWwKIBCRDnO8dHERMDCDxLu08twpW507siRqTFb9IC', NULL, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `as_user_contract_reminder`
--

DROP TABLE IF EXISTS `as_user_contract_reminder`;
CREATE TABLE IF NOT EXISTS `as_user_contract_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOwner` int(11) NOT NULL,
  `mailToRemind` varchar(255) NOT NULL,
  `remindType` varchar(10) NOT NULL,
  `dateRemind` datetime NOT NULL,
  `dateContractEnd` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `idUser` (`idOwner`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_warning`
--

DROP TABLE IF EXISTS `as_warning`;
CREATE TABLE IF NOT EXISTS `as_warning` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `reason` varchar(255) NOT NULL,
  `dateWarning` datetime NOT NULL,
  `dateUnwarning` datetime NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT 1,
  PRIMARY KEY (`id`),
  KEY `as_warning_ibfk_1` (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `as_banishment`
--
ALTER TABLE `as_banishment`
  ADD CONSTRAINT `as_banishment_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_comment`
--
ALTER TABLE `as_comment`
  ADD CONSTRAINT `as_comment_ibfk_1` FOREIGN KEY (`idPost`) REFERENCES `as_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_comment_ibfk_2` FOREIGN KEY (`idAuthor`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_history`
--
ALTER TABLE `as_history`
  ADD CONSTRAINT `as_history_ibfk_1` FOREIGN KEY (`idSchool`) REFERENCES `as_school` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_like_post`
--
ALTER TABLE `as_like_post`
  ADD CONSTRAINT `as_like_post_ibfk_1` FOREIGN KEY (`idPost`) REFERENCES `as_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_like_post_ibfk_2` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_profile_content`
--
ALTER TABLE `as_profile_content`
  ADD CONSTRAINT `as_profile_content_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_profile_content_ibfk_2` FOREIGN KEY (`schoolId`) REFERENCES `as_school` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_profile_content_img`
--
ALTER TABLE `as_profile_content_img`
  ADD CONSTRAINT `as_profile_content_img_ibfk_1` FOREIGN KEY (`idProfileContent`) REFERENCES `as_profile_content` (`id`);

--
-- Contraintes pour la table `as_report_comment`
--
ALTER TABLE `as_report_comment`
  ADD CONSTRAINT `as_report_comment_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_report_comment_ibfk_2` FOREIGN KEY (`idComment`) REFERENCES `as_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_report_post`
--
ALTER TABLE `as_report_post`
  ADD CONSTRAINT `as_report_post_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_report_post_ibfk_2` FOREIGN KEY (`idPost`) REFERENCES `as_post` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_school`
--
ALTER TABLE `as_school`
  ADD CONSTRAINT `as_school_ibfk_1` FOREIGN KEY (`idAdmin`) REFERENCES `as_user` (`id`);

--
-- Contraintes pour la table `as_school_contract_reminder`
--
ALTER TABLE `as_school_contract_reminder`
  ADD CONSTRAINT `as_school_contract_reminder_ibfk_1` FOREIGN KEY (`idOwner`) REFERENCES `as_school` (`id`);

--
-- Contraintes pour la table `as_user_contract_reminder`
--
ALTER TABLE `as_user_contract_reminder`
  ADD CONSTRAINT `as_user_contract_reminder_ibfk_1` FOREIGN KEY (`idOwner`) REFERENCES `as_user` (`id`);

--
-- Contraintes pour la table `as_warning`
--
ALTER TABLE `as_warning`
  ADD CONSTRAINT `as_warning_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
