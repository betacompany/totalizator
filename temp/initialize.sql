CREATE TABLE  `total_matches` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `comp_id` INT NOT NULL ,
 `tour` INT NOT NULL ,
 `comp1_id` INT NOT NULL ,
 `comp2_id` INT NOT NULL ,
 `score1` INT DEFAULT  '0' NOT NULL ,
 `score2` INT DEFAULT  '0' NOT NULL ,
 `played` SMALLINT DEFAULT  '0' NOT NULL ,
 `timestamp` TIMESTAMP NOT NULL ,
 PRIMARY KEY (  `id` ) ,
 INDEX (  `comp_id` ,  `played` ,  `timestamp` )
);

CREATE TABLE  `total_competitions` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR( 1024 ) NOT NULL ,
 PRIMARY KEY (  `id` )
);

CREATE TABLE  `total_competitors` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `name` VARCHAR( 1024 ) NOT NULL ,
 PRIMARY KEY (  `id` )
);

CREATE TABLE  `total_stakes` (
 `id` INT NOT NULL AUTO_INCREMENT ,
 `uid` INT NOT NULL ,
 `match_id` INT NOT NULL ,
 `stake_score1` INT DEFAULT  '0' NOT NULL ,
 `stake_score2` INT DEFAULT  '0' NOT NULL ,
 `played` INT DEFAULT  '0' NOT NULL ,
 `score` INT DEFAULT  '0' NOT NULL ,
 PRIMARY KEY (  `id` ) ,
 INDEX (  `uid` ,  `match_id` )
);
