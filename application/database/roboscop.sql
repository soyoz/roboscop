/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;


# Dump of table lookup_robotgroup
# ------------------------------------------------------------

DROP TABLE IF EXISTS `lookup_robotgroup`;

CREATE TABLE `lookup_robotgroup` (
  `ROBOT_GROUP_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ROBOT_ID` int(11) NOT NULL,
  `ID` int(11) NOT NULL,
  `NAME` varchar(50) DEFAULT NULL,
  PRIMARY KEY (`ROBOT_GROUP_ID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table msn_buddy
# ------------------------------------------------------------

DROP TABLE IF EXISTS `msn_buddy`;

CREATE TABLE `msn_buddy` (
  `BUDDY_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ROBOT_ID` int(11) NOT NULL,
  `ROBOT_GROUP_ID` int(11) NOT NULL,
  `PASSPORT` varchar(200) NOT NULL,
  `SCREEN_NAME` text NOT NULL,
  `FL` tinyint(1) DEFAULT NULL,
  `AL` tinyint(1) DEFAULT NULL,
  `BL` tinyint(1) DEFAULT NULL,
  `RL` tinyint(1) DEFAULT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  `LAST_UPDATE_DATE` int(20) DEFAULT NULL,
  `STATUS` tinyint(1) NOT NULL,
  PRIMARY KEY (`BUDDY_ID`) USING BTREE,
  KEY `robot_id_buddy_id` (`ROBOT_ID`,`BUDDY_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table msn_buddy_message
# ------------------------------------------------------------

DROP TABLE IF EXISTS `msn_buddy_message`;

CREATE TABLE `msn_buddy_message` (
  `BUDDY_MESSAGE_ID` int(11) NOT NULL AUTO_INCREMENT,
  `BUDDY_ID` int(11) NOT NULL,
  `MESSAGE` text NOT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  PRIMARY KEY (`BUDDY_MESSAGE_ID`) USING BTREE
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table service_feed
# ------------------------------------------------------------

DROP TABLE IF EXISTS `service_feed`;

CREATE TABLE `service_feed` (
  `FEED_ID` int(11) NOT NULL AUTO_INCREMENT,
  `URL` text NOT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  `LAST_UPDATE_DATE` int(20) DEFAULT NULL,
  `STATUS` tinyint(1) NOT NULL,
  PRIMARY KEY (`FEED_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table service_feed_import
# ------------------------------------------------------------

DROP TABLE IF EXISTS `service_feed_import`;

CREATE TABLE `service_feed_import` (
  `FEED_IMPORT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `FEED_ID` int(11) NOT NULL,
  `LINK` text,
  `TITLE` text,
  `DESCRIPTION` text,
  `INSERT_DATE` int(20) NOT NULL,
  PRIMARY KEY (`FEED_IMPORT_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table service_feed_notification
# ------------------------------------------------------------

DROP TABLE IF EXISTS `service_feed_notification`;

CREATE TABLE `service_feed_notification` (
  `FEED_NOTIFICATION_ID` int(11) NOT NULL AUTO_INCREMENT,
  `FEED_IMPORT_ID` int(11) NOT NULL,
  `BUDDY_ID` int(11) NOT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  PRIMARY KEY (`FEED_NOTIFICATION_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table service_feed_subscription
# ------------------------------------------------------------

DROP TABLE IF EXISTS `service_feed_subscription`;

CREATE TABLE `service_feed_subscription` (
  `FEED_SUBSCRIPTION_ID` int(11) NOT NULL AUTO_INCREMENT,
  `FEED_ID` int(11) NOT NULL,
  `BUDDY_ID` int(11) NOT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  `LAST_UPDATE_DATE` int(20) DEFAULT NULL,
  `STATUS` tinyint(1) NOT NULL,
  PRIMARY KEY (`FEED_SUBSCRIPTION_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table system_error_log
# ------------------------------------------------------------

DROP TABLE IF EXISTS `system_error_log`;

CREATE TABLE `system_error_log` (
  `ERROR_LOG_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ROBOT_ID` int(11) NOT NULL,
  `FILE` text NOT NULL,
  `LINE` text NOT NULL,
  `CODE` text NOT NULL,
  `MESSAGE` text NOT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  PRIMARY KEY (`ERROR_LOG_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table system_notification
# ------------------------------------------------------------

DROP TABLE IF EXISTS `system_notification`;

CREATE TABLE `system_notification` (
  `NOTIFICATION_ID` int(11) NOT NULL AUTO_INCREMENT,
  `SERVICE_ID` int(11) DEFAULT NULL,
  `BUDDY_ID` int(11) NOT NULL,
  `MESSAGE` text NOT NULL,
  `INSERT_DATE` int(20) NOT NULL,
  `LAST_UPDATE_DATE` int(20) DEFAULT NULL,
  `IS_READ` tinyint(1) NOT NULL,
  PRIMARY KEY (`NOTIFICATION_ID`),
  KEY `is_read_buddy_id` (`IS_READ`,`BUDDY_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;



# Dump of table system_robot
# ------------------------------------------------------------

DROP TABLE IF EXISTS `system_robot`;

CREATE TABLE `system_robot` (
  `ROBOT_ID` int(11) NOT NULL AUTO_INCREMENT,
  `LIMIT` int(10) NOT NULL,
  `PASSPORT` varchar(255) NOT NULL,
  `PASSWORD` varchar(255) NOT NULL,
  `SCREEN_NAME` text NOT NULL,
  `STATUS` tinyint(1) NOT NULL,
  PRIMARY KEY (`ROBOT_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `system_robot` WRITE;
/*!40000 ALTER TABLE `system_robot` DISABLE KEYS */;

INSERT INTO `system_robot` (`ROBOT_ID`, `LIMIT`, `PASSPORT`, `PASSWORD`, `SCREEN_NAME`, `STATUS`)
VALUES
	(1,250,'roboscop.testing@hotmail.com','123robozcop456','Roboscop Testing',1);

/*!40000 ALTER TABLE `system_robot` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table system_robot_service
# ------------------------------------------------------------

DROP TABLE IF EXISTS `system_robot_service`;

CREATE TABLE `system_robot_service` (
  `ROBOT_SERVICE_ID` int(11) NOT NULL AUTO_INCREMENT,
  `ROBOT_ID` int(11) NOT NULL,
  `SERVICE_ID` int(11) NOT NULL,
  `STATUS` tinyint(1) NOT NULL,
  PRIMARY KEY (`ROBOT_SERVICE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `system_robot_service` WRITE;
/*!40000 ALTER TABLE `system_robot_service` DISABLE KEYS */;

INSERT INTO `system_robot_service` (`ROBOT_SERVICE_ID`, `ROBOT_ID`, `SERVICE_ID`, `STATUS`)
VALUES
	(1,1,1,1),
	(2,1,2,1),
	(3,1,3,1);

/*!40000 ALTER TABLE `system_robot_service` ENABLE KEYS */;
UNLOCK TABLES;


# Dump of table system_service
# ------------------------------------------------------------

DROP TABLE IF EXISTS `system_service`;

CREATE TABLE `system_service` (
  `SERVICE_ID` int(11) NOT NULL AUTO_INCREMENT,
  `NAME` varchar(100) NOT NULL,
  PRIMARY KEY (`SERVICE_ID`)
) ENGINE=MyISAM DEFAULT CHARSET=utf8;

LOCK TABLES `system_service` WRITE;
/*!40000 ALTER TABLE `system_service` DISABLE KEYS */;

INSERT INTO `system_service` (`SERVICE_ID`, `NAME`)
VALUES
	(1,'feed'),
	(2,'tercume'),
	(3,'muzik');

/*!40000 ALTER TABLE `system_service` ENABLE KEYS */;
UNLOCK TABLES;



/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;
/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
