-- MySQL dump 10.13  Distrib 5.7.12, for osx10.9 (x86_64)
--
-- Host: 127.0.0.1    Database: core
-- ------------------------------------------------------
-- Server version	5.6.31

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!40101 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `departments`
--

DROP TABLE IF EXISTS `departments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `departments` (
  `department_id` int(11) NOT NULL AUTO_INCREMENT,
  `department_name` varchar(100) NOT NULL,
  `department_desc` varchar(200) DEFAULT NULL,
  `institution_id` int(11) NOT NULL,
  PRIMARY KEY (`department_id`),
  UNIQUE KEY `department_id_UNIQUE` (`department_id`),
  KEY `institution_fk_idx` (`institution_id`),
  CONSTRAINT `dep_institution_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`institution_id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `departments`
--

LOCK TABLES `departments` WRITE;
/*!40000 ALTER TABLE `departments` DISABLE KEYS */;
INSERT INTO `departments` VALUES (1,'教务处','教学运行管理部门',3);
/*!40000 ALTER TABLE `departments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `institution`
--

DROP TABLE IF EXISTS `institution`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `institution` (
  `institution_id` int(11) NOT NULL AUTO_INCREMENT,
  `institution_name` varchar(200) NOT NULL,
  `institution_desc` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`institution_id`),
  UNIQUE KEY `institution_id_UNIQUE` (`institution_id`),
  UNIQUE KEY `institution_name_UNIQUE` (`institution_name`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `institution`
--

LOCK TABLES `institution` WRITE;
/*!40000 ALTER TABLE `institution` DISABLE KEYS */;
INSERT INTO `institution` VALUES (3,'机构名称','111');
/*!40000 ALTER TABLE `institution` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `user`
--

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `user` (
  `user_id` int(11) NOT NULL AUTO_INCREMENT,
  `name` varchar(12) NOT NULL,
  `passwd` varchar(32) DEFAULT NULL,
  `sex` varchar(4) DEFAULT NULL,
  `institution_id` int(11) NOT NULL,
  `deartment_id` int(11) DEFAULT NULL,
  `professional_title` varchar(64) DEFAULT NULL,
  `diploma` varchar(45) DEFAULT NULL,
  `degree` varchar(45) DEFAULT NULL,
  `nation` varchar(10) DEFAULT NULL,
  `native_place` varchar(45) DEFAULT NULL,
  `stuff_id` varchar(45) DEFAULT NULL,
  `qq` varchar(45) DEFAULT NULL COMMENT 'qq还有邮箱的形式',
  `email` varchar(45) DEFAULT NULL,
  `mobile_phone` varchar(88) DEFAULT NULL COMMENT '加密 base64',
  `office_phone` varchar(88) DEFAULT NULL COMMENT 'base64加密',
  `register_time` int(10) NOT NULL,
  `login_q` varchar(32) DEFAULT NULL,
  `login_phone` varchar(32) DEFAULT NULL,
  PRIMARY KEY (`user_id`),
  UNIQUE KEY `user_id_UNIQUE` (`user_id`),
  UNIQUE KEY `email_UNIQUE` (`email`),
  KEY `institution_id_normal` (`institution_id`),
  KEY `department_fk_idx` (`deartment_id`),
  CONSTRAINT `department_fk` FOREIGN KEY (`deartment_id`) REFERENCES `departments` (`department_id`) ON DELETE NO ACTION ON UPDATE CASCADE,
  CONSTRAINT `institution_fk` FOREIGN KEY (`institution_id`) REFERENCES `institution` (`institution_id`) ON DELETE NO ACTION ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `user`
--

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'张三','5f9a9917d364bdb3fa7f61a5a719b694','男',3,1,'助教','研究生','硕士','汉族','江西省赣州','00167','324324235','asfsg@qq.com','13688888888','013-12345678',1469023544,'cce65970cf4b7b772caef8976142e944','ae766006fae78a5cd2f350ecfc717723');
/*!40000 ALTER TABLE `user` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2016-08-01 16:15:05
