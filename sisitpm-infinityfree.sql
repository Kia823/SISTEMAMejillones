-- MySQL dump 10.13  Distrib 8.0.30, for Win64 (x86_64)
--
-- Host: 127.0.0.1    Database: sisitpm
-- ------------------------------------------------------
-- Server version	8.0.30

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
-- Table structure for table `actas_baja`
--

DROP TABLE IF EXISTS `actas_baja`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actas_baja` (
  `id_acta_baja` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_acta` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_activo` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_baja` date NOT NULL,
  `archivo_resolucion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('PENDIENTE','APROBADA','RECHAZADA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id_aprobador` bigint unsigned DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `comentario_aprobacion` text COLLATE utf8mb4_unicode_ci,
  `es_sustitucion` tinyint(1) NOT NULL DEFAULT '0',
  `datos_sustituto` json DEFAULT NULL,
  `id_activo_sustituto` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id_acta_baja`),
  UNIQUE KEY `actas_baja_numero_acta_unique` (`numero_acta`),
  KEY `actas_baja_id_activo_foreign` (`id_activo`),
  KEY `actas_baja_id_usuario_foreign` (`id_usuario`),
  KEY `actas_baja_id_aprobador_foreign` (`id_aprobador`),
  KEY `actas_baja_id_activo_sustituto_foreign` (`id_activo_sustituto`),
  KEY `actas_baja_estado_id_acta_baja_index` (`estado`,`id_acta_baja`),
  CONSTRAINT `actas_baja_id_activo_foreign` FOREIGN KEY (`id_activo`) REFERENCES `activos_fijos` (`id_activo`),
  CONSTRAINT `actas_baja_id_activo_sustituto_foreign` FOREIGN KEY (`id_activo_sustituto`) REFERENCES `activos_fijos` (`id_activo`) ON DELETE SET NULL,
  CONSTRAINT `actas_baja_id_aprobador_foreign` FOREIGN KEY (`id_aprobador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `actas_baja_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actas_baja`
--

LOCK TABLES `actas_baja` WRITE;
/*!40000 ALTER TABLE `actas_baja` DISABLE KEYS */;
/*!40000 ALTER TABLE `actas_baja` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `actas_transferencia`
--

DROP TABLE IF EXISTS `actas_transferencia`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `actas_transferencia` (
  `id_acta_transferencia` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_acta` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `id_activo` bigint unsigned NOT NULL,
  `id_origen_ambiente` bigint unsigned NOT NULL,
  `id_destino_ambiente` bigint unsigned NOT NULL,
  `id_custodio_anterior` bigint unsigned DEFAULT NULL,
  `id_custodio_nuevo` bigint unsigned NOT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `fecha_transferencia` date NOT NULL,
  `estado` enum('PENDIENTE','APROBADA','RECHAZADA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id_aprobador` bigint unsigned DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `comentario_aprobacion` text COLLATE utf8mb4_unicode_ci,
  `estado_anterior` varchar(25) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`id_acta_transferencia`),
  UNIQUE KEY `actas_transferencia_numero_acta_unique` (`numero_acta`),
  KEY `actas_transferencia_id_activo_foreign` (`id_activo`),
  KEY `actas_transferencia_id_origen_ambiente_foreign` (`id_origen_ambiente`),
  KEY `actas_transferencia_id_destino_ambiente_foreign` (`id_destino_ambiente`),
  KEY `actas_transferencia_id_custodio_anterior_foreign` (`id_custodio_anterior`),
  KEY `actas_transferencia_id_custodio_nuevo_foreign` (`id_custodio_nuevo`),
  KEY `actas_transferencia_id_aprobador_foreign` (`id_aprobador`),
  KEY `actas_transferencia_estado_id_acta_transferencia_index` (`estado`,`id_acta_transferencia`),
  CONSTRAINT `actas_transferencia_id_activo_foreign` FOREIGN KEY (`id_activo`) REFERENCES `activos_fijos` (`id_activo`),
  CONSTRAINT `actas_transferencia_id_aprobador_foreign` FOREIGN KEY (`id_aprobador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `actas_transferencia_id_custodio_anterior_foreign` FOREIGN KEY (`id_custodio_anterior`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `actas_transferencia_id_custodio_nuevo_foreign` FOREIGN KEY (`id_custodio_nuevo`) REFERENCES `usuarios` (`id_usuario`),
  CONSTRAINT `actas_transferencia_id_destino_ambiente_foreign` FOREIGN KEY (`id_destino_ambiente`) REFERENCES `ambientes` (`id_ambiente`),
  CONSTRAINT `actas_transferencia_id_origen_ambiente_foreign` FOREIGN KEY (`id_origen_ambiente`) REFERENCES `ambientes` (`id_ambiente`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `actas_transferencia`
--

LOCK TABLES `actas_transferencia` WRITE;
/*!40000 ALTER TABLE `actas_transferencia` DISABLE KEYS */;
/*!40000 ALTER TABLE `actas_transferencia` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `activos_fijos`
--

DROP TABLE IF EXISTS `activos_fijos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `activos_fijos` (
  `id_activo` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo_activo` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL,
  `tipo_bien` enum('ACTIVO_FIJO','NO_ACTIVO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO_FIJO',
  `nombre` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` text COLLATE utf8mb4_unicode_ci,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `marca` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `modelo` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `numero_serie` varchar(80) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `valor_adquisicion` decimal(12,2) NOT NULL DEFAULT '0.00',
  `fecha_adquisicion` date DEFAULT NULL,
  `estado_fisico` enum('B','R','M','FF') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'B',
  `estado_registro` enum('ACTIVO','ASIGNADO','EN_TRANSFERENCIA','BAJA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `imagen` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `ruta_qr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `codigo_qr` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_categoria` bigint unsigned NOT NULL,
  `id_ambiente` bigint unsigned NOT NULL,
  `id_fuente` bigint unsigned NOT NULL,
  `id_custodio` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  `id_item` bigint unsigned DEFAULT NULL,
  PRIMARY KEY (`id_activo`),
  UNIQUE KEY `activos_fijos_codigo_activo_unique` (`codigo_activo`),
  KEY `activos_fijos_id_ambiente_foreign` (`id_ambiente`),
  KEY `activos_fijos_id_fuente_foreign` (`id_fuente`),
  KEY `activos_fijos_id_custodio_foreign` (`id_custodio`),
  KEY `activos_fijos_nombre_index` (`nombre`),
  KEY `activos_fijos_estado_fisico_index` (`estado_fisico`),
  KEY `activos_fijos_fecha_adquisicion_index` (`fecha_adquisicion`),
  KEY `activos_fijos_id_categoria_nombre_index` (`id_categoria`,`nombre`),
  KEY `activos_fijos_id_item_estado_registro_index` (`id_item`,`estado_registro`),
  KEY `activos_fijos_tipo_bien_index` (`tipo_bien`),
  CONSTRAINT `activos_fijos_id_ambiente_foreign` FOREIGN KEY (`id_ambiente`) REFERENCES `ambientes` (`id_ambiente`),
  CONSTRAINT `activos_fijos_id_categoria_foreign` FOREIGN KEY (`id_categoria`) REFERENCES `categorias` (`id_categoria`),
  CONSTRAINT `activos_fijos_id_custodio_foreign` FOREIGN KEY (`id_custodio`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `activos_fijos_id_fuente_foreign` FOREIGN KEY (`id_fuente`) REFERENCES `fuentes_financiamiento` (`id_fuente`),
  CONSTRAINT `activos_fijos_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`) ON DELETE SET NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `activos_fijos`
--

LOCK TABLES `activos_fijos` WRITE;
/*!40000 ALTER TABLE `activos_fijos` DISABLE KEYS */;
/*!40000 ALTER TABLE `activos_fijos` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `ambientes`
--

DROP TABLE IF EXISTS `ambientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `ambientes` (
  `id_ambiente` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(30) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `bloque` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `piso` int DEFAULT NULL,
  `id_carrera` bigint unsigned NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_ambiente`),
  UNIQUE KEY `ambientes_codigo_unique` (`codigo`),
  KEY `ambientes_id_carrera_foreign` (`id_carrera`),
  CONSTRAINT `ambientes_id_carrera_foreign` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `ambientes`
--

LOCK TABLES `ambientes` WRITE;
/*!40000 ALTER TABLE `ambientes` DISABLE KEYS */;
INSERT INTO `ambientes` VALUES (1,'LAB-1','Laboratorio 1','A',1,1,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(2,'LAB-2','Laboratorio 2','A',1,1,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(3,'LAB-3','Laboratorio 3','A',1,1,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(4,'RED-1','Redes','B',1,1,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(5,'REC-1','Rectorado','A',2,7,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(6,'SEC-1','Secretaría General','A',2,7,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(7,'ADM-1','Dirección Administrativa','A',2,7,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(8,'ALM-1','Almacén','C',1,7,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(9,'BIB-1','Biblioteca','B',1,7,'2026-10-03 19:50:21','2026-10-03 19:50:21');
/*!40000 ALTER TABLE `ambientes` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache`
--

DROP TABLE IF EXISTS `cache`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `value` mediumtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache`
--

LOCK TABLES `cache` WRITE;
/*!40000 ALTER TABLE `cache` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `cache_locks`
--

DROP TABLE IF EXISTS `cache_locks`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `cache_locks` (
  `key` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `owner` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `expiration` bigint NOT NULL,
  PRIMARY KEY (`key`),
  KEY `cache_locks_expiration_index` (`expiration`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `cache_locks`
--

LOCK TABLES `cache_locks` WRITE;
/*!40000 ALTER TABLE `cache_locks` DISABLE KEYS */;
/*!40000 ALTER TABLE `cache_locks` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `carreras`
--

DROP TABLE IF EXISTS `carreras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `carreras` (
  `id_carrera` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_carrera`),
  UNIQUE KEY `carreras_codigo_unique` (`codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `carreras`
--

LOCK TABLES `carreras` WRITE;
/*!40000 ALTER TABLE `carreras` DISABLE KEYS */;
INSERT INTO `carreras` VALUES (1,'SIF','Sistemas Informáticos','2026-10-03 19:50:20','2026-10-03 19:50:20'),(2,'INA','Industrias de Alimentos','2026-10-03 19:50:20','2026-10-03 19:50:20'),(3,'GTR','Gastronomía','2026-10-03 19:50:20','2026-10-03 19:50:20'),(4,'MAZ','Mecánica Automotriz','2026-10-03 19:50:20','2026-10-03 19:50:20'),(5,'MEI','Mecánica Industrial','2026-10-03 19:50:20','2026-10-03 19:50:20'),(6,'ELC','Electrónica','2026-10-03 19:50:20','2026-10-03 19:50:20'),(7,'ADM','Unidades Administrativas','2026-10-03 19:50:20','2026-10-03 19:50:20');
/*!40000 ALTER TABLE `carreras` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `categorias`
--

DROP TABLE IF EXISTS `categorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `categorias` (
  `id_categoria` bigint unsigned NOT NULL AUTO_INCREMENT,
  `codigo` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_ambiente` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_categoria`),
  UNIQUE KEY `categorias_codigo_unique` (`codigo`),
  KEY `categorias_nombre_index` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `categorias`
--

LOCK TABLES `categorias` WRITE;
/*!40000 ALTER TABLE `categorias` DISABLE KEYS */;
INSERT INTO `categorias` VALUES (1,'EQP-INF','Equipos Informáticos','Equipos de cómputo y accesorios',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(2,'EQP-ELC','Equipos Electrónicos','Equipos electrónicos y componentes',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(3,'MOB-URB','Mobiliario','Mobiliario y enseres',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(4,'MAT-CONS','Material de Consumo','Escobas, papeleras, engrampadoras, perforadoras',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21');
/*!40000 ALTER TABLE `categorias` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `configuraciones`
--

DROP TABLE IF EXISTS `configuraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `configuraciones` (
  `id_configuracion` bigint unsigned NOT NULL AUTO_INCREMENT,
  `institucion_nombre` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'Instituto Tecnológico "Puerto de Mejillones"',
  `institucion_direccion` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'El Alto, La Paz - Bolivia',
  `institucion_director` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT 'Lic. Jimmy Ovidio Sirpa Choque',
  `gestion_fiscal` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT '2026',
  `prefijo_activo` varchar(20) COLLATE utf8mb4_unicode_ci DEFAULT 'TPM-',
  `validacion_estricta` tinyint(1) NOT NULL DEFAULT '1',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_configuracion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `configuraciones`
--

LOCK TABLES `configuraciones` WRITE;
/*!40000 ALTER TABLE `configuraciones` DISABLE KEYS */;
/*!40000 ALTER TABLE `configuraciones` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `failed_jobs`
--

DROP TABLE IF EXISTS `failed_jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `failed_jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `uuid` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `connection` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `exception` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `failed_at` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP,
  PRIMARY KEY (`id`),
  UNIQUE KEY `failed_jobs_uuid_unique` (`uuid`),
  KEY `failed_jobs_connection_queue_failed_at_index` (`connection`,`queue`,`failed_at`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `failed_jobs`
--

LOCK TABLES `failed_jobs` WRITE;
/*!40000 ALTER TABLE `failed_jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `failed_jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `fuentes_financiamiento`
--

DROP TABLE IF EXISTS `fuentes_financiamiento`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `fuentes_financiamiento` (
  `id_fuente` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre` varchar(100) COLLATE utf8mb4_unicode_ci NOT NULL,
  `codigo` varchar(10) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_fuente`),
  UNIQUE KEY `fuentes_financiamiento_nombre_unique` (`nombre`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `fuentes_financiamiento`
--

LOCK TABLES `fuentes_financiamiento` WRITE;
/*!40000 ALTER TABLE `fuentes_financiamiento` DISABLE KEYS */;
INSERT INTO `fuentes_financiamiento` VALUES (1,'Gobierno Departamental','GD','2026-10-03 19:50:21','2026-10-03 19:50:21'),(2,'Gobierno Municipal','GM','2026-10-03 19:50:21','2026-10-03 19:50:21'),(3,'Gobierno Nacional','GN','2026-10-03 19:50:21','2026-10-03 19:50:21'),(4,'Proyecto Socio Productivo','PSP','2026-10-03 19:50:21','2026-10-03 19:50:21'),(5,'Adquisición','ADQ','2026-10-03 19:50:21','2026-10-03 19:50:21'),(6,'Otros','OTR','2026-10-03 19:50:21','2026-10-03 19:50:21'),(7,'Recursos Propios por Carrera','RPC','2026-10-03 19:50:21','2026-10-03 19:50:21'),(8,'Recursos Propios Institucionales','RPI','2026-10-03 19:50:21','2026-10-03 19:50:21');
/*!40000 ALTER TABLE `fuentes_financiamiento` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `historial_titulares`
--

DROP TABLE IF EXISTS `historial_titulares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `historial_titulares` (
  `id_historial` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_item` bigint unsigned NOT NULL,
  `id_usuario_anterior` bigint unsigned DEFAULT NULL,
  `id_usuario_nuevo` bigint unsigned DEFAULT NULL,
  `nombre_anterior` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `nombre_nuevo` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `tipo` enum('CREACION','ASIGNACION','DETACH','SUSTITUCION','RENUNCIA') COLLATE utf8mb4_unicode_ci NOT NULL,
  `fecha_evento` date NOT NULL,
  `motivo` text COLLATE utf8mb4_unicode_ci,
  `id_usuario_registro` bigint unsigned DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_historial`),
  KEY `historial_titulares_id_usuario_anterior_foreign` (`id_usuario_anterior`),
  KEY `historial_titulares_id_usuario_nuevo_foreign` (`id_usuario_nuevo`),
  KEY `historial_titulares_id_usuario_registro_foreign` (`id_usuario_registro`),
  KEY `historial_titulares_id_item_fecha_evento_index` (`id_item`,`fecha_evento`),
  CONSTRAINT `historial_titulares_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`) ON DELETE CASCADE,
  CONSTRAINT `historial_titulares_id_usuario_anterior_foreign` FOREIGN KEY (`id_usuario_anterior`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `historial_titulares_id_usuario_nuevo_foreign` FOREIGN KEY (`id_usuario_nuevo`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `historial_titulares_id_usuario_registro_foreign` FOREIGN KEY (`id_usuario_registro`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `historial_titulares`
--

LOCK TABLES `historial_titulares` WRITE;
/*!40000 ALTER TABLE `historial_titulares` DISABLE KEYS */;
/*!40000 ALTER TABLE `historial_titulares` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `item_usuario`
--

DROP TABLE IF EXISTS `item_usuario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `item_usuario` (
  `id_item_usuario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_item` bigint unsigned NOT NULL,
  `id_usuario` bigint unsigned NOT NULL,
  `tipo` enum('TITULAR','SECUNDARIO','RESPONSABLE') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'TITULAR',
  `fecha_inicio` date DEFAULT NULL,
  `fecha_fin` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_item_usuario`),
  UNIQUE KEY `item_usuario_id_item_id_usuario_unique` (`id_item`,`id_usuario`),
  KEY `item_usuario_id_usuario_index` (`id_usuario`),
  CONSTRAINT `item_usuario_id_item_foreign` FOREIGN KEY (`id_item`) REFERENCES `items` (`id_item`) ON DELETE CASCADE,
  CONSTRAINT `item_usuario_id_usuario_foreign` FOREIGN KEY (`id_usuario`) REFERENCES `usuarios` (`id_usuario`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `item_usuario`
--

LOCK TABLES `item_usuario` WRITE;
/*!40000 ALTER TABLE `item_usuario` DISABLE KEYS */;
INSERT INTO `item_usuario` VALUES (1,1,1,'TITULAR','2026-01-01',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(2,2,2,'TITULAR','2026-01-01',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(3,3,3,'TITULAR','2026-01-01',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(4,4,4,'TITULAR','2026-01-01',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(5,5,5,'TITULAR','2026-01-01',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(6,6,6,'TITULAR','2026-01-01',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21');
/*!40000 ALTER TABLE `item_usuario` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `items`
--

DROP TABLE IF EXISTS `items`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `items` (
  `id_item` bigint unsigned NOT NULL AUTO_INCREMENT,
  `numero_item` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `descripcion` varchar(150) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `id_carrera` bigint unsigned DEFAULT NULL,
  `id_ambiente` bigint unsigned DEFAULT NULL,
  `unidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `estado` enum('VACANTE','OCUPADO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'VACANTE',
  `fecha_vacancia` date DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  `deleted_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_item`),
  UNIQUE KEY `items_numero_item_unique` (`numero_item`),
  KEY `items_id_carrera_foreign` (`id_carrera`),
  KEY `items_estado_id_carrera_index` (`estado`,`id_carrera`),
  KEY `items_id_ambiente_index` (`id_ambiente`),
  CONSTRAINT `items_id_ambiente_foreign` FOREIGN KEY (`id_ambiente`) REFERENCES `ambientes` (`id_ambiente`) ON DELETE SET NULL,
  CONSTRAINT `items_id_carrera_foreign` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `items`
--

LOCK TABLES `items` WRITE;
/*!40000 ALTER TABLE `items` DISABLE KEYS */;
INSERT INTO `items` VALUES (1,'91','Administradora',NULL,7,'Dirección Administrativa','OCUPADO',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21',NULL),(2,'92','Inventariador',NULL,8,'Departamento de Activos','OCUPADO',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21',NULL),(3,'131','Docente Custodio - Laboratorio 3',1,3,'Laboratorio 3','OCUPADO',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21',NULL),(4,'115','Docente Custodio - Laboratorio 3',1,3,'Laboratorio 3','OCUPADO',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21',NULL),(5,'124','Jefe de Carrera de Sistemas Informáticos',1,NULL,'Escuela de Sistemas Informáticos','OCUPADO',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21',NULL),(6,'88','Rector',NULL,5,'Rectorado','OCUPADO',NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21',NULL);
/*!40000 ALTER TABLE `items` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `job_batches`
--

DROP TABLE IF EXISTS `job_batches`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `job_batches` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `total_jobs` int NOT NULL,
  `pending_jobs` int NOT NULL,
  `failed_jobs` int NOT NULL,
  `failed_job_ids` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `options` mediumtext COLLATE utf8mb4_unicode_ci,
  `cancelled_at` int DEFAULT NULL,
  `created_at` int NOT NULL,
  `finished_at` int DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `job_batches`
--

LOCK TABLES `job_batches` WRITE;
/*!40000 ALTER TABLE `job_batches` DISABLE KEYS */;
/*!40000 ALTER TABLE `job_batches` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `jobs`
--

DROP TABLE IF EXISTS `jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `jobs` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `queue` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `attempts` smallint unsigned NOT NULL,
  `reserved_at` int unsigned DEFAULT NULL,
  `available_at` int unsigned NOT NULL,
  `created_at` int unsigned NOT NULL,
  PRIMARY KEY (`id`),
  KEY `jobs_queue_index` (`queue`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `jobs`
--

LOCK TABLES `jobs` WRITE;
/*!40000 ALTER TABLE `jobs` DISABLE KEYS */;
/*!40000 ALTER TABLE `jobs` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `migrations`
--

DROP TABLE IF EXISTS `migrations`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `migrations` (
  `id` int unsigned NOT NULL AUTO_INCREMENT,
  `migration` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `batch` int NOT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `migrations`
--

LOCK TABLES `migrations` WRITE;
/*!40000 ALTER TABLE `migrations` DISABLE KEYS */;
INSERT INTO `migrations` VALUES (1,'0001_01_01_000000_create_users_table',1),(2,'0001_01_01_000001_create_cache_table',1),(3,'0001_01_01_000002_create_jobs_table',1),(4,'2026_09_24_083254_create_sisitpm_table',1),(5,'2026_10_03_130000_create_items_table',1),(6,'2026_10_03_130100_create_historial_titulares_table',1),(7,'2026_10_03_150000_add_tipo_bien_to_activos_fijos_table',2),(8,'2026_10_03_160000_add_ambiente_to_items_table',3),(9,'2026_10_03_170000_add_flujo_aprobacion_a_las_actas',4);
/*!40000 ALTER TABLE `migrations` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `password_reset_tokens`
--

DROP TABLE IF EXISTS `password_reset_tokens`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `password_reset_tokens` (
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `token` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `password_reset_tokens`
--

LOCK TABLES `password_reset_tokens` WRITE;
/*!40000 ALTER TABLE `password_reset_tokens` DISABLE KEYS */;
/*!40000 ALTER TABLE `password_reset_tokens` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `sessions`
--

DROP TABLE IF EXISTS `sessions`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `sessions` (
  `id` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `user_id` bigint unsigned DEFAULT NULL,
  `ip_address` varchar(45) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `user_agent` text COLLATE utf8mb4_unicode_ci,
  `payload` longtext COLLATE utf8mb4_unicode_ci NOT NULL,
  `last_activity` int NOT NULL,
  PRIMARY KEY (`id`),
  KEY `sessions_user_id_index` (`user_id`),
  KEY `sessions_last_activity_index` (`last_activity`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `sessions`
--

LOCK TABLES `sessions` WRITE;
/*!40000 ALTER TABLE `sessions` DISABLE KEYS */;
INSERT INTO `sessions` VALUES ('ut6zUhx3njirx0tDkPqKpgyjejUhusfxvSJBxggR',NULL,'127.0.0.1','Mozilla/5.0 (Windows NT 10.0; Win64; x64) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/154.0.0.0 Safari/537.36','eyJfdG9rZW4iOiJJNzN3MUtlOU5PR2FDZWxlTnY0N0E3WFlmWTNHWDlKWU9nNGtUdjRkIiwiX2ZsYXNoIjp7Im9sZCI6W10sIm5ldyI6W119LCJfcHJldmlvdXMiOnsidXJsIjoiaHR0cDpcL1wvMTI3LjAuMC4xOjgwMDBcL2xvZ2luIiwicm91dGUiOiJsb2dpbiJ9fQ==',1791140640);
/*!40000 ALTER TABLE `sessions` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `users`
--

DROP TABLE IF EXISTS `users`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `users` (
  `id` bigint unsigned NOT NULL AUTO_INCREMENT,
  `name` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email_verified_at` timestamp NULL DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id`),
  UNIQUE KEY `users_email_unique` (`email`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `users`
--

LOCK TABLES `users` WRITE;
/*!40000 ALTER TABLE `users` DISABLE KEYS */;
/*!40000 ALTER TABLE `users` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `usuarios`
--

DROP TABLE IF EXISTS `usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `usuarios` (
  `id_usuario` bigint unsigned NOT NULL AUTO_INCREMENT,
  `nombre_completo` varchar(150) COLLATE utf8mb4_unicode_ci NOT NULL,
  `ci` varchar(20) COLLATE utf8mb4_unicode_ci NOT NULL,
  `email` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `cargo` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `unidad` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `password` varchar(255) COLLATE utf8mb4_unicode_ci NOT NULL,
  `rol` varchar(50) COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'CUSTODIO',
  `id_carrera` bigint unsigned DEFAULT NULL,
  `estado` enum('ACTIVO','INACTIVO') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'ACTIVO',
  `puede_verificar` tinyint(1) NOT NULL DEFAULT '0',
  `remember_token` varchar(100) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_usuario`),
  UNIQUE KEY `usuarios_ci_unique` (`ci`),
  UNIQUE KEY `usuarios_email_unique` (`email`),
  KEY `usuarios_id_carrera_foreign` (`id_carrera`),
  CONSTRAINT `usuarios_id_carrera_foreign` FOREIGN KEY (`id_carrera`) REFERENCES `carreras` (`id_carrera`) ON DELETE SET NULL
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `usuarios`
--

LOCK TABLES `usuarios` WRITE;
/*!40000 ALTER TABLE `usuarios` DISABLE KEYS */;
INSERT INTO `usuarios` VALUES (1,'Ana Lía Zapana Cortez','4756925','admin@itpm.edu.bo','Administradora','Dirección Administrativa','$2y$12$rn5..p5X3LuV8bYBPOd3HeypUga3CF5VDosW/pzfIqwaVJ99/0Cze','ADMINISTRADOR',NULL,'ACTIVO',1,NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(2,'Juan Carlos Osco Mamani','4243543','inventariador@itpm.edu.bo','Inventariador','Departamento de Activos','$2y$12$hTp9m41o0xbtG39UWeesYOhQ3826DcSZeLNLZSy7ooVjbBYQzAAxi','INVENTARIADOR',NULL,'ACTIVO',1,'k2kcRPdkGmvPpVMk1yGr4DjG6zETgIRRhGcFUkAoia1sv66ZwbOkygcLEIvW','2026-10-03 19:50:21','2026-10-03 19:50:21'),(3,'Patricia Regina Flores Chuquimia','3451328','patricia.mamani@itpm.edu.bo','Docente Custodio','Laboratorio 3','$2y$12$KG/YPVQffPOLVe//GTj8DepWjvDw5GmwA0a08wvqWA1weoWQN/42y','DOCENTE_CUSTODIO',1,'ACTIVO',0,NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21'),(4,'Jhony Chavez Quispe','6032467','jhony.chavez@itpm.edu.bo','Docente Custodio','Laboratorio 3','$2y$12$dfD1fd79ERwqyovzuVFU.ur.dV6Ih.Sp0saPq2/pa5e8HdxFRvxcu','DOCENTE_CUSTODIO',1,'ACTIVO',0,'YAvRqyOrq0QJt8D9xAYzVYnomF7sag6XwmxFWbJieMg2t2bNKjlDIaZM7fDo','2026-10-03 19:50:21','2026-10-03 19:50:21'),(5,'Eliza Nina Coronel','4286280','luis.morales@itpm.edu.bo','Jefe de Carrera de Sistemas Informáticos','Escuela de Sistemas Informáticos','$2y$12$R577ZGf7HvWcWMWGcjQQheABE8/ZLuC/XmiyHWJCniZFFOrco8pHO','JEFE_CARRERA',1,'ACTIVO',0,'ILYA6k9haNHQrpqrHKANMUH4ktL5XvYEsSmhR29PlI3hHDAfwxWD3KB6yomE','2026-10-03 19:50:21','2026-10-03 19:50:21'),(6,'Jimmy Ovidio Sirpa Choque','4960313','rector@itpm.edu.bo','Rector','Rectorado','$2y$12$.3IjX8jxp9qsfA3Bcr0k8Or8QEq4x.AUQzKDl7waCHZ8F4wiTIjVy','RECTOR',NULL,'ACTIVO',0,NULL,'2026-10-03 19:50:21','2026-10-03 19:50:21');
/*!40000 ALTER TABLE `usuarios` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Table structure for table `verificaciones_fisicas`
--

DROP TABLE IF EXISTS `verificaciones_fisicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!50503 SET character_set_client = utf8mb4 */;
CREATE TABLE `verificaciones_fisicas` (
  `id_verificacion` bigint unsigned NOT NULL AUTO_INCREMENT,
  `id_activo` bigint unsigned NOT NULL,
  `id_ambiente_escaneo` bigint unsigned NOT NULL,
  `id_verificador` bigint unsigned NOT NULL,
  `fecha_verificacion` datetime NOT NULL,
  `es_correspondencia_correcta` tinyint(1) NOT NULL DEFAULT '1',
  `sincronizado_offline` tinyint(1) NOT NULL DEFAULT '0',
  `dispositivo_uuid` varchar(255) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `datos_extra` json DEFAULT NULL,
  `estado_fisico_reportado` enum('B','R','M','FF') COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `observaciones` text COLLATE utf8mb4_unicode_ci,
  `estado_aprobacion` enum('PENDIENTE','APROBADA','RECHAZADA','CORREGIDA') COLLATE utf8mb4_unicode_ci NOT NULL DEFAULT 'PENDIENTE',
  `id_aprobador` bigint unsigned DEFAULT NULL,
  `fecha_aprobacion` timestamp NULL DEFAULT NULL,
  `comentario_aprobacion` text COLLATE utf8mb4_unicode_ci,
  `cambio_estado_fisico_solicitado` tinyint(1) NOT NULL DEFAULT '0',
  `estado_fisico_solicitado` varchar(2) COLLATE utf8mb4_unicode_ci DEFAULT NULL,
  `solicitar_baja` tinyint(1) NOT NULL DEFAULT '0',
  `verificacion_finalizada` tinyint(1) NOT NULL DEFAULT '0',
  `created_at` timestamp NULL DEFAULT NULL,
  `updated_at` timestamp NULL DEFAULT NULL,
  PRIMARY KEY (`id_verificacion`),
  KEY `verificaciones_fisicas_id_activo_foreign` (`id_activo`),
  KEY `verificaciones_fisicas_id_ambiente_escaneo_foreign` (`id_ambiente_escaneo`),
  KEY `verificaciones_fisicas_id_verificador_foreign` (`id_verificador`),
  KEY `verificaciones_fisicas_id_aprobador_foreign` (`id_aprobador`),
  CONSTRAINT `verificaciones_fisicas_id_activo_foreign` FOREIGN KEY (`id_activo`) REFERENCES `activos_fijos` (`id_activo`),
  CONSTRAINT `verificaciones_fisicas_id_ambiente_escaneo_foreign` FOREIGN KEY (`id_ambiente_escaneo`) REFERENCES `ambientes` (`id_ambiente`),
  CONSTRAINT `verificaciones_fisicas_id_aprobador_foreign` FOREIGN KEY (`id_aprobador`) REFERENCES `usuarios` (`id_usuario`) ON DELETE SET NULL,
  CONSTRAINT `verificaciones_fisicas_id_verificador_foreign` FOREIGN KEY (`id_verificador`) REFERENCES `usuarios` (`id_usuario`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4 COLLATE=utf8mb4_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

--
-- Dumping data for table `verificaciones_fisicas`
--

LOCK TABLES `verificaciones_fisicas` WRITE;
/*!40000 ALTER TABLE `verificaciones_fisicas` DISABLE KEYS */;
/*!40000 ALTER TABLE `verificaciones_fisicas` ENABLE KEYS */;
UNLOCK TABLES;

--
-- Dumping routines for database 'sisitpm'
--
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

-- Dump completed
