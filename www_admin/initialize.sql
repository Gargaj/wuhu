DROP TABLE IF EXISTS `compoentries`;
CREATE TABLE `compoentries` (
  `id` int(11) NOT NULL auto_increment,
  `compoid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `title` text collate utf8_unicode_ci NOT NULL,
  `author` text collate utf8_unicode_ci NOT NULL,
  `comment` text collate utf8_unicode_ci NOT NULL,
  `orgacomment` text collate utf8_unicode_ci NOT NULL,
  `playingorder` int(11) NOT NULL,
  `filename` text collate utf8_unicode_ci NOT NULL,
  `uploadip` text collate utf8_unicode_ci NOT NULL,
  `uploadtime` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `compoid` (`compoid`,`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `compos`;
CREATE TABLE `compos` (
  `id` int(11) NOT NULL auto_increment,
  `name` text NOT NULL,
  `start` datetime NOT NULL,
  `showauthor` tinyint(4) default '1',
  `votingopen` tinyint(4) default '0',
  `uploadopen` tinyint(4) default '1',
  `updateopen` tinyint(4) default '1',
  `dirname` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `intranet_minuswiki_pages`;
CREATE TABLE `intranet_minuswiki_pages` (
  `id` int(11) NOT NULL auto_increment,
  `title` varchar(255) collate utf8_unicode_ci default NULL,
  `content` text collate utf8_unicode_ci,
  PRIMARY KEY  (`id`),
  KEY `title` (`title`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `intranet_news`;
CREATE TABLE `intranet_news` (
  `id` int(11) NOT NULL auto_increment,
  `date` datetime,
  `eng_title` text collate utf8_unicode_ci NOT NULL,
  `eng_body` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `intranet_toc`;
CREATE TABLE `intranet_toc` (
  `id` int(11) NOT NULL auto_increment,
  `orderfield` int(11) NOT NULL default '0',
  `title` text collate utf8_unicode_ci NOT NULL,
  `link` text collate utf8_unicode_ci NOT NULL,
  `type` enum('normal','loggedin','loggedout','separator') CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `settings`;
CREATE TABLE `settings` (
  `id` int(11) NOT NULL auto_increment,
  `setting` text NOT NULL,
  `value` text NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `users`;
CREATE TABLE `users` (
  `id` int(11) NOT NULL auto_increment,
  `username` text collate utf8_unicode_ci NOT NULL,
  `password` text collate utf8_unicode_ci NOT NULL,
  `nickname` text collate utf8_unicode_ci NOT NULL,
  `group` text collate utf8_unicode_ci NOT NULL,
  `regtime` datetime NOT NULL,
  `regip` text collate utf8_unicode_ci NOT NULL,
  PRIMARY KEY  (`id`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `votekeys`;
CREATE TABLE `votekeys` (
  `id` int(11) NOT NULL auto_increment,
  `userid` int(11) NOT NULL default '0',
  `votekey` text NOT NULL,
  PRIMARY KEY  (`id`),
  KEY `userid` (`userid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `votes_preferential`;
CREATE TABLE `votes_preferential` (
  `id` int(11) NOT NULL auto_increment,
  `compoid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `entry1` int(11) NOT NULL,
  `entry2` int(11) NOT NULL,
  `entry3` int(11) NOT NULL,
  `votedate` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pk_votepref` (`compoid`,`userid`),
  KEY `compoid` (`compoid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

DROP TABLE IF EXISTS `votes_range`;
CREATE TABLE `votes_range` (
  `id` int(11) NOT NULL auto_increment,
  `compoid` int(11) NOT NULL,
  `userid` int(11) NOT NULL,
  `entryorderid` int(11) NOT NULL,
  `vote` int(11) NOT NULL,
  `votedate` datetime NOT NULL,
  PRIMARY KEY  (`id`),
  UNIQUE KEY `pk_voterange` (`compoid`,`userid`,`entryorderid`),
  KEY `compoid` (`compoid`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;

INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (10,'News','News','normal');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (40,'','','separator');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (50,'Login / Register','Login','loggedout');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (60,'Edit profile','ProfileEdit','loggedin');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (70,'Upload entry','UploadEntries','loggedin');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (80,'Update entries','EditEntries','loggedin');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (90,'Vote','Vote','loggedin');
INSERT INTO `intranet_toc` (orderfield,title,link,type) VALUES (100,'Logout','Logout','loggedin');

INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('News','==Welcome to the party==\r\n{{Eval:include_news.php}}');
INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('Login','==Login==\r\n{{Eval:include_login.php}}\r\n==Register==\r\n{{Eval:include_register.php}}');
INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('ProfileEdit','==Edit your profile==\r\n{{Eval:include_profile.php}}');
INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('UploadEntries','==Upload your entry==\r\n{{Eval:include_upload.php}}');
INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('EditEntries','==Edit your entries==\r\n{{Eval:include_entries.php}}');
INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('Vote','==Vote==\r\n{{Eval:include_vote.php}}');
INSERT INTO `intranet_minuswiki_pages` (title,content) VALUES ('Logout','{{Eval:include_logout.php}}');
