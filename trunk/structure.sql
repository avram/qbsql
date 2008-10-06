-- Tables for qbsql database

--
-- Table structure for table `tourney_players`
--

DROP TABLE IF EXISTS `tourney_players`;
CREATE TABLE `tourney_players` (
  `last_name` varchar(40) default NULL,
  `first_name` varchar(40) default NULL,
  `short_name` varchar(40) default NULL,
  `team` int(20) NOT NULL default '0',
  `id` int(20) NOT NULL auto_increment,
  KEY `id` (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `tourney_rounds`
--

DROP TABLE IF EXISTS `tourney_rounds`;
CREATE TABLE `tourney_rounds` (
  `name` varchar(40) default NULL,
  `team1` int(20) NOT NULL default '0',
  `team2` int(20) NOT NULL default '0',
  `score1` int(20) default NULL,
  `score2` int(20) default NULL,
  `tu_heard` int(20) default NULL,
  `id` int(20) NOT NULL default '0',
  `game_id` int(20) NOT NULL auto_increment,
  PRIMARY KEY  (`game_id`),
  KEY `id` (`id`)
) ENGINE=MyISAM;

--
-- Table structure for table `tourney_rounds_players`
--

DROP TABLE IF EXISTS `tourney_rounds_players`;
CREATE TABLE `tourney_rounds_players` (
  `player_id` int(20) NOT NULL default '0',
  `team_id` int(20) NOT NULL default '0',
  `powers` int(20) default NULL,
  `tossups` int(20) default NULL,
  `negs` int(20) default NULL,
  `tu_heard` int(20) default NULL,
  `round_id` int(20) NOT NULL default '0',
  `game_id` int(20) NOT NULL default '0',
  KEY `round_id` (`round_id`),
  KEY `game_id` (`game_id`)
) ENGINE=MyISAM;


--
-- Table structure for table `tourney_teams`
--

DROP TABLE IF EXISTS `tourney_teams`;
CREATE TABLE `tourney_teams` (
  `full_name` varchar(30) default NULL,
  `short_name` varchar(30) default NULL,
  `id` int(20) NOT NULL auto_increment,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM;

