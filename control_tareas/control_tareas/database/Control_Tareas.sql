-- MySQL dump 10.13  Distrib 8.0.43, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: control_tareas
-- ------------------------------------------------------
-- Server version	8.0.43

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
-- Table structure for table `grupos`
--

DROP TABLE IF EXISTS `grupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `grupos` (
  `id_grupo` int NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) NOT NULL,
  PRIMARY KEY (`id_grupo`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `grupos`
--

LOCK TABLES `grupos` WRITE;
/*!40000 ALTER TABLE `grupos` DISABLE KEYS */;
INSERT INTO `grupos` VALUES (1,'Universidad'),(2,'Trabajo'),(3,'Personal');
/*!40000 ALTER TABLE `grupos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `responsables`
--

DROP TABLE IF EXISTS `responsables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `responsables` (
  `id_responsable` int NOT NULL AUTO_INCREMENT,
  `identificacion` varchar(50) NOT NULL,
  `nombre` varchar(100) NOT NULL,
  `apellidos` varchar(150) NOT NULL,
  PRIMARY KEY (`id_responsable`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `responsables`
--

LOCK TABLES `responsables` WRITE;
/*!40000 ALTER TABLE `responsables` DISABLE KEYS */;
INSERT INTO `responsables` VALUES (1,'208400050','Angie','Romero Ceciliano'),(2,'202220222','Adrián','Solano Vargas'),(3,'303330333','Mauricio','Madriz Ceciliano');
/*!40000 ALTER TABLE `responsables` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `tareas`
--

DROP TABLE IF EXISTS `tareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `tareas` (
  `id_tarea` int NOT NULL AUTO_INCREMENT,
  `detalle` text NOT NULL,
  `prioridad` enum('Baja','Media','Alta') NOT NULL,
  `fecha_limite` date DEFAULT NULL,
  `estado` enum('Pendiente','En progreso','Bloqueada','Finalizada') NOT NULL DEFAULT 'Pendiente',
  `fecha_finalizacion` datetime DEFAULT NULL,
  `id_responsable` int DEFAULT NULL,
  `id_grupo` int DEFAULT NULL,
  PRIMARY KEY (`id_tarea`),
  KEY `id_responsable` (`id_responsable`),
  KEY `id_grupo` (`id_grupo`),
  CONSTRAINT `tareas_ibfk_1` FOREIGN KEY (`id_responsable`) REFERENCES `responsables` (`id_responsable`) ON DELETE SET NULL,
  CONSTRAINT `tareas_ibfk_2` FOREIGN KEY (`id_grupo`) REFERENCES `grupos` (`id_grupo`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_0900_ai_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `tareas`
--

LOCK TABLES `tareas` WRITE;
/*!40000 ALTER TABLE `tareas` DISABLE KEYS */;
INSERT INTO `tareas` VALUES (1,'Realizar documentación del proyecto','Alta','2026-06-22','Pendiente',NULL,1,1),(2,'Revisar diseño de base de datos','Media','2026-06-21','En progreso',NULL,2,1),(3,'Comprar materiales pendientes','Baja',NULL,'Pendiente',NULL,NULL,3),(4,'Corregir errores del sistema','Alta','2026-06-23','Bloqueada',NULL,3,2),(5,'Análisis de Requerimientos','Alta','2026-07-12','Pendiente',NULL,1,1);
/*!40000 ALTER TABLE `tareas` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed on 2026-06-18 16:19:28
