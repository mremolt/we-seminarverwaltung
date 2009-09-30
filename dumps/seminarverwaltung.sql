-- phpMyAdmin SQL Dump
-- version 3.2.0.1
-- http://www.phpmyadmin.net
--
-- Host: localhost
-- Erstellungszeit: 30. September 2009 um 14:48
-- Server Version: 5.0.75
-- PHP-Version: 5.3.0-0.dotdeb.8

SET SQL_MODE="NO_AUTO_VALUE_ON_ZERO";


/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;

--
-- Datenbank: `seminarverwaltung`
--

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `benutzer`
--

CREATE TABLE IF NOT EXISTS `benutzer` (
  `id` int(11) NOT NULL auto_increment,
  `vorname` varchar(40) default NULL,
  `name` varchar(40) default NULL,
  `email` varchar(50) default NULL,
  `passwort` varchar(20) default NULL,
  `registriert_seit` date default NULL,
  `anrede` varchar(10) default NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=7 ;

--
-- Daten für Tabelle `benutzer`
--

INSERT INTO `benutzer` (`id`, `vorname`, `name`, `email`, `passwort`, `registriert_seit`, `anrede`) VALUES
(1, 'Frank', 'Reich', 'f.reich@example.com', 'kochtopf', '2008-04-12', 'Herr'),
(2, 'Marie', 'Huana', 'huana@example.com', 'reibekuche', '2009-02-03', 'Frau'),
(3, 'Andreas', 'MeisenbÃ¤r', 'a.meisenbÃ¤r@example.com', 'schÃ¼ssel', '2008-07-15', 'Herr'),
(4, 'Klaus', 'Uhr', 'klaus@ur.org', 'bratpfanne', '2008-02-05', 'Herr'),
(5, 'Mike', 'Rosoft', 'sichtbar_grundlegend@kleinweich.com', 'teekessel', '2009-11-11', 'Herr'),
(6, 'Beatrice', 'LÃ¶dmann', 'beatrice@fraudoktor.de', 'kaffeemuehle', '2006-09-09', 'Dr');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `nimmt_teil`
--

CREATE TABLE IF NOT EXISTS `nimmt_teil` (
  `benutzer_id` int(11) NOT NULL default '0',
  `seminartermin_id` int(11) NOT NULL default '0',
  PRIMARY KEY  (`benutzer_id`,`seminartermin_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

--
-- Daten für Tabelle `nimmt_teil`
--

INSERT INTO `nimmt_teil` (`benutzer_id`, `seminartermin_id`) VALUES
(1, 1),
(1, 2),
(1, 3),
(2, 2),
(3, 2),
(5, 2);

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `seminare`
--

CREATE TABLE IF NOT EXISTS `seminare` (
  `id` int(11) NOT NULL auto_increment,
  `titel` varchar(120) NOT NULL,
  `beschreibung` text,
  `preis` decimal(6,2) default NULL,
  `kategorie` varchar(20) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=50 ;

--
-- Daten für Tabelle `seminare`
--

INSERT INTO `seminare` (`id`, `titel`, `beschreibung`, `preis`, `kategorie`) VALUES
(1, 'Relationale Datenbanken & MySQL', 'Nahezu alle modernen W...', '975.00', 'Datenbanken'),
(2, 'Ruby on Rails', 'Ruby on Rails ist das neue, sensation...', '2500.00', 'Programmierung'),
(3, 'Ajax & DOM-Scripting', 'Ajax ist lÃ¤ngst dem Hype-Stadium ... JavaScript ist dabei ein essentieller Teil ...', '1699.99', 'Programmierung'),
(4, 'Moderne JavaScript-Programmierung', '...gilt als DIE Programmiersprache fÃ¼r clientseitige Web...', '2500.00', 'Programmierung'),
(5, 'Adobe Flash Professional (Grundlagen)', 'Adobe Flash bringt voll animierte, multimediale, interaktive PrÃ¤sentationen und Anwendungen ...', '1500.00', 'Webdesign'),
(6, 'Adobe Flash Professional (ActionScript)', 'FÃ¼r anspruchsvolle Flash-PrÃ¤sentationen und interaktive Anwendungen werden fundierte Kenntnisse in der Programmierung mit ActionScript ...', '1500.00', 'Programmierung'),
(7, 'Digitale Bildbearbeitung mit Adobe Photoshop', 'In diesem Seminar lernen Sie die Grundlagen der digitalen Bildbearbeitung mit Adobe Photoshop ...', '1500.00', 'Webdesign'),
(49, 'Noch ein tolles Seminar', 'nix zu sehen', '1845.99', 'Testseminare');

-- --------------------------------------------------------

--
-- Tabellenstruktur für Tabelle `seminartermine`
--

CREATE TABLE IF NOT EXISTS `seminartermine` (
  `id` int(11) NOT NULL auto_increment,
  `beginn` date default NULL,
  `ende` date default NULL,
  `raum` varchar(30) default NULL,
  `seminar_id` int(11) default NULL,
  PRIMARY KEY  (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 AUTO_INCREMENT=8 ;

--
-- Daten für Tabelle `seminartermine`
--

INSERT INTO `seminartermine` (`id`, `beginn`, `ende`, `raum`, `seminar_id`) VALUES
(1, '2005-06-20', '2005-06-25', 'Schulungsraum 1', 1),
(2, '2005-11-07', '2005-11-12', 'Schulungsraum 2', 1),
(3, '2006-03-20', '2006-03-25', 'Schulungsraum 1', 4),
(4, '2006-12-04', '2006-12-09', 'Besprechungsraum', 4),
(5, '2005-01-17', '2005-01-24', 'Schulungsraum 1', 4),
(6, '2005-05-31', '2005-06-07', 'Aula', 4),
(7, '2005-10-17', '2005-10-24', 'Schulungsraum 2', 4);
