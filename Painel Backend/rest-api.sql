CREATE TABLE IF NOT EXISTS `map` (
	`nid` int(11) NOT NULL AUTO_INCREMENT ,
	`title` varchar(360) NOT NULL ,
	`location` varchar(360) NOT NULL ,
	`Description` text NOT NULL ,
	PRIMARY KEY (`nid`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


CREATE TABLE IF NOT EXISTS `noticias` (
	`id` int(11) NOT NULL AUTO_INCREMENT ,
	`titulo` varchar(360) NOT NULL ,
	`foto` longtext NOT NULL ,
	`noticia` text NOT NULL ,
	PRIMARY KEY (`id`)
 ) ENGINE=InnoDB DEFAULT CHARSET=utf8 AUTO_INCREMENT=1;


