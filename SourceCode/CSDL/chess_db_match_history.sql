-- MySQL dump 10.13  Distrib 8.0.46, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: chess_db
-- ------------------------------------------------------
-- Server version	8.4.3

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

--
-- Table structure for table `match_history`
--

DROP TABLE IF EXISTS `match_history`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `match_history` (
  `id` int NOT NULL AUTO_INCREMENT,
  `user_id` int NOT NULL,
  `opponent_name` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `game_mode` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `result` enum('win','lose','draw') COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_moves` int NOT NULL,
  `played_at` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `match_history_ibfk_1` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `match_history`
--

LOCK TABLES `match_history` WRITE;
/*!40000 ALTER TABLE `match_history` DISABLE KEYS */;
INSERT INTO `match_history` VALUES (1,1,'Bot Cấp 1','bot_mode','lose',0,'2026-05-08 14:36:28'),(2,2,'Đối thủ 1','online_mode','win',1,'2026-05-08 21:57:28'),(3,1,'Đối thủ 2','online_mode','lose',1,'2026-05-08 21:57:29'),(4,2,'Người chơi','online_mode','win',0,'2026-05-08 22:00:25'),(5,1,'Đối thủ 2','online_mode','lose',0,'2026-05-08 22:00:25'),(6,2,'Đối thủ 1','online_mode','lose',0,'2026-05-12 13:56:25'),(7,1,'Đối thủ 2','online_mode','win',0,'2026-05-12 13:56:30'),(8,2,'Bot Cấp 1','bot_mode','lose',24,'2026-05-14 14:16:45'),(9,2,'Đối thủ 1','online_mode','win',5,'2026-05-14 14:20:43'),(10,1,'anhphuong','online_mode','lose',5,'2026-05-14 14:20:43'),(11,2,'Đối thủ 1','online_mode','win',1,'2026-05-14 14:22:44'),(12,1,'Đối thủ 2','online_mode','lose',1,'2026-05-14 14:22:46'),(13,2,'Anhphuc123','online_mode','draw',0,'2026-05-14 14:30:01'),(14,1,'$DannyTroob$','online_mode','draw',0,'2026-05-14 14:30:03'),(15,1,'$DannyTroob$','online_mode','win',37,'2026-05-14 15:11:22'),(16,2,'anhphuc','online_mode','lose',37,'2026-05-14 15:11:23'),(17,2,'Bot Cấp 4','bot_mode','lose',8,'2026-06-15 10:29:03'),(18,1,'$DannyTroob$','online_mode','win',0,'2026-06-15 16:34:53'),(19,2,'Anhphuc123','online_mode','lose',0,'2026-06-15 16:34:54'),(20,2,'anhphuc','online_mode','win',1,'2026-06-23 20:07:12'),(21,1,'$DannyTroob$','online_mode','lose',1,'2026-06-23 20:07:14'),(22,2,'anhphuc','online_mode','win',1,'2026-06-23 21:11:14'),(23,1,'$DannyTroob$','online_mode','lose',1,'2026-06-23 21:11:15'),(24,1,'$DannyTroob$','online_mode','draw',7,'2026-06-24 09:34:13'),(25,2,'Anhphuc123','online_mode','draw',7,'2026-06-24 09:34:14'),(26,1,'$DannyTroob$','online_mode','draw',4,'2026-06-24 10:16:56'),(27,2,'Anhphuc123','online_mode','draw',4,'2026-06-24 10:16:58'),(28,2,'Bot Cấp 4','bot_mode','lose',34,'2026-06-24 17:21:45'),(29,2,'Anhphuc123','online_mode','win',10,'2026-06-24 17:33:54'),(30,1,'anhphuong','online_mode','lose',10,'2026-06-24 17:33:54'),(31,2,'Bot Cấp 10','bot_mode','lose',11,'2026-06-24 21:41:17'),(32,2,'Anhphuc123','online_mode','win',2,'2026-06-24 22:18:20'),(33,1,'anhphuong','online_mode','lose',2,'2026-06-24 22:18:20'),(34,2,'Bot Cấp 9','bot_mode','lose',16,'2026-06-25 09:43:12'),(35,2,'Player 78024','online_mode','win',0,'2026-06-25 10:26:00'),(36,175,'DannyTroob','online_mode','lose',0,'2026-06-25 10:26:01'),(37,2,'Player 78024','online_mode','win',0,'2026-06-25 10:26:57'),(38,175,'DannyTroob','online_mode','lose',0,'2026-06-25 10:26:58'),(39,175,'DannyTroob','online_mode','win',0,'2026-06-25 10:29:45'),(40,2,'Player 78024','online_mode','lose',0,'2026-06-25 10:29:46'),(41,175,'DannyTroob','online_mode','lose',0,'2026-06-25 10:34:37'),(42,2,'Player 78024','online_mode','win',0,'2026-06-25 10:34:37'),(43,175,'DannyTroob','online_mode','lose',0,'2026-06-25 10:34:45'),(44,2,'Player 78024','online_mode','win',0,'2026-06-25 10:34:45'),(45,1,'anhphuong','online_mode','lose',1,'2026-06-25 12:51:58'),(46,1,'anhphuong','online_mode','lose',0,'2026-06-25 12:52:04'),(47,2,'anhphuc','online_mode','lose',0,'2026-06-25 12:52:51'),(48,2,'anhphuc','online_mode','lose',1,'2026-06-25 12:56:53'),(49,1,'anhphuong','online_mode','win',1,'2026-06-25 12:56:53'),(50,1,'DannyTroob','online_mode','lose',1,'2026-06-25 12:57:53'),(51,2,'Anhphuc123','online_mode','win',1,'2026-06-25 12:57:53'),(52,2,'Anhphuc123','online_mode','lose',7,'2026-06-25 13:13:53'),(53,1,'DannyTroob','online_mode','win',7,'2026-06-25 13:13:53'),(54,2,'Anhphuc123','online_mode','win',3,'2026-06-25 13:31:51'),(55,1,'DannyTroob','online_mode','lose',3,'2026-06-25 13:31:51');
/*!40000 ALTER TABLE `match_history` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-25 20:48:46
