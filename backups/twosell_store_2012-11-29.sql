-- MySQL dump 10.13  Distrib 5.1.63, for debian-linux-gnu (x86_64)
--
-- Host: localhost    Database: statoil_canonical
-- ------------------------------------------------------
-- Server version	5.1.63-0+squeeze1

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
-- Table structure for table `twosell_store`
--

DROP TABLE IF EXISTS `twosell_store`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `twosell_store` (
  `id` int(11) NOT NULL AUTO_INCREMENT,
  `internal_id` varchar(255) NOT NULL,
  `title` varchar(255) NOT NULL,
  `chain_id` int(11) DEFAULT NULL,
  `recommendation_config_id` int(11) DEFAULT NULL,
  `active` tinyint(1) NOT NULL,
  `address` varchar(255) DEFAULT NULL,
  `postal_code` varchar(25) DEFAULT NULL,
  `city` varchar(255) DEFAULT NULL,
  `phone` varchar(25) DEFAULT NULL,
  `notes` longtext,
  `block_group_id_suggestion` longtext NOT NULL,
  `block_products_other_then_group` int(11) NOT NULL,
  `group_ids_1` varchar(350) NOT NULL,
  `group_ids_2` varchar(350) NOT NULL,
  `group_ids_3` varchar(350) NOT NULL,
  `group_ids_4` varchar(350) NOT NULL,
  `group_ids_5` varchar(350) NOT NULL,
  `description3_text_1` varchar(300) NOT NULL,
  `description3_text_2` varchar(300) NOT NULL,
  `description3_text_3` varchar(300) NOT NULL,
  `description3_text_4` varchar(300) NOT NULL,
  `description3_text_5` varchar(300) NOT NULL,
  `active_online` tinyint(1) NOT NULL,
  `city_id` int(11) NOT NULL,
  `block_group_id_target` longtext NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `internal_id` (`internal_id`,`chain_id`),
  KEY `twosell_store_9bfe773a` (`chain_id`),
  KEY `twosell_store_5904140d` (`recommendation_config_id`)
) ENGINE=MyISAM AUTO_INCREMENT=99 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `twosell_store`
--

LOCK TABLES `twosell_store` WRITE;
/*!40000 ALTER TABLE `twosell_store` DISABLE KEYS */;
INSERT INTO `twosell_store` VALUES (1,'21225','21225',1,1,0,'','','','','','',0,'','','','','','','','','','',0,0,''),(2,'21314','21314',1,1,1,'Malmövägen, 91','22270','Lund','','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',1,11,''),(3,'21349','21349',1,1,1,'Derbyvägen, 2','21235','Malmö','','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',0,5,''),(4,'21350','21350',1,1,1,'Blidögatan, 42','21124','Malmö','','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',0,5,''),(5,'21352','21352',1,1,1,'Lundavägen, 24','21218','Malmö','','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',0,5,''),(6,'21405','21405',1,1,0,'','','','','Bytt internal id för att köra 29979','',0,'','','','','','','','','','',0,0,''),(7,'22618','22618',1,1,1,'Vendelsövägen,59','12644','Haninge','08-7451301 ','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',0,8,''),(8,'22667','22667',1,1,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(9,'22745','22745',1,1,1,'Norrköpingsvägen, 17','61138','Nyköping','0155-210300 ','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;136;12',0,'','','','','','','','','','',1,9,''),(10,'22795','22795',1,1,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(11,'23645','23645',1,1,1,'Ekgårdsvägen, 4','14175','Kungens kurva','08-7107000 ','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',1,7,''),(12,'23649','23649',1,1,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(13,'23653','23653',1,1,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(14,'26124','26124',1,1,1,'Bellevuevägen, 52','21767','Malmö','','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',1,5,''),(15,'26128','26128',1,1,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(16,'29979','26533',1,1,1,'','','Mantorp','','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',0,12,''),(17,'26636','26636',1,1,1,'Trafikplatsen E:4','15391','Järna','08-55150141 ','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',1,10,''),(18,'26660','26660',1,1,0,'Dalarövägen,2','13645','Haninge','08-7772620 ','','106;60;87;72;85;86;73;6;204;138;203;206;103;75;7;8;10;13;17;20;190;12',0,'','','','','','','','','','',0,8,''),(19,'21209','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(20,'21313','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(21,'21469','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(22,'21535','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(23,'22182','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(24,'22185','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(25,'22605','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(26,'22606','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(27,'22621','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(28,'22624','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(29,'22629','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(30,'22639','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(31,'22658','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(32,'22668','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(33,'22670','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(34,'22673','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(35,'22682','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(36,'22683','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(37,'22711','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(38,'22723','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(39,'22726','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(40,'22749','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(41,'22753','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(42,'22757','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(43,'22765','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(44,'22766','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(45,'22767','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(46,'22770','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(47,'22776','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(48,'22783','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(49,'22797','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(50,'22798','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(51,'22802','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(52,'22803','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(53,'22828','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(54,'22831','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(55,'22837','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(56,'23117','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(57,'23131','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(58,'23185','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(59,'23391','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(60,'23425','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(61,'23509','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(62,'23611','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(63,'24541','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(64,'24542','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(65,'26105','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(66,'26107','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(67,'26113','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(68,'26114','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(69,'26119','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(70,'26120','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(71,'26139','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(72,'26141','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(73,'26145','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(74,'26202','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(75,'26205','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(76,'26533','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(77,'26609','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(78,'26610','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(79,'26617','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(80,'26624','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(81,'26626','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(82,'26629','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(83,'26631','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(84,'26632','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(85,'26634','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(86,'26635','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(87,'26637','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(88,'26638','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(89,'26639','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(90,'26642','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(91,'26643','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(92,'26645','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(93,'26650','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(94,'26655','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(95,'27408','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(96,'27425','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(97,'27603','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,''),(98,'27639','',1,NULL,0,NULL,NULL,NULL,NULL,NULL,'',0,'','','','','','','','','','',0,0,'');
/*!40000 ALTER TABLE `twosell_store` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2012-11-29 17:45:52
