CREATE TABLE IF NOT EXISTS `total_competitions` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(1024) NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `total_competitors` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_id` int(11) DEFAULT NULL,
  `name` varchar(256) NOT NULL DEFAULT '',
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`),
  UNIQUE KEY `ext_id` (`ext_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `total_matches` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `ext_id` int(11) DEFAULT NULL,
  `comp_id` int(11) NOT NULL,
  `tour` int(11) NOT NULL,
  `short_comment` varchar(64) NOT NULL,
  `comp1_id` int(11) NOT NULL,
  `comp2_id` int(11) NOT NULL,
  `score1` int(11) NOT NULL DEFAULT '0',
  `score2` int(11) NOT NULL DEFAULT '0',
  `played` smallint(6) NOT NULL DEFAULT '0',
  `timestamp` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `ext_id` (`ext_id`),
  KEY `comp_id` (`comp_id`,`played`,`timestamp`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `total_stakes` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `uid` int(11) NOT NULL,
  `match_id` int(11) NOT NULL,
  `stake_score1` int(11) NOT NULL DEFAULT '0',
  `stake_score2` int(11) NOT NULL DEFAULT '0',
  `played` int(11) NOT NULL DEFAULT '0',
  `score` int(11) NOT NULL DEFAULT '0',
  PRIMARY KEY (`id`),
  KEY `uid` (`uid`,`match_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `total_leaderboards` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `comp_id` int(11) NOT NULL,
  `finished` tinyint(1) NOT NULL DEFAULT '0'
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

CREATE TABLE IF NOT EXISTS `total_leaderboard_players` (
  `uid` int(11) NOT NULL,
  `lb_id` int(11) NOT NULL,
  `score` int(11) NOT NULL DEFAULT '0',
  `position` int(11) NOT NULL DEFAULT '0',
  UNIQUE KEY `u_lb` (`uid`,`lb_id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;
