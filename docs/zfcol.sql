CREATE TABLE `media` (
  `id` int(2) NOT NULL AUTO_INCREMENT,
  `type` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `movies` (
  `id` int(5) unsigned NOT NULL AUTO_INCREMENT,
  `ownid` int(5) unsigned NOT NULL,
  `name` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `description` text COLLATE utf8_czech_ci NOT NULL,
  `poster` varchar(128) COLLATE utf8_czech_ci NOT NULL,
  `genre` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `origin` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `director` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `starring` varchar(512) COLLATE utf8_czech_ci NOT NULL,
  `rating` int(3) unsigned NOT NULL,
  `trailer` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `media` varchar(16) COLLATE utf8_czech_ci NOT NULL,
  `favorite` tinyint(1) NOT NULL,
  `url` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `createDate` datetime NOT NULL,
  `creator` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

CREATE TABLE `users` (
  `id` int(3) NOT NULL AUTO_INCREMENT,
  `username` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `password` varchar(64) COLLATE utf8_czech_ci NOT NULL,
  `first_name` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `last_name` varchar(32) COLLATE utf8_czech_ci NOT NULL,
  `role` varchar(16) COLLATE utf8_czech_ci NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB  DEFAULT CHARSET=utf8 COLLATE=utf8_czech_ci;

INSERT INTO `users` (`id`, `username`, `password`, `first_name`, `last_name`, `role`) VALUES
(1, 'admin', 'b767e73ff570c2ee968764919ed41c8c1961f092', 'John', 'Doe', 'administrator');

INSERT INTO `media` (`type`) VALUES ('DVD'), ('Blue-Ray');