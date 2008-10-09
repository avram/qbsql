-- Tables for qbsql database

CREATE TABLE `tournaments` (
      `id` int(11) NOT NULL auto_increment,
      `name` varchar(45) NOT NULL,
      `prefix` varchar(20) NOT NULL,
      `username` varchar(30) NOT NULL,
      `password` varchar(30) NOT NULL,
      `locked` tinyint(1) NOT NULL default '0',
      `game_length` int(11) NOT NULL default '20',
      `description` text NOT NULL,
      PRIMARY KEY  (`id`),
      UNIQUE KEY `prefix` (`prefix`)
) ENGINE=MyISAM;
