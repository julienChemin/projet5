-- phpMyAdmin SQL Dump
-- version 4.8.4
-- https://www.phpmyadmin.net/
--
-- Hôte : 127.0.0.1:3306
-- Généré le :  lun. 23 mars 2020 à 20:41
-- Version du serveur :  5.7.24
-- Version de PHP :  7.2.14

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
-- Structure de la table `as_comments`
--

DROP TABLE IF EXISTS `as_comments`;
CREATE TABLE IF NOT EXISTS `as_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPost` int(11) NOT NULL,
  `idAuthor` int(11) NOT NULL,
  `content` text NOT NULL,
  `datePublication` datetime NOT NULL,
  `nbReport` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `idPost` (`idPost`,`idAuthor`),
  KEY `idAuthor` (`idAuthor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

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
) ENGINE=InnoDB AUTO_INCREMENT=32 DEFAULT CHARSET=utf8;

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
(31, 1, 'account', 'school 1 admin 1 a activé le compte de school 1 user 1', '2020-03-13 12:38:08');

-- --------------------------------------------------------

--
-- Structure de la table `as_posts`
--

DROP TABLE IF EXISTS `as_posts`;
CREATE TABLE IF NOT EXISTS `as_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idAuthor` int(11) NOT NULL,
  `imgPath` text NOT NULL,
  `description` text NOT NULL,
  `datePublication` datetime NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idAuthor` (`idAuthor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_reported_comments`
--

DROP TABLE IF EXISTS `as_reported_comments`;
CREATE TABLE IF NOT EXISTS `as_reported_comments` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idComment` int(11) NOT NULL,
  `idAuthor` int(11) NOT NULL,
  `dateReport` datetime NOT NULL,
  `reason` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idComment` (`idComment`,`idAuthor`),
  KEY `idAuthor` (`idAuthor`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

-- --------------------------------------------------------

--
-- Structure de la table `as_reported_posts`
--

DROP TABLE IF EXISTS `as_reported_posts`;
CREATE TABLE IF NOT EXISTS `as_reported_posts` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `idPost` int(11) NOT NULL,
  `idAuthor` int(11) NOT NULL,
  `dateReport` datetime NOT NULL,
  `reason` int(11) NOT NULL,
  PRIMARY KEY (`id`),
  KEY `idPost` (`idPost`,`idAuthor`),
  KEY `idAuthor` (`idAuthor`)
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
  `code` varchar(255) NOT NULL,
  `nbEleve` int(11) NOT NULL,
  `nbActiveAccount` int(11) NOT NULL DEFAULT '0',
  `dateInscription` datetime NOT NULL,
  `dateDeadline` datetime NOT NULL,
  `logo` text NOT NULL,
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`),
  KEY `idAdmin` (`idAdmin`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_school`
--

INSERT INTO `as_school` (`id`, `idAdmin`, `name`, `nameAdmin`, `code`, `nbEleve`, `nbActiveAccount`, `dateInscription`, `dateDeadline`, `logo`, `isActive`) VALUES
(1, 2, 'school 1', 'school 1 admin 1', 'code 1', 5, 1, '2020-01-16 13:55:33', '2020-07-16 00:07:00', 'https://images.pexels.com/photos/2040125/pexels-photo-2040125.jpeg?auto=compress&cs=tinysrgb&dpr=2&h=650&w=940', 1),
(2, 3, 'school 2', 'school 2 admin 1', 'code 2', 3, 1, '2020-01-16 14:32:24', '2020-04-16 13:04:24', 'public/images/question-mark.png', 1),
(3, 12, 'noSchool', 'no school admin 1', 'Ysiiac42kilsfog4ac23b2b', 100000000, 1, '2020-01-24 13:23:47', '3000-01-24 00:01:00', 'https://cdn.pixabay.com/photo/2014/04/03/11/47/people-312122_960_720.png', 1),
(4, 16, 'school test', 'school test admin 1', 'code test', 5, 0, '2020-02-19 00:21:10', '2020-11-18 00:11:00', 'public/images/question-mark.png', 1),
(5, 17, 'retest', 'osef', 'testeditcode', 8, 0, '2020-02-26 20:56:43', '2020-08-26 00:08:00', 'https://i.ytimg.com/vi/AocEZTv9eqs/hqdefault.jpg', 1);

-- --------------------------------------------------------

--
-- Structure de la table `as_users`
--

DROP TABLE IF EXISTS `as_users`;
CREATE TABLE IF NOT EXISTS `as_users` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  `mail` varchar(255) NOT NULL,
  `school` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `temporaryPassword` text,
  `beingReset` tinyint(1) NOT NULL DEFAULT '0',
  `nbWarning` int(11) NOT NULL DEFAULT '0',
  `isBan` tinyint(1) NOT NULL DEFAULT '0',
  `dateBan` datetime DEFAULT NULL,
  `isAdmin` tinyint(1) NOT NULL DEFAULT '0',
  `isModerator` tinyint(1) NOT NULL DEFAULT '0',
  `isActive` tinyint(1) NOT NULL DEFAULT '1',
  `profileBanner` varchar(355) NOT NULL,
  `profilePicture` varchar(355) NOT NULL DEFAULT 'public/images/question-mark.png',
  `noBanner` tinyint(1) NOT NULL DEFAULT '1',
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8;

--
-- Déchargement des données de la table `as_users`
--

INSERT INTO `as_users` (`id`, `name`, `mail`, `school`, `password`, `temporaryPassword`, `beingReset`, `nbWarning`, `isBan`, `dateBan`, `isAdmin`, `isModerator`, `isActive`, `profileBanner`, `profilePicture`, `noBanner`) VALUES
(1, 'Julien Chemin', 'julchemin@orange.fr', 'allSchool', '$2y$10$dwLFjJ4JY8QUbtFgpty55eTaVyaw/g1XXar1P09GhO6Lu00JzZa6G', '$2y$10$QUYa40nmjqPoRJotqkgg6e6ecBnmsCyH062RvSLiLlBGzOnPAQRie', 0, 0, 0, NULL, 1, 0, 1, 'public/images/Julien Cheminaa2851f23146faef4bb998a58740d0ce.jpg', 'https://waifu.clan.su/_ld/1/36417721.jpg', 0),
(2, 'school 1 admin 1', 'school1@admin1.fr', 'school 1', '$2y$10$XUkx5w1ZeA8o2fCZVA4jnugiINucVGgVojX7PFcHiUXZO6JfKbh4O', NULL, 0, 0, 0, NULL, 1, 0, 1, '', 'public/images/question-mark.png', 1),
(3, 'school 2 admin 1', 'school2@admin1.fr', 'school 2', '$2y$10$osVD3E3cOBsNZAKgnyV/8uaL7wI5Y.V/G4GCO89ckBtGJ8nP6yCGu', NULL, 0, 0, 0, NULL, 1, 0, 1, '', '../../public/images/question-mark.png', 1),
(4, 'school 1 admin 2', 'school1@admin2.fr', 'school 1', '$2y$10$AMPHmJItKJDb/w5p93.RaemOPMOJOG1W9uOKpcl87ldcvA.jBH3pa', NULL, 0, 0, 0, NULL, 1, 0, 1, '', '../../public/images/question-mark.png', 1),
(5, 'school 1 moderator 1', 'school1@moderator1.fr', 'school 1', '$2y$10$DKQbFIdAh0ow1kfgDKBFhOiX8Gdy8Hc7ylI0yrGVyC8zC84Rm3l9e', NULL, 0, 0, 0, NULL, 0, 1, 1, '', '../../public/images/question-mark.png', 1),
(6, 'school 1 user 1', 'school1@user1.fr', 'school 1', '$2y$10$9hRf3BcZJUr17hCBw133IOi66snjV6Du/bN/FtI5g2SX6hNuUqdJu', NULL, 0, 0, 0, NULL, 0, 0, 1, '', '../../public/images/question-mark.png', 1),
(9, 'school 1 user inactif 1', 'school1@userinactif1.fr', 'school 1', '$2y$10$tVn4/UttJTo/ioodoijOceJIq5c6G1qWKXGHwL6Yrw6epGudMWJSG', NULL, 0, 0, 0, NULL, 0, 0, 0, '', '../../public/images/question-mark.png', 1),
(11, 'noschool user 1', 'noschool@user1.fr', 'noSchool', '$2y$10$8nJej/tAJSkLXhxSPnvjIe2Kkrq3MYgtyBAdkeCGEt.Dgl5oF28Mq', NULL, 0, 0, 0, NULL, 0, 0, 1, '', '../../public/images/question-mark.png', 1),
(12, 'no school admin 1', 'noschool@admin1.fr', 'noSchool', '$2y$10$GT4./enDDg41G6xCfl2El.lBgJIbCr3gyTRQcJSiDb9rPqVehWKQS', NULL, 0, 0, 0, NULL, 1, 0, 1, '', '../../public/images/question-mark.png', 1),
(13, 'school 2 moderator 1', 'school2@moderator1.fr', 'school 2', '$2y$10$g5M3SrNEpIy8sRmw.jXe7eb22aVE9MTFE6wpqlAjNSMZ7e4xhHXkq', NULL, 0, 0, 0, NULL, 0, 1, 1, '', '../../public/images/question-mark.png', 1),
(14, 'school 2 moderator 2', 'school2@moderator2.fr', 'school 2', '$2y$10$QdI/YQtb3NKjH9Is5KIGouKJMucTGSbMdXKMjX9hgCFl.88zxXhpi', NULL, 0, 0, 0, NULL, 0, 1, 1, '', '../../public/images/question-mark.png', 1),
(15, 'school 1 moderator 2', 'school1@moderator2.fr', 'school 1', '$2y$10$CerSWFudtNUvgOxlDj3gy..fXqZiF2cqvze28LMdP4f2e.kYx1pDm', NULL, 0, 0, 0, NULL, 0, 1, 1, '', '../../public/images/question-mark.png', 1),
(16, 'school test admin 1', 'schooltest@admin1.fr', 'school test', '$2y$10$lYHijLF9fL1plpl.eOKZOO/RmaJHfBmy5V2nOyubgWYj/TLnwcwyW', NULL, 0, 0, 0, NULL, 1, 0, 1, '', '../../public/images/question-mark.png', 1),
(17, 'osef', 'osef@osef.fr', 'retest', '$2y$10$utsERJZtLXjgvSFR4pVm1O3/kNb20u4arWkYoHFz6wTknV/EQp3lS', NULL, 0, 0, 0, NULL, 1, 0, 1, '', '../../public/images/question-mark.png', 1);

--
-- Contraintes pour les tables déchargées
--

--
-- Contraintes pour la table `as_comments`
--
ALTER TABLE `as_comments`
  ADD CONSTRAINT `as_comments_ibfk_1` FOREIGN KEY (`idPost`) REFERENCES `as_posts` (`id`),
  ADD CONSTRAINT `as_comments_ibfk_2` FOREIGN KEY (`idAuthor`) REFERENCES `as_users` (`id`);

--
-- Contraintes pour la table `as_history`
--
ALTER TABLE `as_history`
  ADD CONSTRAINT `as_history_ibfk_1` FOREIGN KEY (`idSchool`) REFERENCES `as_school` (`id`);

--
-- Contraintes pour la table `as_posts`
--
ALTER TABLE `as_posts`
  ADD CONSTRAINT `as_posts_ibfk_1` FOREIGN KEY (`idAuthor`) REFERENCES `as_users` (`id`);

--
-- Contraintes pour la table `as_reported_comments`
--
ALTER TABLE `as_reported_comments`
  ADD CONSTRAINT `as_reported_comments_ibfk_1` FOREIGN KEY (`idAuthor`) REFERENCES `as_users` (`id`),
  ADD CONSTRAINT `as_reported_comments_ibfk_2` FOREIGN KEY (`idComment`) REFERENCES `as_comments` (`id`);

--
-- Contraintes pour la table `as_reported_posts`
--
ALTER TABLE `as_reported_posts`
  ADD CONSTRAINT `as_reported_posts_ibfk_1` FOREIGN KEY (`idAuthor`) REFERENCES `as_users` (`id`),
  ADD CONSTRAINT `as_reported_posts_ibfk_2` FOREIGN KEY (`idPost`) REFERENCES `as_posts` (`id`);

--
-- Contraintes pour la table `as_school`
--
ALTER TABLE `as_school`
  ADD CONSTRAINT `as_school_ibfk_1` FOREIGN KEY (`idAdmin`) REFERENCES `as_users` (`id`);
COMMIT;

/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
