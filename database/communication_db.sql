-- MySQL dump 10.13  Distrib 8.0.41, for Win64 (x86_64)
-- Host: 127.0.0.1    Database: communication_systemdb
-- ------------------------------------------------------
-- Server version	8.0.41

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

-- Table structure for table `attachment`

DROP TABLE IF EXISTS `attachment`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `attachment` (
  `File_ID` int NOT NULL AUTO_INCREMENT,
  `File_Name` varchar(255) NOT NULL,
  `File_Type` varchar(50) DEFAULT NULL,
  `Message_ID` int NOT NULL,
  PRIMARY KEY (`File_ID`),
  KEY `Message_ID` (`Message_ID`),
  CONSTRAINT `attachment_ibfk_1` FOREIGN KEY (`Message_ID`) REFERENCES `message` (`Message_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `attachment`

LOCK TABLES `attachment` WRITE;
/*!40000 ALTER TABLE `attachment` DISABLE KEYS */;
INSERT INTO `attachment` VALUES (1,'report.pdf','PDF',1),(2,'schedule.xlsx','Excel',2);
/*!40000 ALTER TABLE `attachment` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `channel`

DROP TABLE IF EXISTS `channel`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `channel` (
  `Channel_ID` int NOT NULL AUTO_INCREMENT,
  `Type` varchar(50) NOT NULL,
  `Description` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Channel_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `channel`

LOCK TABLES `channel` WRITE;
/*!40000 ALTER TABLE `channel` DISABLE KEYS */;
INSERT INTO `channel` VALUES (1,'Email','Email channel for notifications'),(2,'SMS','SMS channel for notifications');
/*!40000 ALTER TABLE `channel` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `message`

DROP TABLE IF EXISTS `message`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message` (
  `Message_ID` int NOT NULL AUTO_INCREMENT,
  `Content` text NOT NULL,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `Sender_ID` int NOT NULL,
  'Recipient_ID' INT NOT NULL,
  PRIMARY KEY (`Message_ID`),
  KEY `Sender_ID` (`Sender_ID`),
  KEY 'Recipient_ID' ('Recipient_ID'),
  CONSTRAINT `message_ibfk_1` FOREIGN KEY (`Sender_ID`) REFERENCES `user` (`User_ID`),
  CONSTRAINT 'message_ibfk_2' FOREIGN KEY ('Recipient_ID') REFERENCES 'user' ('User_ID')
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `message`

LOCK TABLES `message` WRITE;
/*!40000 ALTER TABLE `message` DISABLE KEYS */;
INSERT INTO `message` VALUES (1,'Hello Bob, please review the report.','2025-03-31 17:22:23',1),(2,'Reminder: Meeting tomorrow.','2025-03-31 17:22:23',2),(3,'System update scheduled.','2025-03-31 17:22:23',3);
/*!40000 ALTER TABLE `message` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `message_log`

DROP TABLE IF EXISTS `message_log`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_log` (
  `Log_ID` int NOT NULL AUTO_INCREMENT,
  `Status` varchar(50) NOT NULL,
  `Read_Receipt` tinyint(1) DEFAULT '0',
  `Message_ID` int NOT NULL,
  PRIMARY KEY (`Log_ID`),
  KEY `Message_ID` (`Message_ID`),
  CONSTRAINT `message_log_ibfk_1` FOREIGN KEY (`Message_ID`) REFERENCES `message` (`Message_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `message_log`

LOCK TABLES `message_log` WRITE;
/*!40000 ALTER TABLE `message_log` DISABLE KEYS */;
INSERT INTO `message_log` VALUES (1,'Delivered',1,1),(2,'Pending',0,2);
/*!40000 ALTER TABLE `message_log` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `message_recipients`

DROP TABLE IF EXISTS `message_recipients`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `message_recipients` (
  `Recipient_ID` int NOT NULL AUTO_INCREMENT,
  `Message_ID` int NOT NULL,
  `Receiver_ID` int NOT NULL,
  PRIMARY KEY (`Recipient_ID`),
  KEY `Message_ID` (`Message_ID`),
  KEY `Receiver_ID` (`Receiver_ID`),
  CONSTRAINT `message_recipients_ibfk_1` FOREIGN KEY (`Message_ID`) REFERENCES `message` (`Message_ID`),
  CONSTRAINT `message_recipients_ibfk_2` FOREIGN KEY (`Receiver_ID`) REFERENCES `user` (`User_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `message_recipients`

LOCK TABLES `message_recipients` WRITE;
/*!40000 ALTER TABLE `message_recipients` DISABLE KEYS */;
INSERT INTO `message_recipients` VALUES (1,1,2),(2,2,1),(3,3,1);
/*!40000 ALTER TABLE `message_recipients` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `notification`

DROP TABLE IF EXISTS `notification`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `notification` (
  `Notification_ID` int NOT NULL AUTO_INCREMENT,
  `Type` varchar(50) NOT NULL,
  `Timestamp` datetime DEFAULT CURRENT_TIMESTAMP,
  `User_ID` int NOT NULL,
  `Channel_ID` int NOT NULL,
  `Message_ID` int NOT NULL,
  PRIMARY KEY (`Notification_ID`),
  KEY `User_ID` (`User_ID`),
  KEY `Channel_ID` (`Channel_ID`),
  KEY `Message_ID` (`Message_ID`),
  CONSTRAINT `notification_ibfk_1` FOREIGN KEY (`User_ID`) REFERENCES `user` (`User_ID`),
  CONSTRAINT `notification_ibfk_2` FOREIGN KEY (`Channel_ID`) REFERENCES `channel` (`Channel_ID`),
  CONSTRAINT `notification_ibfk_3` FOREIGN KEY (`Message_ID`) REFERENCES `message` (`Message_ID`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `notification`

LOCK TABLES `notification` WRITE;
/*!40000 ALTER TABLE `notification` DISABLE KEYS */;
INSERT INTO `notification` VALUES (1,'Message Notification','2025-03-31 17:22:23',1,1,1),(2,'Task Reminder','2025-03-31 17:22:23',2,2,2),(3,'System Alert','2025-03-31 17:22:23',3,1,3);
/*!40000 ALTER TABLE `notification` ENABLE KEYS */;
UNLOCK TABLES;

-- Table structure for table `user`

DROP TABLE IF EXISTS `user`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `user` (
  `User_ID` int NOT NULL AUTO_INCREMENT,
  `Name` varchar(100) NOT NULL,
  `Email` varchar(100) NOT NULL,
  `Role` varchar(50) NOT NULL,
  PRIMARY KEY (`User_ID`),
  UNIQUE KEY `Email` (`Email`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_general_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

-- Dumping data for table `user`

LOCK TABLES `user` WRITE;
/*!40000 ALTER TABLE `user` DISABLE KEYS */;
INSERT INTO `user` VALUES (1,'Alice','alice@example.com','Student'),(2,'Bob','bob@example.com','Supervisor'),(3,'Charlie','charlie@example.com','Admin');
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

-- Dump completed on 2025-03-31 17:36:07
