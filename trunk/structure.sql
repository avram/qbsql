-- Tables for qbsql database
-- See also table_create_queries(..) in functions.php

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
);

CREATE TABLE `qb_admin` (
        `rev` int(11) NOT NULL
);

/** Tables created on the fly by table_create_queries(..)
CREATE TABLE {$prefix}_players (
  last_name varchar(40) default NULL,
  first_name varchar(40) default NULL,
  short_name varchar(40) default NULL,
  team int(20) NOT NULL default '0',
  id int(20) NOT NULL auto_increment,
  KEY id (id)
) ENGINE=MyISAM;

CREATE TABLE {$prefix}_rounds (
  name varchar(40) default NULL,
  team1 int(20) NOT NULL default '0',
  team2 int(20) NOT NULL default '0',
  score1 int(20) default NULL,
  score2 int(20) default NULL,
  tu_heard int(20) default NULL,
  id int(20) NOT NULL default '0',
  ot_tossups1 int(20) default NULL,
  ot_tossups2 int(20) default NULL,
  ot int(20) default '0',
  game_id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (game_id),
  KEY id (id)
) ENGINE=MyISAM;

CREATE TABLE {$prefix}_rounds_players (
  player_id int(20) NOT NULL default '0',
  team_id int(20) NOT NULL default '0',
  powers int(20) default NULL,
  tossups int(20) default NULL,
  negs int(20) default NULL,
  tu_heard int(20) default NULL,
  round_id int(20) NOT NULL default '0',
  game_id int(20) NOT NULL default '0',
  KEY round_id (round_id),
  KEY game_id (game_id)
) ENGINE=MyISAM;

CREATE TABLE {$prefix}_teams (
  full_name varchar(30) default NULL,
  short_name varchar(30) default NULL,
  bracket int(20) default NULL,
  id int(20) NOT NULL auto_increment,
  PRIMARY KEY  (id)
) ENGINE=MyISAM; */
