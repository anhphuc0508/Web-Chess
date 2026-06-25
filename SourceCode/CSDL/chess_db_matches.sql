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
-- Table structure for table `matches`
--

DROP TABLE IF EXISTS `matches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `matches` (
  `id` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `white_id` int NOT NULL,
  `black_id` int NOT NULL,
  `move_history` text COLLATE utf8mb4_unicode_ci,
  `status` enum('playing','finished','aborted') COLLATE utf8mb4_unicode_ci DEFAULT 'playing',
  PRIMARY KEY (`id`),
  KEY `white_id` (`white_id`),
  KEY `black_id` (`black_id`),
  CONSTRAINT `matches_ibfk_1` FOREIGN KEY (`white_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `matches_ibfk_2` FOREIGN KEY (`black_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `matches`
--

LOCK TABLES `matches` WRITE;
/*!40000 ALTER TABLE `matches` DISABLE KEYS */;
INSERT INTO `matches` VALUES ('room_1778252234980',1,2,'[\"e4\",\"d5\"]','finished'),('room_1778252418410',2,1,'[]','finished'),('room_1778568970483',1,2,'[]','finished'),('room_1778743144869',2,1,'[\"e4\",\"d5\",\"f4\",\"c6\",\"Nf3\",\"Na6\",\"Bc4\",\"Be6\",\"O-O\"]','finished'),('room_1778743353292',1,2,'[\"e4\",\"d5\"]','finished'),('room_1778743780661',2,1,'[]','finished'),('room_1778744977955',1,2,'[\"e4\",\"g5\",\"Nf3\",\"f6\",\"g4\",\"e5\",\"d4\",\"Nh6\",\"dxe5\",\"fxe5\",\"Nxe5\",\"Bd6\",\"Nc4\",\"Be7\",\"f4\",\"d6\",\"fxg5\",\"Nxg4\",\"Rg1\",\"Nf6\",\"gxf6\",\"Bxf6\",\"Qh5+\",\"Kf8\",\"Qh6+\",\"Bg7\",\"Qf4+\",\"Qf6\",\"Qxf6+\",\"Bxf6\",\"b4\",\"b5\",\"Bh6+\",\"Bg7\",\"Bxg7+\",\"Ke8\",\"Bxh8\",\"Kd8\",\"Rg7\",\"Nd7\",\"Nc3\",\"bxc4\",\"Rxh7\",\"Rb8\",\"Nd5\",\"Rb5\",\"Rb1\",\"c6\",\"Nf6\",\"Nxf6\",\"Bxf6+\",\"Ke8\",\"Bxc4\",\"Rf5\",\"exf5\",\"Bxf5\",\"Kd2\",\"d5\",\"Rg1\",\"Bxh7\",\"Rg8+\",\"Kf7\",\"Ra8\",\"Kxf6\",\"Rxa7\",\"dxc4\",\"Rxh7\",\"Ke6\",\"h3\",\"c3+\",\"Kxc3\",\"c5\",\"bxc5\"]','finished'),('room_1781516061683',1,2,'[]','finished'),('room_1782219998585',2,1,'[\"d4\"]','finished'),('room_1782223851615',1,2,'[\"e4\",\"d5\"]','finished'),('room_1782268388240',1,2,'[\"d4\",\"Nc6\",\"e4\",\"Nf6\",\"d5\",\"g5\",\"f4\",\"Bh6\",\"c3\",\"O-O\",\"fxg5\",\"e6\",\"Qh5\"]','finished'),('room_1782270908922',1,2,'[\"Nf3\",\"Nf6\",\"Ng1\",\"Ng8\",\"Nf3\",\"Nf6\",\"Ng1\",\"Ng8\"]','finished'),('room_1782297073550',2,1,'[\"d4\",\"h5\",\"c3\",\"h4\",\"Na3\",\"h3\",\"b3\",\"Nf6\",\"Bh6\",\"Ng4\",\"e4\",\"f5\",\"Bb5\",\"c6\",\"f4\",\"d6\",\"Nf3\",\"e6\",\"O-O\"]','finished'),('room_1782313841320',1,2,'[\"e4\",\"e5\",\"Nf3\"]','finished'),('room_1782357954741',175,2,'[]','finished'),('room_1782358012669',175,2,'[]','finished'),('room_1782358180829',2,175,'[]','finished'),('room_1782358471188',2,175,'[]','finished'),('room_1782358482484',175,2,'[]','finished'),('room_1782366515166',1,2,'[\"e4\"]','finished'),('room_1782367045204',2,1,'[\"f4\",\"d5\"]','finished'),('room_1782367983159',2,1,'[\"e4\",\"e5\",\"d4\",\"d5\",\"c4\",\"f5\",\"Na3\",\"c5\",\"Be3\",\"b5\",\"Qb3\",\"g6\",\"O-O-O\"]','finished'),('room_1782369053222',2,1,'[\"e4\",\"c5\",\"d4\",\"d5\",\"b4\"]','finished');
/*!40000 ALTER TABLE `matches` ENABLE KEYS */;
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
