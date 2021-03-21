-- phpMyAdmin SQL Dump
-- version 4.9.2
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  Dim 21 mars 2021 à 11:05
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
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_comment`
--

DROP TABLE IF EXISTS `as_comment`;
CREATE TABLE IF NOT EXISTS `as_comment` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPost` int(11) NOT NULL,
  `idAuthor` int(11) NOT NULL,
  `content` text NOT NULL,
  `datePublication` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idPost` (`idPost`,`idAuthor`),
  KEY `idAuthor` (`idAuthor`)
) ENGINE=InnoDB AUTO_INCREMENT=100 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_comment`
--

INSERT INTO `as_comment` (`id`, `idPost`, `idAuthor`, `content`, `datePublication`) VALUES
(79, 147, 61, 'Curabitur sit amet urna lectus', '2020-08-24 13:22:15'),
(80, 147, 58, 'Vivamus cursus dui vel arcu viverra placerat. Aliquam hendrerit luctus lorem, eget porta urna. In hac habitasse platea dictumst. Phasellus non metus ac odio rhoncus pharetra sodales quis libero. Morbi at dui eros.\r\n\r\nSuspendisse non tellus quis magna aliquet mollis eget ac sapien. Praesent ac mollis ante. Donec gravida neque dui, et hendrerit ex euismod a. Aenean scelerisque euismod dolor, quis interdum odio pellentesque eu. Aliquam sagittis feugiat erat. Nunc ut tincidunt augue. In pulvinar pellentesque maximus. Fusce placerat purus nulla, id condimentum sapien scelerisque non. Praesent vel turpis sed erat sodales pharetra lobortis vel lectus.', '2020-08-24 13:34:22'),
(83, 149, 61, 'test', '2020-08-29 13:35:23'),
(84, 149, 61, 'retry\r\n', '2020-08-29 15:09:55'),
(85, 156, 61, 'try', '2020-08-29 15:12:16'),
(86, 156, 61, 're', '2020-08-29 15:12:18'),
(87, 152, 61, 'try\r\n', '2020-08-29 20:44:03'),
(92, 148, 61, 'test', '2020-11-05 15:08:06'),
(99, 164, 61, 'try\r\n', '2020-11-15 16:14:45');

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
) ENGINE=InnoDB AUTO_INCREMENT=448 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_history`
--

INSERT INTO `as_history` (`id`, `idSchool`, `category`, `entry`, `dateEntry`) VALUES
(19, 3, 'account', 'Julien Chemin a désactivé le compte de noschool user 1', '2020-02-26 21:26:08'),
(20, 3, 'account', 'Julien Chemin a activé le compte de noschool user 1', '2020-02-26 21:26:25'),
(267, 3, 'account', 'Julien Chemin a supprimé le compte de tetete', '2020-08-22 19:12:04'),
(290, 3, 'account', 'Julien Chemin a passé no school admin 1 au grade : modérateur', '2020-08-22 19:45:32'),
(291, 3, 'account', 'Julien Chemin a passé no school admin 1 au grade : administrateur', '2020-08-22 19:45:36'),
(298, 18, 'activityPeriod', 'Bienvenue sur ArtSchool ! Vous vous êtes inscrit pour une période de 3 mois, avec 50 compte(s) affiliés a votre établissement', '2020-08-23 14:09:01'),
(299, 18, 'activityPeriod', 'L\'établissement a été activé', '2020-08-23 14:09:01'),
(300, 18, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 2020/11/23', '2020-08-23 14:09:01'),
(301, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 14:24:19'),
(302, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 14:25:18'),
(303, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 14:26:17'),
(304, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 14:42:13'),
(305, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 14:42:24'),
(306, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:02:03'),
(307, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:03:16'),
(308, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:03:57'),
(309, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:05:50'),
(310, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:07:06'),
(311, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:08:23'),
(312, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:08:37'),
(313, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:11:01'),
(314, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:11:19'),
(315, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:12:47'),
(316, 18, 'profil', 'admin old5ide principal a modifié le logo de l\'établissement', '2020-08-23 15:12:53'),
(317, 18, 'account', 'admin old5ide principal a créé un compte modérateur : admin old5ide 1', '2020-08-23 15:39:31'),
(318, 18, 'account', 'admin old5ide principal a passé admin old5ide 1 au grade : administrateur', '2020-08-23 15:42:19'),
(319, 18, 'account', 'admin old5ide principal a passé admin old5ide 1 au grade : modérateur', '2020-08-23 15:43:24'),
(320, 18, 'account', 'admin old5ide 1 a passé admin old5ide 1 au grade : administrateur', '2020-08-23 15:43:46'),
(321, 18, 'account', 'admin old5ide principal a créé un compte modérateur : moderator old5ide 1', '2020-08-23 15:46:39'),
(322, 18, 'activityPeriod', 'L\'établissement n\'est plus actif', '2020-08-23 15:51:26'),
(323, 18, 'account', 'admin old5ide principal a passé moderator old5ide 1 au grade : administrateur', '2020-08-23 15:52:07'),
(324, 18, 'account', 'admin old5ide principal a passé moderator old5ide 1 au grade : modérateur', '2020-08-23 15:52:10'),
(325, 18, 'activityPeriod', 'L\'établissement a été activé', '2020-08-23 16:02:05'),
(326, 18, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 2020/09/23', '2020-08-23 16:02:05'),
(327, 18, 'account', 'admin old5ide principal a créé un compte modérateur : test', '2020-08-23 16:07:57'),
(328, 18, 'account', 'admin old5ide principal a passé test au grade : utilisateur', '2020-08-23 16:20:45'),
(329, 18, 'account', 'admin old5ide principal a supprimé le compte de test', '2020-08-23 16:21:07'),
(330, 18, 'account', 'oldo - the old donovan a créé un compte affilié à votre établissement', '2020-08-23 16:24:20'),
(331, 18, 'activityPeriod', 'L\'établissement n\'est plus actif', '2020-08-23 16:26:52'),
(332, 18, 'activityPeriod', 'L\'établissement a été activé', '2020-08-23 17:02:41'),
(333, 18, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 2020/09/23', '2020-08-23 17:02:41'),
(335, 20, 'activityPeriod', 'Bienvenue sur ArtSchool ! Vous vous êtes inscrit pour une période de 3 mois, avec 100 compte(s) affiliés a votre établissement', '2020-08-24 01:49:13'),
(336, 20, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 2020/11/23', '2020-08-24 01:49:13'),
(337, 20, 'profil', 'admin current4ngle principal a modifié le logo de l\'établissement', '2020-08-24 10:11:00'),
(338, 20, 'profil', 'admin current4ngle principal a modifié le logo de l\'établissement', '2020-08-24 10:13:17'),
(339, 20, 'account', 'admin current4ngle principal a créé un compte modérateur : admin current4ngle 1', '2020-08-24 10:21:32'),
(340, 20, 'account', 'admin current4ngle principal a passé admin current4ngle 1 au grade : administrateur', '2020-08-24 10:23:55'),
(341, 20, 'account', 'admin current4ngle principal a créé un compte modérateur : moderator current4ngle 1', '2020-08-24 10:25:05'),
(342, 20, 'account', 'curtiss a créé un compte affilié à votre établissement', '2020-08-24 11:08:23'),
(343, 21, 'activityPeriod', 'Bienvenue sur ArtSchool !', '2020-08-24 11:17:59'),
(355, 21, 'activityPeriod', 'L\'établissement a été activé', '2020-08-24 12:24:32'),
(356, 21, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 24/01/2021', '2020-08-24 12:24:32'),
(357, 21, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-08-24 12:27:41'),
(358, 21, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-08-24 12:27:52'),
(359, 21, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-08-24 12:29:03'),
(360, 20, 'profil', 'Julien Chemin a modifié le logo de l\'établissement', '2020-08-24 12:29:18'),
(361, 21, 'account', 'linoa a créé un compte affilié à votre établissement', '2020-08-24 12:31:21'),
(362, 21, 'account', 'lilou a créé un compte affilié à votre établissement', '2020-08-24 12:31:41'),
(363, 21, 'account', 'admin linart principal a désactivé le compte de linoa', '2020-08-24 15:54:35'),
(364, 18, 'profil', 'admin old5ide 1 a remplacé l\'administrateur principal par : moderator old5ide 1', '2020-08-24 18:32:24'),
(365, 18, 'account', 'admin old5ide 1 a passé moderator old5ide 1 au grade : modérateur', '2020-08-24 18:32:51'),
(366, 18, 'account', 'admin old5ide 1 a passé moderator old5ide 1 au grade : administrateur', '2020-08-24 18:33:00'),
(367, 18, 'profil', 'admin old5ide 1 a remplacé l\'administrateur principal par : admin old5ide principal', '2020-08-24 18:33:06'),
(368, 20, 'activityPeriod', 'L\'établissement n\'est plus actif', '2020-08-24 18:35:57'),
(369, 18, 'account', 'admin old5ide 1 a passé admin old5ide 1 au grade : modérateur', '2020-08-24 18:45:19'),
(370, 18, 'account', 'admin old5ide 1 a passé admin old5ide 1 au grade : administrateur', '2020-08-24 18:45:22'),
(371, 18, 'account', 'admin old5ide 1 a passé moderator old5ide 1 au grade : modérateur', '2020-08-24 18:51:56'),
(372, 18, 'account', 'admin old5ide 1 a passé moderator old5ide 1 au grade : administrateur', '2020-08-24 18:55:02'),
(373, 18, 'profil', 'admin old5ide 1 a remplacé l\'administrateur principal par : moderator old5ide 1', '2020-08-24 18:55:14'),
(374, 18, 'profil', 'admin old5ide 1 a remplacé l\'administrateur principal par : admin old5ide principal', '2020-08-24 18:55:39'),
(375, 18, 'profil', 'Julien Chemin a remplacé l\'administrateur principal par : moderator old5ide 1', '2020-08-24 19:09:32'),
(376, 18, 'profil', 'Julien Chemin a remplacé l\'administrateur principal par : admin old5ide principal', '2020-08-24 19:09:46'),
(377, 18, 'account', 'admin old5ide 1 a passé moderator old5ide 1 au grade : modérateur', '2020-08-24 19:16:14'),
(378, 18, 'account', 'admin old5ide principal a créé un compte modérateur : lulu', '2020-08-24 19:24:50'),
(379, 18, 'account', 'admin old5ide principal a supprimé le compte de lulu', '2020-08-24 19:26:08'),
(380, 3, 'account', 'Julien Chemin a activé le compte de user without school', '2020-10-09 11:47:27'),
(381, 3, 'account', 'Julien Chemin a désactivé le compte de user without school', '2020-10-09 11:58:13'),
(382, 21, 'account', '  a créé un compte modérateur : test ( testprenom testnom )', '2020-11-09 01:57:59'),
(383, 21, 'account', '  a créé un compte modérateur : testnew ( test prenom avec espace testnom )', '2020-11-09 02:00:39'),
(384, 21, 'account', '  a passé test prenom avec espace testnom au grade : administrateur', '2020-11-09 15:31:18'),
(385, 21, 'account', '  a passé test prenom avec espace testnom au grade : modérateur', '2020-11-09 15:31:23'),
(386, 21, 'account', '  a passé test prenom avec espace testnom au grade : utilisateur', '2020-11-09 15:31:28'),
(387, 21, 'account', '  a supprimé le compte de testprenom testnom', '2020-11-09 15:31:43'),
(388, 21, 'account', '  a activé le compte de  ', '2020-11-09 15:39:31'),
(389, 21, 'account', '  a désactivé le compte de  ', '2020-11-09 15:39:39'),
(390, 21, 'account', '  a passé test prenom avec espace testnom au grade : modérateur', '2020-11-09 15:39:49'),
(391, 21, 'account', '  a passé test prenom avec espace testnom au grade : administrateur', '2020-11-09 15:39:58'),
(392, 21, 'account', '  a passé test prenom avec espace testnom au grade : modérateur', '2020-11-09 15:40:00'),
(393, 21, 'account', '  a passé test prenom avec espace testnom au grade : utilisateur', '2020-11-09 15:40:03'),
(394, 21, 'account', '  a désactivé le compte de test prenom avec espace testnom', '2020-11-09 15:40:26'),
(395, 21, 'account', '  a activé le compte de test prenom avec espace testnom', '2020-11-09 15:40:29'),
(396, 21, 'account', '  a supprimé le compte de test prenom avec espace testnom', '2020-11-09 15:41:00'),
(397, 18, 'profil', '  a modifié le code d\'affiliation en : okok', '2020-11-09 15:42:13'),
(398, 18, 'profil', '  a modifié le code d\'affiliation en : old5idecode', '2020-11-09 15:42:19'),
(399, 18, 'activityPeriod', 'L\'établissement n\'est plus actif', '2020-11-09 15:45:11'),
(402, 21, 'account', 'Julien Chemin a créé un compte modérateur : tyty ( tyty tyty )', '2020-11-12 17:13:36'),
(403, 21, 'profil', 'time traveler a modifié le nom de votre établissement en : linartedited', '2020-11-12 17:21:14'),
(404, 21, 'profil', 'time traveler a modifié le nom de votre établissement en : Linart', '2020-11-12 17:21:40'),
(405, 21, 'account', 'zzzul aaa a créé un compte affilié à votre établissement', '2020-11-12 18:24:27'),
(406, 21, 'account', 'aauliu zzz a créé un compte affilié à votre établissement', '2020-11-12 18:24:55'),
(407, 21, 'account', 'time traveler a désactivé le compte de aauliu zzz', '2020-11-12 18:47:36'),
(408, 21, 'account', 'time traveler a activé le compte de aauliu zzz', '2020-11-12 18:47:45'),
(409, 21, 'account', 'time traveler a passé aauliu zzz au grade : modérateur', '2020-11-12 20:55:53'),
(410, 21, 'account', 'time traveler a passé aauliu zzz au grade : administrateur', '2020-11-12 20:55:59'),
(411, 21, 'account', 'time traveler a passé aauliu zzz au grade : modérateur', '2020-11-12 20:56:02'),
(412, 21, 'account', 'time traveler a passé aauliu zzz au grade : utilisateur', '2020-11-12 20:56:05'),
(413, 21, 'activityPeriod', 'L\'établissement n\'est plus actif', '2020-11-17 11:15:18'),
(414, 21, 'activityPeriod', 'L\'établissement a été activé', '2020-11-18 23:54:19'),
(415, 21, 'activityPeriod', 'La date de fin d\'abonnement a été repoussé jusqu\'au 18/11/2021', '2020-11-18 23:54:19'),
(416, 21, 'account', 'time traveler a activé le compte de multipass lilou', '2020-11-19 11:42:24'),
(417, 21, 'account', 'multipass lilou a quitté l\'établissement', '2020-11-21 15:36:37'),
(418, 21, 'account', 'multipass lilou a rejoint l\'établissement', '2020-11-21 15:56:06'),
(419, 21, 'account', 'multiPass Lilou a quitté l\'établissement', '2020-11-21 16:02:38'),
(420, 21, 'account', 'multiPass Lilou a rejoint l\'établissement', '2020-11-21 16:02:52'),
(421, 21, 'account', 'time traveler a activé le compte de aauliu zzz', '2020-11-22 18:03:53'),
(422, 21, 'account', 'time traveler a désactivé le compte de aauliu zzz', '2020-11-22 18:04:02'),
(423, 21, 'account', 'multiPass Lilou a quitté l\'établissement', '2020-11-22 22:37:14'),
(424, 21, 'account', 'multiPass Lilou a rejoint l\'établissement', '2020-11-22 22:40:15'),
(425, 21, 'account', 'zzzul aaa a quitté l\'établissement', '2020-11-22 22:40:39'),
(426, 21, 'account', 'time traveler a créé un compte modérateur : midori ( ri mido )', '2020-11-22 22:44:24'),
(427, 21, 'account', 'time traveler a passé ri mido au grade : administrateur', '2020-11-23 11:41:14'),
(428, 21, 'account', 'time traveler a passé ri mido au grade : modérateur', '2020-11-23 11:41:19'),
(429, 21, 'account', 'time traveler a passé ri mido au grade : administrateur', '2020-11-24 00:43:38'),
(430, 21, 'account', 'time traveler a passé ri mido au grade : modérateur', '2020-11-24 00:43:41'),
(431, 21, 'account', 'ri mido a quitté l\'établissement', '2020-11-24 00:44:58'),
(432, 21, 'account', 'riri mido a rejoint l\'établissement', '2020-11-24 11:33:31'),
(433, 21, 'account', 'time traveler a passé riri mido au grade : modérateur', '2020-11-24 11:34:29'),
(434, 3, 'account', 'Julien Chemin a activé le compte de nostradamus way', '2020-12-01 14:23:33'),
(435, 3, 'account', 'Julien Chemin a désactivé le compte de nostradamus way', '2020-12-01 14:44:01'),
(436, 21, 'profil', 'time traveler a modifié l\'adresse mail en : ', '2020-12-03 15:32:03'),
(437, 21, 'profil', 'time traveler a modifié l\'adresse mail en : ', '2020-12-03 15:32:17'),
(438, 21, 'profil', 'time traveler a modifié l\'adresse mail en : ', '2020-12-03 15:32:37'),
(439, 21, 'profil', 'time traveler a modifié le code d\'affiliation en : ', '2020-12-03 15:34:16'),
(440, 21, 'profil', 'time traveler a modifié le code d\'affiliation en : xfvxwv', '2020-12-03 15:56:27'),
(441, 21, 'profil', 'time traveler a modifié le code d\'affiliation en : LinartCode', '2020-12-03 15:56:58'),
(442, 21, 'profil', 'time traveler a modifié l\'adresse mail en : ', '2020-12-03 16:01:14'),
(443, 21, 'profil', 'time traveler a modifié l\'adresse mail en : ', '2020-12-03 16:02:15'),
(444, 21, 'profil', 'time traveler a modifié le logo de l\'établissement', '2020-12-04 15:29:34'),
(445, 21, 'profil', 'time traveler a modifié le nom de votre établissement en : Artléatoire', '2020-12-04 15:29:54'),
(446, 21, 'profil', 'Roger Lapin a modifié l\'adresse mail en : ', '2020-12-04 16:13:37'),
(447, 21, 'profil', 'Roger Lapin a modifié le code d\'affiliation en : ancienCode', '2020-12-04 16:14:03');

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
(1, 149),
(58, 147),
(58, 149),
(58, 298),
(61, 148),
(61, 149),
(61, 152),
(61, 154),
(61, 164),
(61, 166);

-- --------------------------------------------------------

--
-- Structure de la table `as_post`
--

DROP TABLE IF EXISTS `as_post`;
CREATE TABLE IF NOT EXISTS `as_post` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idAuthor` int(11) NOT NULL,
  `idSchool` int(11) NOT NULL,
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
  KEY `idAuthor` (`idAuthor`),
  KEY `idSchool` (`idSchool`)
) ENGINE=InnoDB AUTO_INCREMENT=303 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_post`
--

INSERT INTO `as_post` (`id`, `idAuthor`, `idSchool`, `title`, `filePath`, `urlVideo`, `description`, `datePublication`, `isPrivate`, `authorizedGroups`, `postType`, `fileType`, `onFolder`, `tags`, `nbLike`) VALUES
(147, 61, 21, 'post public 1 - user actif', 'public/images/dl/72f78f5f1c290fef9716f15f4bf886e2.png', NULL, '<p style=\"text-align: left;\"><strong>Fusce mauris sem, lobortis id nibh ut, scelerisque ornare nisl.</strong></p>\r\n<p style=\"text-align: left;\">&nbsp;</p>\r\n<p style=\"text-align: center;\"><em> Fusce ornare urna vel nisl fringilla, vel consectetur eros imperdiet. Sed finibus pretium leo, at elementum eros finibus ac. Morbi sed egestas ex. Maecenas tortor nisl, pulvinar accumsan eros convallis, posuere tincidunt justo. Phasellus iaculis cursus purus, ac rutrum orci aliquam non. Vestibulum tincidunt facilisis condimentum. Curabitur sit amet urna lectus. Integer viverra imperdiet leo nec hendrerit. Morbi ultrices arcu vitae laoreet venenatis. Duis finibus lectus vel diam sollicitudin, eget auctor dui pharetra. Vestibulum in varius risus, ut finibus massa. Vestibulum aliquet, libero eget cursus condimentum, nunc nisl efficitur mi, eu bibendum urna metus a quam. Nullam et mi sed dui vehicula fermentum.</em></p>\r\n<p style=\"text-align: center;\"><em><img src=\"https://i.pinimg.com/originals/78/9d/d0/789dd096dba6a2022f284fefef9aac2f.jpg\" alt=\"\" width=\"142\" height=\"92\" /></em></p>\r\n<p style=\"text-align: right;\">Me<em>.</em></p>', '2020-08-24 13:05:52', 0, NULL, 'userPost', 'image', NULL, ',lilou,Linart,post public', 1),
(148, 61, 21, 'post public 2 - user actif', 'public/images/dl/6fa3583cff7769b31d0fc98cd73f05ec.png', NULL, '<p><em>Curabitur sit amet urna lectus</em></p>', '2020-08-24 13:24:24', 0, NULL, 'userPost', 'image', NULL, ',lilou,post public,Linart', 1),
(149, 61, 21, 'dossier public 1 - user actif', 'public/images/dl/80527e2432c429437bc90f4c2a21b88a.png', NULL, '<p style=\"text-align: center;\">dossier public 1 - user actif</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: left;\"><em><strong>Suspendisse non tellus quis magna aliquet mollis eget ac sapien</strong></em></p>', '2020-08-24 13:26:31', 0, NULL, 'userPost', 'folder', NULL, NULL, 3),
(150, 61, 21, 'dossier public 2 - user actif', 'public/images/dl/7f724fdcb6de833b3e700bf02bb851f9.png', NULL, NULL, '2020-08-24 14:04:46', 0, NULL, 'userPost', 'folder', NULL, NULL, 0),
(152, 61, 21, 'post dans dossier public 1', 'public/images/dl/1fe0b354a9a6a6edbd7d8c8494c534dc.png', NULL, NULL, '2020-08-24 14:10:45', 0, NULL, 'userPost', 'image', 149, ',lilou,post public,on folder,Linart', 1),
(153, 61, 21, 'dossier dans dossier public 1', 'public/images/dl/bedcf22b03b5f009392c75c9aa72e33c.png', NULL, NULL, '2020-08-24 14:14:29', 0, NULL, 'userPost', 'folder', 149, NULL, 0),
(154, 61, 21, 'post dans dossier public 2', 'public/images/dl/884b590de48258d7d92fa1e0110b5c9b.png', NULL, NULL, '2020-08-24 14:17:37', 0, NULL, 'userPost', 'image', 149, ',post public,lilou,Linart,on folder', 1),
(156, 61, 21, 'post on folder on folder', 'public/images/dl/5f429944517f8042a1f1e2dd690f5ad0.png', NULL, NULL, '2020-08-24 14:20:59', 0, NULL, 'userPost', 'image', 153, ',on folder,folder on folder,post public,lilou,Linart', 0),
(164, 57, 20, NULL, 'public/images/dl/faaf6c1312c431c87b8476aaf7f65bc3.png', NULL, NULL, '2020-08-24 15:59:19', 0, NULL, 'userPost', 'image', NULL, ',curtiss,post public', 1),
(165, 57, 20, 'dossier public 1 ', 'public/images/dl/41b131e1442b2c95453df9363e3dd46d.png', NULL, NULL, '2020-08-24 15:59:38', 0, NULL, 'userPost', 'folder', NULL, NULL, 0),
(166, 57, 20, NULL, 'public/images/dl/da3e47512dc5fee0967360ec4983fce5.png', NULL, NULL, '2020-08-24 16:00:15', 0, NULL, 'userPost', 'image', 165, ',post public,curtiss', 1),
(174, 61, 21, NULL, NULL, 'S-W0NX97DB0', NULL, '2020-08-24 16:27:14', 0, NULL, 'userPost', 'video', NULL, ',science', 0),
(286, 58, 21, 'priv fold', NULL, NULL, NULL, '2020-11-19 11:43:56', 1, ',group_1', 'schoolPost', 'folder', NULL, NULL, 0),
(287, 61, 21, 'try', NULL, NULL, NULL, '2020-11-19 11:45:12', 1, 'none', 'schoolPost', 'folder', 286, NULL, 0),
(288, 58, 21, 'test public on private', NULL, NULL, NULL, '2020-11-19 11:45:48', 1, 'none', 'schoolPost', 'folder', 287, NULL, 0),
(298, 58, 21, NULL, 'public/images/dl/87da18278165b5aee390be1fb632457d.gif', NULL, NULL, '2021-01-21 14:45:40', 0, NULL, 'userPost', 'image', NULL, NULL, 1),
(299, 58, 21, 'dossier privé', 'public/images/dl/cda2b7137d2fc0a6aaebfe37c54ce368.gif', NULL, NULL, '2021-03-16 17:15:14', 1, NULL, 'schoolPost', 'folder', NULL, NULL, 0),
(300, 61, 21, NULL, 'public/images/dl/b493daec1cfe2ef1607b12a3494bfa91.png', NULL, NULL, '2021-03-21 11:34:56', 0, NULL, 'userPost', 'image', NULL, ',lilou,post public,red', 0),
(301, 58, 21, NULL, 'public/images/dl/1b30368793bd776cf7e860381bbfbdac.gif', NULL, NULL, '2021-03-21 11:37:50', 1, ',group_1', 'schoolPost', 'image', NULL, NULL, 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=407 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_profile_content`
--

INSERT INTO `as_profile_content` (`id`, `userId`, `schoolId`, `tab`, `size`, `contentOrder`, `align`, `content`) VALUES
(346, 60, NULL, 'profile', 'medium', 1, '', '<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"https://static.zerochan.net/Rinoa.Heartilly.full.871477.jpg\" alt=\"\" width=\"635\" height=\"476\" /></p>'),
(347, 60, NULL, 'profile', 'small', 2, '', '<p style=\"text-align: center;\"><em><strong>\"lorem Vivamus pulvinar eget lectus dictum sagittis. Interdum et malesuada fames ac ante ipsum primis in faucibus. Etiam non tempor tortor. Curabitur fringilla lorem et odio scelerisque, et volutpat nisl laoreet.\"</strong></em></p>'),
(348, 60, NULL, 'profile', 'small', 3, 'elemStart', '<p style=\"text-align: center;\"><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"https://www.exobaston.com/wp-content/uploads/2018/07/linoa-dissidia-final-fantsay-nt.jpg\" alt=\"\" width=\"490\" height=\"275\" /></p>\r\n<p style=\"text-align: center;\"><strong><em>\"ipsum\"</em></strong></p>'),
(349, 60, NULL, 'profile', 'medium', 4, 'elemCenter', '<p style=\"text-align: center;\"><em><strong><img src=\"http://www.ffotaku.com/images/final-fantasy-viii/gifs/Rinoa1.gif\" alt=\"\" width=\"37\" height=\"75\" />primis in faucibus. Etiam non tempor tortor. Curabitur fringilla lorem et odio scelerisque<img src=\"http://www.ffotaku.com/images/final-fantasy-viii/gifs/Rinoa1.gif\" alt=\"\" width=\"35\" height=\"71\" /></strong></em></p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>'),
(350, 60, NULL, 'profile', 'small', 5, 'elemEnd', '<p style=\"text-align: center;\"><img src=\"https://steamusercontent-a.akamaihd.net/ugc/919168520692309624/22552795EAE6EACB4717D7DDB04F9F202C8C07D6/\" alt=\"\" width=\"488\" height=\"274\" /></p>'),
(353, 61, NULL, 'profile', 'medium', 1, 'elemCenter', '<p style=\"text-align: center;\">&lt;--- Morbi sed egestas ex. Maecenas tortor nisl, pulvinar accumsan eros convallis, posuere tincidunt justo. Phasellus iaculis cursus purus, ac rutrum orci aliquam non. Vestibulum tincidunt facilisis condimentum. Curabitur sit amet urna lectus. Integer viverra imperdiet leo nec hendrerit. Morbi ultrices arcu vitae laoreet venenatis.</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">Duis finibus lectus vel diam sollicitudin, eget auctor dui pharetra. Vestibulum in varius risus, ut finibus massa. Vestibulum aliquet, libero eget cursus condimentum, nunc nisl efficitur mi, eu bibendum urna metus a quam. Nullam et mi sed dui vehicula fermentum. ---&gt;</p>'),
(354, 61, NULL, 'profile', 'medium', 3, 'elemEnd', '<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"http://auto.img.v4.skyrock.net/8644/60908644/pics/3203063487_1_4_qZapi4Ar.gif\" alt=\"\" width=\"1003\" height=\"373\" /></p>'),
(355, 61, NULL, 'profile', 'big', 4, '', '<p><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"https://31.media.tumblr.com/tumblr_lft3fkYf8Z1qbpdcto1_500.gif\" alt=\"\" width=\"1499\" height=\"552\" /></p>'),
(356, 61, NULL, 'about', 'big', 1, '', '<p><em>Curabitur sit amet urna lectus</em></p>\r\n<p style=\"text-align: center;\"><em>Curabitur sit amet urna lectus</em></p>\r\n<p style=\"text-align: right;\"><em>Curabitur sit amet urna lectus</em></p>'),
(373, 61, NULL, 'profile', 'medium', 2, 'elemStart', '<p><br /><img style=\"display: block; margin-left: auto; margin-right: auto;\" src=\"http://i.imgur.com/oFtpvhy.jpg\" alt=\"\" width=\"473\" height=\"464\" /></p>'),
(390, 58, NULL, 'profile', 'small', 1, '', '<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">1</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>'),
(399, 58, NULL, 'profile', 'small', 2, '', '<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">2</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>'),
(401, 58, NULL, 'profile', 'big', 4, '', '<p style=\"text-align: center;\">4</p>'),
(402, 58, NULL, 'profile', 'medium', 5, '', '<p style=\"text-align: center;\">5</p>'),
(403, 58, NULL, 'profile', 'small', 6, '', '<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">6</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>\r\n<p style=\"text-align: center;\">&nbsp;</p>'),
(405, 58, NULL, 'profile', 'medium', 3, '', '<p style=\"text-align: center;\">3</p>');

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
) ENGINE=InnoDB AUTO_INCREMENT=162 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_profile_content_img`
--

INSERT INTO `as_profile_content_img` (`id`, `idProfileContent`, `filePath`, `toDelete`) VALUES
(79, 346, 'https://static.zerochan.net/Rinoa.Heartilly.full.871477.jpg', 0),
(80, 348, 'https://www.exobaston.com/wp-content/uploads/2018/07/linoa-dissidia-final-fantsay-nt.jpg', 0),
(81, 349, 'http://www.ffotaku.com/images/final-fantasy-viii/gifs/Rinoa1.gif', 0),
(82, 349, 'http://www.ffotaku.com/images/final-fantasy-viii/gifs/Rinoa1.gif', 0),
(83, 349, 'http://www.ffotaku.com/images/final-fantasy-viii/gifs/Rinoa1.gif', 0),
(84, 349, 'http://www.ffotaku.com/images/final-fantasy-viii/gifs/Rinoa1.gif', 0),
(85, 350, 'https://steamusercontent-a.akamaihd.net/ugc/919168520692309624/22552795EAE6EACB4717D7DDB04F9F202C8C07D6/', 0),
(89, 354, 'http://auto.img.v4.skyrock.net/8644/60908644/pics/3203063487_1_4_qZapi4Ar.gif', 0),
(90, 355, 'https://31.media.tumblr.com/tumblr_lft3fkYf8Z1qbpdcto1_500.gif', 0),
(104, 373, 'http://i.imgur.com/oFtpvhy.jpg', 0);

-- --------------------------------------------------------

--
-- Structure de la table `as_report_comment`
--

DROP TABLE IF EXISTS `as_report_comment`;
CREATE TABLE IF NOT EXISTS `as_report_comment` (
  `idComment` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
  `dateReport` datetime NOT NULL,
  `content` text DEFAULT NULL,
  KEY `idComment` (`idComment`,`idUser`),
  KEY `idAuthor` (`idUser`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_report_other`
--

DROP TABLE IF EXISTS `as_report_other`;
CREATE TABLE IF NOT EXISTS `as_report_other` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idUser` int(11) NOT NULL,
  `dateReport` datetime NOT NULL,
  `content` text NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idUser` (`idUser`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_report_other`
--

INSERT INTO `as_report_other` (`id`, `idUser`, `dateReport`, `content`) VALUES
(20, 58, '2020-12-03 15:19:33', 'L\'utilisateur - admin linart principal ( time traveler ) - ne peut plus se connecter car l\'établissement -  - n\'existe pas / plus.'),
(21, 58, '2020-12-03 15:26:23', 'L\'utilisateur - admin linart principal ( time traveler ) - ne peut plus se connecter car l\'établissement -  - n\'existe pas / plus.');

-- --------------------------------------------------------

--
-- Structure de la table `as_report_post`
--

DROP TABLE IF EXISTS `as_report_post`;
CREATE TABLE IF NOT EXISTS `as_report_post` (
  `idPost` int(11) NOT NULL,
  `idUser` int(11) NOT NULL,
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
  KEY `idAdmin` (`idAdmin`) USING BTREE
) ENGINE=InnoDB AUTO_INCREMENT=24 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_school`
--

INSERT INTO `as_school` (`id`, `idAdmin`, `name`, `nameAdmin`, `mail`, `schoolGroups`, `code`, `nbEleve`, `nbActiveAccount`, `dateInscription`, `logo`, `isActive`, `profileBannerInfo`, `profilePictureInfo`, `profileTextInfo`) VALUES
(3, 12, 'noSchool', 'no school admin 1', '', NULL, 'Ysiiac42kilsfog4ac23b2b', 100000000, 1, '2020-01-24 13:23:47', 'https://cdn.pixabay.com/photo/2014/04/03/11/47/people-312122_960_720.png', 1, NULL, NULL, NULL),
(18, 48, 'Old5ide', 'admin old5ide principal', 'julchemin@orange.fr', NULL, 'old5idecode', 0, 0, '2020-08-23 14:09:01', 'public/images/dl/f082b28f2c11d2128a0168bce4a359e7.png', 0, NULL, NULL, NULL),
(20, 54, 'Current4ngle', 'admin current4ngle principal', 'julchemin@orange.fr', NULL, 'Current4nglecode', 0, 0, '2020-08-24 01:49:13', 'public/images/dl/1c29c2815acf937fc561ea318ad668e1.png', 0, NULL, NULL, NULL),
(21, 58, 'Artléatoire', 'admin linart principal', 'mauvais@mail.fr', ',group_1,group_2', 'ancienCode', 50, 1, '2020-08-24 11:17:59', 'public/images/dl/73d37dcb755e07b63eb2516831a41965.png', 1, 'public/images/dl/1a220e6e40360cedd9f52dbcb523d05f.png false', 'http://localhost/P5_Chemin_Julien/P5_01_Code/public/images/dl/a9598323735f88e7c2429dd292cc2658.png widePicture smallPicture', 'elemStart elemEnd');

-- --------------------------------------------------------

--
-- Structure de la table `as_school_contract_reminder`
--

DROP TABLE IF EXISTS `as_school_contract_reminder`;
CREATE TABLE IF NOT EXISTS `as_school_contract_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOwner` int(11) NOT NULL,
  `remindType` varchar(10) NOT NULL,
  `dateRemind` datetime NOT NULL,
  `dateContractEnd` datetime NOT NULL,
  `done` tinyint(1) NOT NULL DEFAULT 0,
  PRIMARY KEY (`id`),
  KEY `as_school_contract_reminder_ibfk_1` (`idOwner`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_school_contract_reminder`
--

INSERT INTO `as_school_contract_reminder` (`id`, `idOwner`, `remindType`, `dateRemind`, `dateContractEnd`, `done`) VALUES
(54, 21, 'month', '2021-10-18 00:00:00', '2021-11-18 00:00:00', 0);

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
) ENGINE=InnoDB AUTO_INCREMENT=92 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_tag`
--

INSERT INTO `as_tag` (`id`, `name`, `tagCount`) VALUES
(66, 'lilou', 6),
(67, 'Linart', 5),
(68, 'post public', 8),
(69, 'on folder', 3),
(70, 'folder on folder', 1),
(72, 'curtiss', 2),
(80, 'science', 1),
(91, 'red', 1);

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
(147, 'lilou'),
(147, 'Linart'),
(147, 'post public'),
(148, 'lilou'),
(148, 'post public'),
(148, 'Linart'),
(152, 'lilou'),
(152, 'post public'),
(152, 'on folder'),
(152, 'Linart'),
(154, 'post public'),
(154, 'lilou'),
(154, 'Linart'),
(154, 'on folder'),
(156, 'on folder'),
(156, 'folder on folder'),
(156, 'post public'),
(156, 'lilou'),
(156, 'Linart'),
(164, 'curtiss'),
(164, 'post public'),
(166, 'post public'),
(166, 'curtiss'),
(174, 'science'),
(300, 'lilou'),
(300, 'post public'),
(300, 'red');

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
  `pseudo` varchar(50) NOT NULL,
  `firstName` varchar(100) DEFAULT NULL,
  `lastName` varchar(100) DEFAULT NULL,
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
  `profileBannerInfo` text DEFAULT NULL,
  `profilePictureInfo` text DEFAULT NULL,
  `profileTextInfo` text DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=74 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_user`
--

INSERT INTO `as_user` (`id`, `pseudo`, `firstName`, `lastName`, `mail`, `school`, `schoolGroup`, `password`, `temporaryPassword`, `beingReset`, `nbWarning`, `isBan`, `isAdmin`, `isModerator`, `isActive`, `profileBannerInfo`, `profilePictureInfo`, `profileTextInfo`) VALUES
(1, 'Julien Chemin', 'Julien', 'Chemin', 'julchemin@orange.fr', 'allSchool', NULL, '$2y$10$a6WKgH4XrjO8.eq/vQ79E.rMlMfMM4xXrySBb/H2ngZSwM2Ir/fSu', '$2y$10$Oaozfl8KS3bEzzFuKzjvNOD.lhYV22rj6H0pvX2QPN8w.K.lL6hMK', 0, 0, 0, 1, 0, 1, NULL, '', NULL),
(12, 'no school admin 1', 'jean', 'sanschool', 'noschool@admin1.fr', 'noSchool', NULL, '$2y$10$11kGFeXmkx4Znyf3oRPhku7K58Qu44.C/GDzSmcy32P5ygNtvBb9i', NULL, 0, 0, 0, 1, 0, 1, NULL, NULL, NULL),
(48, 'admin old5ide principal', 'jezz', 'oh', 'fdhfdthsfthtsfh@drgdsgsdgdgd.fsfffsf', 'Old5ide', NULL, '$2y$10$IvkI7IYMB1035KfmWhukm.vpoR0F/go3bvnBr0KJOyrWylNwTwyXC', NULL, 0, 0, 0, 1, 0, 0, 'https://cdn.pixabay.com/photo/2018/06/15/01/25/texture-3476000_960_720.jpg false', 'https://ici.radio-canada.ca/concours/la_preuve_par_l_image/2019/images/photo/15_Bretheau_g.jpg widePicture bigPicture', NULL),
(49, 'admin old5ide 1', 'jezz', 'little', 'sdgsdvdsgv@sdrgsdrgds.dgvdsvgf', 'Old5ide', NULL, '$2y$10$DL6pdMRlCQVfS3m0gXFwIe.ewE674rKcyz8K9NFNck6JPVXUcfdC6', NULL, 0, 0, 0, 1, 0, 0, NULL, NULL, NULL),
(50, 'moderator old5ide 1', 'lily', 'moitier', 'dfdgrd@sefqsfsrf.frcfb', 'Old5ide', NULL, '$2y$10$5/VSF7W6o1AfVaTl7ylbbuLOUiBAXH0f3gQpaz.2Xpx.8TiuGElH.', NULL, 0, 0, 0, 0, 1, 0, NULL, NULL, NULL),
(52, 'oldo - the old donovan', 'donovan', 'olodone', 'otod@otodsfresfs.sfseffsf', 'Old5ide', NULL, '$2y$10$Ce9tElltLKM6wvhwLgRvuuwcJXlD5kSQP9HtTl.v3vW6VxYHhuQHW', NULL, 0, 0, 0, 0, 0, 0, 'public/images/dl/45a2214696ea585c9be697d20d818d38.jpg false', 'public/images/dl/786e39a95dbedf1c0098048c1a894289.jpg widePicture bigPicture', NULL),
(54, 'admin current4ngle principal', 'riley', 'demi', 'dsgrdsgrg@dgdgdgs.frdgd', 'Current4ngle', NULL, '$2y$10$k/nG.5NgE6sqLknHvTiAiONs1Uj7LPS95Yof1tBlvqo9vA1g9IbUu', NULL, 0, 0, 0, 1, 0, 0, NULL, NULL, NULL),
(55, 'admin current4ngle 1', 'pitka', 'frogy', 'sgvgdsqgq@qsrgqgrq.grsg', 'Current4ngle', NULL, '$2y$10$ZNyM4AdZVMStIJL9P4nbeu/NO5Qqjcv1vH.8I9ZXZb8RtOLWLHQr6', NULL, 0, 0, 0, 1, 0, 0, NULL, NULL, NULL),
(56, 'moderator current4ngle 1', 'aspie', 'papabear', 'wwgvwds@dsgrsqgvbd.gdxvf', 'Current4ngle', NULL, '$2y$10$PF45nlzruqYW7sj/RgEGa.v8VoQrp4vm5KidJPh4/8H1evV3DwUAW', NULL, 0, 0, 0, 0, 1, 0, NULL, NULL, NULL),
(57, 'curtiss', 'kurt', 'cobinla', 'sfsfsf@sqrgdsqrgv.gqrdgd', 'Current4ngle', NULL, '$2y$10$1ToJBQGbPRYVSJfBMmwQZewW/Wm4KeXdMp6fafOIptEBqTryRwsGi', NULL, 0, 0, 0, 0, 0, 0, 'https://th.bing.com/th/id/OIP.OhqjIK0gItw2jdrjmfOrAAHaEo?pid=Api false', 'https://upload.wikimedia.org/wikipedia/commons/thumb/8/8d/Curtiss_logo.svg/1280px-Curtiss_logo.svg.png widePicture mediumPicture', NULL),
(58, 'admin aleatoire', 'Roger', 'Lapin', 'sqrgfqsgsqg@qsrgqsrgq.gfqrdsf', 'Artléatoire', NULL, '$2y$10$Tt1014DSzfPYcigvwphdMOOpP7ET.HFH34EnDTkytDq3NWdsUn9MG', NULL, 0, 0, 0, 1, 0, 1, 'public/images/dl/7c8ec283216191e1e8bf4db12c1b17c8.gif false', 'http://localhost/P5_Chemin_Julien/P5_01_Code/public/images/dl/5344402f3324b922adc83effc4897998.gif highPicture smallPicture', NULL),
(60, 'linoa', 'rinoa', 'from_final_fantasy_everyone_know_me', 'ggvdvg@dgvrdsqg.gdr', 'Artléatoire', 'group_1', '$2y$10$zsdAbbIf5Fgx4IF.Jo84HeDavlNFJtGS1MpVl.LCTG04/expRNUPe', NULL, 0, 0, 0, 0, 0, 0, 'public/images/dl/328e758c9dc34df2c9f27d2daba7099f.jpg false', 'public/images/dl/d920350b2fb85256bd70df972be6cd4c.jpg widePicture bigPicture', 'elemStart elemCenter elemEnd'),
(61, 'lilou', 'multiPass', 'Lilou', 'lilou@lilou.lilou', 'Artléatoire', 'group_1', '$2y$10$B.YdOXkZVu0Xc/fAA8wWPeTIHaU8WhAlPI/OffGmCvnW8fgih6E9m', NULL, 0, 0, 0, 0, 0, 1, 'https://31.media.tumblr.com/tumblr_lft3fkYf8Z1qbpdcto1_500.gif false', 'http://localhost/P5_Chemin_Julien/P5_01_Code/public/images/dl/f03af32acba7f044837d60be056ee27a.png highPicture bigPicture', 'elemEnd elemCenter elemEnd'),
(63, 'user without school', 'nostradamus', 'way', 'gsgdd@rgdgdrg.dgdrg', 'noSchool', NULL, '$2y$10$CDglK8341HmLdvgeHak6MOdOfCmL0YU8iRVmy1VPR/O/IY1N.DFOG', NULL, 0, 0, 0, 0, 0, 0, 'public/images/dl/aa5b33572511ec7658529740d1ed88f7.jpg false', 'http://localhost/P5_Chemin_Julien/P5_01_Code/public/images/dl/257bb73e27d920eaf3ff390067858673.jpg highPicture smallPicture', 'elemEnd elemCenter elemEnd'),
(70, 'aaa', 'zzzul', 'aaa', 'zzeez@eze.ze', 'noSchool', NULL, '$2y$10$JWRn3pKEyfGYXwC0bxPUDOOUK2dJ6ZcQKQQ2CQpJCnKIvlAfdKGNK', NULL, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(71, 'zzz', 'aauliu', 'zzz', 'zzdsdseez@eze.ze', 'Artléatoire', NULL, '$2y$10$r6QY2dCh5QAE0sXsgpIwxOWNjIQ8MShDDb1JXcUJ3cD59m5op1cq2', NULL, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(72, 'testy', 'testy', 'testy', 'tettest@stset.ets', 'noSchool', NULL, '$2y$10$Glwen1uxn67svsKT5xUcD.pvRZXiQG//YTaAqpHcl1Tm.EQdMIDAu', NULL, 0, 0, 0, 0, 0, 0, NULL, NULL, NULL),
(73, 'midori', 'riri', 'mido', 'midori@midori.midori', 'Artléatoire', NULL, '$2y$10$kNC4Hw7iqXIcKfYNrKIvVObY8ZFbpo8gp89DCY6UMOG9w8.K1dHFu', NULL, 0, 0, 0, 0, 1, 1, NULL, NULL, NULL);

-- --------------------------------------------------------

--
-- Structure de la table `as_user_contract_reminder`
--

DROP TABLE IF EXISTS `as_user_contract_reminder`;
CREATE TABLE IF NOT EXISTS `as_user_contract_reminder` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idOwner` int(11) NOT NULL,
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
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=utf8;

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
-- Contraintes pour la table `as_post`
--
ALTER TABLE `as_post`
  ADD CONSTRAINT `as_post_ibfk_1` FOREIGN KEY (`idAuthor`) REFERENCES `as_user` (`id`),
  ADD CONSTRAINT `as_post_ibfk_2` FOREIGN KEY (`idSchool`) REFERENCES `as_school` (`id`);

--
-- Contraintes pour la table `as_profile_content`
--
ALTER TABLE `as_profile_content`
  ADD CONSTRAINT `as_profile_content_ibfk_1` FOREIGN KEY (`userId`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_profile_content_ibfk_2` FOREIGN KEY (`schoolId`) REFERENCES `as_school` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_report_comment`
--
ALTER TABLE `as_report_comment`
  ADD CONSTRAINT `as_report_comment_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  ADD CONSTRAINT `as_report_comment_ibfk_2` FOREIGN KEY (`idComment`) REFERENCES `as_comment` (`id`) ON DELETE CASCADE ON UPDATE CASCADE;

--
-- Contraintes pour la table `as_report_other`
--
ALTER TABLE `as_report_other`
  ADD CONSTRAINT `as_report_other_ibfk_1` FOREIGN KEY (`idUser`) REFERENCES `as_user` (`id`);

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
  ADD CONSTRAINT `as_school_ibfk_1` FOREIGN KEY (`idAdmin`) REFERENCES `as_user` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

--
-- Contraintes pour la table `as_school_contract_reminder`
--
ALTER TABLE `as_school_contract_reminder`
  ADD CONSTRAINT `as_school_contract_reminder_ibfk_1` FOREIGN KEY (`idOwner`) REFERENCES `as_school` (`id`) ON DELETE NO ACTION ON UPDATE NO ACTION;

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
