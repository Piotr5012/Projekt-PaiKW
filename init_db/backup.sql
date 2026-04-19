-- MySQL dump 10.13  Distrib 8.0.45, for Linux (x86_64)
--
-- Host: localhost    Database: paikw_database
-- ------------------------------------------------------
-- Server version 8.0.45

/*!40101 SET @OLD_CHARACTER_SET_CLIENT=@@CHARACTER_SET_CLIENT */;
/*!40101 SET @OLD_CHARACTER_SET_RESULTS=@@CHARACTER_SET_RESULTS */;
/*!40101 SET @OLD_COLLATION_CONNECTION=@@COLLATION_CONNECTION */;
/*!50503 SET NAMES utf8mb4 */;
/*!40103 SET @OLD_TIME_ZONE=@@TIME_ZONE */;
/*!40103 SET TIME_ZONE='+00:00' */;
/*!40014 SET @OLD_UNIQUE_CHECKS=@@UNIQUE_CHECKS, UNIQUE_CHECKS=0 */;
/*!40014 SET @OLD_FOREIGN_KEY_CHECKS=@@FOREIGN_KEY_CHECKS, FOREIGN_KEY_CHECKS=0 */;
/*!40101 SET @OLD_SQL_MODE=@@SQL_MODE, SQL_MODE='NO_AUTO_VALUE_ON_ZERO' */;
/*!40111 SET @OLD_SQL_NOTES=@@SQL_NOTES, SQL_NOTES=0 */;

--
-- Table structure for table `categories`
--

DROP TABLE IF EXISTS `categories`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categories` (
  `id` int NOT NULL AUTO_INCREMENT,
  `name` varchar(50) NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `name` (`name`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categories`
--

LOCK TABLES `categories` WRITE;
/*!40000 ALTER TABLE `categories` DISABLE KEYS */;
INSERT INTO `categories` VALUES (5,'Finanse'),(4,'Kultura'),(3,'Podróże'),(2,'Rozrywka'),(1,'Styl życia');
/*!40000 ALTER TABLE `categories` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comment_ratings`
--

DROP TABLE IF EXISTS `comment_ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comment_ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `comment_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating_type` enum('like','dislike') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_user_comment` (`user_id`,`comment_id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comment_ratings`
--

LOCK TABLES `comment_ratings` WRITE;
/*!40000 ALTER TABLE `comment_ratings` DISABLE KEYS */;
INSERT INTO `comment_ratings` VALUES (1,1,3,'like'),(4,4,3,'dislike'),(5,5,3,'like'),(6,14,3,'dislike'),(7,17,61,'dislike'),(8,19,62,'like'),(9,21,64,'dislike'),(10,20,64,'dislike'),(11,22,3,'like');
/*!40000 ALTER TABLE `comment_ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `comments`
--

DROP TABLE IF EXISTS `comments`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `comments` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `parent_id` int DEFAULT NULL,
  `content` text NOT NULL,
  `data` datetime DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `post_id` (`post_id`),
  KEY `user_id` (`user_id`),
  KEY `parent_id` (`parent_id`),
  CONSTRAINT `comments_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `comments_ibfk_3` FOREIGN KEY (`parent_id`) REFERENCES `comments` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `comments`
--

LOCK TABLES `comments` WRITE;
/*!40000 ALTER TABLE `comments` DISABLE KEYS */;
INSERT INTO `comments` VALUES (1,11,3,NULL,'dodawanie testowego komentarza','2026-04-11 14:23:49'),(3,11,50,NULL,'nowy komentarz','2026-04-11 14:54:21'),(4,11,3,1,'odpowiedź na komentarz','2026-04-11 15:44:01'),(5,11,3,4,'odpowiedź na odpowiedź na komentarz','2026-04-11 15:44:19'),(6,11,3,1,'inna odpowiedź','2026-04-11 15:44:57'),(7,11,3,5,'odpowiedź na odpowiedź na odpowiedź na komentarz','2026-04-11 15:48:11'),(8,11,3,5,'nowe drzewko odpowiedzi','2026-04-11 15:48:42'),(9,11,3,7,'odpowiedź na odpowiedź na odpowiedź na odpowiedź na komentarz','2026-04-11 15:49:15'),(20,19,64,NULL,'działasz?','2026-04-13 11:07:41'),(21,19,64,20,'Klasa','2026-04-13 11:07:50'),(22,19,65,NULL,'qqqq','2026-04-15 18:32:15'),(23,19,3,22,'nice','2026-04-15 18:34:22'),(24,19,65,NULL,'s','2026-04-16 11:54:11');
/*!40000 ALTER TABLE `comments` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `followers`
--

DROP TABLE IF EXISTS `followers`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `followers` (
  `id` int NOT NULL AUTO_INCREMENT,
  `follower_id` int NOT NULL,
  `followed_id` int NOT NULL,
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_follow` (`follower_id`,`followed_id`),
  KEY `followed_id` (`followed_id`),
  CONSTRAINT `followers_ibfk_1` FOREIGN KEY (`follower_id`) REFERENCES `users` (`id`) ON DELETE CASCADE,
  CONSTRAINT `followers_ibfk_2` FOREIGN KEY (`followed_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=69 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `followers`
--

LOCK TABLES `followers` WRITE;
/*!40000 ALTER TABLE `followers` DISABLE KEYS */;
INSERT INTO `followers` VALUES (64,64,3,'2026-04-16 13:41:31'),(65,65,64,'2026-04-16 14:18:54'),(66,65,3,'2026-04-16 14:19:16');
/*!40000 ALTER TABLE `followers` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `posts`
--

DROP TABLE IF EXISTS `posts`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `posts` (
  `id` int NOT NULL AUTO_INCREMENT,
  `title` varchar(255) NOT NULL,
  `content` text NOT NULL,
  `author_id` int NOT NULL,
  `category_id` int NOT NULL,
  `image` varchar(255) NOT NULL,
  `data` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  KEY `author_id` (`author_id`),
  KEY `category_id` (`category_id`),
  CONSTRAINT `posts_ibfk_1` FOREIGN KEY (`author_id`) REFERENCES `users` (`id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `posts_ibfk_2` FOREIGN KEY (`category_id`) REFERENCES `categories` (`id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `posts`
--

LOCK TABLES `posts` WRITE;
/*!40000 ALTER TABLE `posts` DISABLE KEYS */;
INSERT INTO `posts` VALUES (1,'testowy Post','to jest testowy post',3,4,'../images/69caa9a5562d8_wird sprite.png','2026-03-30 16:49:41'),(2,'testowy post numer 2 edycja','to jest post testowy numer 2 \r\nedytowane\r\nsd\r\nsd',3,3,'../images/post_69dc18da5087e6.64255901.png','2026-03-30 16:54:59'),(6,'test nr 3','to jest test nr 3 ale tak na prawdę to nie',3,4,'../images/69cab6220b23b_przykładowa topologia.png','2026-03-30 17:42:58'),(11,'Duży testowy post na mojego bloga','Cześć! To jest pierwszy, testowy wpis na tym blogu. Służy on wyłącznie do sprawdzenia, jak prezentują się nagłówki, akapity, listy oraz grafiki w nowym szablonie.\nDlaczego tworzę ten test?\nWpis na bloga powinien być zwięzły i ciekawy, a dobrze napisany wstęp zachęca do dalszego czytania. Oto kilka powodów, dla których warto przeprowadzić testy techniczne:\nSprawdzenie formatowania: Czy czcionka jest czytelna?\nWeryfikacja zdjęć: Jak wyglądają zdjęcia w treści?\nReakcja czytelników: Czy system komentarzy działa poprawnie?\n\"Regularne tworzenie wartościowych treści to klucz do sukcesu w 2026 roku\" – jak wskazują poradniki blogowe.\nCo znajdziesz tutaj później?\nW przyszłości ten blog będzie miejscem, w którym podzielę się moimi przemyśleniami na temat technologii, podróży i rozwoju osobistego. Nie zabraknie praktycznych poradników oraz recenzji ciekawych narzędzi.',3,1,'../images/69d95af754c98_miniturka 2.png','2026-04-10 20:17:59'),(19,'Test POST','Test POST (Power-On Self-Test) to automatyczna procedura diagnostyczna uruchamiana przez BIOS/UEFI natychmiast po włączeniu komputera. Sprawdza ona kluczowe podzespoły, takie jak procesor (CPU), pamięć RAM, karta graficzna i kontrolery dysków. Pomyślny wynik pozwala na załadowanie systemu, a błąd sygnalizowany jest dźwiękami lub diodami LED. \r\nDell\r\nDell\r\n +3\r\nKluczowe informacje o teście POST:\r\nCel: Weryfikacja poprawności działania sprzętu przed uruchomieniem systemu operacyjnego.\r\nKolejność działań: Zazwyczaj obejmuje test rejestrów procesora, sumy kontrolnej BIOSu, kontrolera klawiatury, pamięci RAM i karty graficznej.\r\nSygnalizacja błędów (POST codes): Jeśli test wykryje awarię, komputer może wyemitować serię sygnałów dźwiękowych (beep codes) lub wyświetlić kod na diodach diagnostycznych (tzw. karta diagnostyczna POST).\r\nBrak POSTu: Jeśli komputer się włącza (wentylatory działają), ale ekran pozostaje czarny i brak sygnałów dźwiękowych, zazwyczaj oznacza to poważną awarię płyty głównej, procesora lub pamięci RAM',64,1,'../images/69dcce5e051da_platyna.png','2026-04-13 11:07:10'),(20,'sdfsdf','sdfsdf',65,5,'../images/69e0ce4a91280_1.png','2026-04-16 11:55:54');
/*!40000 ALTER TABLE `posts` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ratings`
--

DROP TABLE IF EXISTS `ratings`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ratings` (
  `id` int NOT NULL AUTO_INCREMENT,
  `post_id` int NOT NULL,
  `user_id` int NOT NULL,
  `rating_type` enum('like','dislike') NOT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `unique_vote` (`post_id`,`user_id`),
  KEY `user_id` (`user_id`),
  CONSTRAINT `ratings_ibfk_1` FOREIGN KEY (`post_id`) REFERENCES `posts` (`id`) ON DELETE CASCADE,
  CONSTRAINT `ratings_ibfk_2` FOREIGN KEY (`user_id`) REFERENCES `users` (`id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=36 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ratings`
--

LOCK TABLES `ratings` WRITE;
/*!40000 ALTER TABLE `ratings` DISABLE KEYS */;
INSERT INTO `ratings` VALUES (11,11,50,'like'),(19,6,3,'dislike'),(20,2,3,'dislike'),(23,19,64,'like'),(24,19,65,'dislike'),(25,1,65,'dislike'),(26,19,3,'like'),(28,11,3,'like'),(32,2,64,'dislike'),(35,11,64,'like');
/*!40000 ALTER TABLE `ratings` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` int NOT NULL AUTO_INCREMENT,
  `username` varchar(255) CHARACTER SET utf8mb4 COLLATE utf8mb4_0900_ai_ci NOT NULL,
  `email` varchar(255) NOT NULL,
  `password` varchar(255) NOT NULL,
  `bio` text,
  `avatar` varchar(255) DEFAULT '../avatars/defoult_avatar/defoult_avatar.png',
  `created_at` timestamp NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `email` (`email`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
INSERT INTO `users` VALUES (3,'test','test@wp.pl','$2y$10$xyIakg49dwg1/JNbr7Xb4.ihtlGwLaVYThrR/I8UzEImxPV.EFAci','','../avatars/users_modif_avatars/avatar_3_1776032030.png','2026-04-12 13:33:05'),(50,'test','p.chomiuk@wp.pl','$2y$10$YQTFv4TEIsbeA3g6OF1DoOidJXhGYFlePn71SNq/Ia3.ILX3bGQSK',NULL,'../avatars/defoult_avatar/defoult_avatar.png','2026-04-12 13:33:05'),(54,'cwel','easfs@war.pl','$2y$10$c4tajuJvv4ChO2umhaq6DekWCKIvhBKaB8GYZpsUiff1Vq5DIFCJ.',NULL,'../avatars/defoult_avatar/defoult_avatar.png','2026-04-12 13:33:05'),(57,'doZchakowania','testowy@wp.pl','$2y$10$5tVYjNIIQWyd06stlt7o5OxHnERnujolPAtxAGblL9/o28/v2am0m',NULL,'../avatars/defoult_avatar/defoult_avatar.png','2026-04-12 13:33:05'),(63,'Gowno','Gowno@wp.pl','$2y$10$N8kKXLcbzFClN4amfUPjvOziZWccZTU8kwEZnfj9suKeMJWrx/.qS',NULL,'../avatars/defoult_avatar/defoult_avatar.png','2026-04-13 10:47:49'),(64,'Testowy User','arson5012@wp.pl','$2y$10$Pxra1K7fyqMasQejz8jvOOwWPkeorkRhSgEv39kNgpf9pIbYYVUNq','','../avatars/users_modif_avatars/avatar_64_1776078615.png','2026-04-13 11:04:47'),(65,'hubercik','hubertrad2002@gmail.com','$2y$10$tJQAab9lunzblnP/vYnSqewwDmKPN4QTLckwfb/FtASzYRKUUopya','ssssssssssssssss','../avatars/users_modif_avatars/avatar_65_1776341162.png','2026-04-15 18:31:13');
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-04-16 15:43:05