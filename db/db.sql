
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
DROP TABLE IF EXISTS `Actividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Actividades` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `EmpleadosMin` int(11) NOT NULL,
  `EmpleadosMax` int(11) NOT NULL,
  `Observacion` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ActividadesConfiguraciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ActividadesConfiguraciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `TipoDeLineaDeProduccion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ActividadesConfiguraciones_TiposDeLIneasDeProducciones` (`TipoDeLineaDeProduccion`),
  CONSTRAINT `FK_ActividadesConfiguraciones_TiposDeLIneasDeProducciones` FOREIGN KEY (`TipoDeLineaDeProduccion`) REFERENCES `TiposDeLineasDeProducciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci CHECKSUM=1 DELAY_KEY_WRITE=1 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AdaptadoresFiscalizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AdaptadoresFiscalizaciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Class` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipConceptosIncluidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipConceptosIncluidos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipCondicionIva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipCondicionIva` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipCuitPaises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipCuitPaises` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Cuit` varchar(12) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Descripcion` varchar(60) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `TipoSujeto` int(10) unsigned NOT NULL,
  `TipoDeSujeto` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TipoDeSujeto` (`TipoDeSujeto`),
  CONSTRAINT `ACP_fk001` FOREIGN KEY (`TipoDeSujeto`) REFERENCES `AfipTiposDeSujetos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=777 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipGananciasDeducciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipGananciasDeducciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(500) COLLATE utf8_unicode_ci NOT NULL,
  `Codigo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  `Normativa` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TipoDeduccion` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Codigo` (`Codigo`),
  KEY `TipoDeduccion` (`TipoDeduccion`),
  CONSTRAINT `AFD_fk001` FOREIGN KEY (`TipoDeduccion`) REFERENCES `AfipGananciasDeduccionesTipos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipGananciasDeduccionesDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipGananciasDeduccionesDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Deduccion` int(11) unsigned NOT NULL,
  `Periodo` int(11) unsigned NOT NULL,
  `Monto` decimal(12,2) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Deduccion` (`Deduccion`),
  KEY `Periodo` (`Periodo`),
  CONSTRAINT `AfipGDD_fk001` FOREIGN KEY (`Deduccion`) REFERENCES `AfipGananciasDeducciones` (`Id`),
  CONSTRAINT `AGDP_fk002` FOREIGN KEY (`Periodo`) REFERENCES `AfipGananciasDeduccionesPeriodos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipGananciasDeduccionesPeriodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipGananciasDeduccionesPeriodos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `FechaDesde` (`FechaDesde`),
  UNIQUE KEY `FechaHasta` (`FechaHasta`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipGananciasDeduccionesTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipGananciasDeduccionesTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipGananciasEscalas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipGananciasEscalas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Desde` int(11) DEFAULT NULL,
  `Hasta` int(11) DEFAULT NULL,
  `CanonFijo` int(11) DEFAULT NULL,
  `Alicuota` int(11) NOT NULL,
  `AfipEscalaPeriodo` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `AfipEscalaPeriodo` (`AfipEscalaPeriodo`),
  CONSTRAINT `AGE_fk001` FOREIGN KEY (`AfipEscalaPeriodo`) REFERENCES `AfipGananciasEscalasPeriodos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipGananciasEscalasPeriodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipGananciasEscalasPeriodos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `FechaDesde` (`FechaDesde`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipIdiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipIdiomas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipIncoterms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipIncoterms` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipMonedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipMonedas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipOtrosTributos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipOtrosTributos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipPaises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipPaises` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `NombrePais` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=310 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipProvincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipProvincias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipSituacionesDeRevistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipSituacionesDeRevistas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Activo` tinyint(4) NOT NULL DEFAULT '1',
  `Codigo` varchar(3) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=22 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipTiposDeComprobantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipTiposDeComprobantes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=81 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipTiposDeDocumentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipTiposDeDocumentos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=39 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipTiposDeResponsables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipTiposDeResponsables` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipTiposDeSujetos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipTiposDeSujetos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AfipUnidadesDeMedidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AfipUnidadesDeMedidas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(3) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=55 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Almacenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Almacenes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  `Deposito` int(11) unsigned NOT NULL,
  `TipoDeAlmacen` int(11) unsigned DEFAULT NULL,
  `TieneRack` tinyint(4) NOT NULL DEFAULT '0',
  `RackCantFila` int(11) unsigned DEFAULT NULL,
  `DescFila` varchar(2) DEFAULT NULL,
  `IncrementoFila` int(11) unsigned DEFAULT NULL,
  `CompletaCerosFila` tinyint(4) DEFAULT NULL,
  `RackCantAltura` int(11) unsigned DEFAULT NULL,
  `DescAltura` varchar(2) DEFAULT NULL,
  `IncrementoAltura` int(11) unsigned DEFAULT NULL,
  `CompletaCerosAltura` tinyint(4) DEFAULT NULL,
  `RackCantProfundidad` int(11) unsigned DEFAULT NULL,
  `DescProfundidad` varchar(2) DEFAULT NULL,
  `IncrementoProfundidad` int(11) unsigned DEFAULT NULL,
  `CompletaCerosProfundidad` tinyint(4) DEFAULT NULL,
  `Separador` varchar(1) DEFAULT NULL,
  `OcultarDescSiUno` tinyint(4) DEFAULT NULL,
  `Perspectiva` int(11) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Almacenes_FKIndex1` (`Deposito`),
  KEY `FK_Almacenes_TiposDeAlmacenes` (`TipoDeAlmacen`),
  KEY `IncrementoAltura` (`IncrementoAltura`),
  KEY `IncrementoFila` (`IncrementoFila`),
  KEY `IncrementoProfundidad` (`IncrementoProfundidad`),
  KEY `Perspectiva` (`Perspectiva`),
  CONSTRAINT `Almacenes_fk` FOREIGN KEY (`TipoDeAlmacen`) REFERENCES `TiposDeAlmacenes` (`Id`),
  CONSTRAINT `Almacenes_fk1` FOREIGN KEY (`Deposito`) REFERENCES `Direcciones` (`Id`),
  CONSTRAINT `Almacenes_fk2` FOREIGN KEY (`IncrementoFila`) REFERENCES `TiposDeIncrementos` (`Id`),
  CONSTRAINT `Almacenes_fk3` FOREIGN KEY (`IncrementoProfundidad`) REFERENCES `TiposDeIncrementos` (`Id`),
  CONSTRAINT `Almacenes_fk4` FOREIGN KEY (`IncrementoAltura`) REFERENCES `TiposDeIncrementos` (`Id`),
  CONSTRAINT `FK_Almacenes_AlmacenesPerspectivas` FOREIGN KEY (`Perspectiva`) REFERENCES `AlmacenesPerspectivas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=20 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AlmacenesPerspectivas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AlmacenesPerspectivas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ambitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ambitos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Afip` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Analisis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Analisis` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TipoAnalisis` int(11) unsigned NOT NULL,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  `TipoDeCampo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `TipoAnalisis` (`TipoAnalisis`),
  KEY `FK_Analisis_2` (`TipoDeCampo`),
  CONSTRAINT `Analisis_fk` FOREIGN KEY (`TipoAnalisis`) REFERENCES `TiposDeAnalisis` (`Id`),
  CONSTRAINT `FK_Analisis_2` FOREIGN KEY (`TipoDeCampo`) REFERENCES `TiposDeCampos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`TipoAnalisis`) REFER `vidalacFinal/';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AnalisisModelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AnalisisModelos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `AnalisisTipoModelo` int(11) unsigned NOT NULL,
  `Analisis` int(11) unsigned NOT NULL,
  `ValorMinimo` decimal(11,5) DEFAULT NULL,
  `ValorMaximo` decimal(11,5) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Articulo` (`AnalisisTipoModelo`),
  KEY `Analisis` (`Analisis`),
  CONSTRAINT `AnalisisModelos_fk` FOREIGN KEY (`AnalisisTipoModelo`) REFERENCES `AnalisisTiposModelos` (`Id`),
  CONSTRAINT `AnalisisModelos_fk1` FOREIGN KEY (`Analisis`) REFERENCES `Analisis` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`AnalisisTipoModelo`) REFER `vidalac';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AnalisisMuestras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AnalisisMuestras` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Lote` int(11) unsigned NOT NULL,
  `Identificacion` varchar(50) DEFAULT '',
  `FechaMuestreo` datetime NOT NULL,
  `FechaAnalisis` datetime DEFAULT NULL,
  `Observaciones` text,
  `Controlada` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Lote` (`Lote`),
  CONSTRAINT `AnalisisMuestras_fk` FOREIGN KEY (`Lote`) REFERENCES `Lotes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`Lote`) REFER `vidalacFinal/Lotes`(`';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AnalisisProtocolo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AnalisisProtocolo` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Muestra` int(11) unsigned NOT NULL,
  `Analisis` int(11) unsigned NOT NULL,
  `Valor` varchar(50) DEFAULT NULL,
  `Observaciones` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Analisis` (`Analisis`),
  KEY `Muestra` (`Muestra`),
  CONSTRAINT `AnalisisProtocolo_fk` FOREIGN KEY (`Analisis`) REFERENCES `Analisis` (`Id`),
  CONSTRAINT `AnalisisProtocolo_fk1` FOREIGN KEY (`Muestra`) REFERENCES `AnalisisMuestras` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB; (`Analisis`) REFER `vidalacFinal/Anal';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AnalisisTiposModelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AnalisisTiposModelos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(250) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AnalisisValoresListas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AnalisisValoresListas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Analisis` int(10) unsigned NOT NULL,
  `Valor` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_AnalisisValoresListas_1` (`Analisis`),
  CONSTRAINT `FK_AnalisisValoresListas_1` FOREIGN KEY (`Analisis`) REFERENCES `Analisis` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AreasDeTrabajos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AreasDeTrabajos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `AreasDeTrabajosPersonas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `AreasDeTrabajosPersonas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(10) unsigned NOT NULL,
  `AreaDeTrabajo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_AreasDeTrabajosPersonas_Personas` (`Persona`),
  KEY `FK_AreasDeTrabajosPersonas_AreasDeTrabajos` (`AreaDeTrabajo`),
  CONSTRAINT `FK_AreasDeTrabajosPersonas_AreasDeTrabajos` FOREIGN KEY (`AreaDeTrabajo`) REFERENCES `AreasDeTrabajos` (`Id`),
  CONSTRAINT `FK_AreasDeTrabajosPersonas_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Articulos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Tipo` int(10) unsigned NOT NULL,
  `Codigo` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `CodigoDeBarras` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `UnidadDeMedida` int(10) unsigned DEFAULT NULL,
  `UnidadDeMedidaDeProduccion` int(10) unsigned DEFAULT NULL,
  `FactorDeConversion` decimal(12,8) DEFAULT NULL,
  `TipoDeControlDeStock` int(10) unsigned DEFAULT NULL,
  `EsInsumo` tinyint(4) NOT NULL DEFAULT '0',
  `EsProducido` tinyint(4) NOT NULL DEFAULT '0',
  `EsParaFason` tinyint(4) NOT NULL DEFAULT '0',
  `EsParaVenta` tinyint(4) NOT NULL DEFAULT '0',
  `EsParaCompra` tinyint(4) NOT NULL DEFAULT '0',
  `EsFinal` tinyint(4) NOT NULL DEFAULT '0',
  `ArticuloGrupo` int(10) unsigned NOT NULL,
  `ArticuloSubGrupo` int(10) unsigned DEFAULT NULL,
  `RequiereLote` tinyint(4) NOT NULL DEFAULT '0',
  `IVA` int(10) unsigned DEFAULT NULL,
  `Marca` int(10) unsigned DEFAULT NULL,
  `EsMateriaPrima` tinyint(4) NOT NULL DEFAULT '0',
  `RequiereProtocolo` tinyint(4) DEFAULT NULL,
  `Cuenta` int(10) unsigned DEFAULT NULL,
  `Leyenda` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `RNPA` varchar(10) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `DescripcionLarga` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `PesoNeto` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `PesoBruto` decimal(11,2) unsigned NOT NULL DEFAULT '0.00',
  `EnDesuso` tinyint(4) NOT NULL DEFAULT '0',
  `Old_p_Id` int(11) unsigned DEFAULT NULL,
  `Old_a_Id` int(11) unsigned DEFAULT NULL,
  `Detalle` text,
  PRIMARY KEY (`Id`),
  KEY `FK_Articulos_ArticulosGrupos` (`ArticuloGrupo`),
  KEY `FK_Articulos_ArticulosSubGrupos` (`ArticuloSubGrupo`),
  KEY `FK_Articulos_TiposDeArticulos` (`Tipo`),
  KEY `FK_Articulos_UnidadesDeMedidasDeProduccion` (`UnidadDeMedidaDeProduccion`),
  KEY `FK_Articulos_ConceptosImpositivos` (`IVA`),
  KEY `FK_Articulos_Marcas` (`Marca`),
  KEY `FK_Articulos_TiposDeControlDeStock` (`TipoDeControlDeStock`),
  KEY `fk_Articulos_PlanesDeCuentas` (`Cuenta`),
  KEY `FK_Articulos_UnidadesDeMedidas` (`UnidadDeMedida`),
  CONSTRAINT `FK_Articulos_ArticulosGrupos` FOREIGN KEY (`ArticuloGrupo`) REFERENCES `ArticulosGrupos` (`Id`),
  CONSTRAINT `FK_Articulos_ArticulosSubGrupos` FOREIGN KEY (`ArticuloSubGrupo`) REFERENCES `ArticulosSubGrupos` (`Id`),
  CONSTRAINT `FK_Articulos_ConceptosImpositivos` FOREIGN KEY (`IVA`) REFERENCES `ConceptosImpositivos` (`Id`),
  CONSTRAINT `FK_Articulos_Marcas` FOREIGN KEY (`Marca`) REFERENCES `Marcas` (`Id`),
  CONSTRAINT `fk_Articulos_PlanesDeCuentas` FOREIGN KEY (`Cuenta`) REFERENCES `PlanesDeCuentas` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_Articulos_TiposDeArticulos` FOREIGN KEY (`Tipo`) REFERENCES `TiposDeArticulos` (`Id`),
  CONSTRAINT `FK_Articulos_TiposDeControlDeStock` FOREIGN KEY (`TipoDeControlDeStock`) REFERENCES `TiposDeControlesDeStock` (`Id`),
  CONSTRAINT `FK_Articulos_UnidadesDeMedidas` FOREIGN KEY (`UnidadDeMedida`) REFERENCES `UnidadesDeMedidas` (`Id`),
  CONSTRAINT `FK_Articulos_UnidadesDeMedidasDeProduccion` FOREIGN KEY (`UnidadDeMedidaDeProduccion`) REFERENCES `UnidadesDeMedidas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1310 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosGrupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosGrupos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) NOT NULL,
  `TipoDeArticulo` int(11) unsigned DEFAULT NULL,
  `Old_Id` int(11) unsigned DEFAULT NULL,
  `DescripcionR` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ArticulosGrupos_TiposDeArticulos` (`TipoDeArticulo`),
  CONSTRAINT `FK_ArticulosGrupos_TiposDeArticulos` FOREIGN KEY (`TipoDeArticulo`) REFERENCES `TiposDeArticulos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosListasDePrecios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosListasDePrecios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(400) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `FechaVigencia` date DEFAULT NULL,
  `Observacion` text CHARACTER SET latin1,
  `Activa` tinyint(3) unsigned NOT NULL DEFAULT '1',
  `ListaDefault` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosListasDePreciosDetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosListasDePreciosDetalle` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ListaDePrecio` int(11) unsigned NOT NULL,
  `Articulo` int(10) unsigned NOT NULL,
  `FechaInforme` date DEFAULT NULL,
  `Precio` decimal(12,4) DEFAULT NULL,
  `FechaValidez` date DEFAULT NULL,
  `Divisa` int(10) unsigned NOT NULL DEFAULT '1',
  `Observacion` text,
  PRIMARY KEY (`Id`),
  KEY `Divisa` (`Divisa`),
  KEY `ListaDePrecio` (`ListaDePrecio`),
  KEY `Articulo` (`Articulo`),
  CONSTRAINT `FK_ArticulosListasDePreciosDetalle_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`),
  CONSTRAINT `ProductosListasDePreciosDetalle_fk` FOREIGN KEY (`ListaDePrecio`) REFERENCES `ArticulosListasDePrecios` (`Id`),
  CONSTRAINT `ProductosListasDePreciosDetalle_fk1` FOREIGN KEY (`Divisa`) REFERENCES `TiposDeDivisas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=246 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`Articulo`) REFER `vidalac/articulos`';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosStock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosStock` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Articulo` int(11) unsigned NOT NULL,
  `Stock` decimal(20,4) NOT NULL,
  `Fecha` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Articulo_UNIQUE` (`Articulo`),
  CONSTRAINT `FK_ArticulosStock_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosSubGrupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosSubGrupos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) NOT NULL,
  `ArticuloGrupo` int(11) unsigned DEFAULT NULL,
  `Old_PC_Id` int(11) unsigned DEFAULT NULL,
  `Old_PSC_Id` int(11) unsigned DEFAULT NULL,
  `DescripcionR` varchar(3) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ArticuloGrupo` (`ArticuloGrupo`),
  CONSTRAINT `ArticulosSubGrupos_fk` FOREIGN KEY (`ArticuloGrupo`) REFERENCES `ArticulosGrupos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=27 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosVersiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosVersiones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(11) unsigned NOT NULL,
  `Version` int(11) unsigned NOT NULL,
  `Fecha` date NOT NULL,
  `Descripcion` varchar(255) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TieneFormula` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ArticulosVersiones_Articulos` (`Articulo`),
  CONSTRAINT `FK_ArticulosVersiones_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=722 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosVersionesDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosVersionesDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ArticuloVersionPadre` int(11) unsigned NOT NULL,
  `ArticuloVersionHijo` int(11) unsigned NOT NULL,
  `Proceso` int(11) unsigned DEFAULT NULL,
  `Cantidad` decimal(12,8) unsigned NOT NULL,
  `UnidadDeMedida` int(11) unsigned NOT NULL,
  `TipoDeRelacionArticulo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ArticulosVersionesDetalles_ArticulosVersiones` (`ArticuloVersionPadre`),
  KEY `FK_ArticulosVersionesDetalles_ArticulosVersiones1` (`ArticuloVersionHijo`),
  KEY `FK_ArticulosVersionesDetalles_UnidadesDeMedidas` (`UnidadDeMedida`),
  KEY `FK_ArticulosVersionesDetalles_TiposDeRelacionesArticulos` (`TipoDeRelacionArticulo`),
  CONSTRAINT `FK_ArticulosVersionesDetalles_ArticulosVersiones` FOREIGN KEY (`ArticuloVersionPadre`) REFERENCES `ArticulosVersiones` (`Id`),
  CONSTRAINT `FK_ArticulosVersionesDetalles_ArticulosVersiones1` FOREIGN KEY (`ArticuloVersionHijo`) REFERENCES `ArticulosVersiones` (`Id`),
  CONSTRAINT `FK_ArticulosVersionesDetalles_TiposDeRelacionesArticulos` FOREIGN KEY (`TipoDeRelacionArticulo`) REFERENCES `TiposDeRelacionesArticulos` (`Id`),
  CONSTRAINT `FK_ArticulosVersionesDetalles_UnidadesDeMedidas` FOREIGN KEY (`UnidadDeMedida`) REFERENCES `UnidadesDeMedidas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1642 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ArticulosVersionesRaices`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ArticulosVersionesRaices` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ArticuloVersionRaiz` int(10) unsigned NOT NULL,
  `ArticuloVersionDetalle` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ArticulosVersionesRaices_ArticulosVersiones` (`ArticuloVersionRaiz`),
  KEY `FK_ArticulosVersionesRaices_ArticulosVersionesDetalles` (`ArticuloVersionDetalle`),
  CONSTRAINT `FK_ArticulosVersionesRaices_ArticulosVersiones` FOREIGN KEY (`ArticuloVersionRaiz`) REFERENCES `ArticulosVersiones` (`Id`),
  CONSTRAINT `FK_ArticulosVersionesRaices_ArticulosVersionesDetalles` FOREIGN KEY (`ArticuloVersionDetalle`) REFERENCES `ArticulosVersionesDetalles` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2047 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Bancos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Bancos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  `Prioridad` int(11) unsigned DEFAULT NULL COMMENT 'El orden de prioridad en la utilizacion del banco.',
  `Utilizado` tinyint(4) unsigned DEFAULT NULL,
  `Persona` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `persona` (`Persona`),
  CONSTRAINT `Bancos_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=50 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `BancosMovimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BancosMovimientos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Banco` int(11) unsigned NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Referencia` int(11) unsigned DEFAULT NULL,
  `Concepto` varchar(300) DEFAULT NULL,
  `Debito` decimal(12,2) DEFAULT NULL,
  `Credito` decimal(12,2) DEFAULT NULL,
  `Observaciones` varchar(500) DEFAULT NULL,
  `Saldo` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Banco` (`Banco`,`Referencia`),
  CONSTRAINT `BancosMovimientos_fk` FOREIGN KEY (`Banco`) REFERENCES `Bancos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `BancosSucursales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `BancosSucursales` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `NumeroSucursal` int(10) unsigned DEFAULT NULL,
  `Descripcion` varchar(100) DEFAULT NULL,
  `Banco` int(10) unsigned NOT NULL,
  `Sucursal` varchar(100) NOT NULL,
  `Domicilio` varchar(255) DEFAULT NULL,
  `Localidad` int(10) unsigned NOT NULL,
  `Gerente` varchar(200) DEFAULT NULL,
  `ResponsableCuenta` varchar(200) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Banco_Sucursal_UNIQUE` (`Banco`,`NumeroSucursal`),
  KEY `BancosSucursales_FKIndex2` (`Localidad`),
  CONSTRAINT `BancosSucursales_fk` FOREIGN KEY (`Banco`) REFERENCES `Bancos` (`Id`),
  CONSTRAINT `BancosSucursales_fk1` FOREIGN KEY (`Localidad`) REFERENCES `Localidades` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=49 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Cajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cajas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Cuenta` int(11) unsigned NOT NULL,
  `PermiteNegativo` tinyint(1) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Cuenta` (`Cuenta`),
  CONSTRAINT `Cajas_fk` FOREIGN KEY (`Cuenta`) REFERENCES `PlanesDeCuentas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CajasMovimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CajasMovimientos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Caja` int(10) unsigned NOT NULL,
  `Monto` decimal(12,2) NOT NULL,
  `ComprobanteDetalle` int(11) unsigned DEFAULT NULL,
  `Fecha` datetime NOT NULL,
  `TipoDeMovimiento` int(11) unsigned NOT NULL DEFAULT '3',
  `Observaciones` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TransaccionBancaria` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CajasMovimientos_Cajas` (`Caja`),
  KEY `FK_CajasMovimientos_ComprobantesDetalles` (`ComprobanteDetalle`),
  KEY `TipoDeMovimiento` (`TipoDeMovimiento`),
  KEY `FK_CajasMovimientos_TransaccionesBancarias` (`TransaccionBancaria`),
  CONSTRAINT `FK_CajasMovimientos_Cajas` FOREIGN KEY (`Caja`) REFERENCES `Cajas` (`Id`),
  CONSTRAINT `FK_CajasMovimientos_ComprobantesDetalles` FOREIGN KEY (`ComprobanteDetalle`) REFERENCES `ComprobantesDetalles` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CajasMovimientos_TransaccionesBancarias` FOREIGN KEY (`TransaccionBancaria`) REFERENCES `TransaccionesBancarias` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=716 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Caracteristicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Caracteristicas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  `Nombre` varchar(50) NOT NULL,
  `TipoDeCampo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Nombre1` (`Nombre`),
  KEY `fk_Caracteristicas_TiposDeCampos` (`TipoDeCampo`),
  CONSTRAINT `FK_Caracteristicas_TiposDeCampos` FOREIGN KEY (`TipoDeCampo`) REFERENCES `TiposDeCampos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CaracteristicasListas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CaracteristicasListas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Caracteristica` int(10) unsigned NOT NULL,
  `Valor` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CaracteristicasListas_1` (`Caracteristica`),
  CONSTRAINT `FK_CaracteristicasListas_1` FOREIGN KEY (`Caracteristica`) REFERENCES `Caracteristicas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CaracteristicasModelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CaracteristicasModelos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Caracteristica` int(11) unsigned NOT NULL,
  `Modelo` int(11) unsigned NOT NULL,
  `GeneraNovedadLiquidacion` tinyint(1) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `FK_CaracteristicasTablas_Caracteristicas` (`Caracteristica`),
  KEY `FK_CaracteristicasTablas_Tablas` (`Modelo`),
  CONSTRAINT `FK_CaracteristicasModelos_Caracteristicas` FOREIGN KEY (`Caracteristica`) REFERENCES `Caracteristicas` (`Id`),
  CONSTRAINT `FK_CaracteristicasModelos_Modelos` FOREIGN KEY (`Modelo`) REFERENCES `Modelos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CaracteristicasValores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CaracteristicasValores` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `CaracteristicaModelo` int(11) unsigned NOT NULL,
  `Valor` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `IdModelo` int(11) NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  `FechaCarga` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Caracteristica` (`CaracteristicaModelo`),
  KEY `FK_CaracteristicasValores_Titulos` (`IdModelo`),
  CONSTRAINT `FK_CaracteristicasValores_CaracteristicasModelos` FOREIGN KEY (`CaracteristicaModelo`) REFERENCES `CaracteristicasModelos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CategoriasGrupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CategoriasGrupos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  `Convenio` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Convenio` (`Convenio`),
  CONSTRAINT `CategoriasGrupos_fk` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Chequeras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Chequeras` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ChequeraTipo` int(11) unsigned NOT NULL,
  `CuentaBancaria` int(11) unsigned NOT NULL,
  `Serie` varchar(2) NOT NULL,
  `NumeroInicio` int(10) NOT NULL,
  `Cantidad` int(10) unsigned NOT NULL,
  `NumeroDeChequera` int(10) unsigned NOT NULL,
  `FechaDeEntrega` date NOT NULL,
  `Disponibles` int(10) unsigned DEFAULT NULL,
  `Anulados` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ChequeraTipo` (`ChequeraTipo`),
  KEY `CuentaBancaria` (`CuentaBancaria`),
  CONSTRAINT `Chequeras_fk1` FOREIGN KEY (`ChequeraTipo`) REFERENCES `ChequerasTipos` (`Id`),
  CONSTRAINT `Chequeras_fk2` FOREIGN KEY (`CuentaBancaria`) REFERENCES `CuentasBancarias` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ChequerasTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChequerasTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Cheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Cheques` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ChequeEstado` int(10) unsigned NOT NULL,
  `BancoSucursal` int(10) unsigned NOT NULL,
  `TipoDeEmisorDeCheque` int(10) unsigned NOT NULL,
  `TipoDeCheque` int(10) unsigned DEFAULT NULL,
  `Persona` int(10) unsigned DEFAULT NULL,
  `TerceroEmisor` varchar(100) DEFAULT NULL,
  `PagueseA` varchar(400) DEFAULT NULL,
  `Serie` varchar(2) DEFAULT NULL,
  `Numero` int(10) unsigned DEFAULT NULL,
  `FechaDeEmision` date DEFAULT NULL,
  `FechaDeRecepcion` date DEFAULT NULL,
  `FechaDeVencimiento` date DEFAULT NULL,
  `FechaDeCobro` date DEFAULT NULL,
  `Monto` decimal(12,2) DEFAULT NULL,
  `MontoEnLetras` varchar(500) DEFAULT NULL,
  `FechaDeMovimiento` date DEFAULT NULL,
  `NoALaOrden` tinyint(4) NOT NULL DEFAULT '0',
  `ChequeManual` tinyint(1) DEFAULT '0',
  `Chequera` int(11) unsigned DEFAULT NULL,
  `Cruzado` tinyint(4) DEFAULT '0',
  `Impreso` tinyint(4) DEFAULT '0',
  `Generador` int(11) unsigned DEFAULT NULL,
  `CuentaDeMovimiento` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Cheques_FKIndex1` (`Persona`),
  KEY `Cheques_FKIndex2` (`TipoDeCheque`),
  KEY `Cheques_FKIndex3` (`TipoDeEmisorDeCheque`),
  KEY `Cheques_FKIndex4` (`BancoSucursal`),
  KEY `Cheques_FKIndex5` (`ChequeEstado`),
  KEY `Chequera` (`Chequera`),
  KEY `Cheques_fk2` (`Generador`),
  KEY `FK_Cheques_CuentasBancarias` (`CuentaDeMovimiento`),
  CONSTRAINT `Cheques_fk` FOREIGN KEY (`BancoSucursal`) REFERENCES `BancosSucursales` (`Id`),
  CONSTRAINT `Cheques_fk1` FOREIGN KEY (`Chequera`) REFERENCES `Chequeras` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `Cheques_fk2` FOREIGN KEY (`Generador`) REFERENCES `GeneradorDeCheques` (`Id`),
  CONSTRAINT `Cheques_fk3` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_Cheques_CuentasBancarias` FOREIGN KEY (`CuentaDeMovimiento`) REFERENCES `CuentasBancarias` (`Id`),
  CONSTRAINT `fk_{24B48923-E236-4DC8-8E67-3D19BC921E4A}` FOREIGN KEY (`TipoDeEmisorDeCheque`) REFERENCES `TiposDeEmisoresDeCheques` (`Id`),
  CONSTRAINT `fk_{69CF2E29-E7D6-4754-9533-28C3276B6B86}` FOREIGN KEY (`ChequeEstado`) REFERENCES `ChequesEstados` (`Id`),
  CONSTRAINT `fk_{A54ADD92-7747-4049-919C-C3DE01EAFE81}` FOREIGN KEY (`TipoDeCheque`) REFERENCES `TiposDeCheques` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7297 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ChequesBloqueos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChequesBloqueos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ChequeBloqueoTipo` int(10) unsigned NOT NULL,
  `FechaInicio` date DEFAULT NULL,
  `FechaFin` date DEFAULT NULL,
  `MontoMaximo` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ChequesBloqueos_FKIndex2` (`ChequeBloqueoTipo`),
  CONSTRAINT `fk_{07321882-DFF8-487A-BFA2-2F8140195B5D}` FOREIGN KEY (`ChequeBloqueoTipo`) REFERENCES `ChequesBloqueosTipos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`ChequeBloqueoTipo`) REFER `vidalac/c';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ChequesBloqueosTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChequesBloqueosTipos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ChequesEstados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChequesEstados` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Ingresado, Vendido, Cobrado, En cartera, Emitido para pago; ';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CodigosActividadesAfip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CodigosActividadesAfip` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Codigo` int(11) DEFAULT NULL,
  `Descripcion` varchar(500) DEFAULT NULL,
  `Observaciones` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Codigo` (`Codigo`),
  UNIQUE KEY `Codigo_2` (`Codigo`),
  UNIQUE KEY `Codigo_3` (`Codigo`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Comprobantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Comprobantes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `Punto` int(11) NOT NULL DEFAULT '1',
  `Numero` int(11) unsigned DEFAULT NULL,
  `Cerrado` tinyint(1) NOT NULL DEFAULT '0',
  `Monto` decimal(12,2) DEFAULT NULL,
  `TipoDeComprobante` int(11) unsigned NOT NULL,
  `FechaEmision` date NOT NULL,
  `FechaVencimiento` date DEFAULT NULL,
  `LibroIVA` int(11) unsigned DEFAULT NULL,
  `Divisa` int(11) unsigned NOT NULL DEFAULT '1',
  `ValorDivisa` decimal(12,6) unsigned NOT NULL DEFAULT '1.000000',
  `Observaciones` text,
  `DescuentoEnMonto` decimal(12,2) unsigned DEFAULT NULL,
  `DescuentoEnPorcentaje` decimal(11,0) unsigned DEFAULT NULL,
  `ComprobantePadre` int(11) unsigned DEFAULT NULL,
  `ComprobanteRelacionado` int(11) unsigned DEFAULT NULL,
  `DepositoEntrega` int(11) unsigned DEFAULT NULL,
  `FechaEntrega` datetime DEFAULT NULL,
  `Prioridad` int(11) unsigned DEFAULT NULL,
  `DepositoSalida` int(11) unsigned DEFAULT NULL,
  `ObservacionesImpresas` text,
  `CotCodigo` varchar(12) DEFAULT NULL,
  `CotFechaValidez` date DEFAULT NULL,
  `ValorDeclarado` decimal(12,2) unsigned DEFAULT NULL,
  `FleteFormaPago` int(11) unsigned DEFAULT NULL,
  `TransportistaRetiroDeOrigen` int(11) unsigned DEFAULT NULL,
  `TransportistaEntregoEnDestino` int(11) unsigned DEFAULT NULL,
  `ConceptoImpositivo` int(11) unsigned DEFAULT NULL,
  `ConceptoImpositivoPorcentaje` decimal(12,2) DEFAULT NULL,
  `MontoImponible` decimal(12,2) DEFAULT NULL,
  `Despachado` tinyint(1) NOT NULL DEFAULT '0',
  `Modificado` tinyint(4) unsigned DEFAULT '0',
  `Anulado` tinyint(4) NOT NULL DEFAULT '0',
  `ListaDePrecio` int(11) unsigned DEFAULT NULL,
  `CondicionDePago` int(11) unsigned DEFAULT NULL,
  `FechaCierre` datetime DEFAULT '0000-00-00 00:00:00',
  `CuentaBancaria` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Comprobantes_Personas` (`Persona`),
  KEY `FK_Comprobantes_TiposDeComprobantes` (`TipoDeComprobante`),
  KEY `FK_Comprobantes_LibrosIVA` (`LibroIVA`),
  KEY `FK_Comprobantes_Divisas` (`Divisa`),
  KEY `FK_Comprobantes_TransportistaRetiroDeOrigen` (`TransportistaRetiroDeOrigen`),
  KEY `FK_Comprobantes_TransportistasEntregoEnDestino` (`TransportistaEntregoEnDestino`),
  KEY `FK_Comprobantes_ConceptosImpositivos` (`ConceptoImpositivo`),
  KEY `Comprobantes_fk` (`DepositoEntrega`),
  KEY `Comprobantes_fk1` (`DepositoSalida`),
  KEY `ListaDePrecio` (`ListaDePrecio`),
  KEY `FK_Comprobantes_CondicionesDePagos` (`CondicionDePago`),
  KEY `ComprobantePadre` (`ComprobantePadre`),
  KEY `ComprobanteRelacionado` (`ComprobanteRelacionado`),
  KEY `FechaEmision` (`FechaEmision`),
  KEY `CuentaBancaria` (`CuentaBancaria`),
  KEY `FK_Comprobantes_FletesFormasPagos` (`FleteFormaPago`),
  CONSTRAINT `Comprobantes_fk` FOREIGN KEY (`DepositoEntrega`) REFERENCES `Direcciones` (`Id`),
  CONSTRAINT `Comprobantes_fk1` FOREIGN KEY (`DepositoSalida`) REFERENCES `Direcciones` (`Id`),
  CONSTRAINT `Comprobantes_fk2` FOREIGN KEY (`ListaDePrecio`) REFERENCES `ArticulosListasDePrecios` (`Id`),
  CONSTRAINT `Comprobantes_fk3` FOREIGN KEY (`ComprobanteRelacionado`) REFERENCES `Comprobantes` (`Id`),
  CONSTRAINT `Comprobantes_fk4` FOREIGN KEY (`CuentaBancaria`) REFERENCES `CuentasBancarias` (`Id`),
  CONSTRAINT `FK_Comprobantes_ConceptosImpositivos` FOREIGN KEY (`ConceptoImpositivo`) REFERENCES `ConceptosImpositivos` (`Id`),
  CONSTRAINT `FK_Comprobantes_CondicionesDePagos` FOREIGN KEY (`CondicionDePago`) REFERENCES `TiposDeCondicionesDePago` (`Id`),
  CONSTRAINT `FK_Comprobantes_Divisas` FOREIGN KEY (`Divisa`) REFERENCES `TiposDeDivisas` (`Id`),
  CONSTRAINT `FK_Comprobantes_FletesFormasPagos` FOREIGN KEY (`FleteFormaPago`) REFERENCES `FletesFormasPagos` (`Id`),
  CONSTRAINT `FK_Comprobantes_LibrosIVA` FOREIGN KEY (`LibroIVA`) REFERENCES `LibrosIVA` (`Id`),
  CONSTRAINT `FK_Comprobantes_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_Comprobantes_TiposDeComprobantes` FOREIGN KEY (`TipoDeComprobante`) REFERENCES `TiposDeComprobantes` (`Id`),
  CONSTRAINT `FK_Comprobantes_TransportistaRetiroDeOrigen` FOREIGN KEY (`TransportistaRetiroDeOrigen`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_Comprobantes_TransportistasEntregoEnDestino` FOREIGN KEY (`TransportistaEntregoEnDestino`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=23111 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ComprobantesCheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ComprobantesCheques` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Comprobante` int(10) unsigned NOT NULL,
  `Cheque` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ComprobantesCheques_Comprobantes` (`Comprobante`),
  KEY `FK_ComprobantesCheques_Cheques` (`Cheque`),
  CONSTRAINT `FK_ComprobantesCheques_Cheques` FOREIGN KEY (`Cheque`) REFERENCES `Cheques` (`Id`),
  CONSTRAINT `FK_ComprobantesCheques_Comprobantes` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ComprobantesDeExportaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ComprobantesDeExportaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Comprobante` int(11) unsigned NOT NULL,
  `PaisDestino` int(11) unsigned NOT NULL,
  `CuitPaisDestino` int(11) unsigned NOT NULL,
  `Incoterm` int(11) unsigned NOT NULL,
  `Idioma` int(11) unsigned NOT NULL,
  `ConceptoIncluido` int(11) unsigned NOT NULL,
  `FormaDePago` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `IncotermDescripcion` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Comprobante` (`Comprobante`),
  KEY `PaisDestino` (`PaisDestino`),
  KEY `CuitPaisDestino` (`CuitPaisDestino`),
  KEY `Incoterm` (`Incoterm`),
  KEY `Idioma` (`Idioma`),
  KEY `ConceptoIncluido` (`ConceptoIncluido`),
  CONSTRAINT `CDE_fk001` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `CDE_fk002` FOREIGN KEY (`PaisDestino`) REFERENCES `Paises` (`Id`),
  CONSTRAINT `CDE_fk003` FOREIGN KEY (`CuitPaisDestino`) REFERENCES `PaisesCuit` (`Id`),
  CONSTRAINT `CDE_fk005` FOREIGN KEY (`Incoterm`) REFERENCES `AfipIncoterms` (`Id`),
  CONSTRAINT `CDE_fk006` FOREIGN KEY (`Idioma`) REFERENCES `Idiomas` (`Id`),
  CONSTRAINT `CDE_fk007` FOREIGN KEY (`ConceptoIncluido`) REFERENCES `AfipConceptosIncluidos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ComprobantesDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ComprobantesDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Comprobante` int(11) unsigned NOT NULL,
  `ComprobanteRelacionado` int(11) unsigned DEFAULT NULL,
  `Articulo` int(11) unsigned DEFAULT NULL,
  `CuentaCasual` int(11) DEFAULT NULL,
  `Cantidad` decimal(10,2) DEFAULT NULL,
  `PrecioUnitario` decimal(12,4) DEFAULT '0.0000',
  `PrecioUnitarioMExtranjera` decimal(12,4) DEFAULT '0.0000',
  `Monto` decimal(12,4) DEFAULT NULL,
  `MontoMExtranjera` decimal(12,4) DEFAULT NULL,
  `DescuentoEnMonto` decimal(12,4) DEFAULT '0.0000',
  `DescuentoEnPorcentaje` decimal(7,4) DEFAULT '0.0000',
  `Modificado` tinyint(1) DEFAULT NULL,
  `Observaciones` varchar(200) DEFAULT NULL,
  `Caja` int(11) unsigned DEFAULT NULL,
  `ConceptoImpositivo` int(11) unsigned DEFAULT NULL,
  `Cheque` int(11) unsigned DEFAULT NULL,
  `TransaccionBancaria` int(11) unsigned DEFAULT NULL,
  `TraeProtocolo` tinyint(1) unsigned DEFAULT '0',
  `TarjetaDeCreditoCupon` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ComprobantesDetalles_fk` (`Comprobante`),
  KEY `ComprobanteRelacionado` (`ComprobanteRelacionado`),
  KEY `FK_ComprobantesDetalles_Articulos` (`Articulo`),
  KEY `FK_ComprobantesDetalle_ConceptosImpositivos` (`ConceptoImpositivo`),
  KEY `FK_ComprobantesDetalles_Cheques` (`Cheque`),
  KEY `FK_ComprobantesDetalles_TransaccionesBancarias` (`TransaccionBancaria`),
  KEY `TarjetaDeCreditoCupon` (`TarjetaDeCreditoCupon`),
  CONSTRAINT `ComprobantesDetalles_fk` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`),
  CONSTRAINT `ComprobantesDetalles_fk1` FOREIGN KEY (`ComprobanteRelacionado`) REFERENCES `Comprobantes` (`Id`),
  CONSTRAINT `ComprobantesDetalles_fk2` FOREIGN KEY (`TarjetaDeCreditoCupon`) REFERENCES `TarjetasDeCreditoCupones` (`Id`),
  CONSTRAINT `FK_ComprobantesDetalles_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`),
  CONSTRAINT `FK_ComprobantesDetalles_Cheques` FOREIGN KEY (`Cheque`) REFERENCES `Cheques` (`Id`),
  CONSTRAINT `FK_ComprobantesDetalles_TransaccionesBancarias` FOREIGN KEY (`TransaccionBancaria`) REFERENCES `TransaccionesBancarias` (`Id`),
  CONSTRAINT `FK_ComprobantesDetalle_ConceptosImpositivos` FOREIGN KEY (`ConceptoImpositivo`) REFERENCES `ConceptosImpositivos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=15596 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ComprobantesRelacionados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ComprobantesRelacionados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ComprobantePadre` int(11) unsigned NOT NULL,
  `ComprobanteHijo` int(11) unsigned NOT NULL,
  `MontoAsociado` decimal(12,4) DEFAULT '0.0000',
  PRIMARY KEY (`Id`),
  KEY `ComprobantePadre` (`ComprobantePadre`),
  KEY `ComprobanteHijo` (`ComprobanteHijo`),
  CONSTRAINT `ComprobantesRelacionados_fk` FOREIGN KEY (`ComprobantePadre`) REFERENCES `Comprobantes` (`Id`),
  CONSTRAINT `ComprobantesRelacionados_fk1` FOREIGN KEY (`ComprobanteHijo`) REFERENCES `Comprobantes` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=6475 DEFAULT CHARSET=utf8 AVG_ROW_LENGTH=50;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ComprobantesRelacionadosDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ComprobantesRelacionadosDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ComprobanteRelacionado` int(11) unsigned NOT NULL,
  `Articulo` int(11) unsigned NOT NULL,
  `Cantidad` decimal(12,2) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ComprobanteRelacionado` (`ComprobanteRelacionado`),
  CONSTRAINT `ComprobantesRelacionadosDetalles_fk` FOREIGN KEY (`ComprobanteRelacionado`) REFERENCES `ComprobantesRelacionados` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=2702 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ConceptosImpositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConceptosImpositivos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  `ParaVenta` tinyint(4) NOT NULL DEFAULT '0',
  `ParaCompra` tinyint(4) NOT NULL DEFAULT '0',
  `ParaCobro` tinyint(4) NOT NULL DEFAULT '0',
  `ParaPago` tinyint(4) NOT NULL DEFAULT '0',
  `ParaCalculoCosto` tinyint(4) NOT NULL DEFAULT '0',
  `PorcentajeActual` decimal(6,2) NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaUltimoCambio` date DEFAULT NULL,
  `FechaBaja` date DEFAULT NULL,
  `EsRetencion` tinyint(4) NOT NULL DEFAULT '0',
  `EsPercepcion` tinyint(4) NOT NULL DEFAULT '0',
  `EsIVA` tinyint(4) DEFAULT '0',
  `EsIVADefault` tinyint(4) DEFAULT '0',
  `SeAplicaEmpresa` tinyint(4) NOT NULL DEFAULT '0',
  `EnUso` tinyint(4) NOT NULL DEFAULT '1',
  `EnteRecaudador` int(11) unsigned NOT NULL,
  `CuentaActivo` int(11) unsigned NOT NULL,
  `CuentaPasivo` int(11) unsigned NOT NULL,
  `MontoMinimo` int(11) NOT NULL DEFAULT '0',
  `TipoDeMontoMinimo` int(11) unsigned DEFAULT NULL,
  `TipoDeConcepto` int(11) unsigned NOT NULL,
  `Afip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `EnteRecaudador` (`EnteRecaudador`),
  KEY `TipoDeMontoMinimo` (`TipoDeMontoMinimo`),
  KEY `TipoDeConcepto` (`TipoDeConcepto`),
  KEY `Cuenta` (`CuentaActivo`),
  KEY `CuentaPasivo` (`CuentaPasivo`),
  CONSTRAINT `ConceptosImpositivos_fk` FOREIGN KEY (`CuentaActivo`) REFERENCES `PlanesDeCuentas` (`Id`),
  CONSTRAINT `ConceptosImpositivos_fk1` FOREIGN KEY (`TipoDeMontoMinimo`) REFERENCES `TiposDeMontosMinimos` (`Id`),
  CONSTRAINT `ConceptosImpositivos_fk2` FOREIGN KEY (`CuentaPasivo`) REFERENCES `PlanesDeCuentas` (`Id`),
  CONSTRAINT `FK_ConceptosImpositivos` FOREIGN KEY (`EnteRecaudador`) REFERENCES `EntesRecaudadores` (`Id`),
  CONSTRAINT `FK_ConceptosImpositivos_TipoDeConcepto` FOREIGN KEY (`TipoDeConcepto`) REFERENCES `TiposDeConceptos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB; (`Cuenta`) REFER `vidalacFinal/Planes';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ConceptosTiposDeLiquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConceptosTiposDeLiquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Concepto` int(11) unsigned NOT NULL,
  `TipoDeLiquidacion` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Concepto` (`Concepto`),
  KEY `TipoDeLiquidacion` (`TipoDeLiquidacion`),
  CONSTRAINT `ConceptosTiposDeLiquidaciones_fk` FOREIGN KEY (`Concepto`) REFERENCES `VariablesDetalles` (`Id`),
  CONSTRAINT `ConceptosTiposDeLiquidaciones_fk1` FOREIGN KEY (`TipoDeLiquidacion`) REFERENCES `TiposDeLiquidaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Convenios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Convenios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ConveniosCategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConveniosCategorias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Codigo` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Convenio` int(11) unsigned NOT NULL,
  `CategoriaGrupo` int(11) unsigned DEFAULT NULL,
  `Detalle` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Codigo` (`Codigo`),
  UNIQUE KEY `Codigo_2` (`Codigo`),
  KEY `Convenio` (`Convenio`),
  KEY `CategoriaGrupo` (`CategoriaGrupo`),
  CONSTRAINT `ConveniosCategorias_fk` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`),
  CONSTRAINT `ConveniosCategorias_fk1` FOREIGN KEY (`CategoriaGrupo`) REFERENCES `CategoriasGrupos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=56 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ConveniosCategoriasDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConveniosCategoriasDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ConvenioCategoria` int(11) unsigned NOT NULL,
  `Valor` decimal(12,2) unsigned NOT NULL,
  `ValorNoRemunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ConvenioCategoria` (`ConvenioCategoria`),
  CONSTRAINT `FK_ConveniosCategoriasDetalles_ConveniosCategorias` FOREIGN KEY (`ConvenioCategoria`) REFERENCES `ConveniosCategorias` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=341 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ConveniosLicencias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ConveniosLicencias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Articulo` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Convenio` int(11) unsigned NOT NULL,
  `SituacionDeRevista` int(11) unsigned NOT NULL,
  `Detalle` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  KEY `Convenio` (`Convenio`),
  KEY `SituacionDeRevista` (`SituacionDeRevista`),
  CONSTRAINT `ConveniosLicencias_fk` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`),
  CONSTRAINT `ConveniosLicencias_fk1` FOREIGN KEY (`SituacionDeRevista`) REFERENCES `SituacionesDeRevistas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=53 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CronProgramaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CronProgramaciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `CronTarea` int(10) unsigned NOT NULL,
  `Hora` time NOT NULL,
  `Dia` tinyint(3) unsigned NOT NULL,
  `Tipo` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_CronProgramaciones_1` (`CronTarea`),
  KEY `fk_CronProgramaciones_2` (`Tipo`),
  CONSTRAINT `fk_CronProgramaciones_1` FOREIGN KEY (`CronTarea`) REFERENCES `CronTareas` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `fk_CronProgramaciones_2` FOREIGN KEY (`Tipo`) REFERENCES `CronTiposProgramaciones` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CronTareas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CronTareas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  `Script` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CronTiposProgramaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CronTiposProgramaciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CuentasBancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CuentasBancarias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `BancoSucursal` int(11) unsigned NOT NULL,
  `TipoDeCuenta` int(10) unsigned NOT NULL,
  `Numero` varchar(50) DEFAULT NULL,
  `Cbu` varchar(22) DEFAULT NULL,
  `Propia` tinyint(1) NOT NULL DEFAULT '0',
  `Persona` int(11) unsigned DEFAULT NULL,
  `Titular` varchar(150) DEFAULT NULL,
  `CuitTitular` varchar(13) DEFAULT NULL,
  `Cuenta` int(11) unsigned NOT NULL,
  `FechaAlta` date DEFAULT NULL,
  `FechaCierre` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `CuentasBancarias_FKIndex1` (`BancoSucursal`),
  KEY `FK_CuentasBancarias_TiposDeCuentas` (`TipoDeCuenta`),
  KEY `Persona` (`Persona`),
  KEY `Cuenta` (`Cuenta`),
  CONSTRAINT `CuentasBancarias_fk` FOREIGN KEY (`BancoSucursal`) REFERENCES `BancosSucursales` (`Id`),
  CONSTRAINT `CuentasBancarias_fk1` FOREIGN KEY (`Cuenta`) REFERENCES `PlanesDeCuentas` (`Id`),
  CONSTRAINT `CuentasBancarias_fk3` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `FK_CuentasBancarias_TiposDeCuentas` FOREIGN KEY (`TipoDeCuenta`) REFERENCES `TiposDeCuentas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CuentasBancariasMovimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CuentasBancariasMovimientos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL,
  `CuentaBancaria` int(11) unsigned NOT NULL,
  `Fecha` date NOT NULL,
  `Monto` decimal(12,2) NOT NULL,
  `TransaccionBancaria` int(11) unsigned DEFAULT NULL,
  `Observaciones` text,
  `ComprobanteCheque` int(10) unsigned DEFAULT NULL,
  `Comprobante` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CuentasBancariasMovimientos_CuentasBancarias` (`CuentaBancaria`),
  KEY `FK_CuentasBancariasMovimientos_TransaccionesBancarias` (`TransaccionBancaria`),
  KEY `FK_CuentasBancariasMovimientos_Comprobantes` (`Comprobante`),
  KEY `FK_CuentasBancariasMovimientos_ComprobantesCheques` (`ComprobanteCheque`),
  CONSTRAINT `FK_CuentasBancariasMovimientos_Comprobantes` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CuentasBancariasMovimientos_ComprobantesCheques` FOREIGN KEY (`ComprobanteCheque`) REFERENCES `ComprobantesCheques` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE,
  CONSTRAINT `FK_CuentasBancariasMovimientos_CuentasBancarias` FOREIGN KEY (`CuentaBancaria`) REFERENCES `CuentasBancarias` (`Id`),
  CONSTRAINT `FK_CuentasBancariasMovimientos_TransaccionesBancarias` FOREIGN KEY (`TransaccionBancaria`) REFERENCES `TransaccionesBancarias` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=264 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `CuentasCorrientes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `CuentasCorrientes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned DEFAULT NULL,
  `FechaDeCarga` datetime NOT NULL,
  `Comprobante` int(11) unsigned DEFAULT NULL,
  `NumeroComprobante` varchar(20) DEFAULT NULL,
  `FechaComprobante` date DEFAULT NULL,
  `Debe` decimal(12,4) DEFAULT NULL,
  `Haber` decimal(12,4) DEFAULT NULL,
  `Observaciones` text,
  `DescripcionComprobante` varchar(50) DEFAULT NULL,
  `TipoDeComprobante` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_CuentasCorrientes` (`TipoDeComprobante`),
  KEY `Persona` (`Persona`),
  KEY `FK_CuentasCorrientes_Comprobantes` (`Comprobante`),
  CONSTRAINT `CuentasCorrientes_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_CuentasCorrientes` FOREIGN KEY (`TipoDeComprobante`) REFERENCES `TiposDeComprobantes` (`Id`),
  CONSTRAINT `FK_CuentasCorrientes_Comprobantes` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`) ON DELETE CASCADE ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=7384 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB; (`TipoDeComprobante`) REFER `vidalacF';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `DeduccionesGanancias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DeduccionesGanancias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `Tipo` int(11) unsigned NOT NULL,
  `EnDesuso` tinyint(4) DEFAULT NULL,
  `CodigoAFIP` int(11) unsigned DEFAULT NULL,
  `CodigoAFIPMotivo` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Tipo` (`Tipo`),
  CONSTRAINT `DeduccionesGanancias_fk1` FOREIGN KEY (`Tipo`) REFERENCES `DeduccionesGananciasTipos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `DeduccionesGananciasTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DeduccionesGananciasTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Descuentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Descuentos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Tipo` int(11) unsigned NOT NULL,
  `Numero` int(11) NOT NULL,
  `NumeroDeReferencia` int(11) DEFAULT NULL,
  `Persona` int(11) unsigned DEFAULT NULL,
  `Servicio` int(11) unsigned DEFAULT NULL,
  `MontoTotal` decimal(13,4) NOT NULL,
  `MontoCuota` decimal(13,4) NOT NULL DEFAULT '0.0000',
  `PorcentajeCuota` decimal(2,2) NOT NULL DEFAULT '0.00',
  `CantidadCuota` int(11) NOT NULL DEFAULT '0',
  `Intereses` decimal(2,2) NOT NULL DEFAULT '0.00',
  `Fecha` date NOT NULL,
  `Observaciones` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  KEY `FK_Descuentos_TiposDeDescuentos` (`Tipo`),
  KEY `FK_Descuentos_Personas` (`Persona`),
  KEY `FK_Descuentos_Servicios` (`Servicio`),
  CONSTRAINT `FK_Descuentos_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_Descuentos_Servicios` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`),
  CONSTRAINT `FK_Descuentos_TiposDeDescuentos` FOREIGN KEY (`Tipo`) REFERENCES `DescuentosTipos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `DescuentosDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DescuentosDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descuento` int(11) unsigned NOT NULL,
  `Cuota` int(11) NOT NULL,
  `MontoCuota` decimal(13,4) NOT NULL DEFAULT '0.0000',
  `MontoCuotaPagado` decimal(13,4) unsigned DEFAULT '0.0000',
  `Intereses` decimal(2,2) DEFAULT NULL,
  `ReciboDetalle` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_DescuentosDetalles_Descuentos` (`Descuento`),
  KEY `FK_DescuentosDetalles_LiquidacionesRecibosDetalles` (`ReciboDetalle`),
  CONSTRAINT `FK_DescuentosDetalles_Descuentos` FOREIGN KEY (`Descuento`) REFERENCES `Descuentos` (`Id`),
  CONSTRAINT `FK_DescuentosDetalles_LiquidacionesRecibosDetalles` FOREIGN KEY (`ReciboDetalle`) REFERENCES `LiquidacionesRecibosDetalles` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `DescuentosTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `DescuentosTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  `Limite` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Direcciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Direcciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Direccion` varchar(250) NOT NULL,
  `PisoDepartamento` varchar(30) DEFAULT NULL,
  `Comentario` varchar(250) DEFAULT NULL,
  `Localidad` int(11) unsigned DEFAULT NULL,
  `CodigoPostalGP` varchar(50) DEFAULT NULL,
  `TipoDeDireccion` int(10) unsigned NOT NULL DEFAULT '1',
  `Persona` int(11) unsigned NOT NULL,
  `DireccionGoogleMaps` varchar(400) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Localidad` (`Localidad`),
  KEY `TipoDireccion` (`TipoDeDireccion`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `Direcciones_fk` FOREIGN KEY (`Localidad`) REFERENCES `Localidades` (`Id`),
  CONSTRAINT `Direcciones_fk2` FOREIGN KEY (`TipoDeDireccion`) REFERENCES `TiposDeDirecciones` (`Id`),
  CONSTRAINT `Direcciones_fk3` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=86 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`Localidad`) REFER `vidalacFinal/Loc';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Emails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Emails` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `Email` varchar(50) NOT NULL DEFAULT '',
  `Contacto` varchar(60) NOT NULL DEFAULT '',
  `CargoEnLaEmpresa` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `Emails_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=30 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 12288 kB; (`Cliente`) REFER `vidalacFinal/Clien';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `EmailsEnviados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EmailsEnviados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Formulario` varchar(100) NOT NULL DEFAULT '',
  `FechaHora` datetime DEFAULT NULL,
  `Observaciones` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Empresas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Empresas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `Descripcion_2` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `EntesRecaudadores`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EntesRecaudadores` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `Ambito` int(11) unsigned DEFAULT NULL,
  `Direccion` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CodigoPostal` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Ambito` (`Ambito`),
  CONSTRAINT `EntesRecaudadores_fk` FOREIGN KEY (`Ambito`) REFERENCES `Ambitos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB; (`Ambito`) REFER `vidalacFinal/Ambito';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `EstadosCiviles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `EstadosCiviles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `FacturacionElectronicaAfip`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FacturacionElectronicaAfip` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Comprobante` int(10) unsigned DEFAULT NULL,
  `Cuit` double DEFAULT NULL,
  `PtoVta` int(11) DEFAULT NULL,
  `CbteTipo` int(11) DEFAULT NULL,
  `Resultado` char(1) DEFAULT NULL,
  `Reproceso` char(1) DEFAULT NULL,
  `Concepto` tinyint(4) DEFAULT NULL,
  `DocTipo` tinyint(4) DEFAULT NULL,
  `DocNro` varchar(45) DEFAULT NULL,
  `CbteDesde` bigint(20) DEFAULT NULL,
  `CbteHasta` bigint(20) DEFAULT NULL,
  `CbteFch` varchar(8) DEFAULT NULL,
  `CAE` varchar(14) DEFAULT NULL,
  `CAEFchVto` varchar(8) DEFAULT NULL,
  `Obs` text,
  `FchProceso` varchar(8) DEFAULT NULL,
  `Direccion` varchar(300) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_FacturacionElectronicaAfip_Comprobantes` (`Comprobante`),
  CONSTRAINT `FK_FacturacionElectronicaAfip_Comprobantes` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=113 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `FamiliaresPersonas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FamiliaresPersonas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PersonaEmpleado` int(11) unsigned NOT NULL,
  `TipoDeFamiliar` int(11) unsigned NOT NULL,
  `PersonaFamiliar` int(11) unsigned NOT NULL,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  `TipoDeEscolaridad` int(11) unsigned DEFAULT NULL,
  `FamiliarACargo` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  KEY `FK_Familiares_Personas` (`PersonaEmpleado`),
  KEY `FK_Familiares_TiposDeEscolaridades` (`TipoDeEscolaridad`),
  KEY `FK_Familiares_TiposDeFamiliares` (`TipoDeFamiliar`),
  KEY `FK_FamiliaresPersonas_Personas1` (`PersonaFamiliar`),
  CONSTRAINT `FK_FamiliaresPersonas_Personas` FOREIGN KEY (`PersonaEmpleado`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_FamiliaresPersonas_Personas1` FOREIGN KEY (`PersonaFamiliar`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_FamiliaresPersonas_TiposDeEscolaridades` FOREIGN KEY (`TipoDeEscolaridad`) REFERENCES `TiposDeEscolaridades` (`Id`),
  CONSTRAINT `FK_FamiliaresPersonas_TiposDeFamiliares` FOREIGN KEY (`TipoDeFamiliar`) REFERENCES `TiposDeFamiliares` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Feriados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Feriados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `TipoDeFeriado` int(10) unsigned NOT NULL,
  `Convenio` int(11) unsigned DEFAULT NULL,
  `FechaOrigen` date NOT NULL,
  `FechaEfectiva` date DEFAULT NULL,
  `Observaciones` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `FechaOrigen` (`FechaOrigen`),
  UNIQUE KEY `FechaEfectiva` (`FechaEfectiva`),
  KEY `Convenio` (`Convenio`),
  KEY `FK_Feriados_TiposDeFeriados` (`TipoDeFeriado`),
  CONSTRAINT `Feriados_fk` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`),
  CONSTRAINT `FK_Feriados_TiposDeFeriados` FOREIGN KEY (`TipoDeFeriado`) REFERENCES `TiposDeFeriados` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Fletes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Fletes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Transportista` int(10) unsigned NOT NULL,
  `Costo` decimal(11,2) DEFAULT '0.00',
  `Remito` int(11) unsigned DEFAULT NULL,
  `FleteFormaPago` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Remito` (`Remito`),
  KEY `FK_Fletes_Transportistas` (`Transportista`),
  KEY `FleteTipoPago` (`FleteFormaPago`),
  CONSTRAINT `FK_Fletes_FletesFormasPagos` FOREIGN KEY (`FleteFormaPago`) REFERENCES `FletesFormasPagos` (`Id`),
  CONSTRAINT `FK_Fletes_Transportistas` FOREIGN KEY (`Transportista`) REFERENCES `TransportistasBORRAR` (`Id`),
  CONSTRAINT `Fletes_fk` FOREIGN KEY (`Remito`) REFERENCES `RemitosBORRAR` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`FleteTipoDePago`) REFER `vidalac/Fle';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `FletesFormasPagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FletesFormasPagos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `FormulasProductos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `FormulasProductos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Producto` int(11) unsigned NOT NULL,
  `Insumo` int(11) unsigned NOT NULL,
  `Cantidad` decimal(15,6) DEFAULT NULL,
  `Prod_newArticulo` int(11) unsigned DEFAULT NULL,
  `Prod_newArtVersion` int(11) unsigned DEFAULT NULL,
  `Ins_newArticulo` int(11) unsigned DEFAULT NULL,
  `Ins_newArtVersion` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB; (`Articulo`) REFER `vidalacFinal/Arti';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GananciasConceptos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GananciasConceptos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `GananciaDeduccionTipo` int(11) unsigned DEFAULT NULL,
  `EnDesuso` tinyint(4) DEFAULT NULL,
  `AfipGananciaDeduccion` int(11) unsigned DEFAULT NULL,
  `CodigoAFIPDeduccionesMotivo` int(11) unsigned DEFAULT NULL,
  `GananciaConceptoTipo` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Tipo` (`GananciaDeduccionTipo`),
  KEY `CodigoAFIP` (`AfipGananciaDeduccion`),
  KEY `Tipo_2` (`GananciaConceptoTipo`),
  CONSTRAINT `GC_fk001` FOREIGN KEY (`GananciaConceptoTipo`) REFERENCES `GananciasConceptosTipos` (`Id`),
  CONSTRAINT `GC_fk002` FOREIGN KEY (`GananciaDeduccionTipo`) REFERENCES `GananciasDeduccionesTipos` (`Id`),
  CONSTRAINT `GC_fk003` FOREIGN KEY (`AfipGananciaDeduccion`) REFERENCES `AfipGananciasDeducciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=41 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GananciasConceptosTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GananciasConceptosTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GananciasDeduccionesTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GananciasDeduccionesTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(300) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GeneradorDeCheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GeneradorDeCheques` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `Chequera` int(11) unsigned NOT NULL,
  `ProximoNumero` int(11) DEFAULT NULL,
  `FechaGeneracion` date NOT NULL,
  `FechaPrimerPago` date NOT NULL,
  `MontoTotal` decimal(11,2) NOT NULL,
  `DistanciaEnDias` int(11) DEFAULT NULL,
  `CantidadDeCheques` int(11) DEFAULT NULL,
  `AlaOrden` varchar(150) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Observaciones` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Cruzado` tinyint(4) NOT NULL DEFAULT '1',
  `NoALaOrden` tinyint(4) NOT NULL DEFAULT '0',
  `OrdenDePago` int(11) unsigned DEFAULT NULL,
  `MontoRealGenerado` decimal(11,2) DEFAULT NULL,
  `NumeroChequeInicial` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `OrdenDePago` (`OrdenDePago`),
  KEY `Chequera` (`Chequera`),
  KEY `Proveedor` (`Persona`),
  CONSTRAINT `GeneradorDeCheques_fk` FOREIGN KEY (`OrdenDePago`) REFERENCES `OrdenesDePagos` (`Id`),
  CONSTRAINT `GeneradorDeCheques_fk1` FOREIGN KEY (`Chequera`) REFERENCES `Chequeras` (`Id`),
  CONSTRAINT `GeneradorDeCheques_fk2` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1733 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 12288 kB; (`OrdenDePago`) REFER `vidalacFinal/O';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GruposDePersonas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GruposDePersonas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  `EnDesuso` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GruposDePersonasDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GruposDePersonasDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `GrupoDePersona` int(11) unsigned NOT NULL,
  `Persona` int(11) unsigned NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `GrupoDePersonas` (`GrupoDePersona`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `FK_GruposDePersonasDetalles_GruposDePersonas` FOREIGN KEY (`GrupoDePersona`) REFERENCES `GruposDePersonas` (`Id`),
  CONSTRAINT `GruposDePersonasDetalles_fk1` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GruposDeUsuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GruposDeUsuarios` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `GruposDeUsuariosRoles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `GruposDeUsuariosRoles` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `GrupoDeUsuario` int(11) unsigned NOT NULL,
  `Rol` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_GruposDeUsuariosRoles_GruposDeUsuarios` (`GrupoDeUsuario`),
  KEY `FK_GruposDeUsuariosRoles_Roles` (`Rol`),
  CONSTRAINT `FK_GruposDeUsuariosRoles_GruposDeUsuarios` FOREIGN KEY (`GrupoDeUsuario`) REFERENCES `GruposDeUsuarios` (`Id`),
  CONSTRAINT `FK_GruposDeUsuariosRoles_Roles` FOREIGN KEY (`Rol`) REFERENCES `Roles` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=57 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Idiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Idiomas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Afip` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `I_fk001` FOREIGN KEY (`Afip`) REFERENCES `AfipIdiomas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Jobs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Jobs` (
  `id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `handler` blob NOT NULL,
  `queue` varchar(255) NOT NULL DEFAULT 'default',
  `attempts` int(10) unsigned NOT NULL DEFAULT '0',
  `run_at` datetime DEFAULT NULL,
  `locked_at` datetime DEFAULT NULL,
  `locked_by` varchar(255) DEFAULT NULL,
  `failed_at` datetime DEFAULT NULL,
  `error` text,
  `created_at` datetime NOT NULL,
  `user` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`id`)
) ENGINE=InnoDB AUTO_INCREMENT=121 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LibrosDiarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LibrosDiarios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Comprobante` int(11) unsigned NOT NULL,
  `NroAsiento` int(11) unsigned NOT NULL COMMENT 'un comprobante puede requerir mas de un asiento para registrarse',
  `FechaAsiento` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Comprobante` (`Comprobante`),
  CONSTRAINT `LibrosDiarios_fk` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=52 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LibrosDiariosDetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LibrosDiariosDetalle` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Asiento` int(11) unsigned NOT NULL,
  `Cuenta` int(11) unsigned NOT NULL,
  `Debe` decimal(11,2) DEFAULT '0.00',
  `Haber` decimal(11,2) DEFAULT '0.00',
  PRIMARY KEY (`Id`),
  KEY `Cuenta` (`Cuenta`),
  KEY `Asiento` (`Asiento`),
  CONSTRAINT `LibrosDiariosDetalle_fk` FOREIGN KEY (`Cuenta`) REFERENCES `PlanesDeCuentas` (`Id`),
  CONSTRAINT `LibrosDiariosDetalle_fk1` FOREIGN KEY (`Asiento`) REFERENCES `LibrosDiarios` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=98 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LibrosIVA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LibrosIVA` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Mes` int(11) NOT NULL,
  `Anio` int(11) NOT NULL,
  `Cerrado` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=61 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LibrosIVADetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LibrosIVADetalles` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Comprobante` int(11) unsigned DEFAULT NULL,
  `Persona` int(11) unsigned DEFAULT NULL,
  `LibroIVA` int(11) unsigned NOT NULL,
  `TipoDeLibro` int(10) unsigned NOT NULL,
  `ImporteNetoGravado105` decimal(12,2) DEFAULT '0.00',
  `ImporteNetoGravado210` decimal(12,2) DEFAULT '0.00',
  `ImporteNetoGravado270` decimal(12,2) DEFAULT '0.00',
  `ImporteIVA105` decimal(12,2) DEFAULT '0.00',
  `ImporteIVA210` decimal(12,2) DEFAULT '0.00',
  `ImporteIVA270` decimal(12,2) DEFAULT '0.00',
  `ImporteImpuestosInternos` decimal(12,2) DEFAULT '0.00',
  `ImporteConceptosExentosONoGravados` decimal(12,2) DEFAULT '0.00',
  `ImportePercepcionesIVA` decimal(12,2) DEFAULT '0.00',
  `ImportePercepcionesGanancias` decimal(12,2) DEFAULT '0.00',
  `ImportePercepcionesSuss` decimal(12,2) DEFAULT '0.00',
  `ImportePercepcionesIB` decimal(12,2) DEFAULT '0.00',
  `ImporteOtrasPercepcionesImpuestosNacionales` decimal(12,2) DEFAULT '0.00',
  `ImporteOtrasPercepcionesImpuestosProvinciales` decimal(12,2) DEFAULT '0.00',
  `ImportePercepcionesTasaMunicipales` decimal(12,2) DEFAULT '0.00',
  `ImporteTotalComprobante` decimal(12,2) DEFAULT '0.00',
  `ImporteRetencionesIVA` decimal(12,2) DEFAULT '0.00',
  `ImporteRetencionesGanancias` decimal(12,2) DEFAULT '0.00',
  `ImporteRetencionesSuss` decimal(12,2) DEFAULT '0.00',
  `ImporteRetencionesIB` decimal(12,2) DEFAULT '0.00',
  `ImporteRetencionesTasaMunicipales` decimal(12,2) DEFAULT '0.00',
  `ImporteOtrasRetencionesImpuestosProvinciales` decimal(12,2) DEFAULT '0.00',
  `ImporteOtrasRetencionesImpuestosNacionales` decimal(12,2) DEFAULT '0.00',
  `Numero` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_LibrosDeIVADetalles_Comprobante` (`Comprobante`),
  KEY `FK_LibrosDeIVADetalles_Persona` (`Persona`),
  KEY `FK_LibrosDeIVADetalle_LibroIVA` (`LibroIVA`),
  KEY `FK_LibrosIVADetalles_TiposDeLibrosIva` (`TipoDeLibro`),
  CONSTRAINT `FK_LibrosDeIVADetalles_Comprobante` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `FK_LibrosDeIVADetalles_Persona` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_LibrosDeIVADetalle_LibroIVA` FOREIGN KEY (`LibroIVA`) REFERENCES `LibrosIVA` (`Id`),
  CONSTRAINT `FK_LibrosIVADetalles_TiposDeLibrosIVA` FOREIGN KEY (`TipoDeLibro`) REFERENCES `TiposDeLibrosIVA` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9278 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LineasDeProducciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LineasDeProducciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `TipoDeLineaDeProduccion` int(10) unsigned NOT NULL,
  `Interdeposito` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_LineasDeProducciones_Almacenes` (`Interdeposito`),
  KEY `FK_LineasDeProducciones_TiposDeLineasDeProducciones` (`TipoDeLineaDeProduccion`),
  CONSTRAINT `FK_LineasDeProducciones_Almacenes` FOREIGN KEY (`Interdeposito`) REFERENCES `Almacenes` (`Id`),
  CONSTRAINT `FK_LineasDeProducciones_TiposDeLineasDeProducciones` FOREIGN KEY (`TipoDeLineaDeProduccion`) REFERENCES `TiposDeLineasDeProducciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LineasDeProduccionesActividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LineasDeProduccionesActividades` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Actividad` int(10) unsigned NOT NULL,
  `ActividadConfiguracion` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_LineasDeProduccionesProcesos_Actividades` (`Actividad`),
  KEY `FK_LineasDeProduccionesProcesos_ActividadesConfiguraciones` (`ActividadConfiguracion`),
  CONSTRAINT `FK_LineasDeProduccionesProcesos_Actividades` FOREIGN KEY (`Actividad`) REFERENCES `Actividades` (`Id`),
  CONSTRAINT `FK_LineasDeProduccionesProcesos_ActividadesConfiguraciones` FOREIGN KEY (`ActividadConfiguracion`) REFERENCES `ActividadesConfiguraciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LineasDeProduccionesPersonas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LineasDeProduccionesPersonas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Produccion` int(10) unsigned NOT NULL,
  `Actividad` int(10) unsigned NOT NULL,
  `Persona` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_LineasDeProduccionesPersonas_Personas` (`Persona`),
  KEY `FK_LineasDeProduccionesPersonas_Producciones` (`Produccion`),
  KEY `FK_LineasDeProduccionesPersonas_Actividades` (`Actividad`),
  CONSTRAINT `FK_LineasDeProduccionesPersonas_Actividades` FOREIGN KEY (`Actividad`) REFERENCES `Actividades` (`Id`),
  CONSTRAINT `FK_LineasDeProduccionesPersonas_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_LineasDeProduccionesPersonas_Producciones` FOREIGN KEY (`Produccion`) REFERENCES `Producciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Liquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Liquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TipoDeLiquidacion` int(11) unsigned NOT NULL,
  `EsDePrueba` tinyint(4) NOT NULL DEFAULT '0',
  `LiquidacionPeriodo` int(11) unsigned NOT NULL,
  `Ejecutada` datetime DEFAULT NULL,
  `Usuario` int(10) unsigned NOT NULL,
  `Cerrada` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `TipoDeLiquidacion` (`TipoDeLiquidacion`),
  KEY `PeriodoLiquidacion` (`LiquidacionPeriodo`),
  CONSTRAINT `FK_Liquidaciones_LiquidacionesPeriodos` FOREIGN KEY (`LiquidacionPeriodo`) REFERENCES `LiquidacionesPeriodos` (`Id`),
  CONSTRAINT `Liquidaciones_fk` FOREIGN KEY (`TipoDeLiquidacion`) REFERENCES `TiposDeLiquidaciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=10 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesPeriodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesPeriodos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Anio` int(11) DEFAULT NULL,
  `Valor` int(11) DEFAULT NULL,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  `TipoDeLiquidacionPeriodo` int(11) unsigned NOT NULL,
  `Descripcion` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `TipoDePeriodoDeLiquidacion` (`TipoDeLiquidacionPeriodo`),
  CONSTRAINT `FK_LiquidacionesPeriodos_TiposDeLiquidacionesPeriodos` FOREIGN KEY (`TipoDeLiquidacionPeriodo`) REFERENCES `TiposDeLiquidacionesPeriodos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesRecibos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesRecibos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Liquidacion` int(11) unsigned NOT NULL,
  `Persona` int(11) unsigned NOT NULL,
  `Servicio` int(11) unsigned NOT NULL,
  `Ajuste` int(10) unsigned NOT NULL DEFAULT '0',
  `Periodo` int(10) unsigned NOT NULL,
  `FechaCalculo` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Liquidacion` (`Liquidacion`),
  KEY `fk_LiquidacionesRecibos_1` (`Periodo`),
  KEY `FK_LiquidacionesRecibos_Personas` (`Persona`),
  KEY `ServicioPeriodo` (`Servicio`,`Periodo`),
  KEY `PeriodoServicio` (`Periodo`,`Servicio`),
  CONSTRAINT `fk_LiquidacionesRecibos_1` FOREIGN KEY (`Periodo`) REFERENCES `LiquidacionesPeriodos` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION,
  CONSTRAINT `FK_LiquidacionesRecibos_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_LiquidacionesRecibos_Servicios` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`),
  CONSTRAINT `LiquidacionesRecibos_fk` FOREIGN KEY (`Liquidacion`) REFERENCES `Liquidaciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=812 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesRecibosDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesRecibosDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LiquidacionRecibo` int(11) unsigned NOT NULL,
  `VariableDetalle` int(11) unsigned NOT NULL,
  `Monto` decimal(11,4) NOT NULL,
  `MontoCalculado` decimal(11,4) NOT NULL,
  `PeriodoDevengado` int(11) unsigned NOT NULL,
  `Detalle` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `LiquidacionRecibo` (`LiquidacionRecibo`),
  KEY `VariableDetalle` (`VariableDetalle`),
  KEY `FK_LiquidacionesRecibosDetalles_LiquidacionesPeriodos` (`PeriodoDevengado`),
  CONSTRAINT `FK_LiquidacionesRecibosDetalles_LiquidacionesPeriodos` FOREIGN KEY (`PeriodoDevengado`) REFERENCES `LiquidacionesPeriodos` (`Id`),
  CONSTRAINT `LiquidacionesRecibosDetalles_fk` FOREIGN KEY (`LiquidacionRecibo`) REFERENCES `LiquidacionesRecibos` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `LiquidacionesRecibosDetalles_fk1` FOREIGN KEY (`VariableDetalle`) REFERENCES `VariablesDetalles` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=20671 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesTablas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesTablas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Convenio` int(11) unsigned NOT NULL,
  `Grupo` int(11) unsigned DEFAULT NULL,
  `TipoDeLiquidacionTabla` int(11) unsigned NOT NULL,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Convenio` (`Convenio`),
  KEY `TipoDeTabla` (`TipoDeLiquidacionTabla`),
  KEY `FK_LiquidacionesTablas_LiquidacionesTablasCategoriasGrupos` (`Grupo`),
  CONSTRAINT `FK_LiquidacionesTablas_Convenios` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`),
  CONSTRAINT `FK_LiquidacionesTablas_LiquidacionesTablasCategoriasGrupos` FOREIGN KEY (`Grupo`) REFERENCES `LiquidacionesTablasCategoriasGrupos` (`Id`),
  CONSTRAINT `FK_LiquidacionesTablas_TiposDeLiquidacionesTablas` FOREIGN KEY (`TipoDeLiquidacionTabla`) REFERENCES `TiposDeLiquidacionesTablas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesTablasCategoriasGrupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesTablasCategoriasGrupos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesTablasDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesTablasDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LiquidacionTabla` int(11) unsigned NOT NULL,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `InicioRango` int(11) DEFAULT NULL,
  `FinRango` int(11) DEFAULT NULL,
  `Valor` decimal(12,2) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `ConvenioTabla` (`LiquidacionTabla`),
  CONSTRAINT `FK_LiquidacionesTablasDetalles_LiquidacionesTablas` FOREIGN KEY (`LiquidacionTabla`) REFERENCES `LiquidacionesTablas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=179 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesVariablesCalculadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesVariablesCalculadas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `LiquidacionRecibo` int(11) unsigned NOT NULL,
  `VariableDetalle` int(11) unsigned DEFAULT NULL,
  `Valor` text COLLATE utf8_unicode_ci,
  `Nombre` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `LiquidacionRecibo` (`LiquidacionRecibo`),
  KEY `Variable` (`VariableDetalle`),
  CONSTRAINT `FK_LiquidacionesVariablesCalculadas_VariablesDetalles` FOREIGN KEY (`VariableDetalle`) REFERENCES `VariablesDetalles` (`Id`),
  CONSTRAINT `LiquidacionesVariablesCalculadas_fk` FOREIGN KEY (`LiquidacionRecibo`) REFERENCES `LiquidacionesRecibos` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=24092 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `LiquidacionesVariablesDesactivadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `LiquidacionesVariablesDesactivadas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Liquidacion` int(11) unsigned NOT NULL,
  `Variable` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Variable` (`Variable`),
  KEY `Liquidacion` (`Liquidacion`),
  CONSTRAINT `LiquidacionesVariablesDesactivadas_fk` FOREIGN KEY (`Variable`) REFERENCES `Variables` (`Id`),
  CONSTRAINT `LiquidacionesVariablesDesactivadas_fk1` FOREIGN KEY (`Liquidacion`) REFERENCES `Liquidaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Localidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Localidades` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Provincia` int(10) unsigned NOT NULL,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  `CodigoPostal` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Localidades_FKIndex1` (`Provincia`),
  CONSTRAINT `Localidades_fk` FOREIGN KEY (`Provincia`) REFERENCES `Provincias` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contiene las Localidades de las Tablas relacionales; InnoDB ';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Lotes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Lotes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Numero` varchar(50) NOT NULL,
  `Articulo` int(11) unsigned DEFAULT NULL,
  `Cantidad` int(11) NOT NULL,
  `FechaElaboracion` date NOT NULL,
  `FechaVencimiento` date DEFAULT NULL,
  `Persona` int(10) unsigned DEFAULT NULL,
  `Propio` tinyint(1) NOT NULL DEFAULT '0',
  `Observaciones` text,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Numero` (`Numero`,`Persona`,`Propio`),
  KEY `Articulo` (`Articulo`),
  KEY `FK_Lotes_2` (`Persona`),
  CONSTRAINT `FK_Lotes_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`),
  CONSTRAINT `FK_Lotes_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=62 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Marcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Marcas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  `Propia` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `Produccion` tinyint(4) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=68 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `MenuesPrincipales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MenuesPrincipales` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Texto` varchar(45) DEFAULT NULL,
  `MenuPrincipal` int(11) unsigned DEFAULT NULL,
  `Modulo` int(11) unsigned DEFAULT NULL,
  `Activo` tinyint(1) NOT NULL DEFAULT '0',
  `TienePanel` tinyint(1) NOT NULL DEFAULT '0',
  `Orden` int(11) unsigned DEFAULT '0',
  `Icono` varchar(45) DEFAULT NULL,
  `PanelAncho` int(10) unsigned DEFAULT NULL,
  `PanelAlto` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_MenuesPrincipales_Modulos` (`Modulo`),
  KEY `fk_MenuesPrincipales_MenuesPrincipales` (`MenuPrincipal`),
  CONSTRAINT `FK_MenuesPrincipales_Modulos` FOREIGN KEY (`Modulo`) REFERENCES `Modulos` (`Id`),
  CONSTRAINT `MenuesPrincipales_fk` FOREIGN KEY (`MenuPrincipal`) REFERENCES `MenuesPrincipales` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=407 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`MenuPrincipal`) REFER `vidalac/Menue';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Meses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Meses` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Mmis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Mmis` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Identificador` varchar(7) NOT NULL DEFAULT '',
  `Deposito` int(11) unsigned DEFAULT NULL,
  `Almacen` int(11) unsigned DEFAULT NULL,
  `Ubicacion` int(11) unsigned DEFAULT NULL,
  `UnidadDeMedida` int(11) unsigned NOT NULL,
  `RemitoArticulo` int(11) unsigned DEFAULT NULL,
  `TipoDePalet` int(11) unsigned NOT NULL,
  `CantidadOriginal` decimal(12,4) DEFAULT NULL,
  `CantidadActual` decimal(12,4) DEFAULT NULL,
  `ParaFason` tinyint(1) NOT NULL DEFAULT '0',
  `FechaIngreso` datetime DEFAULT NULL,
  `FechaVencimiento` date DEFAULT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `HabilitadoParaProduccion` tinyint(4) unsigned NOT NULL DEFAULT '0',
  `MmiTipo` int(11) unsigned NOT NULL,
  `MmiPadre` int(11) unsigned DEFAULT NULL,
  `RemitoArticuloSalida` int(11) unsigned DEFAULT NULL,
  `FechaCierre` datetime DEFAULT NULL,
  `Lote` int(11) unsigned DEFAULT NULL,
  `Articulo` int(11) unsigned NOT NULL,
  `ArticuloVersion` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Lotes_FKIndex2` (`UnidadDeMedida`),
  KEY `Lotes_FKIndex4` (`Ubicacion`),
  KEY `fk_Lotes_TiposDePalets` (`TipoDePalet`),
  KEY `Estado` (`HabilitadoParaProduccion`),
  KEY `Almacen` (`Almacen`),
  KEY `MmiTipo` (`MmiTipo`),
  KEY `FK_Mmis_7` (`Lote`),
  KEY `RemitoArticulo` (`RemitoArticulo`),
  KEY `RemitoArticuloSalida` (`RemitoArticuloSalida`),
  KEY `FK_Mmis_ArticulosVersiones` (`ArticuloVersion`),
  KEY `FK_Mmis_Articulos` (`Articulo`),
  KEY `FK_Mmis_Direcciones` (`Deposito`),
  CONSTRAINT `FK_Mmis_7` FOREIGN KEY (`Lote`) REFERENCES `Lotes` (`Id`),
  CONSTRAINT `FK_Mmis_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`),
  CONSTRAINT `FK_Mmis_ArticulosVersiones` FOREIGN KEY (`ArticuloVersion`) REFERENCES `ArticulosVersiones` (`Id`),
  CONSTRAINT `FK_Mmis_Direcciones` FOREIGN KEY (`Deposito`) REFERENCES `Direcciones` (`Id`),
  CONSTRAINT `Mmis_fk` FOREIGN KEY (`RemitoArticulo`) REFERENCES `ComprobantesDetalles` (`Id`),
  CONSTRAINT `Mmis_fk1` FOREIGN KEY (`TipoDePalet`) REFERENCES `TiposDePalets` (`Id`),
  CONSTRAINT `Mmis_fk2` FOREIGN KEY (`UnidadDeMedida`) REFERENCES `UnidadesDeMedidas` (`Id`),
  CONSTRAINT `Mmis_fk3` FOREIGN KEY (`RemitoArticuloSalida`) REFERENCES `ComprobantesDetalles` (`Id`),
  CONSTRAINT `Mmis_fk4` FOREIGN KEY (`MmiTipo`) REFERENCES `MmisTipos` (`Id`),
  CONSTRAINT `Mmis_fk5` FOREIGN KEY (`Ubicacion`) REFERENCES `Ubicaciones` (`Id`),
  CONSTRAINT `Mmis_fk6` FOREIGN KEY (`Almacen`) REFERENCES `Almacenes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=322 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `MmisAcciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MmisAcciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `DescripcionTag` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `DescripcionTag` (`DescripcionTag`),
  UNIQUE KEY `Descripcion_2` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=18 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=FIXED;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `MmisMovimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MmisMovimientos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Mmi` int(11) unsigned NOT NULL,
  `MmiAccion` int(11) unsigned DEFAULT NULL,
  `Cantidad` float(11,2) unsigned DEFAULT '0.00',
  `CantidadActual` float(11,2) DEFAULT NULL,
  `Fecha` datetime DEFAULT NULL,
  `UbicacionOrigen` int(11) unsigned DEFAULT NULL,
  `AlmacenOrigen` int(11) unsigned DEFAULT NULL,
  `UbicacionDestino` int(11) unsigned DEFAULT NULL,
  `AlmacenDestino` int(11) unsigned DEFAULT NULL,
  `Descripcion` varchar(256) NOT NULL,
  `Operacion` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `LotesMovimientos_FKIndex1` (`Mmi`),
  KEY `AlmacenDestino` (`AlmacenDestino`),
  KEY `AlmacenOrigen` (`AlmacenOrigen`),
  KEY `MmiAccion` (`MmiAccion`),
  KEY `Ubicacion` (`UbicacionOrigen`),
  KEY `UbicacionDestino` (`UbicacionDestino`),
  CONSTRAINT `MmisMovimientos_fk` FOREIGN KEY (`AlmacenOrigen`) REFERENCES `Almacenes` (`Id`),
  CONSTRAINT `MmisMovimientos_fk1` FOREIGN KEY (`AlmacenDestino`) REFERENCES `Almacenes` (`Id`),
  CONSTRAINT `MmisMovimientos_fk2` FOREIGN KEY (`UbicacionOrigen`) REFERENCES `Ubicaciones` (`Id`),
  CONSTRAINT `MmisMovimientos_fk3` FOREIGN KEY (`UbicacionDestino`) REFERENCES `Ubicaciones` (`Id`),
  CONSTRAINT `MmisMovimientos_fk4` FOREIGN KEY (`Mmi`) REFERENCES `Mmis` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `MmisMovimientos_fk5` FOREIGN KEY (`MmiAccion`) REFERENCES `MmisAcciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=166 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Describe el moviemiento del contienido dentro del  lote; Inn';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `MmisTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MmisTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ModalidadesDeContrataciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModalidadesDeContrataciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `CodigoAFIP` varchar(10) COLLATE utf8_unicode_ci NOT NULL,
  `Activo` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `CodigoAfip` (`CodigoAFIP`),
  UNIQUE KEY `CodigoAFIP_2` (`CodigoAFIP`)
) ENGINE=InnoDB AUTO_INCREMENT=14 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ModalidadesDePagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModalidadesDePagos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Son las formas de pago aceptada por el proveedor; InnoDB fre';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ModalidadesIVA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModalidadesIVA` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '' COMMENT 'Responsable Inscripto,Monotributista,Excento, etc.',
  `Afip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `MI_fk001` FOREIGN KEY (`Afip`) REFERENCES `AfipTiposDeResponsables` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Monotributistas, excento e inscrpto; InnoDB free: 4096 kB; I';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Modelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Modelos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=576 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ModelosCaracterizables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModelosCaracterizables` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Modelo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Modelo` (`Modelo`),
  CONSTRAINT `ModelosCaracterizables_fk` FOREIGN KEY (`Modelo`) REFERENCES `Modelos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ModelosCaracterizablesCaracteristicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModelosCaracterizablesCaracteristicas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ObjetoCaracterizable` int(11) unsigned NOT NULL,
  `Caracteristica` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Caracteristica` (`Caracteristica`),
  KEY `ObjetoCaracterizable` (`ObjetoCaracterizable`),
  CONSTRAINT `ObjetosCaracterizablesCaracteristicas_fk` FOREIGN KEY (`Caracteristica`) REFERENCES `Caracteristicas` (`Id`),
  CONSTRAINT `ObjetosCaracterizablesCaracteristicas_fk1` FOREIGN KEY (`ObjetoCaracterizable`) REFERENCES `ModelosCaracterizables` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Modulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Modulos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) NOT NULL DEFAULT '',
  `Titulo` varchar(45) DEFAULT NULL,
  `Controlador` varchar(45) DEFAULT NULL,
  `Accion` varchar(45) DEFAULT NULL,
  `Parametros` varchar(100) DEFAULT NULL,
  `Modulo` varchar(45) CHARACTER SET utf8 COLLATE utf8_bin DEFAULT NULL,
  `Descripcion` text,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=728 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ModulosModelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ModulosModelos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Modulo` int(10) unsigned NOT NULL,
  `Modelo` int(10) unsigned NOT NULL,
  `Ver` tinyint(3) unsigned NOT NULL,
  `Modificar` tinyint(3) unsigned NOT NULL,
  `Crear` tinyint(3) unsigned NOT NULL,
  `Borrar` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ModulosModelos_1` (`Modulo`),
  KEY `FK_ModulosModelos_2` (`Modelo`),
  CONSTRAINT `FK_ModulosModelos_1` FOREIGN KEY (`Modulo`) REFERENCES `Modulos` (`Id`),
  CONSTRAINT `FK_ModulosModelos_2` FOREIGN KEY (`Modelo`) REFERENCES `Modelos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=331 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 12288 kB; (`Modulo`) REFER `vidalacFinal/Modulo';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `NEW_Articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NEW_Articulos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Tipo` int(11) unsigned NOT NULL,
  `Codigo` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `CodigoDeBarras` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `UnidadDeMedida` int(11) unsigned NOT NULL,
  `UnidadDeMedidaDeProduccion` int(11) unsigned DEFAULT NULL,
  `FactorDeConversion` decimal(12,8) DEFAULT NULL,
  `TipoDeControlDeStock` int(11) unsigned DEFAULT NULL,
  `EsInsumo` tinyint(4) NOT NULL DEFAULT '0',
  `EsProducido` tinyint(4) NOT NULL DEFAULT '0',
  `EsParaVenta` tinyint(4) NOT NULL DEFAULT '0',
  `EsParaCompra` tinyint(4) NOT NULL DEFAULT '0',
  `EsFinal` tinyint(4) NOT NULL DEFAULT '0',
  `ArticuloSubGrupo` int(11) unsigned DEFAULT NULL,
  `ArticuloGrupo` int(11) unsigned DEFAULT NULL,
  `RequiereLote` tinyint(4) NOT NULL DEFAULT '0',
  `AnalisisTipoModelo` int(11) unsigned DEFAULT NULL,
  `Marca` int(11) unsigned DEFAULT NULL,
  `EsMateriaPrima` tinyint(4) NOT NULL DEFAULT '0',
  `RequiereProtocolo` tinyint(4) DEFAULT NULL,
  `IVA` int(11) unsigned DEFAULT NULL,
  `Cuenta` int(11) unsigned DEFAULT NULL,
  `Leyenda` varchar(200) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `RNPA` varchar(11) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `DescripcionLarga` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Old_p_Id` int(11) unsigned DEFAULT NULL,
  `Old_a_Id` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1285 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `NEW_ArticulosVersiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NEW_ArticulosVersiones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(11) unsigned NOT NULL,
  `Version` int(11) unsigned NOT NULL,
  `Fecha` date NOT NULL,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `Old_p_Id` int(11) unsigned NOT NULL,
  `Old_a_Id` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=609 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `NEW_ArticulosVersionesDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NEW_ArticulosVersionesDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ArticuloVersionPadre` int(11) unsigned NOT NULL,
  `ArticuloVersionHijo` int(11) unsigned NOT NULL,
  `Proceso` int(11) unsigned DEFAULT NULL,
  `Cantidad` decimal(12,8) unsigned NOT NULL,
  `UnidadDeMedida` int(11) unsigned NOT NULL,
  `TempOrden` int(11) unsigned NOT NULL,
  `PackagingGramosPorUnidad` decimal(12,8) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1193 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `NovedadesDeLiquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NovedadesDeLiquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Operacion` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `FechaCarga` datetime NOT NULL,
  `FechaInicioNovedad` date DEFAULT NULL,
  `FechaFinNovedad` date DEFAULT NULL,
  `IdNovedad` int(11) unsigned NOT NULL,
  `Tabla` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Modelo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Modificacion` text COLLATE utf8_unicode_ci,
  `Usuario` int(11) unsigned NOT NULL,
  `Estado` int(11) unsigned NOT NULL,
  `Procesado` tinyint(4) DEFAULT NULL,
  `TipoDeNovedad` int(11) unsigned DEFAULT NULL,
  `Descripcion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Jerarquia` int(11) unsigned DEFAULT NULL,
  `IdJerarquia` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TipoDeNovedad` (`TipoDeNovedad`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `NovedadesDeLiquidacionesEstados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `NovedadesDeLiquidacionesEstados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Old_Articulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Old_Articulos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Tipo` int(11) unsigned NOT NULL,
  `Codigo` varchar(50) DEFAULT NULL,
  `Descripcion` varchar(255) NOT NULL COMMENT 'Descripcion General',
  `Producto` int(11) unsigned DEFAULT NULL,
  `EsProducido` tinyint(1) NOT NULL DEFAULT '0',
  `Cantidad` decimal(12,2) DEFAULT NULL,
  `UnidadDeMedida` int(11) unsigned DEFAULT NULL,
  `Packaging` int(11) unsigned DEFAULT NULL,
  `Packaging1` int(11) unsigned DEFAULT NULL,
  `Packaging2` int(11) unsigned DEFAULT NULL,
  `Packaging3` int(11) unsigned DEFAULT NULL,
  `Packaging4` int(11) unsigned DEFAULT NULL,
  `CantidadPorPackaging1` int(11) unsigned DEFAULT NULL,
  `CantidadPorPackaging2` int(11) unsigned DEFAULT NULL,
  `CantidadPorPackaging3` int(11) unsigned DEFAULT NULL,
  `CantidadPorPackaging4` int(11) unsigned DEFAULT NULL,
  `CodigoDeBarras` varchar(100) DEFAULT NULL,
  `RequiereProtocolo` tinyint(1) NOT NULL DEFAULT '0',
  `Cuenta` int(11) unsigned DEFAULT NULL,
  `Leyenda` varchar(200) DEFAULT NULL,
  `EsInsumo` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `EsParaVenta` tinyint(1) unsigned NOT NULL DEFAULT '0',
  `EsParaCompra` tinyint(1) unsigned NOT NULL,
  `SeUtilizaParaFason` tinyint(1) NOT NULL DEFAULT '0',
  `ArticuloGrupo` int(11) unsigned DEFAULT NULL,
  `ArticuloSubGrupo` int(11) unsigned DEFAULT NULL,
  `Marca` int(11) unsigned DEFAULT NULL,
  `Packaging1GramosPorUnidad` decimal(12,4) DEFAULT NULL,
  `Packaging2GramosPorUnidad` decimal(12,4) DEFAULT NULL,
  `Packaging3GramosPorUnidad` decimal(12,4) DEFAULT NULL,
  `Packaging4GramosPorUnidad` decimal(12,4) DEFAULT NULL,
  `PackagingGramosPorUnidad` decimal(12,4) DEFAULT NULL,
  `RequiereLote` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `IVA` int(11) unsigned NOT NULL DEFAULT '1',
  `RNPA` varchar(10) DEFAULT NULL,
  `DescripcionLarga` varchar(255) DEFAULT NULL,
  `TipoDeControlDeStock` int(11) NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=406 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contiene los datos de los articulos; InnoDB free: 4096 kB; (';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Old_Productos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Old_Productos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) DEFAULT NULL,
  `UnidadDeMedida` int(11) unsigned NOT NULL,
  `ProductoCategoria` int(11) unsigned NOT NULL,
  `ProductoSubCategoria` int(11) unsigned DEFAULT NULL,
  `DescripcionReducida` varchar(20) DEFAULT NULL,
  `AnalisisTipoModelo` int(11) unsigned DEFAULT NULL,
  `TieneFormula` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `EsInsumo` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `RequiereAnalisis` tinyint(3) unsigned NOT NULL,
  `AG` int(11) unsigned DEFAULT NULL,
  `ASG` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `Descripcion_2` (`Descripcion`),
  KEY `UnidadDeMedida` (`UnidadDeMedida`),
  KEY `ProductoCategoria` (`ProductoCategoria`),
  KEY `ProdcutoSubCategoria` (`ProductoSubCategoria`) USING BTREE,
  KEY `FK_Productos_AnalisisTipoModelo` (`AnalisisTipoModelo`),
  CONSTRAINT `FK_Productos_AnalisisTipoModelo` FOREIGN KEY (`AnalisisTipoModelo`) REFERENCES `AnalisisTiposModelos` (`Id`),
  CONSTRAINT `Productos_fk` FOREIGN KEY (`UnidadDeMedida`) REFERENCES `UnidadesDeMedidas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=326 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `OrdenesDeProducciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrdenesDeProducciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(11) unsigned NOT NULL,
  `ArticuloVersion` int(11) unsigned NOT NULL,
  `Cantidad` int(11) unsigned NOT NULL,
  `FechaOrdenDeProduccion` date NOT NULL,
  `FechaInicio` datetime NOT NULL,
  `Lote` int(11) unsigned NOT NULL,
  `LineaDeProduccion` int(11) unsigned NOT NULL,
  `ActividadConfiguracion` int(10) unsigned NOT NULL,
  `Estado` int(11) unsigned NOT NULL,
  `TipoDePrioridad` int(11) unsigned DEFAULT NULL,
  `Observaciones` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Instrucciones` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  `Persona` int(11) unsigned DEFAULT NULL,
  `OrdenDePrioridad` int(11) DEFAULT NULL,
  `FechaFin` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_OrdenesDeProducciones_Estados` (`Estado`),
  KEY `FK_OrdenesDeProducciones_Articulos` (`Articulo`),
  KEY `FK_OrdenesDeProducciones_Lotes` (`Lote`),
  KEY `FK_OrdenesDeProducciones_TiposDePrioridades` (`TipoDePrioridad`),
  KEY `FK_OrdenesDeProducciones_LineasDeProducciones` (`LineaDeProduccion`),
  KEY `FK_OrdenesDeProducciones_Personas` (`Persona`),
  KEY `FK_OrdenesDeProducciones_ActividadesConfiguraciones` (`ActividadConfiguracion`),
  KEY `FK_OrdenesDeProducciones_ArticulosVersiones` (`ArticuloVersion`),
  CONSTRAINT `FK_OrdenesDeProducciones_ActividadesConfiguraciones` FOREIGN KEY (`ActividadConfiguracion`) REFERENCES `ActividadesConfiguraciones` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_Articulos` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_ArticulosVersiones` FOREIGN KEY (`ArticuloVersion`) REFERENCES `ArticulosVersiones` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_Estados` FOREIGN KEY (`Estado`) REFERENCES `OrdenesDeProduccionesEstados` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_LineasDeProducciones` FOREIGN KEY (`LineaDeProduccion`) REFERENCES `LineasDeProducciones` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_Lotes` FOREIGN KEY (`Lote`) REFERENCES `Lotes` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_OrdenesDeProducciones_TiposDePrioridades` FOREIGN KEY (`TipoDePrioridad`) REFERENCES `TiposDePrioridades` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `OrdenesDeProduccionesDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrdenesDeProduccionesDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `OrdenDeProduccion` int(11) unsigned NOT NULL,
  `ArticuloVersion` int(11) unsigned NOT NULL,
  `Cantidad` decimal(11,2) DEFAULT NULL,
  `Fecha` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `OrdenDeProduccion` (`OrdenDeProduccion`),
  KEY `Articulo` (`ArticuloVersion`),
  CONSTRAINT `OrdenesDeProduccionDetalles_fk` FOREIGN KEY (`OrdenDeProduccion`) REFERENCES `OrdenesDeProducciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `OrdenesDeProduccionesEstados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrdenesDeProduccionesEstados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `EsFinal` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `OrdenesDeProduccionesMmis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `OrdenesDeProduccionesMmis` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `OrdenDeProduccionDetalle` int(11) unsigned NOT NULL,
  `Mmi` int(11) unsigned NOT NULL,
  `CantidadActual` decimal(12,4) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `OrdenDeProduccionDetalle` (`OrdenDeProduccionDetalle`),
  KEY `Mmi` (`Mmi`),
  CONSTRAINT `OrdenesDeProduccionMmis_fk` FOREIGN KEY (`OrdenDeProduccionDetalle`) REFERENCES `OrdenesDeProduccionesDetalles` (`Id`),
  CONSTRAINT `OrdenesDeProduccionMmis_fk1` FOREIGN KEY (`Mmi`) REFERENCES `Mmis` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=31 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Organismos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Organismos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  `Sigla` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TipoDeOrganismo` int(10) unsigned NOT NULL,
  `CodigoAFIP` int(11) unsigned NOT NULL,
  `Activo` tinyint(4) DEFAULT '1',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Sigla` (`Sigla`),
  UNIQUE KEY `Sigla_2` (`Sigla`),
  KEY `FK_Organismos_TiposDeOrganismos` (`TipoDeOrganismo`),
  CONSTRAINT `FK_Organismos_TiposDeOrganismos` FOREIGN KEY (`TipoDeOrganismo`) REFERENCES `TiposDeOrganismos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Paises` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  `CodigoTel` int(11) unsigned NOT NULL,
  `Nacionalidad` varchar(45) NOT NULL DEFAULT '',
  `Afip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `P_fk001` FOREIGN KEY (`Afip`) REFERENCES `AfipPaises` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PaisesCuit`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PaisesCuit` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Pais` int(11) unsigned NOT NULL,
  `AfipCuitPais` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `AfipCuitPais` (`AfipCuitPais`),
  KEY `Pais` (`Pais`),
  CONSTRAINT `PC_fk001` FOREIGN KEY (`AfipCuitPais`) REFERENCES `AfipCuitPaises` (`Id`),
  CONSTRAINT `PC_fk003` FOREIGN KEY (`Pais`) REFERENCES `Paises` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PermisosExportaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PermisosExportaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Comprobante` int(11) unsigned NOT NULL,
  `PaisDestino` int(11) unsigned NOT NULL,
  `NroPermiso` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `PaisDestino` (`PaisDestino`),
  KEY `Comprobante` (`Comprobante`),
  CONSTRAINT `PE_fk001` FOREIGN KEY (`PaisDestino`) REFERENCES `Paises` (`Id`),
  CONSTRAINT `PE_fk002` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Personas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Personas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Dni` int(11) DEFAULT NULL,
  `TipoDeDocumento` int(10) unsigned DEFAULT NULL,
  `RazonSocial` varchar(1024) DEFAULT NULL,
  `Denominacion` varchar(100) DEFAULT NULL,
  `Sexo` int(11) unsigned DEFAULT NULL,
  `Cuit` varchar(13) DEFAULT NULL,
  `EstadoCivil` int(11) unsigned DEFAULT NULL,
  `ModalidadIva` int(11) unsigned DEFAULT NULL,
  `ModalidadGanancia` int(11) unsigned DEFAULT NULL,
  `NroInscripcionIB` varchar(15) DEFAULT NULL,
  `TipoInscripcionIB` int(11) unsigned DEFAULT NULL,
  `PaginaWeb` varchar(250) DEFAULT NULL,
  `LegajoNumero` int(11) DEFAULT NULL,
  `FechaNacimiento` date DEFAULT NULL,
  `FechaDeAlta` date NOT NULL,
  `LimiteDeCredito` int(11) DEFAULT NULL,
  `TransportePorDefecto` int(11) DEFAULT NULL,
  `EsProveedor` tinyint(1) DEFAULT NULL,
  `EsCliente` tinyint(1) DEFAULT NULL,
  `EsVendedor` tinyint(1) DEFAULT NULL,
  `EsTransporte` tinyint(1) DEFAULT NULL,
  `EsEmpleado` tinyint(1) DEFAULT NULL,
  `ClienteBorrar` int(11) DEFAULT NULL,
  `ProveedorBorrar` int(11) DEFAULT NULL,
  `EsFamiliar` tinyint(1) DEFAULT NULL,
  `AntiguedadReconocida` date DEFAULT NULL,
  `AntiguedadReconocidaAFecha` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TipoInscripcionIB` (`TipoInscripcionIB`),
  KEY `ModalidadGanancia` (`ModalidadGanancia`),
  KEY `ModalidadIva` (`ModalidadIva`),
  KEY `EstadoCivil` (`EstadoCivil`),
  KEY `Sexo` (`Sexo`),
  KEY `FK_Personas_6` (`TipoDeDocumento`),
  CONSTRAINT `FK_Personas_6` FOREIGN KEY (`TipoDeDocumento`) REFERENCES `TiposDeDocumentos` (`Id`),
  CONSTRAINT `Personas_fk1` FOREIGN KEY (`TipoInscripcionIB`) REFERENCES `TiposDeInscripcionesIB` (`Id`),
  CONSTRAINT `Personas_fk2` FOREIGN KEY (`ModalidadGanancia`) REFERENCES `TiposDeInscripcionesGanancias` (`Id`),
  CONSTRAINT `Personas_fk3` FOREIGN KEY (`ModalidadIva`) REFERENCES `ModalidadesIVA` (`Id`),
  CONSTRAINT `Personas_fk4` FOREIGN KEY (`Sexo`) REFERENCES `Sexos` (`Id`),
  CONSTRAINT `Personas_fk5` FOREIGN KEY (`EstadoCivil`) REFERENCES `EstadosCiviles` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=90 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasActividades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasActividades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `CodigoActividad` int(11) unsigned NOT NULL,
  `Persona` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `CodigoActividad` (`CodigoActividad`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `PersonasActividades_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 44032 kB; (`Proveedor`) REFER `vidalac/Proveedo';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasAfiliaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasAfiliaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `Organismo` int(11) unsigned NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_PersonasAfiliaciones_Personas` (`Persona`),
  KEY `FK_PersonasAfiliaciones_Organismos` (`Organismo`),
  CONSTRAINT `FK_PersonasAfiliaciones_Organismos` FOREIGN KEY (`Organismo`) REFERENCES `Organismos` (`Id`),
  CONSTRAINT `FK_PersonasAfiliaciones_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=195 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasAfiliacionesAdherentes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasAfiliacionesAdherentes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PersonaAfiliacion` int(11) unsigned NOT NULL,
  `FamiliarPersona` int(11) unsigned NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_PersonasAfiliacionesAdherentes_PersonasAfiliaciones` (`PersonaAfiliacion`),
  KEY `FK_PersonasAfiliacionesAdherentes_Familiares` (`FamiliarPersona`),
  CONSTRAINT `FK_PersonasAfiliacionesAdherentes_FamiliaresPersonas` FOREIGN KEY (`FamiliarPersona`) REFERENCES `FamiliaresPersonas` (`Id`),
  CONSTRAINT `FK_PersonasAfiliacionesAdherentes_PersonasAfiliaciones` FOREIGN KEY (`PersonaAfiliacion`) REFERENCES `PersonasAfiliaciones` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasConceptosImpositivos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasConceptosImpositivos` (
  `Id` int(11) NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned DEFAULT NULL,
  `ConceptoImpositivo` int(11) unsigned NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  `Porcentaje` decimal(11,2) DEFAULT NULL,
  `MontoNoImponible` decimal(11,2) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ConceptoImpositivo` (`ConceptoImpositivo`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `PersonasConceptosImpositivos_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `PersonasConceptosImpositivos_fk1_new` FOREIGN KEY (`ConceptoImpositivo`) REFERENCES `ConceptosImpositivos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=70 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci COMMENT='InnoDB free: 12288 kB; (`Proveedor`) REFER `vidalacFinal/Pro';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasDeduccionesGanancias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasDeduccionesGanancias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `DeduccionGanancia` int(11) unsigned NOT NULL,
  `PeriodoFiscal` int(11) unsigned NOT NULL,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  `VigenteProximoPeriodo` tinyint(1) DEFAULT NULL,
  `MontoAnual` decimal(12,4) DEFAULT NULL,
  `MontoMensual` decimal(12,4) DEFAULT NULL,
  `PeriodoMensual` int(11) unsigned DEFAULT NULL,
  `FechaCarga` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Persona` (`Persona`),
  KEY `DeduccionGanancia` (`DeduccionGanancia`),
  KEY `PeriodoFiscal` (`PeriodoFiscal`),
  KEY `PeriodoMensual` (`PeriodoMensual`),
  CONSTRAINT `PersonasDeduccionesGanancias_fk1` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `PersonasDeduccionesGanancias_fk2` FOREIGN KEY (`DeduccionGanancia`) REFERENCES `DeduccionesGanancias` (`Id`),
  CONSTRAINT `PersonasDeduccionesGanancias_fk3` FOREIGN KEY (`PeriodoFiscal`) REFERENCES `LiquidacionesPeriodos` (`Id`),
  CONSTRAINT `PersonasDeduccionesGanancias_fk4` FOREIGN KEY (`PeriodoMensual`) REFERENCES `LiquidacionesPeriodos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasGananciasDeducciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasGananciasDeducciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `GananciaDeduccion` int(11) unsigned NOT NULL,
  `AnioGanancia` int(11) unsigned NOT NULL,
  `MesDesde` int(11) unsigned NOT NULL,
  `MesHasta` int(10) unsigned NOT NULL,
  `Monto` decimal(12,2) DEFAULT NULL,
  `VigenteProximoPeriodo` tinyint(4) DEFAULT NULL,
  `FechaCarga` datetime DEFAULT NULL,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Familiar` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Persona` (`Persona`),
  KEY `AfipGananciaDeduccion` (`GananciaDeduccion`),
  KEY `LiquidacionPeriodoDesde` (`MesDesde`),
  KEY `LiquidacionPeriodoHasta` (`MesHasta`),
  KEY `GananciaPeriodo` (`AnioGanancia`),
  KEY `Familiar` (`Familiar`),
  CONSTRAINT `PGD_fk001` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `PGD_fk002` FOREIGN KEY (`MesDesde`) REFERENCES `Meses` (`Id`),
  CONSTRAINT `PGD_fk003` FOREIGN KEY (`MesHasta`) REFERENCES `Meses` (`Id`),
  CONSTRAINT `PGD_fk004` FOREIGN KEY (`Familiar`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `PGD_fk006` FOREIGN KEY (`GananciaDeduccion`) REFERENCES `GananciasConceptos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=29 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasGananciasLiquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasGananciasLiquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `Liquidacion` int(11) unsigned NOT NULL,
  `GananciaConcepto` int(11) unsigned NOT NULL,
  `Monto` decimal(12,2) DEFAULT NULL,
  `MontoAcumulado` decimal(12,4) DEFAULT NULL,
  `GananciaMesPeriodo` int(11) NOT NULL,
  `GananciaAnioPeriodo` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `Persona` (`Persona`),
  KEY `LiquidacionPeriodo` (`Liquidacion`),
  KEY `GananciaConcepto` (`GananciaConcepto`),
  CONSTRAINT `PGL_fk001` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `PGL_fk002` FOREIGN KEY (`Liquidacion`) REFERENCES `Liquidaciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=67 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasGananciasPluriempleo`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasGananciasPluriempleo` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `CuitEmpleador` int(11) NOT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date DEFAULT NULL,
  `FechaCarga` datetime DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `PGP_fk002` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasGananciasPluriempleoDetalle`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasGananciasPluriempleoDetalle` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PersonaGananciaPluriempleo` int(11) unsigned NOT NULL,
  `FechaDeLiquidacion` date NOT NULL,
  `RemuneracionBrutaTotal` decimal(12,2) NOT NULL,
  `AporteJubilacion` decimal(12,2) DEFAULT NULL,
  `AporteObraSocial` decimal(12,2) DEFAULT NULL,
  `AporteSindical` decimal(12,2) DEFAULT NULL,
  `ImporteRetribucionesNoHabituales` decimal(12,2) DEFAULT NULL,
  `RetencionGanancias` decimal(12,2) DEFAULT NULL,
  `DevolucionGanancia` decimal(12,4) DEFAULT NULL,
  `Ajustes` decimal(12,2) DEFAULT NULL,
  `FechaCarga` datetime NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `PersonaGananciaPluriempleo` (`PersonaGananciaPluriempleo`),
  CONSTRAINT `PGP_fk001` FOREIGN KEY (`PersonaGananciaPluriempleo`) REFERENCES `PersonasGananciasPluriempleo` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasGananciasPluriempleoPeriodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasGananciasPluriempleoPeriodos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `PersonaGananciaPluriempleo` int(11) unsigned NOT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date DEFAULT NULL,
  `EmpresaQueRetiene` int(11) unsigned NOT NULL,
  `EsEnteRecaudador` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `PersonaGananciaPluriempleo` (`PersonaGananciaPluriempleo`),
  KEY `EmpresaQueRetiene` (`EmpresaQueRetiene`),
  CONSTRAINT `PGPP_fk001` FOREIGN KEY (`PersonaGananciaPluriempleo`) REFERENCES `PersonasGananciasPluriempleo` (`Id`),
  CONSTRAINT `PGPP_fk005` FOREIGN KEY (`EmpresaQueRetiene`) REFERENCES `Empresas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasListasDePrecios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasListasDePrecios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(10) unsigned DEFAULT NULL,
  `Persona` int(11) unsigned DEFAULT NULL,
  `FechaInforme` date DEFAULT NULL,
  `PrecioUltimaCompra` decimal(12,4) DEFAULT NULL,
  `FechaUltimaCompra` date DEFAULT NULL,
  `Observaciones` text,
  `Divisa` int(10) unsigned NOT NULL DEFAULT '1',
  `FacturaCompra` int(10) unsigned DEFAULT NULL,
  `FacturaCompraArticulo` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Divisa` (`Divisa`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `PersonasListasDePrecios_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1565 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`Articulo`) REFER `vidalac/articulos`';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasListasDePreciosInformados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasListasDePreciosInformados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(10) unsigned DEFAULT NULL,
  `Persona` int(10) unsigned DEFAULT NULL,
  `PorCantidad` int(11) DEFAULT NULL,
  `PrecioInformado` decimal(12,2) DEFAULT NULL,
  `FechaInforme` date DEFAULT NULL,
  `FechaVigenciaHasta` date NOT NULL,
  `Observaciones` text,
  `Activo` tinyint(4) unsigned NOT NULL DEFAULT '1',
  `Divisa` int(11) unsigned NOT NULL,
  `ValorDivisa` decimal(12,4) NOT NULL DEFAULT '1.0000',
  PRIMARY KEY (`Id`),
  KEY `Divisa` (`Divisa`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `PersonasListasDePreciosInformados_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`Articulo`) REFER `vidalac/articulos`';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasModalidadesDePagos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasModalidadesDePagos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned DEFAULT NULL,
  `ModalidadDePago` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `ModalidadDePago` (`ModalidadDePago`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `ClientesModalidadesDePagos_fk1_new` FOREIGN KEY (`ModalidadDePago`) REFERENCES `ModalidadesDePagos` (`Id`),
  CONSTRAINT `PersonasModalidadesDePagos_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB; (`Cliente`) REFER `vidalacFinal/Clien';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasRegistrosDePrecios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasRegistrosDePrecios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(10) unsigned NOT NULL,
  `Persona` int(11) unsigned DEFAULT NULL,
  `FechaInforme` date DEFAULT NULL,
  `PrecioUltimo` decimal(12,4) DEFAULT NULL,
  `FechaPrecioUltimo` date DEFAULT NULL,
  `Observaciones` text,
  `TipoDeDivisa` int(10) unsigned NOT NULL DEFAULT '1',
  `Comprobante` int(10) unsigned DEFAULT NULL,
  `ComprobanteDetalle` int(10) unsigned DEFAULT NULL,
  `ValorDivisa` decimal(12,4) NOT NULL,
  `TipoDeRegistroDePrecio` int(11) unsigned DEFAULT NULL,
  `Cantidad` decimal(12,2) DEFAULT NULL,
  `Historico` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Divisa` (`TipoDeDivisa`),
  KEY `Persona` (`Persona`),
  KEY `TipoDeRegistroDePrecio` (`TipoDeRegistroDePrecio`),
  KEY `Articulo` (`Articulo`),
  KEY `Comprobante` (`Comprobante`),
  KEY `ComprobanteDetalle` (`ComprobanteDetalle`),
  CONSTRAINT `PersonasRegistrosDePrecios_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `PersonasRegistrosDePrecios_fk1` FOREIGN KEY (`TipoDeDivisa`) REFERENCES `TiposDeDivisas` (`Id`),
  CONSTRAINT `PersonasRegistrosDePrecios_fk2` FOREIGN KEY (`ComprobanteDetalle`) REFERENCES `ComprobantesDetalles` (`Id`),
  CONSTRAINT `PersonasRegistrosDePrecios_fk3` FOREIGN KEY (`Articulo`) REFERENCES `Articulos` (`Id`),
  CONSTRAINT `PersonasRegistrosDePrecios_fk4` FOREIGN KEY (`Comprobante`) REFERENCES `Comprobantes` (`Id`),
  CONSTRAINT `[OwnerName]_fk[num_for_dup]` FOREIGN KEY (`TipoDeRegistroDePrecio`) REFERENCES `TiposDeRegistrosDePrecios` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PersonasTitulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PersonasTitulos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Titulo` int(11) unsigned NOT NULL,
  `Persona` int(11) unsigned NOT NULL,
  `FechaDeCarga` date NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Titulos_Personas` (`Persona`),
  KEY `FK_PersonasTitulos_Titulos` (`Titulo`),
  CONSTRAINT `FK_PersonasTitulos_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_PersonasTitulos_Titulos` FOREIGN KEY (`Titulo`) REFERENCES `Titulos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PlanesDeCuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PlanesDeCuentas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Padre` int(11) DEFAULT NULL,
  `Descripcion` varchar(200) NOT NULL DEFAULT '',
  `Jerarquia` varchar(50) NOT NULL DEFAULT '',
  `SaldoHabitualDeudor` tinyint(4) NOT NULL DEFAULT '0',
  `Imputable` tinyint(1) unsigned DEFAULT NULL,
  `Grupo` int(11) unsigned DEFAULT NULL,
  `SeOcupa` tinyint(1) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Grupo` (`Grupo`),
  CONSTRAINT `PlanesDeCuentas_fk` FOREIGN KEY (`Grupo`) REFERENCES `PlanesDeCuentasGrupos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=332 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PlanesDeCuentasGrupos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PlanesDeCuentasGrupos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Producciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Producciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Comienzo` datetime DEFAULT NULL,
  `Final` datetime DEFAULT NULL,
  `OrdenDeProduccion` int(11) unsigned NOT NULL,
  `MotivoDeFinalizacion` int(10) unsigned DEFAULT NULL,
  `DescripcionFinalizacion` text CHARACTER SET utf8 COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  KEY `ordenProdForeing` (`OrdenDeProduccion`),
  KEY `FK_Producciones_ProduccionesMotivosDeFinalizaciones` (`MotivoDeFinalizacion`),
  CONSTRAINT `FK_Producciones_ProduccionesMotivosDeFinalizaciones` FOREIGN KEY (`MotivoDeFinalizacion`) REFERENCES `ProduccionesMotivosDeFinalizaciones` (`Id`),
  CONSTRAINT `ordenProdForeing` FOREIGN KEY (`OrdenDeProduccion`) REFERENCES `OrdenesDeProducciones` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ProduccionesMmis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProduccionesMmis` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Produccion` int(10) unsigned NOT NULL,
  `Mmi` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProduccionesMmis_Producciones` (`Produccion`),
  KEY `FK_ProduccionesMmis_Mmis` (`Mmi`),
  CONSTRAINT `FK_ProduccionesMmis_Mmis` FOREIGN KEY (`Mmi`) REFERENCES `Mmis` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ProduccionesMmis_Producciones` FOREIGN KEY (`Produccion`) REFERENCES `Producciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=25 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ProduccionesMmisMovimientos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProduccionesMmisMovimientos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Mmi` int(10) unsigned NOT NULL,
  `Produccion` int(10) unsigned NOT NULL,
  `Cantidad` decimal(12,4) NOT NULL,
  `Fecha` datetime DEFAULT NULL,
  `Tipo` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProduccionesMmisMovimientos_Producciones` (`Produccion`),
  KEY `FK_ProduccionesMmisMovimientos_Mmis` (`Mmi`),
  CONSTRAINT `FK_ProduccionesMmisMovimientos_Mmis` FOREIGN KEY (`Mmi`) REFERENCES `Mmis` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `FK_ProduccionesMmisMovimientos_Producciones` FOREIGN KEY (`Produccion`) REFERENCES `Producciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=48 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ProduccionesMotivosDeFinalizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProduccionesMotivosDeFinalizaciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ProveedoresMarcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProveedoresMarcas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Marca` int(10) unsigned NOT NULL,
  `Proveedor` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_ProveedoresMarcas_1` (`Proveedor`),
  KEY `Marca` (`Marca`),
  CONSTRAINT `ProveedoresMarcas_fk` FOREIGN KEY (`Proveedor`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `ProveedoresMarcas_fk1` FOREIGN KEY (`Marca`) REFERENCES `Marcas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`Proveedor`) REFER `vidalacFinal/Pro';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Provincias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  `Pais` int(11) unsigned NOT NULL,
  `Afip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_Provincias_Paises` (`Pais`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `Provincias_fk` FOREIGN KEY (`Pais`) REFERENCES `Paises` (`Id`) ON UPDATE CASCADE,
  CONSTRAINT `P_fk008` FOREIGN KEY (`Afip`) REFERENCES `AfipProvincias` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=26 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contiene las Provincias de las Tablas relacionales; InnoDB f';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PuntosDeRemitos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PuntosDeRemitos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Numero` int(11) DEFAULT NULL,
  `Imprime` tinyint(4) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `PuntosDeVentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `PuntosDeVentas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Numero` int(11) DEFAULT NULL,
  `Adaptador` int(10) unsigned DEFAULT NULL,
  `Caja` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_PuntosDeVentas_1` (`Adaptador`),
  KEY `fk_PuntosDeVentas_1_idx` (`Caja`),
  CONSTRAINT `FK_PuntosDeVentas_1` FOREIGN KEY (`Adaptador`) REFERENCES `AdaptadoresFiscalizaciones` (`Id`),
  CONSTRAINT `PuntosDeVentas_fk1` FOREIGN KEY (`Caja`) REFERENCES `Cajas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Retenciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Retenciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Fecha` date DEFAULT NULL,
  `Cliente` int(10) unsigned DEFAULT NULL,
  `Proveedor` int(10) unsigned DEFAULT NULL,
  `Monto` decimal(10,2) DEFAULT '0.00',
  `TipoRetencion` int(10) unsigned NOT NULL,
  `Factura` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Retenciones_1` (`Cliente`),
  KEY `FK_Retenciones_2` (`Proveedor`),
  KEY `FK_Retenciones_3` (`TipoRetencion`),
  CONSTRAINT `FK_Retenciones_1` FOREIGN KEY (`Cliente`) REFERENCES `ClientesBORRAR` (`Id`),
  CONSTRAINT `FK_Retenciones_2` FOREIGN KEY (`Proveedor`) REFERENCES `ProveedoresBORRAR` (`Id`),
  CONSTRAINT `FK_Retenciones_3` FOREIGN KEY (`TipoRetencion`) REFERENCES `ConceptosImpositivos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 12288 kB; (`Rol`) REFER `vidalacFinal/Roles`(`I';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `RolesModelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RolesModelos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Rol` int(10) unsigned DEFAULT NULL,
  `Modelo` int(10) unsigned NOT NULL,
  `Ver` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Modificar` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Crear` tinyint(3) unsigned NOT NULL DEFAULT '0',
  `Borrar` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Rol` (`Rol`),
  KEY `FK_RolesModelos_2` (`Modelo`),
  CONSTRAINT `FK_RolesModelos_2` FOREIGN KEY (`Modelo`) REFERENCES `Modelos` (`Id`),
  CONSTRAINT `RolesModelos_fk` FOREIGN KEY (`Rol`) REFERENCES `Roles` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=122 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 12288 kB; (`Modelo`) REFER `vidalacFinal/Modelo';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `RolesModulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `RolesModulos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Rol` int(10) unsigned NOT NULL,
  `Modulo` int(10) unsigned NOT NULL,
  `Privilegio` varchar(45) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Roles` (`Rol`),
  KEY `Modulo` (`Modulo`),
  CONSTRAINT `Modulo` FOREIGN KEY (`Modulo`) REFERENCES `Modulos` (`Id`),
  CONSTRAINT `Roles` FOREIGN KEY (`Rol`) REFERENCES `Roles` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=104 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 12288 kB; (`Modulo`) REFER `vidalacFinal/Modulo';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Servicios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Servicios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Convenio` int(11) unsigned DEFAULT NULL,
  `ConvenioCategoria` int(11) unsigned DEFAULT NULL,
  `CalificacionProfesional` int(11) unsigned NOT NULL,
  `Empresa` int(11) unsigned DEFAULT NULL,
  `Persona` int(11) unsigned NOT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  `TipoDeBaja` int(11) unsigned DEFAULT NULL,
  `ModalidadDeContratacion` int(11) unsigned NOT NULL,
  `TipoDeJornada` int(11) unsigned DEFAULT NULL,
  `Observaciones` text COLLATE utf8_unicode_ci,
  PRIMARY KEY (`Id`),
  KEY `Persona` (`Persona`),
  KEY `Convenio` (`Convenio`),
  KEY `Empresa` (`Empresa`),
  KEY `TipoDeBaja` (`TipoDeBaja`),
  KEY `TipoDeJornada` (`TipoDeJornada`),
  KEY `FK_Servicios_ConveniosCategorias` (`ConvenioCategoria`),
  KEY `FK_Servicios_ModalidadesDeContrataciones` (`ModalidadDeContratacion`),
  KEY `FK_Servicios_ServiciosCalificacionesProfesionales` (`CalificacionProfesional`),
  CONSTRAINT `FK_Servicios_ConveniosCategorias` FOREIGN KEY (`ConvenioCategoria`) REFERENCES `ConveniosCategorias` (`Id`),
  CONSTRAINT `FK_Servicios_ModalidadesDeContrataciones` FOREIGN KEY (`ModalidadDeContratacion`) REFERENCES `ModalidadesDeContrataciones` (`Id`),
  CONSTRAINT `FK_Servicios_ServiciosCalificacionesProfesionales` FOREIGN KEY (`CalificacionProfesional`) REFERENCES `ServiciosCalificacionesProfesionales` (`Id`),
  CONSTRAINT `Servicios_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `Servicios_fk1` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`),
  CONSTRAINT `Servicios_fk2` FOREIGN KEY (`Empresa`) REFERENCES `Empresas` (`Id`),
  CONSTRAINT `Servicios_fk3` FOREIGN KEY (`TipoDeBaja`) REFERENCES `TiposDeBajas` (`Id`),
  CONSTRAINT `Servicios_fk4` FOREIGN KEY (`TipoDeJornada`) REFERENCES `TiposDeJornadas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=106 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ServiciosCalificacionesProfesionales`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiciosCalificacionesProfesionales` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(40) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=21 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ServiciosFeriados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiciosFeriados` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Feriado` int(11) unsigned NOT NULL,
  `Servicio` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_FeriadosPersonas_Feriados` (`Feriado`),
  KEY `FK_FeriadosPersonas_Personas` (`Servicio`),
  CONSTRAINT `FK_ServiciosFeriados_Feriados` FOREIGN KEY (`Feriado`) REFERENCES `Feriados` (`Id`),
  CONSTRAINT `FK_ServiciosFeriados_Servicios` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=9 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ServiciosHorasExtras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiciosHorasExtras` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Servicio` int(11) unsigned NOT NULL,
  `Horas` decimal(4,2) unsigned NOT NULL,
  `Mes` int(10) unsigned NOT NULL,
  `Anio` int(10) unsigned NOT NULL,
  `TipoDeHoraExtra` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_PersonasHorasExtras_TiposDeHorasExtras` (`TipoDeHoraExtra`),
  KEY `FK_PersonasHorasExtras_Personas` (`Servicio`),
  CONSTRAINT `FK_ServiciosHorasExtras_Servicios` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`),
  CONSTRAINT `FK_ServiciosHorasExtras_TiposDeHorasExtras` FOREIGN KEY (`TipoDeHoraExtra`) REFERENCES `TiposDeHorasExtras` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ServiciosHorasTrabajadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiciosHorasTrabajadas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Servicio` int(11) unsigned NOT NULL,
  `LiquidacionPeriodo` int(11) unsigned NOT NULL,
  `CantidadHoras` int(11) unsigned NOT NULL,
  `CantidadHorasFeriadosNoTrabajados` int(11) DEFAULT NULL,
  `CantidadHorasFeriadosTrabajados` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Servicio` (`Servicio`),
  KEY `LiquidacionPeriodo` (`LiquidacionPeriodo`),
  CONSTRAINT `SHT_fk001` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`),
  CONSTRAINT `SHT_fk002` FOREIGN KEY (`LiquidacionPeriodo`) REFERENCES `LiquidacionesPeriodos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ServiciosSituacionesDeRevistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ServiciosSituacionesDeRevistas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `Servicio` int(11) unsigned DEFAULT NULL,
  `ConvenioLicencia` int(11) unsigned DEFAULT NULL,
  `FechaInicio` date NOT NULL,
  `FechaFin` date DEFAULT NULL,
  `SituacionDeRevista` int(11) unsigned NOT NULL,
  `Observaciones` text COLLATE utf8_unicode_ci,
  `Imagen` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ConvenioLicencia` (`ConvenioLicencia`),
  KEY `Servicio` (`Servicio`),
  KEY `Persona` (`Persona`),
  KEY `FK_ServiciosSituacionesDeRevistas_SituacionesDeRevistas` (`SituacionDeRevista`),
  CONSTRAINT `FK_ServiciosSituacionesDeRevistas_ConveniosLicencias` FOREIGN KEY (`ConvenioLicencia`) REFERENCES `ConveniosLicencias` (`Id`),
  CONSTRAINT `FK_ServiciosSituacionesDeRevistas_Personas` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `FK_ServiciosSituacionesDeRevistas_Servicios` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`),
  CONSTRAINT `FK_ServiciosSituacionesDeRevistas_SituacionesDeRevistas` FOREIGN KEY (`SituacionDeRevista`) REFERENCES `SituacionesDeRevistas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=574 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Sexos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Sexos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  `DescripcionR` varchar(1) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `SituacionesDeRevistas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SituacionesDeRevistas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  `CodigoAFIP` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TipoDeSueldo` int(11) unsigned NOT NULL,
  `CuentaParaAntiguedad` tinyint(4) DEFAULT NULL,
  `Activo` tinyint(4) NOT NULL DEFAULT '1',
  `Aplicacion` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TipoDeSueldo` (`TipoDeSueldo`),
  KEY `Aplicacion` (`Aplicacion`),
  CONSTRAINT `SituacionesDeRevistas_fk` FOREIGN KEY (`TipoDeSueldo`) REFERENCES `TiposDeSueldos` (`Id`),
  CONSTRAINT `SituacionesDeRevistas_fk1` FOREIGN KEY (`Aplicacion`) REFERENCES `SituacionesDeRevistasAplicaciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=46 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `SituacionesDeRevistasAplicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `SituacionesDeRevistasAplicaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TarjetasDeCredito`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TarjetasDeCredito` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Numero` bigint(16) unsigned NOT NULL,
  `Persona` int(11) unsigned DEFAULT NULL,
  `TarjetaCreditoMarca` int(11) unsigned DEFAULT NULL,
  `EntidadEmisora` int(11) unsigned DEFAULT NULL,
  `Propia` tinyint(4) DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Numero` (`Numero`),
  KEY `EntidadEmisora` (`EntidadEmisora`),
  KEY `Persona` (`Persona`),
  KEY `TarjetaCreditoMarca` (`TarjetaCreditoMarca`),
  CONSTRAINT `TarjetasDeCredito_fk1` FOREIGN KEY (`EntidadEmisora`) REFERENCES `Bancos` (`Id`),
  CONSTRAINT `TarjetasDeCredito_fk2` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`),
  CONSTRAINT `TarjetasDeCredito_fk3` FOREIGN KEY (`TarjetaCreditoMarca`) REFERENCES `TarjetasDeCreditoMarcas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TarjetasDeCreditoCupones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TarjetasDeCreditoCupones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TarjetaDeCredito` int(11) unsigned DEFAULT NULL,
  `NumeroCupon` int(11) DEFAULT NULL,
  `FechaCupon` datetime NOT NULL,
  `Monto` decimal(12,4) NOT NULL,
  `CantidadDePagos` int(11) NOT NULL,
  `TipoDeMovimiento` int(11) unsigned DEFAULT NULL,
  `Utilizado` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TarjetaDeCredito` (`TarjetaDeCredito`),
  KEY `TarjetasDeCreditoCupones_fk2` (`TipoDeMovimiento`),
  CONSTRAINT `TarjetasDeCreditoCupones_fk1` FOREIGN KEY (`TarjetaDeCredito`) REFERENCES `TarjetasDeCredito` (`Id`),
  CONSTRAINT `TarjetasDeCreditoCupones_fk2` FOREIGN KEY (`TipoDeMovimiento`) REFERENCES `TiposDeMovimientosTarjetas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TarjetasDeCreditoMarcas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TarjetasDeCreditoMarcas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Proveedor` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `fk_TarjetasDeCreditoMarcas_1_idx` (`Proveedor`),
  CONSTRAINT `fk_TarjetasDeCreditoMarcas_1` FOREIGN KEY (`Proveedor`) REFERENCES `Personas` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Telefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Telefonos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned NOT NULL,
  `TipoDeTelefono` int(10) unsigned NOT NULL COMMENT 'De la Casa, De la Empresa, De la Sucursal,Del Deposito,Del negocio,Personal, Celular,Etc.',
  `Caracteristica` varchar(10) DEFAULT NULL,
  `Numero` varchar(15) NOT NULL,
  `Deposito` int(11) unsigned DEFAULT NULL,
  `Transportista` int(11) unsigned DEFAULT NULL,
  `Interno` varchar(20) DEFAULT NULL,
  `Observaciones` text,
  `BancoSucursal` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Deposito` (`Deposito`),
  KEY `TipoTelefono` (`TipoDeTelefono`),
  KEY `Transportista` (`Transportista`),
  KEY `BancoSucursal` (`BancoSucursal`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `FK_Telefonos_TiposDeTelefonos` FOREIGN KEY (`TipoDeTelefono`) REFERENCES `TiposDeTelefonos` (`Id`),
  CONSTRAINT `Telefonos_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`) ON DELETE CASCADE,
  CONSTRAINT `Telefonos_fk1` FOREIGN KEY (`Deposito`) REFERENCES `Depositos_BORRAR` (`Id`),
  CONSTRAINT `Telefonos_fk2` FOREIGN KEY (`BancoSucursal`) REFERENCES `BancosSucursales` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=58 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contiene los telefonos de las Tablas relacionales; InnoDB fr';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Temp_ConveniosCategoriasDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Temp_ConveniosCategoriasDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `ConvenioCategoria` int(11) unsigned NOT NULL,
  `Valor` decimal(12,2) unsigned NOT NULL,
  `ValorNoRemunerativo` decimal(12,2) NOT NULL DEFAULT '0.00',
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=210 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Temp_NovedadesDeLiquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Temp_NovedadesDeLiquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Operacion` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  `FechaCarga` datetime NOT NULL,
  `FechaInicioNovedad` date DEFAULT NULL,
  `FechaFinNovedad` date DEFAULT NULL,
  `IdNovedad` int(11) unsigned NOT NULL,
  `Tabla` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Modelo` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Modificacion` text COLLATE utf8_unicode_ci,
  `Usuario` int(11) unsigned NOT NULL,
  `Estado` int(11) unsigned NOT NULL,
  `Procesado` tinyint(4) DEFAULT NULL,
  `TipoDeNovedad` int(11) unsigned DEFAULT NULL,
  `Descripcion` varchar(500) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Jerarquia` int(11) unsigned DEFAULT NULL,
  `IdJerarquia` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeAlmacenes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeAlmacenes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeAnalisis`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeAnalisis` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeArticulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeArticulos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) CHARACTER SET latin1 NOT NULL DEFAULT '',
  `DescripcionReducida` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `CuentaBase` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeBajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeBajas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeCampos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCampos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='ej: varchar, int, datetime, etc.; InnoDB free: 4096 kB; Inno';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeCheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCheques` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contiene el tipo de cheque. Ejemplo: Posdatado, al dia, etc.';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeComprobantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeComprobantes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL,
  `Ficticio` tinyint(4) NOT NULL DEFAULT '0',
  `Multiplicador` int(10) NOT NULL,
  `Grupo` int(10) unsigned DEFAULT NULL,
  `DiscriminaImpuesto` tinyint(4) DEFAULT NULL,
  `Afip` int(11) unsigned DEFAULT NULL,
  `Codigo` varchar(4) NOT NULL,
  `CompensaCon` int(10) unsigned DEFAULT NULL,
  `TipoDeLetra` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Grupo` (`Grupo`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `TC_fk001` FOREIGN KEY (`Afip`) REFERENCES `AfipTiposDeComprobantes` (`Id`),
  CONSTRAINT `TiposDeComprobantes_fk` FOREIGN KEY (`Grupo`) REFERENCES `TiposDeGruposDeComprobantes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=63 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeConceptos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeConceptos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 69632 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeConceptosLiquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeConceptosLiquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(150) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  `DescripcionR` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `OrdenEjecucion` int(11) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `DescripcionR` (`DescripcionR`),
  UNIQUE KEY `DescripcionR_2` (`DescripcionR`),
  UNIQUE KEY `OrdenEjecucion` (`OrdenEjecucion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeCondicionesDePago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCondicionesDePago` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeControlesDeStock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeControlesDeStock` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeCuentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCuentas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Codigo` varchar(20) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeDirecciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeDirecciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeDivisas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeDivisas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  `CambioActual` decimal(11,2) DEFAULT NULL,
  `SimboloMonetario` varchar(3) DEFAULT NULL,
  `Afip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `TDC_fk001` FOREIGN KEY (`Afip`) REFERENCES `AfipMonedas` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeDocumentos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeDocumentos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Afip` int(10) unsigned DEFAULT NULL,
  `Mostrar` tinyint(3) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `TDC_fk002` FOREIGN KEY (`Afip`) REFERENCES `AfipTiposDeDocumentos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=37 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeEmisoresDeCheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeEmisoresDeCheques` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT=' Propio, De cliente, de terceros; InnoDB free: 4096 kB; Inno';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeEscolaridades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeEscolaridades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeFamiliares`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeFamiliares` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(80) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeFeriados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeFeriados` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(250) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeGruposDeComprobantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeGruposDeComprobantes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL,
  `Codigo` varchar(4) NOT NULL,
  `EsEmitido` smallint(6) DEFAULT NULL,
  `GeneraAsiento` smallint(6) unsigned DEFAULT NULL,
  `TipoDeNumero` int(11) unsigned DEFAULT NULL,
  `FormatoDeNumero` int(11) unsigned DEFAULT NULL,
  `Observaciones` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=19 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeHorasExtras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeHorasExtras` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Convenio` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_TiposDeHorasExtras_Convenios` (`Convenio`),
  CONSTRAINT `FK_TiposDeHorasExtras_Convenios` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeIncrementos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeIncrementos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(20) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeInscripcionesGanancias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeInscripcionesGanancias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeInscripcionesIB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeInscripcionesIB` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeJornadas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeJornadas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `TipoDeSueldo` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `TipoDeSueldo` (`TipoDeSueldo`),
  CONSTRAINT `TiposDeJornadas_fk` FOREIGN KEY (`TipoDeSueldo`) REFERENCES `TiposDeSueldos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeLibrosIVA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeLibrosIVA` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeLineasDeProducciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeLineasDeProducciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeLiquidaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeLiquidaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `DescripcionCorta` varchar(10) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeLiquidacionesPeriodos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeLiquidacionesPeriodos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Codigo` varchar(1) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `Codigo` (`Codigo`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeLiquidacionesTablas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeLiquidacionesTablas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeMontosMinimos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeMontosMinimos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `descripcion` (`Descripcion`),
  UNIQUE KEY `Descripcion_2` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeMovimientosBancarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeMovimientosBancarios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `Id` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeMovimientosCajas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeMovimientosCajas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL,
  `EsDeArqueo` smallint(6) NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeMovimientosTarjetas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeMovimientosTarjetas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci PACK_KEYS=0;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeNovedades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeNovedades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeOrganismos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeOrganismos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDePalets`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDePalets` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDePrioridades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDePrioridades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeRegistrosDePrecios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeRegistrosDePrecios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Codigo` varchar(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `Codigo` (`Codigo`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeRelacionesArticulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeRelacionesArticulos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeSueldos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeSueldos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `PorcentajePago` decimal(11,2) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeTelefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeTelefonos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeTitulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeTitulos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeTransaccionesBancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeTransaccionesBancarias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeUnidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeUnidades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) NOT NULL,
  PRIMARY KEY (`Id`,`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TiposDeVariables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeVariables` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) COLLATE utf8_unicode_ci NOT NULL,
  `Detalle` varchar(1000) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Titulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Titulos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `TipoDeTitulo` int(11) unsigned DEFAULT NULL,
  `TituloNivelAcademico` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_Titulos_TiposDeTitulos` (`TipoDeTitulo`),
  KEY `FK_Titulos_TitulosNivelesAcademicos` (`TituloNivelAcademico`),
  CONSTRAINT `FK_Titulos_TiposDeTitulos` FOREIGN KEY (`TipoDeTitulo`) REFERENCES `TiposDeTitulos` (`Id`),
  CONSTRAINT `FK_Titulos_TitulosNivelesAcademicos` FOREIGN KEY (`TituloNivelAcademico`) REFERENCES `TitulosNivelesAcademicos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TitulosNivelesAcademicos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TitulosNivelesAcademicos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `TransaccionesBancarias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TransaccionesBancarias` (
  `Id` int(11) unsigned zerofill NOT NULL AUTO_INCREMENT,
  `Persona` int(10) unsigned NOT NULL,
  `CtaOrigen` int(11) unsigned DEFAULT NULL,
  `CtaDestino` int(11) unsigned DEFAULT NULL,
  `Monto` decimal(12,2) NOT NULL,
  `Fecha` date DEFAULT NULL,
  `Utilizado` tinyint(1) DEFAULT '0',
  `TipoDeTransaccionBancaria` int(11) unsigned NOT NULL,
  `TipoDeMovimiento` int(11) unsigned NOT NULL,
  `Numero` varchar(20) DEFAULT NULL,
  `Observaciones` text,
  `Caja` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `CtaOrigen` (`CtaOrigen`),
  KEY `CtaDestino` (`CtaDestino`),
  KEY `TipoDeTransaccionBancaria` (`TipoDeTransaccionBancaria`),
  KEY `TipoDeMovimiento` (`TipoDeMovimiento`),
  KEY `FK_TransaccionesBancarias_Personas` (`Persona`),
  KEY `Caja` (`Caja`),
  CONSTRAINT `TransaccionesBancarias_fk` FOREIGN KEY (`CtaOrigen`) REFERENCES `CuentasBancarias` (`Id`),
  CONSTRAINT `TransaccionesBancarias_fk1` FOREIGN KEY (`CtaDestino`) REFERENCES `CuentasBancarias` (`Id`),
  CONSTRAINT `TransaccionesBancarias_fk2` FOREIGN KEY (`Caja`) REFERENCES `Cajas` (`Id`),
  CONSTRAINT `TransaccionesBancarias_fk4` FOREIGN KEY (`TipoDeTransaccionBancaria`) REFERENCES `TiposDeTransaccionesBancarias` (`Id`),
  CONSTRAINT `TransaccionesBancarias_fk5` FOREIGN KEY (`TipoDeMovimiento`) REFERENCES `TiposDeMovimientosBancarios` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=357 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ubicaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ubicaciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(20) DEFAULT NULL,
  `Almacen` int(11) unsigned NOT NULL,
  `Fila` int(10) unsigned NOT NULL,
  `Profundidad` varchar(3) NOT NULL DEFAULT '',
  `Altura` varchar(3) NOT NULL DEFAULT '',
  `Observaciones` text,
  `Existente` tinyint(1) unsigned NOT NULL DEFAULT '1',
  PRIMARY KEY (`Id`),
  KEY `Racks_FKIndex1` (`Almacen`),
  KEY `Profundidad` (`Profundidad`),
  KEY `Altura` (`Altura`),
  CONSTRAINT `Ubicaciones_fk` FOREIGN KEY (`Almacen`) REFERENCES `Almacenes` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=1147 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `UnidadesDeMedidas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UnidadesDeMedidas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '' COMMENT 'Describir el nombre de la medida utilizada',
  `DescripcionR` varchar(5) NOT NULL DEFAULT '',
  `UnidadMinima` decimal(16,4) DEFAULT NULL,
  `EsUnidadMinima` tinyint(1) unsigned DEFAULT NULL,
  `TipoDeUnidad` int(11) unsigned DEFAULT NULL,
  `Afip` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `TipoDeUnidad` (`TipoDeUnidad`),
  KEY `Afip` (`Afip`),
  CONSTRAINT `UDM_fk001` FOREIGN KEY (`Afip`) REFERENCES `AfipUnidadesDeMedidas` (`Id`),
  CONSTRAINT `UnidadesDeMedidas_fk` FOREIGN KEY (`TipoDeUnidad`) REFERENCES `TiposDeUnidades` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Detalle de las unidades de medidas utilizadas';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Usuarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Usuarios` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Nombre` varchar(45) NOT NULL DEFAULT '',
  `Clave` varchar(45) NOT NULL DEFAULT '',
  `Completo` varchar(100) NOT NULL DEFAULT '',
  `GrupoDeUsuario` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Index_2` (`Nombre`),
  KEY `FK_Usuarios_1` (`GrupoDeUsuario`),
  CONSTRAINT `FK_Usuarios_GruposDeUsuarios` FOREIGN KEY (`GrupoDeUsuario`) REFERENCES `GruposDeUsuarios` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=11 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`Rol`) REFER `vidalacFinal/Roles`(`I';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `UsuariosConfiguracionesEscritorios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsuariosConfiguracionesEscritorios` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `ColorFondo` varchar(6) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `ImagenFondo` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `ImagenFondoPosicion` varchar(100) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Usuario` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_UsuariosConfiguracionEscritorio_Usuarios` (`Usuario`),
  CONSTRAINT `FK_UsuariosConfiguracionEscritorio_Usuarios` FOREIGN KEY (`Usuario`) REFERENCES `Usuarios` (`Id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `UsuariosEmails`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsuariosEmails` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Email` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Usuario` int(10) unsigned NOT NULL,
  `Smtp` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Clave` varchar(45) COLLATE utf8_unicode_ci NOT NULL,
  `Google` tinyint(3) unsigned NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `UsuariosEscritorio`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsuariosEscritorio` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Usuario` int(10) unsigned NOT NULL,
  `MenuPrincipal` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Usuario` (`Usuario`),
  KEY `MenuesPrinciaples_fk` (`MenuPrincipal`),
  CONSTRAINT `MenuesPrinciaples_fk` FOREIGN KEY (`MenuPrincipal`) REFERENCES `MenuesPrincipales` (`Id`),
  CONSTRAINT `UsuariosConfiguraciones_fk` FOREIGN KEY (`Usuario`) REFERENCES `Usuarios` (`Id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=45 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 12288 kB; (`MenuPrincipal`) REFER `vidalacFinal';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `UsuariosLogs`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `UsuariosLogs` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Fecha` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
  `Evento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Usuario` int(10) unsigned NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `FK_UsuariosLogs_1` (`Usuario`),
  CONSTRAINT `FK_UsuariosLogs_1` FOREIGN KEY (`Usuario`) REFERENCES `Usuarios` (`Id`) ON UPDATE CASCADE
) ENGINE=InnoDB AUTO_INCREMENT=12106 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `VBancosCuentas`;
/*!50001 DROP VIEW IF EXISTS `VBancosCuentas`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `VBancosCuentas` (
  `Descripcion` varchar(176),
  `CuentaBancariaNumero` varchar(50),
  `Cbu` varchar(22),
  `Persona` int(11) unsigned,
  `Propia` tinyint(1),
  `Banco` varchar(100),
  `CuentaBancariaId` int(11) unsigned,
  `BancoSucursalId` int(10) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `VCuentasBancariasProveedores`;
/*!50001 DROP VIEW IF EXISTS `VCuentasBancariasProveedores`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `VCuentasBancariasProveedores` (
  `Id` int(11) unsigned,
  `Numero` varchar(50),
  `Cbu` varchar(22),
  `Domicilio` varchar(255),
  `Banco` varchar(100),
  `Sucursal` varchar(100),
  `Localidad` varchar(100)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `VFCACantidades`;
/*!50001 DROP VIEW IF EXISTS `VFCACantidades`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `VFCACantidades` (
  `Id` varbinary(23),
  `FacturaCompra` int(11) unsigned,
  `FacturaCompraArticulo` int(11) unsigned,
  `Articulo` int(11) unsigned,
  `CantitadFactura` decimal(10,2),
  `CantitadRemito` decimal(10,2),
  `NumeroRemito` int(11) unsigned,
  `PuntoRemito` int(11)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `VPersonasCuentasCorrientes`;
/*!50001 DROP VIEW IF EXISTS `VPersonasCuentasCorrientes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `VPersonasCuentasCorrientes` (
  `Persona` int(11) unsigned,
  `RazonSocial` varchar(1024),
  `DescripcionComprobante` varchar(50),
  `Descripcion` varchar(50),
  `FechaComprobante` date,
  `Debe` decimal(12,4),
  `Haber` decimal(12,4)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `VSaldoCuentasCorrientes`;
/*!50001 DROP VIEW IF EXISTS `VSaldoCuentasCorrientes`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `VSaldoCuentasCorrientes` (
  `Persona` int(11) unsigned,
  `Debe` decimal(34,4),
  `Haber` decimal(34,4),
  `Saldo` decimal(35,4)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `Variables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Variables` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `Nombre` varchar(50) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Codigo` varchar(20) COLLATE utf8_unicode_ci DEFAULT NULL,
  `TipoDeVariable` int(11) unsigned NOT NULL,
  `TipoDeConceptoLiquidacion` int(11) unsigned DEFAULT NULL,
  `VariableCategoria` int(10) unsigned DEFAULT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  `Activo` tinyint(4) NOT NULL DEFAULT '1',
  `TipoDeConcepto` int(11) unsigned DEFAULT NULL,
  `NoHabitual` tinyint(4) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `TipoDeConceptoLiquidacion` (`TipoDeConceptoLiquidacion`),
  KEY `TipoDeVariable` (`TipoDeVariable`),
  KEY `FK_Variables_VariablesCategorias` (`VariableCategoria`),
  KEY `TipoDeDeduccion` (`TipoDeConcepto`),
  CONSTRAINT `FK_Variables_VariablesCategorias` FOREIGN KEY (`VariableCategoria`) REFERENCES `VariablesCategorias` (`Id`),
  CONSTRAINT `Variables_fk` FOREIGN KEY (`TipoDeVariable`) REFERENCES `TiposDeVariables` (`Id`),
  CONSTRAINT `Variables_fk1` FOREIGN KEY (`TipoDeConceptoLiquidacion`) REFERENCES `TiposDeConceptosLiquidaciones` (`Id`),
  CONSTRAINT `V_fk001` FOREIGN KEY (`TipoDeConcepto`) REFERENCES `VariablesTiposDeConceptos` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=114 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `VariablesCategorias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VariablesCategorias` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `VariablesDetalles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VariablesDetalles` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Variable` int(11) unsigned NOT NULL,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci DEFAULT NULL,
  `Convenio` int(11) unsigned DEFAULT NULL,
  `Empresa` int(11) unsigned DEFAULT NULL,
  `ConvenioCategoria` int(11) unsigned DEFAULT NULL,
  `GrupoDePersona` int(11) unsigned DEFAULT NULL,
  `Servicio` int(11) unsigned DEFAULT NULL,
  `FechaDesde` date NOT NULL,
  `FechaHasta` date DEFAULT NULL,
  `Formula` text COLLATE utf8_unicode_ci NOT NULL,
  `FormulaDetalle` text COLLATE utf8_unicode_ci,
  `Selector` text COLLATE utf8_unicode_ci,
  `Obseraciones` text COLLATE utf8_unicode_ci,
  `VariableJerarquia` int(10) unsigned DEFAULT NULL,
  `FechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `ConceptoGenerico` (`Variable`),
  KEY `Empresa` (`Empresa`),
  KEY `Convenio` (`Convenio`),
  KEY `ConvenioCategoria` (`ConvenioCategoria`,`Id`),
  KEY `ConvenioCategoria_2` (`ConvenioCategoria`),
  KEY `GrupoDePersonas` (`GrupoDePersona`),
  KEY `FK_VariablesDetalles_Servicios` (`Servicio`),
  KEY `FK_VariablesDetalles_VariablesJerarquias` (`VariableJerarquia`),
  CONSTRAINT `Conceptos_fk1` FOREIGN KEY (`Empresa`) REFERENCES `Empresas` (`Id`),
  CONSTRAINT `Conceptos_fk2` FOREIGN KEY (`Convenio`) REFERENCES `Convenios` (`Id`),
  CONSTRAINT `Conceptos_fk3` FOREIGN KEY (`ConvenioCategoria`) REFERENCES `ConveniosCategorias` (`Id`),
  CONSTRAINT `FK_VariablesDetalles_GruposDePersonas` FOREIGN KEY (`GrupoDePersona`) REFERENCES `GruposDePersonas` (`Id`),
  CONSTRAINT `FK_VariablesDetalles_Servicios` FOREIGN KEY (`Servicio`) REFERENCES `Servicios` (`Id`),
  CONSTRAINT `FK_VariablesDetalles_VariablesJerarquias` FOREIGN KEY (`VariableJerarquia`) REFERENCES `VariablesJerarquias` (`Id`),
  CONSTRAINT `VariablesDetalles_fk` FOREIGN KEY (`Variable`) REFERENCES `Variables` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=452 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `VariablesJerarquias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VariablesJerarquias` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(150) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `VariablesTiposDeConceptos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `VariablesTiposDeConceptos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  `TipoDeConceptoLiquidacion` int(11) unsigned DEFAULT NULL,
  `GananciaConcepto` int(11) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  KEY `TipoDeConceptoLiquidacion` (`TipoDeConceptoLiquidacion`),
  KEY `GananciaConcepto` (`GananciaConcepto`),
  CONSTRAINT `VTC_fk001` FOREIGN KEY (`GananciaConcepto`) REFERENCES `GananciasConceptos` (`Id`),
  CONSTRAINT `VTDC_fk001` FOREIGN KEY (`TipoDeConceptoLiquidacion`) REFERENCES `TiposDeConceptosLiquidaciones` (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=16 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver_________Bienes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver_________Bienes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  `BienTipo` int(10) unsigned NOT NULL,
  `EsInsumoORepuesto` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `BienTipo` (`BienTipo`),
  CONSTRAINT `FK_Bienes_BienesTipos` FOREIGN KEY (`BienTipo`) REFERENCES `Ver_________BienesTipos` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver_________BienesCaracteristicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver_________BienesCaracteristicas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Bien` int(11) unsigned NOT NULL,
  `Caracteristica` int(11) unsigned NOT NULL,
  `EnDesuso` tinyint(1) unsigned NOT NULL DEFAULT '0',
  PRIMARY KEY (`Id`),
  KEY `fk_BienesCaracteristicas_Caracteristicas` (`Caracteristica`),
  KEY `fk_BienesCaracteristicas_Bienes` (`Bien`),
  CONSTRAINT `FK_BienesCaracteristicas_Bienes` FOREIGN KEY (`Bien`) REFERENCES `Ver_________Bienes` (`Id`),
  CONSTRAINT `FK_BienesCaracteristicas_Caracteristicas` FOREIGN KEY (`Caracteristica`) REFERENCES `Caracteristicas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver_________BienesDelInventario`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver_________BienesDelInventario` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  `Bien` int(11) unsigned NOT NULL,
  `CodigoDeBarra` int(11) DEFAULT NULL,
  `FechaAlta` date NOT NULL,
  `FechaBaja` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_InventarioGeneral_Bienes` (`Bien`),
  CONSTRAINT `FK_BienesDelInventario_Bienes` FOREIGN KEY (`Bien`) REFERENCES `Ver_________Bienes` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver_________BienesDelInventarioCaracteristicas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver_________BienesDelInventarioCaracteristicas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `BienDelInventario` int(11) unsigned NOT NULL,
  `BienCaracteristica` int(11) unsigned NOT NULL,
  `Valor` varchar(500) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_BienesDelInventarioCaracteristicas_BienesDelInventario` (`BienDelInventario`),
  KEY `fk_BienesDelInventarioCaracteristicas_BienesCaracteristicas` (`BienCaracteristica`),
  CONSTRAINT `BienesDelInventarioCaracteristicas_fk` FOREIGN KEY (`BienCaracteristica`) REFERENCES `Ver_________BienesCaracteristicas` (`Id`),
  CONSTRAINT `BienesDelInventarioCaracteristicas_fk1` FOREIGN KEY (`BienDelInventario`) REFERENCES `Ver_________BienesDelInventario` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver_________BienesTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver_________BienesTipos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver_________CuentasCorrientesImputacionesComprobantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver_________CuentasCorrientesImputacionesComprobantes` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Imputacion` varchar(1) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________FormulasInsumos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________FormulasInsumos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Articulo` int(11) unsigned NOT NULL,
  `Insumo` int(11) unsigned NOT NULL,
  `Cantidad` decimal(12,6) DEFAULT NULL,
  `UnidadDeMedida` int(11) unsigned NOT NULL,
  `Precio` decimal(12,4) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Articulo` (`Articulo`),
  KEY `UnidadDeMedida` (`UnidadDeMedida`),
  KEY `Insumo` (`Insumo`),
  CONSTRAINT `FormulasInsumos_fk2` FOREIGN KEY (`UnidadDeMedida`) REFERENCES `UnidadesDeMedidas` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________SubDiarioDeIva`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________SubDiarioDeIva` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `TipoSubDiario` int(11) unsigned DEFAULT NULL,
  `Periodo` int(11) unsigned DEFAULT NULL,
  `Actividad` int(11) unsigned DEFAULT NULL,
  `Jurisdiccion` int(11) unsigned DEFAULT NULL,
  `Cliente` int(11) unsigned DEFAULT NULL,
  `Proveedor` int(11) unsigned DEFAULT NULL,
  `ModaliadadIVA` int(11) unsigned DEFAULT NULL,
  `CUIT` varchar(13) DEFAULT NULL,
  `Fecha` date DEFAULT NULL,
  `Comprobante` int(11) unsigned DEFAULT NULL,
  `TipoComprobante` int(11) unsigned DEFAULT NULL,
  `TipoDeCompra` int(11) DEFAULT NULL,
  `NumeroComprobante` varchar(40) DEFAULT NULL,
  `Neto27%` decimal(12,2) DEFAULT NULL,
  `Neto21%` decimal(12,2) DEFAULT NULL,
  `Neto10,5%` decimal(12,2) DEFAULT NULL,
  `SubTotalNeto` decimal(12,2) DEFAULT NULL,
  `Exento` decimal(12,2) DEFAULT NULL,
  `CpasaRNI` decimal(12,2) DEFAULT NULL,
  `CpasaRS` decimal(12,2) DEFAULT NULL,
  `Internos` decimal(12,2) DEFAULT NULL,
  `IVA27%` decimal(12,2) DEFAULT NULL,
  `IVA21%` decimal(12,2) DEFAULT NULL,
  `IVA10,5%` decimal(12,2) DEFAULT NULL,
  `SubTotalIVA` decimal(12,2) DEFAULT NULL,
  `Acre.13,5%` decimal(12,2) DEFAULT NULL,
  `Acre10,5%` decimal(12,2) DEFAULT NULL,
  `Acre.5,25%` decimal(12,2) DEFAULT NULL,
  `SubTotalAcre.` decimal(12,2) DEFAULT NULL,
  `Percep.IVA` decimal(12,2) DEFAULT NULL,
  `Retenc.IVA` decimal(12,2) DEFAULT NULL,
  `Percep.IB` decimal(12,2) DEFAULT NULL,
  `BaseRet.IB` decimal(12,2) DEFAULT NULL,
  `Retenc.IB` decimal(12,2) DEFAULT NULL,
  `Percep.IG` decimal(12,2) DEFAULT NULL,
  `Retenc.IG` decimal(12,2) DEFAULT NULL,
  `Retenc.SUSS` decimal(12,2) DEFAULT NULL,
  `Otros` decimal(12,2) DEFAULT NULL,
  `TotalRetPerc` decimal(12,2) DEFAULT NULL,
  `OtrosS/I` int(11) DEFAULT NULL,
  `VtaBienDeUso` tinyint(1) DEFAULT NULL,
  `G.Imputable` tinyint(1) DEFAULT NULL,
  `ImponibleIB` decimal(12,2) DEFAULT NULL,
  `ExentoIB` decimal(12,2) DEFAULT NULL,
  `Renta` int(11) DEFAULT NULL,
  `TipoCompraIG` int(11) DEFAULT NULL,
  `ImponibleIG` decimal(12,2) DEFAULT NULL,
  `DeducibleIG` decimal(12,2) DEFAULT NULL,
  `NoDeducibleIB` decimal(12,2) DEFAULT NULL,
  `ExentoIG` decimal(12,2) DEFAULT NULL,
  `RubroC/V` int(11) DEFAULT NULL,
  `ActividadIB` int(11) DEFAULT NULL,
  `NumeroIB` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `Periodo` (`Periodo`),
  KEY `Actividad` (`Actividad`),
  KEY `Jurisdiccion` (`Jurisdiccion`),
  KEY `Cliente` (`Cliente`),
  KEY `Proveedor` (`Proveedor`),
  KEY `ModaliadadIVA` (`ModaliadadIVA`),
  KEY `Comprobante` (`Comprobante`),
  KEY `TipoSubDiario` (`TipoSubDiario`),
  KEY `TipoComprobante` (`TipoComprobante`),
  CONSTRAINT `SubDiarioDeIva_fk` FOREIGN KEY (`Periodo`) REFERENCES `LibrosIVA` (`Id`),
  CONSTRAINT `SubDiarioDeIva_fk2` FOREIGN KEY (`Jurisdiccion`) REFERENCES `Provincias` (`Id`),
  CONSTRAINT `SubDiarioDeIva_fk3` FOREIGN KEY (`Cliente`) REFERENCES `ClientesBORRAR` (`Id`),
  CONSTRAINT `SubDiarioDeIva_fk4` FOREIGN KEY (`Proveedor`) REFERENCES `ProveedoresBORRAR` (`Id`),
  CONSTRAINT `SubDiarioDeIva_fk5` FOREIGN KEY (`ModaliadadIVA`) REFERENCES `ModalidadesIVA` (`Id`),
  CONSTRAINT `SubDiarioDeIva_fk6` FOREIGN KEY (`Comprobante`) REFERENCES `TiposDeComprobantes` (`Id`),
  CONSTRAINT `SubDiarioDeIva_fk7` FOREIGN KEY (`TipoSubDiario`) REFERENCES `Ver__________TiposDeSubDiarios` (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________TiposDeCostos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________TiposDeCostos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________TiposDeExportaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________TiposDeExportaciones` (
  `Id` int(10) unsigned NOT NULL,
  `Descripcion` varchar(250) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Desde` date NOT NULL,
  `Hasta` date NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________TiposDeLetras`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________TiposDeLetras` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(1) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________TiposDeNumeros`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________TiposDeNumeros` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) NOT NULL,
  PRIMARY KEY (`Id`),
  UNIQUE KEY `Descripcion` (`Descripcion`),
  UNIQUE KEY `Descripcion_2` (`Descripcion`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Ver__________TiposDeSubDiarios`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Ver__________TiposDeSubDiarios` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `Workflows`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Workflows` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  `TipoEntrada` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `WorkflowsEvents`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WorkflowsEvents` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `Event` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci NOT NULL,
  `TipoSalida` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `WorkflowsPubSub`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WorkflowsPubSub` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Workflow` int(10) unsigned DEFAULT NULL,
  `Event` int(10) unsigned DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `workflow` (`Workflow`),
  KEY `workflowevent` (`Event`),
  CONSTRAINT `workflowevent` FOREIGN KEY (`Event`) REFERENCES `WorkflowsEvents` (`Id`) ON DELETE NO ACTION ON UPDATE NO ACTION
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `WorkflowsVersiones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `WorkflowsVersiones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Workflow` int(10) unsigned NOT NULL,
  `Logica` blob NOT NULL,
  `Version` int(11) NOT NULL,
  PRIMARY KEY (`Id`),
  KEY `workflow` (`Workflow`)
) ENGINE=InnoDB AUTO_INCREMENT=2 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ZonasDeVentas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZonasDeVentas` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `ZonasPorPersonas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ZonasPorPersonas` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Persona` int(11) unsigned DEFAULT NULL,
  `ZonaDeVenta` int(10) unsigned DEFAULT NULL,
  `FechaDesde` date DEFAULT NULL,
  `FechaHasta` date DEFAULT NULL,
  PRIMARY KEY (`Id`),
  KEY `fk_ZonasPorVendedores_ZonasDeVentas_new` (`ZonaDeVenta`),
  KEY `Persona` (`Persona`),
  CONSTRAINT `fk_ZonasPorVendedores_ZonasDeVentas_new` FOREIGN KEY (`ZonaDeVenta`) REFERENCES `ZonasDeVentas` (`Id`),
  CONSTRAINT `ZonasPorPersonas_fk` FOREIGN KEY (`Persona`) REFERENCES `Personas` (`Id`) ON DELETE CASCADE
) ENGINE=InnoDB DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; (`Vendedor`) REFER `vidalac/vendedores';
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_CUITPais`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_CUITPais` (
  `CUIT Pais` double DEFAULT NULL,
  `Descrip CUIT Pais` varchar(255) DEFAULT NULL,
  `Cod# Tipo Sujeto` double DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL,
  `F5` varchar(255) DEFAULT NULL,
  `F6` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_ConceptosIncluidos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_ConceptosIncluidos` (
  `Código` double DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_DocumentoidComprador`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_DocumentoidComprador` (
  `Código` double DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Idiomas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Idiomas` (
  `Código` double DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Incoterms`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Incoterms` (
  `Código` varchar(255) DEFAULT NULL,
  `Descripcion` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Monedas`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Monedas` (
  `Código` varchar(255) DEFAULT NULL,
  `Moneda` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Operacion_condicionIVA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Operacion_condicionIVA` (
  `Código` double DEFAULT NULL,
  `Descripción` varchar(20) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL,
  `F5` varchar(255) DEFAULT NULL,
  `F6` varchar(255) DEFAULT NULL,
  `F7` varchar(255) DEFAULT NULL,
  `F8` varchar(255) DEFAULT NULL,
  `F9` varchar(255) DEFAULT NULL,
  `F10` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Otrostributos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Otrostributos` (
  `Código` varchar(255) DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Paises`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Paises` (
  `Código` double DEFAULT NULL,
  `Denominación` varchar(255) DEFAULT NULL,
  `País` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Provincias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Provincias` (
  `Código` double DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_TiposDeSujetos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_TiposDeSujetos` (
  `Codigo` double DEFAULT NULL,
  `Descripcion` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Tiposdecomprobantes`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Tiposdecomprobantes` (
  `Código` varchar(255) DEFAULT NULL,
  `Denominación` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Tiposderesponsables`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Tiposderesponsables` (
  `Código` double DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL,
  `F3` varchar(255) DEFAULT NULL,
  `F4` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `borrar_Unidadesdemedida`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `borrar_Unidadesdemedida` (
  `Codigo` varchar(255) DEFAULT NULL,
  `Descripción` varchar(255) DEFAULT NULL
) ENGINE=InnoDB DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `temp_fechasCierre`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `temp_fechasCierre` (
  `Id` int(10) unsigned NOT NULL DEFAULT '0',
  `Fecha` timestamp NOT NULL DEFAULT '0000-00-00 00:00:00',
  `Evento` varchar(255) COLLATE utf8_unicode_ci NOT NULL,
  `Usuario` int(10) unsigned NOT NULL
) ENGINE=MyISAM DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;
DROP TABLE IF EXISTS `vArticulosArbol`;
/*!50001 DROP VIEW IF EXISTS `vArticulosArbol`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vArticulosArbol` (
  `Id` int(11) unsigned,
  `Articulo` int(11) unsigned,
  `Version` int(11) unsigned,
  `Fecha` date,
  `Descripcion` varchar(255),
  `Padre` int(11) unsigned
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vRelFacturasArticulosOrdenesArticulos`;
/*!50001 DROP VIEW IF EXISTS `vRelFacturasArticulosOrdenesArticulos`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vRelFacturasArticulosOrdenesArticulos` (
  `Id` int(11) unsigned,
  `FAID` int(11) unsigned,
  `Numero` varbinary(18),
  `Cantidad` decimal(10,2),
  `PrecioUnitario` decimal(12,4)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
DROP TABLE IF EXISTS `vTransportesUsados`;
/*!50001 DROP VIEW IF EXISTS `vTransportesUsados`*/;
SET @saved_cs_client     = @@character_set_client;
SET character_set_client = utf8;
/*!50001 CREATE TABLE `vTransportesUsados` (
  `Id` int(11) unsigned,
  `RazonSocialRetira` varchar(1024),
  `RazonSocialEntrega` varchar(1024)
) ENGINE=MyISAM */;
SET character_set_client = @saved_cs_client;
/*!50003 DROP FUNCTION IF EXISTS `fAdmin_UnirPersonas` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fAdmin_UnirPersonas`(
        PersonaPorEliminarId INTEGER(11),
        PersonaQueQuedaId INTEGER(11)
    ) RETURNS int(11)
BEGIN 
update 	AreasDeTrabajosPersonas 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	Cheques 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	Comprobantes 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	Comprobantes 
set 	TransportistaRetiroDeOrigen = PersonaQueQuedaId
where 	TransportistaRetiroDeOrigen = PersonaPorEliminarId;
update 	Comprobantes 
set 	TransportistaEntregoEnDestino = PersonaQueQuedaId
where 	TransportistaEntregoEnDestino = PersonaPorEliminarId;
update 	CuentasBancarias 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	CuentasCorrientes 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	Direcciones 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	Emails 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	GeneradorDeCheques 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	LibrosIVADetalles 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	LineasDeProduccionesPersonas 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	Lotes 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	OrdenesDeProducciones 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	PedidosDeCotizaciones 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	PersonasActividades 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	PersonasConceptosImpositivos 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	PersonasListasDePrecios 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	PersonasListasDePreciosInformados 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	PersonasModalidadesDePagos 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	ProveedoresMarcas 
set 	Proveedor = PersonaQueQuedaId
where 	Proveedor = PersonaPorEliminarId;
update 	Telefonos 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
update 	ZonasPorPersonas 
set 	Persona = PersonaQueQuedaId
where 	Persona = PersonaPorEliminarId;
RETURN 1; 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fArticuloPackagingDescripcion` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fArticuloPackagingDescripcion`(Articulo INT) RETURNS varchar(250) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE Packaging4 varchar(100);
    DECLARE Packaging3 varchar(100);
    DECLARE Packaging2 varchar(100);
    DECLARE Packaging1 varchar(100);
    DECLARE Packaging varchar(100);
    DECLARE Producto varchar(100);
    DECLARE Cantidad4 int;
    DECLARE Cantidad3 int;
    DECLARE Cantidad2 int;
    DECLARE Cantidad1 int;
    DECLARE Cantidad decimal(12,2);
    DECLARE UnidadDeMedida varchar(100);
    DECLARE Descripcion varchar(250);
    SELECT  P0.Descripcion,
            P.DescripcionReducida, A.Cantidad,U.Descripcion,
            P1.DescripcionReducida, A.CantidadPorPackaging1,
            P2.DescripcionReducida, A.CantidadPorPackaging2,
            P3.DescripcionReducida, A.CantidadPorPackaging3,
            P4.DescripcionReducida, A.CantidadPorPackaging4
             into  Producto,
                   Packaging,Cantidad,UnidadDeMedida,
                   Packaging1,Cantidad1,
                   Packaging2,Cantidad2,
                   Packaging3,Cantidad3,
                   Packaging4,Cantidad4
    FROM Articulos A
      left outer join Productos P
        on A.Packaging = P.Id
      left outer join Productos P1
        on A.Packaging1 = P1.Id
      left outer join Productos P2
        on A.Packaging2 = P2.Id
      left outer join Productos P3
        on A.Packaging3 = P3.Id
      left outer join Productos P4
        on A.Packaging4 = P4.Id
      left outer join UnidadesDeMedidas U
        on A.UnidadDeMedida = U.Id
      left outer join Productos P0
        on A.Producto = P0.Id
    where A.Id = Articulo;
    set Descripcion = "";
    IF Packaging4 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging4, ' de ',Cantidad4);
    END IF;
    IF Packaging3 is not null then
       
        set Descripcion = Concat(Descripcion,' ',Packaging3, ' de ',Cantidad3);
    END IF;
    IF Packaging2 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging2, ' de ',Cantidad2);
    END IF;
    IF Packaging1 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging1, ' de ',Cantidad1);
    END IF;
    IF Packaging is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging);
    END IF;
    IF Cantidad is not null then
        set Descripcion = Concat(Descripcion,' de ',cast(Cantidad as char));
    END IF;
    IF UnidadDeMedida is not null then
        set Descripcion = Concat(Descripcion,' ',UnidadDeMedida);
    END IF;
  RETURN Descripcion;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fArticulosDescripcionCompleta` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fArticulosDescripcionCompleta`(Articulo INT) RETURNS varchar(250) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE Packaging4 varchar(100);
    DECLARE Packaging3 varchar(100);
    DECLARE Packaging2 varchar(100);
    DECLARE Packaging1 varchar(100);
    DECLARE Packaging varchar(100);
    DECLARE Producto varchar(100);
    DECLARE Cantidad4 int;
    DECLARE Cantidad3 int;
    DECLARE Cantidad2 int;
    DECLARE Cantidad1 int;
    DECLARE Cantidad decimal(12,2);
    DECLARE UnidadDeMedida varchar(100);
    DECLARE Descripcion varchar(250);
    SELECT  P0.Descripcion,
            P.DescripcionReducida, A.Cantidad,U.Descripcion,
            P1.DescripcionReducida, A.CantidadPorPackaging1,
            P2.DescripcionReducida, A.CantidadPorPackaging2,
            P3.DescripcionReducida, A.CantidadPorPackaging3,
            P4.DescripcionReducida, A.CantidadPorPackaging4
             into  Producto,
                   Packaging,Cantidad,UnidadDeMedida,
                   Packaging1,Cantidad1,
                   Packaging2,Cantidad2,
                   Packaging3,Cantidad3,
                   Packaging4,Cantidad4
    FROM Articulos A
      left outer join Productos P
        on A.Packaging = P.Id
      left outer join Productos P1
        on A.Packaging1 = P1.Id
      left outer join Productos P2
        on A.Packaging2 = P2.Id
      left outer join Productos P3
        on A.Packaging3 = P3.Id
      left outer join Productos P4
        on A.Packaging4 = P4.Id
      left outer join UnidadesDeMedidas U
        on A.UnidadDeMedida = U.Id
      left outer join Productos P0
        on A.Producto = P0.Id
    where A.Id = Articulo;
    IF Producto is not null then
        set Descripcion = concat(Producto," en ");
    END IF;
    IF Packaging4 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging4, ' de ',Cantidad4);
    END IF;
    IF Packaging3 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging3, ' de ',Cantidad3);
    END IF;
    IF Packaging2 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging2, ' de ',Cantidad2);
    END IF;
    IF Packaging1 is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging1, ' de ',Cantidad1);
    END IF;
    IF Packaging is not null then
        set Descripcion = Concat(Descripcion,' ',Packaging);
    END IF;
    IF Cantidad is not null then
        set Descripcion = Concat(Descripcion,' de ',cast(Cantidad as char));
    END IF;
    IF UnidadDeMedida is not null then
        set Descripcion = Concat(Descripcion,' ',UnidadDeMedida);
    END IF;
  RETURN Descripcion;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fArticulosRequeridosProduccionAFecha` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fArticulosRequeridosProduccionAFecha`(
        idArticulo INT,
        fecha datetime
    ) RETURNS decimal(12,4)
BEGIN
    DECLARE total DECIMAL(19,4);
    SELECT 	ifnull(sum(d.Cantidad),0) into total 
    FROM 	OrdenesDeProduccionesDetalles d  
            inner join OrdenesDeProducciones o on o.Id = d.OrdenDeProduccion
    where 	d.ArticuloVersion in (Select Id from ArticulosVersiones where Articulo = idArticulo)
    and 	o.Estado = 2 
    and 	o.FechaInicio <= fecha;
    return total;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fArticuloStockPorCantidad` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fArticuloStockPorCantidad`(idArticulo INT,cantidad Decimal(12,4)) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
	DECLARE uMedida DECIMAL(19,4);
	DECLARE total DECIMAL(19,4);
  Select UnidadMinima into uMedida 
    from 
        UnidadesDeMedidas um Inner Join 
        Productos p on p.UnidadDeMedida = um.Id inner Join
        Articulos a on a.Producto = p.Id and a.Id = idArticulo;
    
  SELECT 
			  cantidad *
			  IF(A.CantidadPorPackaging1 is null,1,A.CantidadPorPackaging1) *
			  IF(A.CantidadPorPackaging2 is null,1,A.CantidadPorPackaging2) *
			  IF(A.CantidadPorPackaging3 is null,1,A.CantidadPorPackaging3) *
			  IF(A.CantidadPorPackaging4 is null,1,A.CantidadPorPackaging4) *
			  (A.Cantidad * Um.UnidadMinima / uMedida)
		   into total
   FROM
			Articulos A join UnidadesDeMedidas Um on A.UnidadDeMedida = Um.Id
   Where A.Id = idArticulo ;
  
  RETURN total;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCantSinAsociarRelHijo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCantSinAsociarRelHijo`(idComprobante INT, Articulo INT) RETURNS decimal(10,0)
    READS SQL DATA
BEGIN
    DECLARE usado DECIMAL;
    select 	ifnull(sum(CRD.Cantidad),0) into usado
    from	`ComprobantesRelacionados` CR,
    		`ComprobantesRelacionadosDetalles` CRD
    where	CR.Id = CRD.ComprobanteRelacionado
    and		CR.ComprobanteHijo = idComprobante
    and		CRD.Articulo = Articulo;     
            
  RETURN usado;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCompPago_Monto_aPagar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCompPago_Monto_aPagar`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
DECLARE Monto 				DECIMAL(12,4);
DECLARE GrupoPadre 			INTEGER;
DECLARE MultiplicadorPadre 	INTEGER;
DECLARE Cerrado 			INTEGER;
	
select 	TC.Grupo, C.Cerrado
into	GrupoPadre, Cerrado
from 	TiposDeComprobantes TC
		inner join Comprobantes C 		on C.TipoDeComprobante = TC.Id
where	C.Id = idComprobante;
IF (GrupoPadre = 11) THEN 
	set MultiplicadorPadre = -1;
ELSE
	IF (GrupoPadre = 9) THEN 
    	set MultiplicadorPadre = 1;
    ELSE
     	set MultiplicadorPadre = 0;
    END IF;
END IF;
if Cerrado = 0 then
	
      	select 	sum(fComprobante_Monto_Disponible(CR.ComprobanteHijo)*TC.Multiplicador*MultiplicadorPadre)
      	into 	Monto
      	from 	ComprobantesRelacionados CR
              	inner join Comprobantes CH 			on CH.Id = CR.ComprobanteHijo
              	inner join TiposDeComprobantes TC	on TC.Id = CH.TipoDeComprobante
      	where	CR.ComprobantePadre = idComprobante;
Else
		
		select 	sum(CR.MontoAsociado*TC.Multiplicador*MultiplicadorPadre)
		into 	Monto
		from 	ComprobantesRelacionados CR
				inner join Comprobantes CH 			on CH.Id = CR.ComprobanteHijo
		        inner join TiposDeComprobantes TC	on TC.Id = CH.TipoDeComprobante
		where	CR.ComprobantePadre = idComprobante;
end if;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCompPago_Monto_Pagado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCompPago_Monto_Pagado`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
DECLARE Monto 		DECIMAL(12,4);
SELECT 	sum(IFNULL(CD.PrecioUnitario,0.0000)) 
INTO 	Monto
FROM 	ComprobantesDetalles CD 
WHERE 	CD.Comprobante = idComprobante;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCompPago_Monto_Pagado_con_CI` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCompPago_Monto_Pagado_con_CI`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
DECLARE Monto 		DECIMAL(12,4);
SELECT 	SUM(IFNULL(CD.PrecioUnitario,0.0000)) 
INTO 	Monto
FROM 	ComprobantesDetalles CD 
WHERE 	CD.ConceptoImpositivo is not null and CD.Comprobante = idComprobante;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCompPago_Monto_Pagado_con_Efectivo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCompPago_Monto_Pagado_con_Efectivo`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
DECLARE Monto 		DECIMAL(12,4);
SELECT 	SUM(IFNULL(CD.PrecioUnitario,0.0000)) 
INTO 	Monto
FROM 	ComprobantesDetalles CD 
WHERE 	(
		CD.Caja is not null 
		OR		
		CD.Cheque is not null
        )
AND 	CD.Comprobante = idComprobante;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCompPago_Monto_qResta` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCompPago_Monto_qResta`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
DECLARE Monto 				DECIMAL(12,4) default 0.0000;
select 	ifnull(sum(fComprobante_Monto_Disponible(CR.ComprobanteHijo)),0.0000)
into 	Monto
from 	ComprobantesRelacionados CR
		inner join Comprobantes CH 			on CH.Id = CR.ComprobanteHijo
where	CR.ComprobantePadre = idComprobante
and 	fSigno_Comprobante_xID(CR.ComprobantePadre,CR.ComprobanteHijo) < 0;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fCompPago_Monto_qSuma` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fCompPago_Monto_qSuma`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
DECLARE Monto 				DECIMAL(12,4) default 0.0000;
select 	ifnull(sum(fComprobante_Monto_Disponible(CR.ComprobanteHijo)),0.0000)
into 	Monto
from 	ComprobantesRelacionados CR
		inner join Comprobantes CH 			on CH.Id = CR.ComprobanteHijo
where	CR.ComprobantePadre = idComprobante
and 	fSigno_Comprobante_xID(CR.ComprobantePadre,CR.ComprobanteHijo) > 0;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobanteTotal` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobanteTotal`(
        idComprobante INTEGER
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
	DECLARE Comprobante 		INTEGER;
	DECLARE FechaEmision 		DATETIME;
	DECLARE TipoDeComprobante 	INTEGER;		
	DECLARE MontoTotal 		DECIMAL(12,4);
	DECLARE NetoGravado 		DECIMAL(12,4);
	SELECT
	  `C`.`Id`                AS `Id`,
	  `C`.`FechaEmision`      AS `FechaEmision`,
	  `C`.`TipoDeComprobante` AS `TipoDeComprobante`,
	  ROUND(((IFNULL((SELECT SUM(`vCDe`.`Monto`) AS `M1` FROM `vComprobanteDetalle` `vCDe` WHERE (`vCDe`.`IdComprobante` = `C`.`Id`)),0) - IFNULL((SELECT SUM(`vCD`.`Descuento`) AS `M2` FROM `vComprobanteDescuento` `vCD` WHERE (`vCD`.`Id` = `C`.`Id`)),0)) + IFNULL((SELECT SUM(`vCI`.`Monto`) AS `M3` FROM `vConceptosImpositivos` `vCI` WHERE (`vCI`.`ComprobantePadre` = `C`.`Id`)),0)),4) AS `MontoTotal`,
	  ROUND(((SELECT SUM(((`CD`.`Cantidad` * `CD`.`PrecioUnitario`) * IFNULL((1 - (`CD`.`DescuentoEnPorcentaje` / 100)),1))) AS `M4` FROM `ComprobantesDetalles` `CD` WHERE (`CD`.`Comprobante` = `C`.`Id`)) - IFNULL(`C`.`DescuentoEnMonto`,0)),4) AS `NetoGravado` 
	  INTO 
	  Comprobante,
	  FechaEmision,
	  TipoDeComprobante,
	  MontoTotal,
	  NetoGravado
	FROM `Comprobantes` `C` WHERE C.Id = idComprobante;
	RETURN MontoTotal;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_ConceptosImp_Totales` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_ConceptosImp_Totales`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN
	DECLARE Monto DECIMAL(12,4);
	SELECT IFNULL(SUM(C.Monto),0.0000)
    INTO	Monto
    FROM 	Comprobantes C
    WHERE 	C.ComprobantePadre = idComprobante;
	RETURN ROUND(Monto,4); 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_Estado_Pago` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_Estado_Pago`(
        idComprobante INT
    ) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE MontoDelComprobante DECIMAL(15,6);
    DECLARE FaltanteDePago	DECIMAL(15,6);
    DECLARE Pagado  		DECIMAL(15,6);
    DECLARE estado 			VARCHAR(20);
    DECLARE CondicionDePago 	INT;
    DECLARE Anulado			 	INT;
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN " ";     
    SET estado = '';
    SET CondicionDePago = 0;
     
    SELECT  IFNULL(C.CondicionDePago,0) , C.Anulado
    INTO 	CondicionDePago, Anulado
    FROM    Comprobantes C
    WHERE   C.Id = idComprobante
	AND     C.Cerrado = 1;	
     
    IF Anulado = 1 THEN
    	SET estado = 'Anulado';
    ELSE
        IF CondicionDePago = 2 THEN
            SET estado = 'Contado';
        ELSE
            Select IFNULL(fComprobante_Monto_Total(idComprobante),0) into MontoDelComprobante;
            
            SELECT ifnull(fComprobante_Monto_Disponible(idComprobante),0) INTO FaltanteDePago;
            
            SELECT Ifnull(fComprobante_Monto_Gastado(idComprobante),0) INTO Pagado;
            
            SET estado = '???';
            
            IF Pagado = 0 THEN
                SET estado = 'Nada';
            ELSE
                IF FaltanteDePago = 0 THEN
                    SET estado = 'Totalmente';
                ELSE
                    SET estado = 'Parcialmente';
                END IF;
            END IF;
            
        END IF;
    END IF;
    
    RETURN estado COLLATE utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_Monto_Disponible` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_Monto_Disponible`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN 
DECLARE MontoGastado DECIMAL(12,4) DEFAULT 0.0000; 
DECLARE MontoDisponible DECIMAL(12,4) DEFAULT 0.0000; 
SELECT IFNULL(SUM(MontoAsociado),0.0000) 
INTO MontoGastado 
FROM ComprobantesRelacionados CR
		INNER JOIN Comprobantes CP ON CR.ComprobantePadre = CP.Id
		INNER JOIN TiposDeComprobantes TP ON CP.TipoDeComprobante = TP.Id 
WHERE TP.Grupo IN (9,11) 
AND CP.Cerrado = 1 
AND CR.ComprobanteHijo = idComprobante; 
SELECT fComprobante_Monto_Total(idComprobante) - MontoGastado 
INTO MontoDisponible; 
IF MontoDisponible < 0.01 THEN 
	SET MontoDisponible = 0.0000; 
END IF; 
RETURN MontoDisponible; 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_Monto_Gastado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_Monto_Gastado`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN 
	DECLARE MontoGastado DECIMAL(12,4); 
    
    SELECT 	SUM(MontoAsociado) INTO MontoGastado 
    FROM 	ComprobantesRelacionados CR
			INNER JOIN Comprobantes CP ON CR.ComprobantePadre = CP.Id
			INNER JOIN TiposDeComprobantes TP ON CP.TipoDeComprobante = TP.Id 
    WHERE 	TP.Grupo IN (9,11) 
    AND 	CP.Cerrado = 1 
    AND 	CR.ComprobanteHijo = idComprobante; 
    
	RETURN MontoGastado; 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_Monto_Total` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_Monto_Total`(
        `idComprobante` INTEGER
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
		DECLARE NG DECIMAL(12,4);
		DECLARE CI DECIMAL(12,4);
        DECLARE MT DECIMAL(12,4);
		DECLARE DescuentoEnMonto 	DECIMAL(12,4);
		DECLARE Letra				INTEGER;
    
        
        SELECT  IFNULL(C.DescuentoEnMonto,0.0000),
        		IFNULL(TC.TipoDeLetra,0)
        INTO 	DescuentoEnMonto,
        		Letra
        FROM 	Comprobantes C
        inner join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
        WHERE 	C.Id = idComprobante;
        
        
        
        if Letra in (0,1,5) then	
            SELECT fComprobante_NetoGravado(idComprobante) INTO NG;
            SELECT fComprobante_ConceptosImp_Totales(idComprobante) INTO CI;
            set MT = NG + CI;
        End If;
        
        
        if Letra in (2,4) then
            SELECT 	SUM((( IFNULL(CD.Cantidad * CD.PrecioUnitario,0.0000) ) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
            INTO 	MT 
            FROM 	ComprobantesDetalles CD
            WHERE 	CD.Comprobante = idComprobante;
            set MT = MT - DescuentoEnMonto;
        end IF;
        
        
        if Letra = 3 THEN
            SELECT fComprobante_NetoGravado(idComprobante) INTO MT;
        End if; 
        
        
        
        RETURN MT;
    
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_NetoGravado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_NetoGravado`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(12,4)
BEGIN 
	DECLARE NetoGravado 		DECIMAL(12,4); 
	DECLARE NetoArticulos 		DECIMAL(12,4);
    DECLARE MontoTotal			DECIMAL(12,4); 
	DECLARE DescuentoEnMonto 	DECIMAL(12,4); 
	DECLARE Letra				INTEGER;
    
    
    SELECT 	IFNULL(C.DescuentoEnMonto,0.0000),
            IFNULL(TC.TipoDeLetra,0)
    INTO 	DescuentoEnMonto,
            Letra
	FROM 	Comprobantes C
    inner join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
	WHERE 	C.Id = idComprobante;
		
    if Letra in (0,1,5,3) then
        
        SELECT 	SUM(((IFNULL(CD.Cantidad * CD.PrecioUnitario,0.0000)) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
        INTO 	NetoArticulos 
        FROM 	ComprobantesDetalles CD
        WHERE 	CD.Comprobante = idComprobante;
	End if;
   
	-- 2: B y 4: E
    If Letra in (2,4) then
        
        
        SELECT 	SUM(((IFNULL(CD.Cantidad * CD.PrecioUnitario / (1 + CI.PorcentajeActual / 100),0.0000)) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
        INTO 	NetoArticulos 
        FROM 	ComprobantesDetalles CD
        inner join ConceptosImpositivos CI on CI.Id = CD.ConceptoImpositivo
        WHERE 	CD.Comprobante = idComprobante;           
    end IF;
    
    
        
 	if (DescuentoEnMonto > 0.0001) THEN    
    	set NetoGravado = NetoArticulos - DescuentoEnMonto;
    else
    	set NetoGravado = NetoArticulos;
    end if; 
RETURN NetoGravado; 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fComprobante_NetoGravado_xCI` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fComprobante_NetoGravado_xCI`(
        idComprobante INTEGER(11),
        ConceptoImpositivo INTEGER(11)
    ) RETURNS decimal(12,2)
BEGIN
    DECLARE NetoGravado 			DECIMAL(12,4);
    DECLARE NetoArticulos 			DECIMAL(12,4);
    DECLARE NetoGravadoGeneral		DECIMAL(12,4);
    DECLARE Porcentaje				DECIMAL(7,4);
	
	DECLARE DescuentoEnMonto 		DECIMAL(12,4); 
	DECLARE DescuentoProporcional 	DECIMAL(12,4); 
    DECLARE Letra					INTEGER;
    
    
    SELECT 	IFNULL(C.DescuentoEnMonto,0.0000),
            IFNULL(TC.TipoDeLetra,0) 
    INTO 	DescuentoEnMonto,
            Letra
	FROM 	Comprobantes C
    inner join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
	WHERE 	C.Id = idComprobante;
	
    if Letra in (0,1,5,3) then
        
        SELECT 	SUM(((IFNULL(CD.Cantidad * CD.PrecioUnitario,0.0000)) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
        INTO 	NetoArticulos 
        FROM 	ComprobantesDetalles CD
        WHERE 	CD.Comprobante = idComprobante
        AND		CD.ConceptoImpositivo = ConceptoImpositivo;        	
    end if;
    
	
    If Letra in (2,4) then
        
        SELECT 	SUM(((IFNULL(CD.Cantidad * CD.PrecioUnitario / (1 + CI.PorcentajeActual / 100),0.0000)) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
        INTO 	NetoArticulos 
        FROM 	ComprobantesDetalles CD
        inner join ConceptosImpositivos CI on CI.Id = CD.ConceptoImpositivo
        WHERE 	CD.Comprobante = idComprobante
        AND		CD.ConceptoImpositivo = ConceptoImpositivo;           
    end IF;
    
    
    
    
    if (DescuentoEnMonto > 0.0001) then
        
        
        
        
        if Letra in (0,1,5,3) then
            
            SELECT 	SUM(((IFNULL(CD.Cantidad * CD.PrecioUnitario,0.0000)) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
            INTO 	NetoGravadoGeneral 
            FROM 	ComprobantesDetalles CD
            WHERE 	CD.Comprobante = idComprobante;      	
        end if;
        
        
        If Letra in (2) then
            
            SELECT 	SUM(((IFNULL(CD.Cantidad * CD.PrecioUnitario / (1 + CI.PorcentajeActual / 100),0.0000)) * IFNULL((1 - (CD.DescuentoEnPorcentaje / 100)),1))) 
            INTO 	NetoGravadoGeneral 
            FROM 	ComprobantesDetalles CD
            inner join ConceptosImpositivos CI on CI.Id = CD.ConceptoImpositivo
            WHERE 	CD.Comprobante = idComprobante;          
        end IF;
        
        
        
        
        
         
        select DescuentoEnMonto * ( NetoArticulos / NetoGravadoGeneral ) 
        into 	DescuentoProporcional;
        
        
        
        SELECT 	NetoArticulos - DescuentoProporcional
        INTO 	NetoGravado; 
        
    else
        set 	NetoGravado = NetoArticulos;
    end if;
RETURN NetoGravado;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fEstadoRelHijo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fEstadoRelHijo`(idComprobante INT) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE usado DECIMAL;
    DECLARE trae DECIMAL;
    DECLARE estado VARCHAR(20);
	select 	ifnull(sum(CRD.Cantidad),0) into usado
    from	`ComprobantesRelacionados` CR,
    		`ComprobantesRelacionadosDetalles` CRD
    where	CR.Id = CRD.ComprobanteRelacionado
    and		CR.ComprobanteHijo = idComprobante;     
	
    select ifnull(sum(CD.Cantidad),0) into trae
    from	`Comprobantes` C,
    		`ComprobantesDetalles` CD
    where	CD.`Comprobante` = C.`Id`
    and		C.Id = idComprobante;
    
	IF usado = 0 THEN 
    	set estado = 'Nada';
	ELSE 
    	IF usado < trae THEN
        	set estado = 'Parcialmente';
        ELSE 
        	IF usado = trae THEN
        		set estado = 'Totalmente';
			ELSE
        		set estado = 'Excedido';
            END IF;
        END IF;
    END IF;
            
  RETURN estado collate utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fEstadoRelHijoPago` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fEstadoRelHijoPago`(
        idComprobante INT
    ) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE MontoDelComprobante DECIMAL(15,6);
    DECLARE FaltanteDePago	DECIMAL(15,6);
    DECLARE Pagado  		DECIMAL(15,6);
    DECLARE estado 			VARCHAR(20);
    DECLARE CondicionDePago 	INT;
    DECLARE Anulado			 	INT;
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN " ";     
    SET estado = '';
    SET CondicionDePago = 0;
    
    
    
    SELECT  IFNULL(C.CondicionDePago,0) , C.Anulado
    INTO 	CondicionDePago, Anulado
    FROM    Comprobantes C
    WHERE   C.Id = idComprobante
	AND     C.Cerrado = 1;	
    
    
    IF Anulado = 1 THEN
	SET estado = 'Anulado';
    ELSE
	SELECT IFNULL(fComprobante_Monto_Total(idComprobante),0) INTO MontoDelComprobante;
	    
	SELECT IFNULL(fComprobante_Monto_Disponible(idComprobante),0) INTO FaltanteDePago;
	    
	SELECT IFNULL(fComprobante_Monto_Gastado(idComprobante),0) INTO Pagado;
	    
	SET estado = '???';
	    
        IF Pagado = 0 THEN
	    SET estado = 'Nada';
        ELSE
	    IF FaltanteDePago = 0 THEN
		SET estado = 'Totalmente';
		IF CondicionDePago = 2 THEN
		    SET estado = 'Contado';
		END IF;                                        
	    ELSE
		SET estado = 'Parcialmente';
	    END IF;
	END IF;
    END IF;
    
    RETURN estado COLLATE utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fEstadoRelHijoPago2` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fEstadoRelHijoPago2`(idComprobante INT) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE usado 		DECIMAL(15,6);
    DECLARE trae  		DECIMAL(15,6);
    DECLARE monto  		DECIMAL(15,6);
    DECLARE descuento1  	DECIMAL(15,6);
    DECLARE descuento2 		DECIMAL(15,6);
    DECLARE descuento3 		DECIMAL(15,6);
    DECLARE montoconcepto  	DECIMAL(15,6);                   
    DECLARE estado 		VARCHAR(20);
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN " ";     
    
    SELECT  SUM(`ComprobantesDetalles`.`Cantidad` * `ComprobantesDetalles`.`PrecioUnitario`) INTO monto 
    FROM `ComprobantesDetalles`
	JOIN `Comprobantes`
		ON `ComprobantesDetalles`.`Comprobante` = `Comprobantes`.`Id`
    WHERE `Comprobantes`.`Id` = idComprobante
	and `Comprobantes`.Cerrado = 1;
	
    SELECT  sum((((`ComprobantesDetalles`.`Cantidad` * `ComprobantesDetalles`.`PrecioUnitario`) * `ComprobantesDetalles`.`DescuentoEnPorcentaje`) / 100)) into descuento1 
    FROM `ComprobantesDetalles`
	JOIN `Comprobantes`
		ON `ComprobantesDetalles`.`Comprobante` = `Comprobantes`.`Id`
    WHERE `Comprobantes`.`Id` = idComprobante
	AND `Comprobantes`.Cerrado = 1;
	
    SELECT  sum(IFNULL(`ComprobantesDetalles`.`DescuentoEnMonto`,0)) INTO descuento2 
    FROM `ComprobantesDetalles`
	JOIN `Comprobantes`
		ON `ComprobantesDetalles`.`Comprobante` = `Comprobantes`.`Id`
    WHERE `Comprobantes`.`Id` = idComprobante
	AND `Comprobantes`.Cerrado = 1;
	
    SELECT  IFNULL(`Comprobantes`.`DescuentoEnMonto`,0) INTO descuento3 
    FROM `Comprobantes`
    WHERE `Comprobantes`.`Id` = idComprobante
	AND `Comprobantes`.Cerrado = 1;			
		
    SELECT distinct
	 sum(IFNULL(`ComprobantePadre`.`Monto`,0)) into `montoconcepto`
    FROM `Comprobantes`
	LEFT JOIN `Comprobantes` `ComprobantePadre`
	   ON `ComprobantePadre`.`ComprobantePadre` = `Comprobantes`.`Id`
	LEFT JOIN `TiposDeComprobantes`
	   ON `ComprobantePadre`.`TipoDeComprobante` = `TiposDeComprobantes`.`Id` AND `TiposDeComprobantes`.`DiscriminaImpuesto` = 1
    WHERE `Comprobantes`.`Id` =	idComprobante
	AND `Comprobantes`.Cerrado = 1;	
	
      IF monto IS NULL THEN
        SET monto = 0;
      END IF;	
      IF descuento1 IS NULL THEN
        SET descuento1 = 0;
      END IF;
      IF descuento2 IS NULL THEN
        SET descuento2 = 0;
      END IF;
      IF descuento3 IS NULL THEN
        SET descuento3 = 0;
      END IF;
      IF montoconcepto IS NULL THEN
        SET montoconcepto = 0;
      END IF;	
      
    SET  usado =  monto - descuento1 - descuento2 - descuento3 + montoconcepto; 
            
    SELECT   SUM(IFNULL(CD.PrecioUnitario,0)) INTO trae 
      FROM Comprobantes C,
           ComprobantesDetalles CD,
           ComprobantesRelacionados CR
      WHERE C.Id = CD.Comprobante
            AND C.Id = CR.ComprobantePadre
            AND C.Cerrado = 1
            AND CR.ComprobanteHijo = idComprobante;
      
      IF usado is null then
        set usado = 0;
      end if;
      IF trae is null then
        set trae = 0;
      end if;
    	IF trae = 0 THEN
        	set estado = 'Nada';
    	ELSE
          IF usado > trae THEN
            set estado = 'Parcialmente';
          ELSE
            IF usado = trae THEN
              set estado = 'Totalmente';
            ELSE
              IF usado < trae THEN
                set estado = 'Totalmente';
              END IF;
            END IF;
          END IF;
      END IF;
  RETURN estado collate utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fEstadoRelPadre` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fEstadoRelPadre`(idComprobante INT) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE usado DECIMAL(15,6);
	DECLARE trae DECIMAL(15,6);
	DECLARE estado VARCHAR(20);
	DECLARE EXIT HANDLER FOR NOT FOUND RETURN "0,0,0"; 	
	
    select 	ifnull(sum(CRD.Cantidad),0) into usado
    from	`ComprobantesRelacionados` CR,
    		`ComprobantesRelacionadosDetalles` CRD
    where	CR.Id = CRD.ComprobanteRelacionado
    and		CR.ComprobantePadre = idComprobante;
    
    select ifnull(sum(CD.Cantidad),0) into trae
    from	`Comprobantes` C,
    		`ComprobantesDetalles` CD
    where	CD.`Comprobante` = C.`Id`
    and		C.Id = idComprobante;
    
	IF usado = 0 THEN 
    	set estado = 'Nada';
	ELSE 
    	IF usado < trae THEN
        	set estado = 'Parcialmente';
        ELSE 
        	IF usado = trae THEN
        		set estado = 'Totalmente';
			ELSE
        		set estado = 'Excedido';
            END IF;
        END IF;
    END IF;
            
  RETURN estado;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fFaltanteRecibirAFechaPorArticulo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fFaltanteRecibirAFechaPorArticulo`(
        fecha datetime,
        idArticulo INT
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
    
    DECLARE entregado DECIMAL(12,4);
    DECLARE pedido DECIMAL(12,4);
    
    
    
    
    
    SELECT 	IFNULL(SUM(CD.Cantidad),0) INTO pedido
    FROM	Comprobantes C
    		inner join ComprobantesDetalles CD on CD.Comprobante = C.Id
    WHERE	C.TipoDeComprobante in (Select Id from TiposDeComprobantes where Grupo = 5) 
    AND   	C.Cerrado = 1
    AND   	C.FechaEntrega <= fecha 
    AND   	C.FechaEntrega >= DATE_SUB(NOW(),INTERVAL 30 day)
    AND   	CD.Articulo = idArticulo;
    
    
    
    
    
    select 	ifnull(sum(CRD.Cantidad),0) into entregado
    from  	Comprobantes C 
	  		inner join ComprobantesRelacionados CR on 			C.Id = CR.ComprobantePadre
	  		inner join ComprobantesRelacionadosDetalles CRD on 	CR.Id = CRD.ComprobanteRelacionado
    where	C.Cerrado = 1 
    And 	C.TipoDeComprobante in (Select Id from TiposDeComprobantes where Grupo = 4)
    and		C.FechaEmision <= fecha
    and   	CRD.Articulo = idArticulo
    AND 	CR.ComprobanteHijo in ( 
                  SELECT distinct C.Id 
                  FROM	Comprobantes C
                  		inner join ComprobantesDetalles CD on C.Id = CD.Comprobante
                  WHERE C.TipoDeComprobante in (Select Id from TiposDeComprobantes where Grupo = 5)
                  AND   C.Cerrado = 1
                  AND   C.FechaEntrega <= fecha 
                  AND   C.FechaEntrega >= DATE_SUB(NOW(),INTERVAL 30 day)
                  AND   CD.Articulo = idArticulo
    		);
   
  RETURN pedido - entregado;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fGet_ProductoDeUnArticulo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`%`*/ /*!50003 FUNCTION `fGet_ProductoDeUnArticulo`(
        `ArtVersion` INTEGER
    ) RETURNS int(4)
BEGIN
	DECLARE idArticulo 	INTEGER;
    SELECT a.Id into idArticulo
    FROM ArticulosVersionesDetalles avd 
    INNER JOIN ArticulosVersiones av 	ON avd.ArticuloVersionHijo = av.Id 
    INNER JOIN Articulos a 				ON av.Articulo = a.id 
    WHERE avd.ArticuloVersion = ArtVersion
    AND a.EsMateriaPrima = 1
    AND avd.TipoDeRelacionArticulo = 1
    AND a.ArticuloGrupo <> 1;
    
    RETURN idArticulo;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fMeses` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fMeses`(XNumero NUMERIC(20,2)) RETURNS varchar(60) CHARSET utf8
    DETERMINISTIC
BEGIN 
DECLARE XlnEntero INT; 
DECLARE Xresultado VARCHAR(60);
SET Xresultado = '';
SET XlnEntero = 1 ; 
Select
CASE WHEN XNumero = 1 THEN "ENERO"
WHEN XNumero = 2 THEN "FEBRERO"
WHEN XNumero = 3 THEN "MARZO"
WHEN XNumero = 4 THEN "ABRIL"
WHEN XNumero = 5 THEN "MAYO"
WHEN XNumero = 6 THEN "JUNIO"
WHEN XNumero = 7 THEN "JULIO"
WHEN XNumero = 8 THEN "AGOSTO"
WHEN XNumero = 9 THEN "SETIEMBRE"
WHEN XNumero = 10 THEN "OCTUBRE"
WHEN XNumero = 11 THEN "NOVIEMBRE"
WHEN XNumero = 12 THEN "DICIEMBRE"
ELSE "esto no es un mes" END into Xresultado;
RETURN Xresultado; 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fNumeroALetras` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fNumeroALetras`(XNumero NUMERIC(20,2)) RETURNS varchar(512) CHARSET utf8
    DETERMINISTIC
BEGIN 
DECLARE XlnEntero INT; 
DECLARE XlcRetorno VARCHAR(512); 
DECLARE XlnTerna INT; 
DECLARE XlcMiles VARCHAR(512); 
DECLARE XlcCadena VARCHAR(512); 
DECLARE XlnUnidades INT; 
DECLARE XlnDecenas INT; 
DECLARE XlnCentenas INT; 
DECLARE XlnFraccion INT; 
DECLARE Xresultado varchar(512); 
SET XlnEntero = FLOOR(XNumero); 
SET XlnFraccion = (XNumero - XlnEntero) * 100; 
SET XlcRetorno = ''; 
SET XlnTerna = 1 ; 
    WHILE( XlnEntero > 0) DO 
        
        SET XlcCadena = ''; 
        SET XlnUnidades = XlnEntero MOD 10; 
        SET XlnEntero = FLOOR(XlnEntero/10); 
        SET XlnDecenas = XlnEntero MOD 10; 
        SET XlnEntero = FLOOR(XlnEntero/10); 
        SET XlnCentenas = XlnEntero MOD 10; 
        SET XlnEntero = FLOOR(XlnEntero/10); 
        
        SET XlcCadena = 
            CASE 
                WHEN XlnUnidades = 1 AND XlnTerna = 1 THEN CONCAT('UNO ', XlcCadena) 
                WHEN XlnUnidades = 1 AND XlnTerna <> 1 THEN CONCAT('UN ', XlcCadena) 
                WHEN XlnUnidades = 2 THEN CONCAT('DOS ', XlcCadena) 
                WHEN XlnUnidades = 3 THEN CONCAT('TRES ', XlcCadena) 
                WHEN XlnUnidades = 4 THEN CONCAT('CUATRO ', XlcCadena) 
                WHEN XlnUnidades = 5 THEN CONCAT('CINCO ', XlcCadena) 
                WHEN XlnUnidades = 6 THEN CONCAT('SEIS ', XlcCadena) 
                WHEN XlnUnidades = 7 THEN CONCAT('SIETE ', XlcCadena) 
                WHEN XlnUnidades = 8 THEN CONCAT('OCHO ', XlcCadena) 
                WHEN XlnUnidades = 9 THEN CONCAT('NUEVE ', XlcCadena) 
                ELSE XlcCadena 
            END; 
        
        SET XlcCadena = 
            CASE 
                WHEN XlnDecenas = 1 THEN 
                    CASE XlnUnidades 
                        WHEN 0 THEN 'DIEZ ' 
                        WHEN 1 THEN 'ONCE ' 
                        WHEN 2 THEN 'DOCE ' 
                        WHEN 3 THEN 'TRECE ' 
                        WHEN 4 THEN 'CATORCE ' 
                        WHEN 5 THEN 'QUINCE' 
                        ELSE CONCAT('DIECI', XlcCadena) 
                    END 
                WHEN XlnDecenas = 2 AND XlnUnidades = 0 THEN CONCAT('VEINTE ', XlcCadena) 
                WHEN XlnDecenas = 2 AND XlnUnidades <> 0 THEN CONCAT('VEINTI', XlcCadena) 
                WHEN XlnDecenas = 3 AND XlnUnidades = 0 THEN CONCAT('TREINTA ', XlcCadena) 
                WHEN XlnDecenas = 3 AND XlnUnidades <> 0 THEN CONCAT('TREINTA Y ', XlcCadena) 
                WHEN XlnDecenas = 4 AND XlnUnidades = 0 THEN CONCAT('CUARENTA ', XlcCadena) 
                WHEN XlnDecenas = 4 AND XlnUnidades <> 0 THEN CONCAT('CUARENTA Y ', XlcCadena) 
                WHEN XlnDecenas = 5 AND XlnUnidades = 0 THEN CONCAT('CINCUENTA ', XlcCadena) 
                WHEN XlnDecenas = 5 AND XlnUnidades <> 0 THEN CONCAT('CINCUENTA Y ', XlcCadena) 
                WHEN XlnDecenas = 6 AND XlnUnidades = 0 THEN CONCAT('SESENTA ', XlcCadena) 
                WHEN XlnDecenas = 6 AND XlnUnidades <> 0 THEN CONCAT('SESENTA Y ', XlcCadena) 
                WHEN XlnDecenas = 7 AND XlnUnidades = 0 THEN CONCAT('SETENTA ', XlcCadena) 
                WHEN XlnDecenas = 7 AND XlnUnidades <> 0 THEN CONCAT('SETENTA Y ', XlcCadena) 
                WHEN XlnDecenas = 8 AND XlnUnidades = 0 THEN CONCAT('OCHENTA ', XlcCadena) 
                WHEN XlnDecenas = 8 AND XlnUnidades <> 0 THEN CONCAT('OCHENTA Y ', XlcCadena) 
                WHEN XlnDecenas = 9 AND XlnUnidades = 0 THEN CONCAT('NOVENTA ', XlcCadena) 
                WHEN XlnDecenas = 9 AND XlnUnidades <> 0 THEN CONCAT('NOVENTA Y ', XlcCadena) 
                ELSE XlcCadena 
            END; 
        
        SET XlcCadena = 
            CASE 
                WHEN XlnCentenas = 1 AND XlnUnidades = 0 AND XlnDecenas = 0 THEN CONCAT('CIEN ', XlcCadena) 
                WHEN XlnCentenas = 1 AND NOT(XlnUnidades = 0 AND XlnDecenas = 0) THEN CONCAT('CIENTO ', XlcCadena) 
                WHEN XlnCentenas = 2 THEN CONCAT('DOSCIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 3 THEN CONCAT('TRESCIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 4 THEN CONCAT('CUATROCIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 5 THEN CONCAT('QUINIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 6 THEN CONCAT('SEISCIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 7 THEN CONCAT('SETECIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 8 THEN CONCAT('OCHOCIENTOS ', XlcCadena) 
                WHEN XlnCentenas = 9 THEN CONCAT('NOVECIENTOS ', XlcCadena) 
                ELSE XlcCadena 
            END; 
        
        SET XlcCadena = 
            CASE 
                WHEN XlnTerna = 1 THEN XlcCadena 
                WHEN XlnTerna = 2 AND (XlnUnidades + XlnDecenas + XlnCentenas <> 0) THEN CONCAT(XlcCadena,  'MIL ') 
                WHEN XlnTerna = 3 AND (XlnUnidades + XlnDecenas + XlnCentenas <> 0) AND XlnUnidades = 1 AND XlnDecenas = 0 AND XlnCentenas = 0 THEN CONCAT(XlcCadena, 'MILLON ') 
                WHEN XlnTerna = 3 AND (XlnUnidades + XlnDecenas + XlnCentenas <> 0) AND NOT (XlnUnidades = 1 AND XlnDecenas = 0 AND XlnCentenas = 0) THEN CONCAT(XlcCadena, 'MILLONES ') 
                WHEN XlnTerna = 4 AND (XlnUnidades + XlnDecenas + XlnCentenas <> 0) THEN CONCAT(XlcCadena, 'MIL MILLONES ') 
                ELSE '' 
            END; 
        
        SET XlcRetorno = CONCAT(XlcCadena, XlcRetorno); 
        SET XlnTerna = XlnTerna + 1; 
    END WHILE; 
    IF XlnTerna = 1 THEN SET XlcRetorno = 'CERO'; END IF; 
SET Xresultado = CONCAT(RTRIM(XlcRetorno), ' CON ', LTRIM(XlnFraccion), '/100 '); 
RETURN ifnull(Xresultado, 'CERO CON 0/100'); 
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fNumeroCompleto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fNumeroCompleto`(
        idComprobante INTEGER,
        formato VARCHAR(2)
    ) RETURNS varchar(30) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE Tipo 			INT;
    DECLARE Grupo 			INT;
    DECLARE CodigoComp		VARCHAR(4);
    DECLARE CodigoGrupo		VARCHAR(4);
    DECLARE Punto 			INT;
    DECLARE PuntoVta		VARCHAR(4);
    DECLARE PuntoRemito		VARCHAR(4);
    DECLARE Numero 			BIGINT;
    DECLARE numeroCompleto 	VARCHAR(30);
    DECLARE FormatoDeNumero INT;
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN " ";     
    
    SELECT 	C.Punto,
    		PV.Numero,
            PR.Numero,
    		IFNULL(C.Numero,0) AS Numero,
    		C.TipoDeComprobante,
            TC.Grupo,
            TC.Codigo,
            TGC.Codigo,
            TGC.FormatoDeNumero
    INTO 
            Punto,
            PuntoVta,
            PuntoRemito,
            Numero, 
            Tipo,
            Grupo,
            CodigoComp,
            CodigoGrupo,
            FormatoDeNumero
            
    FROM    Comprobantes C
    		INNER JOIN TiposDeComprobantes TC 	ON C.TipoDeComprobante = TC.Id
            INNER JOIN TiposDeGruposDeComprobantes TGC 	ON TC.Grupo = TGC.Id 
    		LEFT OUTER JOIN PuntosDeVentas PV 	ON C.Punto = PV.Id
            LEFT OUTER JOIN PuntosDeRemitos PR 	ON C.Punto = PR.Id 
    WHERE   C.Id = idComprobante;
	
CASE FormatoDeNumero
    WHEN 1 THEN SET numeroCompleto = CONCAT(LPAD(Punto , 4, '0'),'-',LPAD(Numero, 8, '0'));
	WHEN 2 THEN SET numeroCompleto = LPAD(Numero, 12,'0');
	WHEN 3 THEN SET numeroCompleto = CONCAT(LPAD(PuntoVta , 4, '0'),'-',LPAD(Numero, 8, '0'));
	WHEN 4 THEN SET numeroCompleto = CONCAT(LPAD(PuntoRemito , 4, '0'),'-',LPAD(Numero, 8, '0'));
	WHEN 5 THEN SET numeroCompleto = Numero;
	WHEN 6 THEN SET numeroCompleto = 's/n';
    ELSE  		SET numeroCompleto = '???';
END CASE;    
    
    IF (UPPER(formato) LIKE '%R%') THEN
    	SET numeroCompleto = CONCAT(CodigoComp,': ',numeroCompleto);
    ELSE 
 
	    IF (UPPER(formato) LIKE '%C%' ) THEN 
		SET numeroCompleto = CONCAT(CodigoComp,': ',numeroCompleto);
	    END IF;
	    
	    IF (UPPER(formato) LIKE '%G%') THEN
		SET numeroCompleto = CONCAT('[ ',CodigoGrupo,' ] ',numeroCompleto);
	    END IF;   
    
    END IF;    
    
    RETURN numeroCompleto COLLATE utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fPersona_ChequesPendientes_A_Fecha` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fPersona_ChequesPendientes_A_Fecha`(
        idPersona INTEGER,
        fecha DATE
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
    DECLARE saldo 					DECIMAL(12,4);
    DECLARE montoChequesPendientes 	DECIMAL(12,4); 	
    set montoChequesPendientes = 0;
    
    if(fecha <= date(now())) then
        
        
        SELECT  IFNULL(SUM(CH.Monto),0) INTO montoChequesPendientes
        FROM 	Cheques CH
        INNER JOIN ComprobantesDetalles CD ON CD.Cheque = CH.Id
        INNER JOIN Comprobantes C ON C.Id = CD.Comprobante
        INNER JOIN TiposDeComprobantes TC ON TC.Id = C.TipoDeComprobante
        INNER JOIN CuentasCorrientes CC ON CC.Comprobante = C.Id
        WHERE 	CC.Persona = idPersona 
        AND		CC.FechaComprobante <= fecha
        AND		CH.TipoDeEmisorDeCheque <> 1 
        AND		CH.ChequeEstado IN (6,8) 	    
        AND 	TC.Grupo IN (11);        
    else
    	
    	
        SELECT  IFNULL(sum(CH.Monto),0) into montoChequesPendientes
        FROM 	Cheques CH
        INNER JOIN ComprobantesDetalles CD on CD.Cheque = CH.Id
        INNER JOIN Comprobantes C on C.Id = CD.Comprobante
        INNER JOIN TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
        INNER JOIN CuentasCorrientes CC on CC.Comprobante = C.Id
        WHERE 	CC.Persona = idPersona 
        AND		CC.FechaComprobante <= fecha
        AND		CH.TipoDeEmisorDeCheque <> 1 
        AND		CH.ChequeEstado in (6,8) 
        and		CH.FechaDeVencimiento >= fecha
        AND 	TC.Grupo IN (11);
    end if;
    
    return ROUND(montoChequesPendientes,2);
    
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fPersona_CuentaCorriente_A_Fecha` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fPersona_CuentaCorriente_A_Fecha`(
        idPersona INTEGER,
        fecha DATE,
        sinChequesPendientes INTEGER
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
    DECLARE saldo 					DECIMAL(12,4);
    DECLARE montoChequesPendientes 	DECIMAL(12,4); 	
    set saldo = 0;
    set montoChequesPendientes = 0;
    
    SELECT 	(ifnull(SUM(CC.Debe),0) - IFNULL(SUM(CC.Haber),0)) into saldo
	FROM 	CuentasCorrientes CC 
	WHERE 	CC.Persona = idPersona 
    AND 	CC.FechaComprobante <= fecha;
	
    if (sinChequesPendientes = 1) then
       
    	select fPersona_ChequesPendientes_A_Fecha(idPersona,fecha) into montoChequesPendientes;
    
        if saldo >= 0 then 
            set saldo = saldo - montoChequesPendientes;
        else
            set saldo = saldo + montoChequesPendientes;
        end if;
    
    end if;
    
    return ROUND(saldo,2);
    
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fPersona_Cuenta_Saldo_A_Fecha` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fPersona_Cuenta_Saldo_A_Fecha`(
        idPersona INTEGER,
        fecha DATE
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
	DECLARE debe 	DECIMAL(12,4);
	DECLARE haber 	DECIMAL(12,4);
    DECLARE saldo 	DECIMAL(12,4);
    DECLARE montoChequesPendientes DECIMAL(12,4); 	
	
    set debe  = 0;
    SET haber = 0;
    set saldo = 0;
    set montoChequesPendientes = 0;
    
    SELECT 	ifnull((SUM(CC.Debe)),0),IFNULL((SUM(CC.Haber)),0)  into debe, haber
	FROM 	CuentasCorrientes CC 
	WHERE 	CC.Persona = idPersona 
    AND 	CC.FechaComprobante <= fecha;
    
    if(fecha <= date(now())) then
	    SELECT  IFNULL(SUM(CH.Monto),0) INTO montoChequesPendientes
	    FROM 	Cheques CH
	    INNER JOIN ComprobantesDetalles CD ON CD.Cheque = CH.Id
	    INNER JOIN Comprobantes C ON C.Id = CD.Comprobante
	    INNER JOIN TiposDeComprobantes TC ON TC.Id = C.TipoDeComprobante
	    INNER JOIN CuentasCorrientes CC ON CC.Comprobante = C.Id
	    WHERE 	CC.Persona = idPersona 
	    AND		CC.FechaComprobante <= fecha
	    AND		CH.TipoDeEmisorDeCheque <> 1 
	    AND		CH.ChequeEstado IN (6,8) 	    
	    AND 	TC.Grupo IN (11);        
    else 	
	    SELECT  IFNULL(sum(CH.Monto),0) into montoChequesPendientes
	    FROM 	Cheques CH
	    INNER JOIN ComprobantesDetalles CD on CD.Cheque = CH.Id
	    INNER JOIN Comprobantes C on C.Id = CD.Comprobante
	    INNER JOIN TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
	    INNER JOIN CuentasCorrientes CC on CC.Comprobante = C.Id
	    WHERE 	CC.Persona = idPersona 
	    AND		CC.FechaComprobante <= fecha
	    AND		CH.TipoDeEmisorDeCheque <> 1 
	    AND		CH.ChequeEstado in (6,8) 
        and		CH.FechaDeVencimiento >= fecha
	    AND 	TC.Grupo IN (11);
    end if;
    
    
    set saldo = debe - haber;
    
    if saldo >= 0 then 
    	set saldo = saldo - montoChequesPendientes;
    else
    	set saldo = saldo + montoChequesPendientes;
    end if;
    	
    return ROUND(saldo,2);
    
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fProducidoAFechaPorArticulo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fProducidoAFechaPorArticulo`(
        fecha datetime,
        idArticulo INT
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
	DECLARE total 		DECIMAL(12,4);
    DECLARE inicioDia 	DATETIME;
    DECLARE finDia 		DATETIME;
	DECLARE dia 		DATETIME;		
	set dia 		= DATE(fecha);
    set inicioDia 	= ADDTIME(dia, '00:00:01');
    set finDia 		= ADDTIME(dia, '23:59:59');   
  	SELECT 	ifnull(sum(Cantidad),0) into total 
    from 	OrdenesDeProducciones 
  	where 	FechaFin <= inicioDia 
    and 	FechaFin >= finDia;
  
  	RETURN total;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fReciboSueldo_MontoCalculado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fReciboSueldo_MontoCalculado`(
        idRecibo INTEGER
    ) RETURNS decimal(12,2)
    READS SQL DATA
BEGIN
 
 DECLARE varMonto DECIMAL(12,4);
 set varMonto = 0.0000;
 
 SELECT IFNULL(SUM(MontoCalculado),0)
 INTO varMonto 
 from LiquidacionesRecibosDetalles
 where LiquidacionRecibo = idRecibo
 ;
 
 
 RETURN varMonto;
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fReciboSueldo_MontoPagado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fReciboSueldo_MontoPagado`(
        idRecibo INTEGER
    ) RETURNS decimal(12,2)
    READS SQL DATA
BEGIN
 
 DECLARE varMonto DECIMAL(12,4);
 set varMonto = 0.0000;
 
 SELECT IFNULL(SUM(Monto),0)
 INTO varMonto
 from LiquidacionesRecibosDetalles
 where LiquidacionRecibo = idRecibo
 ;
 
 
 RETURN varMonto;
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fReciboSueldo_MontoRetroactivos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fReciboSueldo_MontoRetroactivos`(
        idRecibo INTEGER
    ) RETURNS decimal(12,2)
    READS SQL DATA
BEGIN
 
 DECLARE varMonto DECIMAL(12,4);
 set varMonto = 0.0000;
 
 SELECT ifnull(SUM(LRD.MontoCalculado),0)
 INTO varMonto 
 from 	LiquidacionesRecibosDetalles LRD
 inner 	join LiquidacionesRecibos LR on LR.Id = LRD.LiquidacionRecibo
 where 	LRD.LiquidacionRecibo = idRecibo
 and 	LRD.PeriodoDevengado <> LR.Periodo
 ;
 
 RETURN varMonto;
 END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fSigno_Comprobante_xID` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fSigno_Comprobante_xID`(
        idComprobantePadre INTEGER,
        idComprobanteHijo INTEGER
    ) RETURNS int(1)
    READS SQL DATA
BEGIN
		DECLARE MultiplicadorPadre 		INTEGER;
		DECLARE MultiplicadorHijo 		INTEGER;
        DECLARE GrupoPadre 				INTEGER;
        DECLARE TipoHijo				INTEGER;
        	
        select 	TC.Grupo
        into	GrupoPadre
        from 	TiposDeComprobantes TC
                inner join Comprobantes C 		on C.TipoDeComprobante = TC.Id
        where	C.Id = idComprobantePadre;
        
        IF (GrupoPadre = 11) THEN 
            set MultiplicadorPadre = -1;
        ELSE
            IF (GrupoPadre = 9) THEN 
                set MultiplicadorPadre = 1;
            ELSE
                set MultiplicadorPadre = 0;
            END IF;
        END IF;
        
        select 	TC.Multiplicador, TC.Id
        into	MultiplicadorHijo, TipoHijo
        from 	TiposDeComprobantes TC
                inner join Comprobantes C 		on C.TipoDeComprobante = TC.Id
        where	C.Id = idComprobanteHijo;
		
        
        
        
        
        
        
        
        
        
        
        IF (TipoHijo = 49) THEN 
        	set MultiplicadorHijo = MultiplicadorHijo * (-1);
        END If;
        
        Return MultiplicadorPadre*MultiplicadorHijo;
    
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fSigno_Comprobante_xTIPO` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fSigno_Comprobante_xTIPO`(
        TipoComprobantePadre INTEGER,
        TipoComprobanteHijo INTEGER
    ) RETURNS int(1)
    READS SQL DATA
BEGIN
		DECLARE MultiplicadorPadre 		INTEGER;
		DECLARE MultiplicadorHijo 		INTEGER;
        DECLARE GrupoPadre 				INTEGER;
        	
        select 	TC.Grupo
        into	GrupoPadre
        from 	TiposDeComprobantes TC
        where	TC.Id = TipoComprobantePadre;
        
        IF (GrupoPadre = 11) THEN 
            set MultiplicadorPadre = -1;
        ELSE
            IF (GrupoPadre = 9) THEN 
                set MultiplicadorPadre = 1;
            ELSE
                set MultiplicadorPadre = 0;
            END IF;
        END IF;
        
        select 	TC.Multiplicador
        into	MultiplicadorHijo
        from 	TiposDeComprobantes TC
        where	TC.Id = TipoComprobanteHijo;
        
		
        
        
        
        
        
        
        
        
        
        
        IF (TipoComprobanteHijo = 49) THEN 
        	set MultiplicadorHijo = MultiplicadorHijo * (-1);
        END If;        
		
        Return MultiplicadorPadre*MultiplicadorHijo;
    
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fStockArticuloEsInsumo` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fStockArticuloEsInsumo`(idArticulo INT) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE total DECIMAL;
    
    SELECT sum(CantidadActual) into total 
    From Mmis m
    where m.ArticuloVersion = idArticulo and m.FechaCierre is null;
    
    select if (total is null, 0,total) into total;
  RETURN total;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fStockArticuloFecha` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fStockArticuloFecha`(
        idArticulo INTEGER,
        fechaConsulta DATETIME
    ) RETURNS decimal(12,4)
BEGIN
    DECLARE eofSelect BOOL DEFAULT FALSE;
    
    DECLARE idMMI INT;
    DECLARE total DECIMAL(12,4) DEFAULT 0;
    DECLARE cant  DECIMAL(12,4) DEFAULT 0;
        
    DECLARE cur1
        CURSOR FOR
        select 	distinct M.Id
        from 	Mmis M 
        where	M.Articulo = idArticulo
        and		(  		M.FechaCierre is null 
    			or 		M.FechaCierre > fechaConsulta);
        
    DECLARE
        CONTINUE HANDLER FOR
        SQLSTATE '02000'
            SET eofSelect = TRUE;
    OPEN cur1;
    myLoop: LOOP
        FETCH cur1 INTO idMMI;
        IF eofSelect THEN
            CLOSE cur1;
            LEAVE myLoop;
        END IF;
	
	SET cant = 0;
        
        select 	ifnull(CantidadActual,0) into cant
        from 	MmisMovimientos 
        where 	Mmi = idMMI
        and		Fecha <= fechaConsulta
        order by Fecha DESC
        limit 1;
        
        set total = total + cant;
    END LOOP;
    
    RETURN total;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fStockProducto` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fStockProducto`(idProducto INT, esAlmacen INT, idUMedida INT) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
	DECLARE uMedida DECIMAL;
	DECLARE total DECIMAL;
  Select UnidadMinima into uMedida from UnidadesDeMedidas where Id = idUMedida;
	IF esAlmacen = 0 THEN
    
  SELECT sum(
			  CantidadActual *
			  IF(A.CantidadPorPackaging1 is null,1,A.CantidadPorPackaging1) *
			  IF(A.CantidadPorPackaging2 is null,1,A.CantidadPorPackaging2) *
			  IF(A.CantidadPorPackaging3 is null,1,A.CantidadPorPackaging3) *
			  IF(A.CantidadPorPackaging4 is null,1,A.CantidadPorPackaging4) *
			  (A.Cantidad * Um.UnidadMinima / uMedida)
		  ) into total
   FROM
					Mmis m
			  join Articulos A on A.Id = m.Articulo and A.Producto = idProducto
			  join UnidadesDeMedidas Um on A.UnidadDeMedida = Um.Id
   Where m.FechaCierre is null;
   
  ELSE
    SELECT sum(
			  CantidadActual *
			  IF(A.CantidadPorPackaging1 is null,1,A.CantidadPorPackaging1) *
			  IF(A.CantidadPorPackaging2 is null,1,A.CantidadPorPackaging2) *
			  IF(A.CantidadPorPackaging3 is null,1,A.CantidadPorPackaging3) *
			  IF(A.CantidadPorPackaging4 is null,1,A.CantidadPorPackaging4) *
			  (A.Cantidad * Um.UnidadMinima / uMedida)
		  ) into total
   FROM
					Mmis m
			  join Articulos A on A.Id = m.Articulo and A.Producto = idProducto
			  join UnidadesDeMedidas Um on A.UnidadDeMedida = Um.Id
   Where m.FechaCierre is null and almacen = esAlmacen;
  END IF;
  RETURN total;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `fValorActualCategoria` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `fValorActualCategoria`(
        idCategoria INTEGER
    ) RETURNS decimal(12,4)
    READS SQL DATA
BEGIN
		
	DECLARE ValorActual 		DECIMAL(12,4);
	set ValorActual = 0.0000;
    
    SELECT 	ifnull(Valor,0.00)
    INTO 	ValorActual 
    from 	ConveniosCategoriasDetalles 
    WHERE 	ConvenioCategoria = idCategoria
    AND		FechaDesde <= date(now())
    AND     ifnull(FechaHasta,'2999-01-01') >= date(now())
    ORDER BY Id asc
    LIMIT 1;
    
    
	
    RETURN ValorActual;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `temp_fCompPago_Monto_aPagar` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `temp_fCompPago_Monto_aPagar`(
        idComprobante INTEGER(11)
    ) RETURNS decimal(10,4)
BEGIN
DECLARE Monto 				DECIMAL(10,4);
DECLARE GrupoPadre 			INTEGER;
DECLARE MultiplicadorPadre 	INTEGER;
DECLARE Cerrado 			INTEGER;
	
SELECT 	TC.Grupo, C.Cerrado
INTO	GrupoPadre, Cerrado
FROM 	TiposDeComprobantes TC
		INNER JOIN Comprobantes C 		ON C.TipoDeComprobante = TC.Id
WHERE	C.Id = idComprobante;
IF (GrupoPadre = 11) THEN 
	SET MultiplicadorPadre = -1;
ELSE
	IF (GrupoPadre = 9) THEN 
    	SET MultiplicadorPadre = 1;
    ELSE
     	SET MultiplicadorPadre = 0;
    END IF;
END IF;
	
	
	
	
	
SELECT 	SUM(fComprobante_Monto_Disponible(CR.ComprobanteHijo)*TC.Multiplicador*MultiplicadorPadre)
INTO 	Monto
FROM 	ComprobantesRelacionados CR
	INNER JOIN Comprobantes CH 			ON CH.Id = CR.ComprobanteHijo
	INNER JOIN TiposDeComprobantes TC	ON TC.Id = CH.TipoDeComprobante
WHERE	CR.ComprobantePadre = idComprobante;
RETURN ROUND(Monto,4);
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `xxx_fComprobanteTotal___old` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `xxx_fComprobanteTotal___old`(
        idComprobante INTEGER
    ) RETURNS decimal(10,4)
    READS SQL DATA
BEGIN
	DECLARE Comprobante 		integer;
	DECLARE FechaEmision 		datetime;
	DECLARE TipoDeComprobante 	INTEGER;		
	DECLARE MontoTotal 		decimal(10,4);
	DECLARE NetoGravado 		DECIMAL(10,4);
	SELECT
	  `C`.`Id`                AS `Id`,
	  `C`.`FechaEmision`      AS `FechaEmision`,
	  `C`.`TipoDeComprobante` AS `TipoDeComprobante`,
	  ROUND(((IFNULL((SELECT SUM(`vCDe`.`Monto`) AS `M1` FROM `vComprobanteDetalle` `vCDe` WHERE (`vCDe`.`IdComprobante` = `C`.`Id`)),0) - IFNULL((SELECT SUM(`vCD`.`Descuento`) AS `M2` FROM `vComprobanteDescuento` `vCD` WHERE (`vCD`.`Id` = `C`.`Id`)),0)) + IFNULL((SELECT SUM(`vCI`.`Monto`) AS `M3` FROM `vConceptosImpositivos` `vCI` WHERE (`vCI`.`ComprobantePadre` = `C`.`Id`)),0)),4) AS `MontoTotal`,
	  ROUND(((SELECT SUM(((`CD`.`Cantidad` * `CD`.`PrecioUnitario`) * IFNULL((1 - (`CD`.`DescuentoEnPorcentaje` / 100)),1))) AS `M4` FROM `ComprobantesDetalles` `CD` WHERE (`CD`.`Comprobante` = `C`.`Id`)) - IFNULL(`C`.`DescuentoEnMonto`,0)),4) AS `NetoGravado` 
	  into 
	  Comprobante,
	  FechaEmision,
	  TipoDeComprobante,
	  MontoTotal,
	  NetoGravado
	FROM `Comprobantes` `C` WHERE C.Id = idComprobante;
	RETURN MontoTotal;
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `xxx_fEstadoRelHijoPago2___old` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `xxx_fEstadoRelHijoPago2___old`(idComprobante INT) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE usado 		DECIMAL(15,6);
    DECLARE trae  		DECIMAL(15,6);
    DECLARE monto  		DECIMAL(15,6);
    DECLARE descuento1  	DECIMAL(15,6);
    DECLARE descuento2 		DECIMAL(15,6);
    DECLARE descuento3 		DECIMAL(15,6);
    DECLARE montoconcepto  	DECIMAL(15,6);                   
    DECLARE estado 		VARCHAR(20);
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN " ";     
    
    SELECT  SUM(`ComprobantesDetalles`.`Cantidad` * `ComprobantesDetalles`.`PrecioUnitario`) INTO monto 
    FROM `ComprobantesDetalles`
	JOIN `Comprobantes`
		ON `ComprobantesDetalles`.`Comprobante` = `Comprobantes`.`Id`
    WHERE `Comprobantes`.`Id` = idComprobante
	and `Comprobantes`.Cerrado = 1;
	
    SELECT  sum((((`ComprobantesDetalles`.`Cantidad` * `ComprobantesDetalles`.`PrecioUnitario`) * `ComprobantesDetalles`.`DescuentoEnPorcentaje`) / 100)) into descuento1 
    FROM `ComprobantesDetalles`
	JOIN `Comprobantes`
		ON `ComprobantesDetalles`.`Comprobante` = `Comprobantes`.`Id`
    WHERE `Comprobantes`.`Id` = idComprobante
	AND `Comprobantes`.Cerrado = 1;
	
    SELECT  sum(IFNULL(`ComprobantesDetalles`.`DescuentoEnMonto`,0)) INTO descuento2 
    FROM `ComprobantesDetalles`
	JOIN `Comprobantes`
		ON `ComprobantesDetalles`.`Comprobante` = `Comprobantes`.`Id`
    WHERE `Comprobantes`.`Id` = idComprobante
	AND `Comprobantes`.Cerrado = 1;
	
    SELECT  IFNULL(`Comprobantes`.`DescuentoEnMonto`,0) INTO descuento3 
    FROM `Comprobantes`
    WHERE `Comprobantes`.`Id` = idComprobante
	AND `Comprobantes`.Cerrado = 1;			
		
    SELECT distinct
	 sum(IFNULL(`ComprobantePadre`.`Monto`,0)) into `montoconcepto`
    FROM `Comprobantes`
	LEFT JOIN `Comprobantes` `ComprobantePadre`
	   ON `ComprobantePadre`.`ComprobantePadre` = `Comprobantes`.`Id`
	LEFT JOIN `TiposDeComprobantes`
	   ON `ComprobantePadre`.`TipoDeComprobante` = `TiposDeComprobantes`.`Id` AND `TiposDeComprobantes`.`DiscriminaImpuesto` = 1
    WHERE `Comprobantes`.`Id` =	idComprobante
	AND `Comprobantes`.Cerrado = 1;	
	
      IF monto IS NULL THEN
        SET monto = 0;
      END IF;	
      IF descuento1 IS NULL THEN
        SET descuento1 = 0;
      END IF;
      IF descuento2 IS NULL THEN
        SET descuento2 = 0;
      END IF;
      IF descuento3 IS NULL THEN
        SET descuento3 = 0;
      END IF;
      IF montoconcepto IS NULL THEN
        SET montoconcepto = 0;
      END IF;	
      
    SET  usado =  monto - descuento1 - descuento2 - descuento3 + montoconcepto; 
            
    SELECT   SUM(IFNULL(CD.PrecioUnitario,0)) INTO trae 
      FROM Comprobantes C,
           ComprobantesDetalles CD,
           ComprobantesRelacionados CR
      WHERE C.Id = CD.Comprobante
            AND C.Id = CR.ComprobantePadre
            AND C.Cerrado = 1
            AND CR.ComprobanteHijo = idComprobante;
      
      IF usado is null then
        set usado = 0;
      end if;
      IF trae is null then
        set trae = 0;
      end if;
    	IF trae = 0 THEN
        	set estado = 'Nada';
    	ELSE
          IF usado > trae THEN
            set estado = 'Parcialmente';
          ELSE
            IF usado = trae THEN
              set estado = 'Totalmente';
            ELSE
              IF usado < trae THEN
                set estado = 'Totalmente';
              END IF;
            END IF;
          END IF;
      END IF;
  RETURN estado collate utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP FUNCTION IF EXISTS `xxx_fEstadoRelHijoPago___old` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 FUNCTION `xxx_fEstadoRelHijoPago___old`(
        idComprobante INT
    ) RETURNS varchar(20) CHARSET utf8
    READS SQL DATA
BEGIN
    DECLARE MontoDelComprobante 		DECIMAL(15,6);
    DECLARE Pagado  		DECIMAL(15,6);
    DECLARE monto  		DECIMAL(15,6);
    DECLARE descuento1  	DECIMAL(15,6);
    DECLARE descuento2 		DECIMAL(15,6);
    DECLARE montoND  		DECIMAL(15,6);
    DECLARE descuento1ND  	DECIMAL(15,6);
    DECLARE descuento2ND	DECIMAL(15,6);    
    DECLARE descuento3 		DECIMAL(15,6);
    DECLARE montoconcepto  	DECIMAL(15,6);
    declare MontoNotasQueDescuentan          DECIMAL(15,6);         
    DECLARE estado 		VARCHAR(20);
    DECLARE CondicionDePago INT;
    DECLARE EXIT HANDLER FOR NOT FOUND RETURN " ";     
	set estado = '';
    set CondicionDePago = 0;
    
    
    
    SELECT  IFNULL(C.DescuentoEnMonto,0), 
    		IFNULL(C.CondicionDePago,0) 
    INTO 	descuento3, 
    		CondicionDePago
    FROM    Comprobantes C
    WHERE   C.Id = idComprobante
	AND     C.Cerrado = 1;	
    
    
    if CondicionDePago = 2 then
        set estado = 'Contado';
    else
        
        
        
        SELECT  SUM(CD.Cantidad * CD.PrecioUnitario) , 
        		sum((((CD.Cantidad * CD.PrecioUnitario) * CD.DescuentoEnPorcentaje) / 100)),
                sum(IFNULL(CD.DescuentoEnMonto,0))
        INTO 	monto, 		
        		descuento1, 
                descuento2  
        FROM 	ComprobantesDetalles CD
                INNER JOIN Comprobantes C ON CD.Comprobante = C.Id and C.Cerrado = 1
        WHERE   C.Id = idComprobante ;
        
        
        
        
        SELECT  sum(IFNULL(Padre.Monto,0)) into montoconcepto
        FROM    Comprobantes C 
                LEFT JOIN Comprobantes Padre ON Padre.ComprobantePadre = C.Id
                LEFT JOIN TiposDeComprobantes TC ON Padre.TipoDeComprobante = TC.Id AND TC.DiscriminaImpuesto = 1
        WHERE   C.Id = idComprobante
        AND     C.Cerrado = 1;	
        
        IF monto IS NULL THEN           SET monto = 0;          END IF;	
        IF descuento1 IS NULL THEN      SET descuento1 = 0;     END IF;
        IF descuento2 IS NULL THEN      SET descuento2 = 0;     END IF;
        IF descuento3 IS NULL THEN      SET descuento3 = 0;     END IF;
        IF montoconcepto IS NULL THEN   SET montoconcepto = 0;  END IF;	
        
        
        SET  MontoDelComprobante =  monto - descuento1 - descuento2 - descuento3 + montoconcepto; 
        
        
        SELECT  SUM(IFNULL(CD.PrecioUnitario,0)) INTO Pagado 
        FROM    Comprobantes C
                INNER JOIN ComprobantesDetalles CD       ON C.Id = CD.Comprobante
                INNER JOIN ComprobantesRelacionados CR   ON C.Id = CR.ComprobantePadre
        WHERE   C.Cerrado = 1
        AND     CR.ComprobanteHijo = idComprobante;
        
        SELECT  SUM(CD.Cantidad * CD.PrecioUnitario) , 
        	SUM((((CD.Cantidad * CD.PrecioUnitario) * CD.DescuentoEnPorcentaje) / 100)),
                SUM(IFNULL(CD.DescuentoEnMonto,0))
        INTO 	montoND, 		
        	descuento1ND, 
                descuento2ND  
        FROM 	ComprobantesRelacionados CR
                inner join ComprobantesRelacionados ND on CR.ComprobantePadre = ND.ComprobantePadre and ND.ComprobanteHijo <> idComprobante
                inner join ComprobantesDetalles CD on CD.Comprobante = ND.ComprobanteHijo
                inner join Comprobantes C on C.Id = CD.Comprobante 
                inner join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante and TC.Grupo in (8,9) 
        where 	CR.ComprobanteHijo = idComprobante;
        
               
        SET  MontoNotasQueDescuentan =  montoND - descuento1ND - descuento2ND; 
          
        IF MontoDelComprobante 		is null then     set MontoDelComprobante = 0;         end if;
        IF Pagado 			is null then                  set Pagado = 0;                     end if;
        IF MontoNotasQueDescuentan 	IS NULL THEN                  SET MontoNotasQueDescuentan = 0;                     END IF;
        set estado = '???';
        
        IF Pagado = 0 THEN
            set estado = 'Nada';
        ELSE
            IF (truncate((MontoDelComprobante - Pagado - MontoNotasQueDescuentan),2) > '0,00') THEN
                set estado = 'Parcialmente';
            ELSE
                set estado = 'Totalmente';
            END IF;
        END IF;
        
    end if;
    
    RETURN estado collate utf8_unicode_ci;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `AFIP_exportador_LibroIVA_Compra` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `AFIP_exportador_LibroIVA_Compra`(
        IN `idLibro` INTEGER(11) UNSIGNED,
        IN `tipoLibro` INTEGER(11) UNSIGNED
    )
BEGIN

SELECT

 PeriodoImputacionDelComprobante,
 LPAD(FechaComprobante,10,' '),
 TipoDeComprobante,
 PuntoDeVenta,
 NumeroComprobante,
 LPAD(Cuit,11,'0'),
 RPAD(RazonSocial,40,' '),
 ImporteNetoGravado105,
 ImporteNetoGravado210,
 ImporteNetoGravado270,
 ImporteIVA105,
 ImporteIVA210,
 ImporteIVA270,
 ImporteImpuestosInternos,
 ImporteConceptosExentosONoGravados,
 ImportePercepcionesIVA,
 ImporteOtrasPercepcionesImpuestosNacionales, 
 ImportePercepcionesImpuestosProvinciales,
 ImportePercepcionesTasaMunicipales,
 ImporteTotalComprobante

FROM (
select 
 CONCAT(LPAD(L.Mes,2,'0'),'/',LPAD(L.Anio,4,'0')) as PeriodoImputacionDelComprobante,
 CAST(DATE_FORMAT(C.FechaEmision, '%d/%m/%Y') AS CHAR CHARSET utf8) as FechaComprobante,
 ATC.Codigo as TipoDeComprobante,
 C.Punto as PuntoDeVenta,
 ifnull(C.Numero,0) as NumeroComprobante,
 IF (C.Anulado = 0,REPLACE(P.Cuit,'-',''),"") as Cuit,
 IF (C.Anulado = 0,LEFT(P.RazonSocial,40),"ANULADA") as RazonSocial,
 LD.ImporteNetoGravado105 as ImporteNetoGravado105,
 LD.ImporteNetoGravado210 as ImporteNetoGravado210,
 LD.ImporteNetoGravado270 as ImporteNetoGravado270,
 LD.ImporteIVA105 as ImporteIVA105,
 LD.ImporteIVA210 as ImporteIVA210,
 LD.ImporteIVA270 as ImporteIVA270,
 LD.ImporteImpuestosInternos as ImporteImpuestosInternos,
 LD.ImporteConceptosExentosONoGravados as ImporteConceptosExentosONoGravados,
 
 LD.ImportePercepcionesIVA +
 LD.ImporteRetencionesIVA as ImportePercepcionesIVA,
 
 LD.ImporteOtrasPercepcionesImpuestosNacionales + 
 LD.ImporteOtrasRetencionesImpuestosNacionales +
 LD.ImportePercepcionesGanancias + 
 LD.ImporteRetencionesGanancias +
 LD.ImportePercepcionesSuss + 
 LD.ImporteRetencionesSuss as ImporteOtrasPercepcionesImpuestosNacionales, 
 
 LD.ImporteOtrasPercepcionesImpuestosProvinciales + 
 LD.ImporteOtrasRetencionesImpuestosProvinciales +
 LD.ImportePercepcionesIB + ImporteRetencionesIB as ImportePercepcionesImpuestosProvinciales,
 
 LD.ImportePercepcionesTasaMunicipales +
 LD.ImporteRetencionesTasaMunicipales as ImportePercepcionesTasaMunicipales,

 LD.ImporteTotalComprobante as ImporteTotalComprobante,
 L.Id as IdLibro,
 1 as Orden,
 C.FechaEmision as Fecha
from LibrosIVADetalles LD
left join LibrosIVA L on L.Id = LD.LibroIVA
left join Comprobantes C on C.Id = LD.Comprobante
left join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
left join AfipTiposDeComprobantes ATC on ATC.Id = TC.Afip
left join Personas P on P.Id = C.Persona 
where LD.Comprobante IS NOT NULL
and L.Id = idLibro
and LD.TipoDeLibro = tipoLibro

UNION

select 
 CONCAT(LPAD(L.Mes,2,'0'),'/',LPAD(L.Anio,4,'0')) as PeriodoImputacionDelComprobante,
 "" as FechaComprobante,
 CASE LEFT(LD.Numero,2) 
 WHEN 'FA' THEN 1
 WHEN 'FB' THEN 6
 END as TipoDeComprobante,
 CAST(SUBSTR(LD.Numero,4,4) as SIGNED) as PuntoDeVenta,
 CAST(SUBSTR(LD.Numero,9,8) as SIGNED) as NumeroComprobante,
 "" as Cuit,
 "ANULADA (SIN EMITIR)" as RazonSocial,
 0 as ImporteNetoGravado105,
 0 as ImporteNetoGravado210,
 0 as ImporteNetoGravado270,
 0 as ImporteIVA105,
 0 as ImporteIVA210,
 0 as ImporteIVA270,
 0 as ImporteImpuestosInternos,
 0 as ImporteConceptosExentosONoGravados,
 0 as ImportePercepcionesIVA,
 0 as ImporteOtrasPercepcionesImpuestosNacionales, 
 0 as ImportePercepcionesImpuestosProvinciales,
 0 as ImportePercepcionesTasaMunicipales,
 0 as ImporteTotalComprobante,
 L.Id as IdLibro,
 2 as Orden,
 null as Fecha
from LibrosIVADetalles LD
left join LibrosIVA L on L.Id = LD.LibroIVA
where LD.Comprobante IS NULL
and L.Id = idLibro
and LD.TipoDeLibro = tipoLibro
) AS UnionLibroIVA
Order by Orden asc, Fecha asc, NumeroComprobante asc;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `AFIP_exportador_LibroIVA_Venta` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `AFIP_exportador_LibroIVA_Venta`(
        IN `idLibro` INTEGER(11) UNSIGNED,
        IN `tipoLibro` INTEGER(11) UNSIGNED
    )
BEGIN

SELECT

-- PeriodoImputacionDelComprobante,
 LPAD(FechaComprobante,10,' '),
 TipoDeComprobante,
 PuntoDeVenta,
 NumeroComprobanteDesde,
 NumeroComprobanteHasta,
 LPAD(Cuit,11,'0'),
 RPAD(RazonSocial,40,' '),
 ImporteNetoGravado105,
 ImporteNetoGravado210,
-- ImporteNetoGravado270,
 ImporteIVA105,
 ImporteIVA210,
-- ImporteIVA270,
 ImporteImpuestosInternos,
 ImporteConceptosExentosONoGravados,
 ImportePercepcionesIVA,
 ImporteOtrasPercepcionesImpuestosNacionales, 
 ImportePercepcionesImpuestosProvinciales,
 ImportePercepcionesTasaMunicipales,
 ImporteTotalComprobante

FROM (
select 
-- CONCAT(LPAD(L.Mes,2,'0'),'/',LPAD(L.Anio,4,'0')) as PeriodoImputacionDelComprobante,
 CAST(DATE_FORMAT(C.FechaEmision, '%d/%m/%Y') AS CHAR CHARSET utf8) as FechaComprobante,
 ATC.Codigo as TipoDeComprobante,
 C.Punto as PuntoDeVenta,
 ifnull(C.Numero,0) as NumeroComprobanteDesde,
 ifnull(C.Numero,0) as NumeroComprobanteHasta,
 IF (C.Anulado = 0,REPLACE(P.Cuit,'-',''),"") as Cuit,
 IF (C.Anulado = 0,LEFT(P.RazonSocial,40),"ANULADA") as RazonSocial,
 LD.ImporteNetoGravado105 as ImporteNetoGravado105,
 LD.ImporteNetoGravado210 as ImporteNetoGravado210,
-- LD.ImporteNetoGravado270 as ImporteNetoGravado270,
 LD.ImporteIVA105 as ImporteIVA105,
 LD.ImporteIVA210 as ImporteIVA210,
-- LD.ImporteIVA270 as ImporteIVA270,
 LD.ImporteImpuestosInternos as ImporteImpuestosInternos,
 LD.ImporteConceptosExentosONoGravados as ImporteConceptosExentosONoGravados,
 
 LD.ImportePercepcionesIVA +
 LD.ImporteRetencionesIVA as ImportePercepcionesIVA,
 
 LD.ImporteOtrasPercepcionesImpuestosNacionales + 
 LD.ImporteOtrasRetencionesImpuestosNacionales +
 LD.ImportePercepcionesGanancias + 
 LD.ImporteRetencionesGanancias +
 LD.ImportePercepcionesSuss + 
 LD.ImporteRetencionesSuss as ImporteOtrasPercepcionesImpuestosNacionales, 
 
 LD.ImporteOtrasPercepcionesImpuestosProvinciales + 
 LD.ImporteOtrasRetencionesImpuestosProvinciales +
 LD.ImportePercepcionesIB + ImporteRetencionesIB as ImportePercepcionesImpuestosProvinciales,
 
 LD.ImportePercepcionesTasaMunicipales +
 LD.ImporteRetencionesTasaMunicipales as ImportePercepcionesTasaMunicipales,

 LD.ImporteTotalComprobante as ImporteTotalComprobante,
 L.Id as IdLibro,
 1 as Orden,
 C.FechaEmision as Fecha
from LibrosIVADetalles LD
left join LibrosIVA L on L.Id = LD.LibroIVA
left join Comprobantes C on C.Id = LD.Comprobante
left join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
left join AfipTiposDeComprobantes ATC on ATC.Id = TC.Afip
left join Personas P on P.Id = C.Persona 
where LD.Comprobante IS NOT NULL
and L.Id = idLibro
and LD.TipoDeLibro = tipoLibro

UNION

select 
-- CONCAT(LPAD(L.Mes,2,'0'),'/',LPAD(L.Anio,4,'0')) as PeriodoImputacionDelComprobante,
 "" as FechaComprobante,
 CASE LEFT(LD.Numero,2) 
 WHEN 'FA' THEN 1
 WHEN 'FB' THEN 6
 END as TipoDeComprobante,
 CAST(SUBSTR(LD.Numero,4,4) as SIGNED) as PuntoDeVenta,
 CAST(SUBSTR(LD.Numero,9,8) as SIGNED) as NumeroComprobanteDesde,
 CAST(SUBSTR(LD.Numero,9,8) as SIGNED) as NumeroComprobanteHasta,
 "" as Cuit,
 "ANULADA (SIN EMITIR)" as RazonSocial,
 0 as ImporteNetoGravado105,
 0 as ImporteNetoGravado210,
-- 0 as ImporteNetoGravado270,
 0 as ImporteIVA105,
 0 as ImporteIVA210,
-- 0 as ImporteIVA270,
 0 as ImporteImpuestosInternos,
 0 as ImporteConceptosExentosONoGravados,
 0 as ImportePercepcionesIVA,
 0 as ImporteOtrasPercepcionesImpuestosNacionales, 
 0 as ImportePercepcionesImpuestosProvinciales,
 0 as ImportePercepcionesTasaMunicipales,
 0 as ImporteTotalComprobante,
 L.Id as IdLibro,
 2 as Orden,
 null as Fecha
from LibrosIVADetalles LD
left join LibrosIVA L on L.Id = LD.LibroIVA
where LD.Comprobante IS NULL
and L.Id = idLibro
and LD.TipoDeLibro = tipoLibro
) AS UnionLibroIVA
Order by Orden asc, Fecha asc, NumeroComprobanteDesde asc;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Almacenes_Rack` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Almacenes_Rack`(
        IN IdAlmacen INTEGER(11),
        IN Orientacion INTEGER(11)
    )
BEGIN
	
if (Orientacion = 1 or Orientacion not in (1,2,3)) then
		select	U.Id						as Id,
				U.Descripcion				as Descripcion,
				U.Almacen					as Almacen,
				
				U.Fila 						as Fila,
                AL.RackCantFila				as CantFila,
				U.Altura 					as Altura,
                AL.RackCantAltura			as CantAltura,
				U.Profundidad 				as Profundidad,
                AL.RackCantProfundidad		as CantProfundidad,
				
				U.Existente					as Existente,
				A.Descripcion 				as A_Descripcion,
				M.Id 						as Mmi_Id,
				M.Identificador 			as Mmi_Identificador,
				M.UnidadDeMedida 			as Mmi_UnidadDeMedida,
				M.Articulo 					as Mmi_Articulo,
             M.ArticuloVersion  		as Mmi_ArticuloVersion,
				M.RemitoArticulo 			as Mmi_RemitoArticulo,
				M.RemitoArticuloSalida 		as Mmi_RemitoArticuloSalida,
				M.TipoDePalet 				as Mmi_TipoDePalet,
				M.CantidadOriginal 			as Mmi_CantidadOriginal,
				M.CantidadActual 			as Mmi_CantidadActual,
				M.ParaFason 				as Mmi_ParaFason,
				M.FechaIngreso 				as Mmi_FechaIngreso,
				M.FechaVencimiento 			as Mmi_FechaVencimiento,
				M.Descripcion 				   as Mmi_Descripcion,
				M.HabilitadoParaProduccion 	as Mmi_HabilitadoParaProduccion,
				
				M.MmiTipo 					as Mmi_MmiTipo,
            Lote.Numero as Lote_Numero,
            Lote.FechaVencimiento  as Lote_FechaVencimiento,
            Lote.FechaElaboracion  as Lote_FechaElaboracion,
            CE.Numero                as CE_Numero,
            CE.Punto                 as CE_Punto,
            CS.Numero                as CS_Numero,
            CS.Punto                 as CS_Punto
		from	Ubicaciones U
		inner join Almacenes AL				on AL.Id	= U.Almacen 
        left join Mmis M 					on U.Id 	= M.Ubicacion and M.FechaCierre IS NULL
		left join ComprobantesDetalles CD 	on CD.Id 	= M.RemitoArticulo
      left join ComprobantesDetalles CDS 	on CDS.Id 	= M.RemitoArticuloSalida
      left join Comprobantes CE on CE.Id 	= CD.Comprobante
      left join Comprobantes CS on CS.Id 	= CDS.Comprobante
		left join Articulos A				on A.Id		= CD.Articulo
      left join Lotes Lote on Lote.Id = M.Lote
		where U.Almacen = idAlmacen
		order by 	U.Profundidad ASC,
					U.Altura DESC,
					U.Fila ASC;
END If;
if (Orientacion = 2) then
		select	U.Id						as Id,
				U.Descripcion				as Descripcion,
				U.Almacen					as Almacen,
				
				U.Fila 						as Fila,
				AL.RackCantFila				as CantFila,
                U.Altura 					as Profundidad,
				AL.RackCantAltura			as CantProfundidad,
                U.Profundidad 				as Altura,
                AL.RackCantProfundidad		as CantAltura,
				
				U.Existente					as Existente,
				A.Descripcion 				as A_Descripcion,
				M.Id 						as Mmi_Id,
				M.Identificador 			as Mmi_Identificador,
				M.UnidadDeMedida 			as Mmi_UnidadDeMedida,
				M.Articulo 					as Mmi_Articulo,
             M.ArticuloVersion  		as Mmi_ArticuloVersion,
				M.RemitoArticulo 			as Mmi_RemitoArticulo,
				M.RemitoArticuloSalida 		as Mmi_RemitoArticuloSalida,
				M.TipoDePalet 				as Mmi_TipoDePalet,
				M.CantidadOriginal 			as Mmi_CantidadOriginal,
				M.CantidadActual 			as Mmi_CantidadActual,
				M.ParaFason 				as Mmi_ParaFason,
				M.FechaIngreso 				as Mmi_FechaIngreso,
				M.FechaVencimiento 			as Mmi_FechaVencimiento,
				M.Descripcion 				as Mmi_Descripcion,
				M.HabilitadoParaProduccion 	as Mmi_HabilitadoParaProduccion,
				
				M.MmiTipo 					as Mmi_MmiTipo,
            Lote.Numero as Lote_Numero,
            Lote.FechaVencimiento  as Lote_FechaVencimiento,
            Lote.FechaElaboracion  as Lote_FechaElaboracion,
            CE.Numero                as CE_Numero,
            CE.Punto                 as CE_Punto,
            CS.Numero                as CS_Numero,
            CS.Punto                 as CS_Punto
		from	Ubicaciones U
		inner join Almacenes AL				on AL.Id	= U.Almacen 
        left join Mmis M 					on U.Id 	= M.Ubicacion and M.FechaCierre IS NULL
		left join ComprobantesDetalles CD 	on CD.Id 	= M.RemitoArticulo
      left join ComprobantesDetalles CDS 	on CDS.Id 	= M.RemitoArticuloSalida
      left join Comprobantes CE on CE.Id 	= CD.Comprobante
      left join Comprobantes CS on CS.Id 	= CDS.Comprobante
		left join Articulos A				on A.Id		= CD.Articulo 
      left join Lotes Lote  on Lote.Id = M.Lote
		where U.Almacen = idAlmacen
		order by 	U.Altura ASC,
					U.Profundidad DESC,
					U.Fila ASC;    
	
END If;
if (Orientacion = 3) then
		select	U.Id						as Id,
				U.Descripcion				as Descripcion,
				U.Almacen					as Almacen,
				
				U.Fila 						as Profundidad,
				AL.RackCantFila				as CantProfundidad,
                U.Altura 					as Altura,
				AL.RackCantAltura			as CantAltura,
                U.Profundidad 				as Fila,
                AL.RackCantProfundidad		as CantFila,
				
				U.Existente					as Existente,
				A.Descripcion 				as A_Descripcion,
				M.Id 						as Mmi_Id,
				M.Identificador 			as Mmi_Identificador,
				M.UnidadDeMedida 			as Mmi_UnidadDeMedida,
				M.Articulo 					as Mmi_Articulo,
             M.ArticuloVersion  		as Mmi_ArticuloVersion,
				M.RemitoArticulo 			as Mmi_RemitoArticulo,
				M.RemitoArticuloSalida 		as Mmi_RemitoArticuloSalida,
				M.TipoDePalet 				as Mmi_TipoDePalet,
				M.CantidadOriginal 			as Mmi_CantidadOriginal,
				M.CantidadActual 			as Mmi_CantidadActual,
				M.ParaFason 				as Mmi_ParaFason,
				M.FechaIngreso 				as Mmi_FechaIngreso,
				M.FechaVencimiento 			as Mmi_FechaVencimiento,
				M.Descripcion 				as Mmi_Descripcion,
				M.HabilitadoParaProduccion 	as Mmi_HabilitadoParaProduccion,
				
				M.MmiTipo 					as Mmi_MmiTipo,
            Lote.Numero as Lote_Numero,
            Lote.FechaVencimiento  as Lote_FechaVencimiento,
            Lote.FechaElaboracion  as Lote_FechaElaboracion,
            CE.Numero                as CE_Numero,
            CE.Punto                 as CE_Punto,
            CS.Numero                as CS_Numero,
            CS.Punto                 as CS_Punto
		from	Ubicaciones U
		inner join Almacenes AL				on AL.Id	= U.Almacen 
        left join Mmis M 					on U.Id 	= M.Ubicacion and M.FechaCierre IS NULL
		left join ComprobantesDetalles CD 	on CD.Id 	= M.RemitoArticulo
      left join ComprobantesDetalles CDS 	on CDS.Id 	= M.RemitoArticuloSalida
      left join Comprobantes CE on CE.Id 	= CD.Comprobante
      left join Comprobantes CS on CS.Id 	= CDS.Comprobante
		left join Articulos A				on A.Id		= M.Articulo
      left join Lotes Lote on Lote.Id = M.Lote
		where U.Almacen = idAlmacen
		order by 	U.Fila ASC,
					U.Altura DESC,
					U.Profundidad ASC;    
	
END If;
End */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ArbolDeArticulos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `ArbolDeArticulos`(
        IN ArtVer INTEGER(11),
        IN Salida VARCHAR(5)
    )
BEGIN
	    
        DECLARE done                        INT DEFAULT FALSE;
        DECLARE Art                         INTEGER;
        DECLARE ArtVerDet                   INTEGER;
        DECLARE ArtVerHijo                  INTEGER; 
        DECLARE cantidad		            INTEGER;
        DECLARE CantProducto				decimal(12,4);
        DECLARE CantidadProductoTotal		decimal(12,4); 
        
        DECLARE cur1 CURSOR FOR SELECT AV.Articulo, AVD.Id, AVD.ArticuloVersionHijo FROM ArticulosVersiones AV inner join ArticulosVersionesDetalles AVD on AV.Id = AVD.ArticuloVersionHijo WHERE AVD.ArticuloVersionPadre = ArtVer;
		DECLARE cur2 CURSOR FOR SELECT ifnull(avd.Cantidad,1) FROM ArbolArticulos_temp aatx inner join Articulos a on aatx.IdArticulo = a.Id left join ArticulosVersionesDetalles avd ON aatx.IdArticuloVersionDetalle = avd.Id inner join ArticulosVersiones av ON aatx.IdArticuloVersion = av.Id where aatx.IdArticulo not in (select AX2.IdArticulo FROM Arbol2 AX2);         
        
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;    
  
	    SET @@max_sp_recursion_depth=255; 
	    
	    DROP TABLE IF EXISTS `ArbolArticulos_temp`; 
	    
        CREATE TEMPORARY TABLE ArbolArticulos_temp
          (
          IdTabla INT(11) NOT NULL AUTO_INCREMENT,
          IdArticulo INT,
          IdArticuloVersionPadre INT,
          IdArticuloVersionDetalle INT,
          IdArticuloVersion INt,
          PRIMARY KEY  (`IdTabla`),
          UNIQUE KEY `IdTabla` (`IdTabla`)
          );
          
	    DROP TABLE IF EXISTS `Arbol2`; 
	    
        CREATE TEMPORARY TABLE Arbol2
          (
          IdTabla INT(11) NOT NULL AUTO_INCREMENT,
          IdArticulo INT,
          PRIMARY KEY  (`IdTabla`),
          UNIQUE KEY `IdTabla` (`IdTabla`)
          );          
        
        SELECT AV.Articulo INTO Art FROM ArticulosVersiones AV WHERE AV.Id = ArtVer;
        
        INSERT INTO ArbolArticulos_temp ( IdArticulo,  IdArticuloVersionPadre,  IdArticuloVersionDetalle, IdArticuloVersion) 
        VALUES (Art, NULL, NULL, ArtVer);
        
        select count(AVD.Id) into cantidad from ArticulosVersionesDetalles AVD where AVD.ArticuloVersionPadre = ArtVer;
        
        if(cantidad <> 0) then 
        
            OPEN cur1;
                REPEAT
                    FETCH cur1 INTO Art, ArtVerDet, ArtVerHijo;
                    IF NOT done THEN
                        
                        INSERT INTO ArbolArticulos_temp (IdArticulo,  IdArticuloVersionPadre,  IdArticuloVersionDetalle, IdArticuloVersion)
                        VALUES (Art, ArtVer, ArtVerDet, ArtVerHijo);
                        
                        CALL ArbolDeArticulosRecorrido(ArtVerHijo); 	
                        					
                    END IF;
                UNTIL done END REPEAT;
            CLOSE cur1;             
            
        end if;
    
	if (Salida = 'TC' or Salida = '') then
		SELECT  *
		FROM 	ArbolArticulos_temp aat3 
		inner join Articulos a 						on aat3.IdArticulo = a.Id 
		left join ArticulosVersiones avp 			on aat3.IdArticuloVersionPadre = avp.Id
		left join ArticulosVersionesDetalles avd 	on aat3.IdArticuloVersionDetalle = avd.Id
		inner join ArticulosVersiones av 			on aat3.IdArticuloVersion = av.Id;
	end if;
	if (Salida = 'P') then
    
        insert into Arbol2 (IdArticulo)
        SELECT 	aat2.IdArticulo 
        FROM 	ArbolArticulos_temp aat2 
        inner join Articulos A2 on aat2.IdArticulo = A2.Id 
        where 	A2.ArticuloGrupo = 1 
        and 	A2.UnidadDeMedida <> 3;
    	
    	
    	SET done = false;
        set CantidadProductoTotal = 1;
        
        OPEN cur2;
        REPEAT
            FETCH cur2 INTO CantProducto;
            IF NOT done THEN
                        
				set CantidadProductoTotal = CantProducto * CantidadProductoTotal;
                        					
            END IF;
        UNTIL done END REPEAT;
    	CLOSE cur2;    
		SELECT	a.Descripcion as Descripcion,
        		a.Id as IdArticulo,
                av.Version as ArticuloVersion,
                ifnull(avd.UnidadDeMedida,a.UnidadDeMedida) as UnidadDeMedida,
                CantidadProductoTotal  	 
		FROM 	ArbolArticulos_temp aat4 
		inner join Articulos a 						on aat4.IdArticulo = a.Id 
		left join ArticulosVersionesDetalles avd 	ON aat4.IdArticuloVersionDetalle = avd.Id
		inner join ArticulosVersiones av 			ON aat4.IdArticuloVersion = av.Id
        where 	a.EsMateriaPrima = 1
        and		a.ArticuloGrupo <> 1;
        
        
	end if;    
    
    
    
    
    
	SET @@max_sp_recursion_depth=0;	
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `ArbolDeArticulosRecorrido` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `ArbolDeArticulosRecorrido`(IN ArtVer INTEGER)
BEGIN
	    
        DECLARE done                        INT DEFAULT FALSE;
        DECLARE Art                         INTEGER;
        DECLARE ArtVerDet                   INTEGER;
        DECLARE ArtVerHijo                  INTEGER;  
        DECLARE cur1 CURSOR FOR SELECT AV.Articulo, AVD.Id, AVD.ArticuloVersionHijo FROM ArticulosVersiones AV INNER JOIN ArticulosVersionesDetalles AVD ON AV.Id = AVD.ArticuloVersionHijo WHERE AVD.ArticuloVersionPadre = ArtVer;
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;         
        
        OPEN cur1;
            REPEAT
                FETCH cur1 INTO Art, ArtVerDet, ArtVerHijo;
                IF NOT done THEN
                
                    INSERT INTO ArbolArticulos_temp (IdArticulo,  IdArticuloVersionPadre,  IdArticuloVersionDetalle, IdArticuloVersion)
                    VALUES (Art, ArtVer, ArtVerDet, ArtVerHijo);
                    
                    CALL ArbolDeArticulosRecorrido(ArtVerHijo); 	
                                        
                END IF;
            UNTIL done END REPEAT;
         CLOSE cur1;                  
                     
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `arreglar_Articulos` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `arreglar_Articulos`(
        IN Id_Art_Padre INTEGER(11),
        IN Id_VersionPadre INTEGER(11),
        IN Id_Art_Hijo INTEGER(11),
        IN Id_VersionHijo INTEGER(11),
        IN CantidadUnidades INTEGER(11),
        IN m1 INTEGER(11),
        IN m2 INTEGER(11),
        IN m3 INTEGER(11)
    )
BEGIN
DECLARE error INTEGER DEFAULT 0;
DECLARE CONTINUE HANDLER FOR SQLEXCEPTION SET error=1;
    INSERT INTO Articulos
    (
      Id,
        Tipo,  Codigo,  Descripcion,  CodigoDeBarras,
        UnidadDeMedida,
        UnidadDeMedidaDeProduccion,
        FactorDeConversion,  TipoDeControlDeStock,  EsInsumo,
        EsProducido,
        EsParaVenta,
        EsFinal,
        EsParaCompra,  ArticuloSubGrupo,  ArticuloGrupo,
        RequiereLote,  IVA,  Marca,
        EsMateriaPrima,  RequiereProtocolo,  Cuenta,
        Leyenda,  RNPA,  DescripcionLarga
    ) 
    select 	
        Id_Art_Hijo,
        Tipo,  Codigo,  Descripcion,  CodigoDeBarras,
        3,
        3,
        FactorDeConversion,  TipoDeControlDeStock,  EsInsumo,
        1,
        1,
        1,
        EsParaCompra,  ArticuloSubGrupo,  ArticuloGrupo,
        RequiereLote,  IVA,  Marca,
        EsMateriaPrima,  RequiereProtocolo,  Cuenta,
        Leyenda,  RNPA,  DescripcionLarga   
    from 	  Articulos A
    where 	A.Id = Id_Art_Padre;
    update  Articulos
    set     DescripcionLarga  	= concat(DescripcionLarga, ' x',CantidadUnidades ,'u'),
            Descripcion       	= concat(Descripcion, ' x',CantidadUnidades ,'u'),
            UnidadDeMedida		= 3,
        	UnidadDeMedidaDeProduccion = 3,
	        EsProducido 		= 1,
    	    EsParaVenta 		= 1,
        	EsFinal 			= 1            
    where   Id = Id_Art_Padre;
    insert into ArticulosVersiones
    (
        Id,
        Articulo,
        Version,
        Fecha,
        Descripcion
    ) 
    Values
    (
        Id_VersionHijo,
        Id_Art_Hijo,
        1,
        '2012-11-28',
        'Ver. Inicial'
    );
    insert into ArticulosVersionesDetalles
    (
        ArticuloVersionPadre,
        ArticuloVersionHijo,
        Cantidad,
        UnidadDeMedida
    )
    values
    (
        Id_VersionPadre,
        Id_VersionHijo,
        CantidadUnidades,
        3
    );
 
   
    if (m1) then
        update  ArticulosVersionesDetalles 
        set     ArticuloVersionPadre = Id_VersionHijo
        where   Id = m1;
    end if;
    if (m2) then
        update  ArticulosVersionesDetalles 
        set     ArticuloVersionPadre = Id_VersionHijo
        where   Id = m2;     
    end if;
    if (m3) then
        update  ArticulosVersionesDetalles 
        set     ArticuloVersionPadre = Id_VersionHijo
        where   Id = m3;
    end if;
iF error=1 THEN	
	ROLLBACK;
ELSE
	COMMIT;
END IF;
    
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_Cliente_o_Proveedor_Detalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_Cliente_o_Proveedor_Detalle`(
        IN tipoPersona VARCHAR(10)
    )
BEGIN
If (tipoPersona = 'Proveedor') Then
    select 	P.Id 									AS idPersona,
            P.RazonSocial							AS RazonSocial,
            P.Denominacion							AS Denominacion,
            fPersona_CuentaCorriente_A_Fecha(P.Id,DATE(now()),0) AS SaldoCtaCte,
            ''										AS EsProveedor,
            if (IFNULL(P.EsCliente,0),'(C)','')		AS EsCliente,
            P.cuit									AS CUIT,
            P.NroInscripcionIB 						AS NroIB,
            SUBSTR(P.RazonSocial,1,1) 				AS Letra
    from 	CuentasCorrientes CC
    inner join 	Personas P on P.Id = CC.Persona
    where	EsProveedor = 1
    group by P.RazonSocial
    order by P.RazonSocial;
Else
    select 	P.Id 									AS idPersona,
            P.RazonSocial							AS RazonSocial,
            P.Denominacion							AS Denominacion,
            fPersona_CuentaCorriente_A_Fecha(P.Id,DATE(now()),0) AS SaldoCtaCte,
            if (IFNULL(P.EsProveedor,0),'(P)','')	AS EsProveedor,
            ''										AS EsCliente,
            P.cuit									AS CUIT,
            P.NroInscripcionIB 						AS NroIB,
            SUBSTR(P.RazonSocial,1,1) 				AS Letra
    from 	CuentasCorrientes CC
    inner join 	Personas P on P.Id = CC.Persona
    where	EsCliente = 1
    group by P.RazonSocial
    order by P.RazonSocial;
End If;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobanteElectronicoExportacion_Cabecera` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobanteElectronicoExportacion_Cabecera`(
        IN IdComprobante INTEGER(11)
    )
BEGIN
SELECT 
 C.Id 						AS IdComprobante,
 TC.Descripcion 			AS TipoDeComprobante,
 TC.Codigo              	AS CodigoDeComprobante,
 ''							AS Localidad,
 ''							AS Provincia,
 ''							AS Cuit,
 UPPER(P.RazonSocial) 		AS RazonSocial, 
 UPPER(FEA.Direccion) 		AS Direccion,
 fNumeroCompleto(C.Id,'') 	AS NumeroCompleto, 
 C.FechaEmision 			AS FechaEmision, 
 UPPER(MI.Descripcion) 		AS ModalidadIVA, 
 ACP.Cuit 					AS CuitPaisDestino, 
 UPPER(TCP.Descripcion) 	AS CondicionPago,
 C.ObservacionesImpresas 	AS ObservacionesImpresas,
 C.Persona 					AS Persona,
 fComprobante_Monto_Total(IdComprobante) 	AS MontoTotal,
 CONCAT( 	SUBSTR(FEA.CAEFchVto,7,2),'/',
 			SUBSTR(FEA.CAEFchVto,5,2),'/',
 			SUBSTR(FEA.CAEFchVto,1,4)) 		AS CAE_FechaVencimiento, 
 FEA.Obs 					AS CAE_Obs, 
 FEA.CAE 					AS CAE_Numero,
 UPPER(AP.Descripcion) 		AS PaisDestino,
 CONCAT(AI.Codigo,'-',CDE.`IncotermDescripcion`) 		AS Incoterm,
 UPPER(AID.Descripcion) 	AS Idioma,
 UPPER(CDE.FormaDePago) 	AS FormaDePago,
 UPPER(AM.Descripcion) 		AS Moneda,
 C.ValorDivisa 				AS CotizacionMoneda
 
FROM 
 Comprobantes C
 LEFT JOIN ComprobantesDeExportaciones 			CDE	 	ON C.Id 	= CDE.Comprobante
 LEFT JOIN Paises 								PA 		ON PA.Id 	= CDE.PaisDestino
 LEFT JOIN AfipPaises 							AP 		ON AP.Id 	= PA.Afip
 LEFT JOIN PaisesCuit 							PC 		ON PA.Id 	= PC.Pais
 LEFT JOIN AfipCuitPaises 						ACP 	ON ACP.Id 	= PC.AfipCuitPais
 LEFT JOIN AfipIncoterms 						AI 		ON AI.Id 	= CDE.Incoterm
 
 LEFT JOIN Idiomas 							I 		ON I.Id 	= CDE.Idioma
 LEFT JOIN AfipIdiomas 						AID 	ON AID.Id 	= I.Afip
 LEFT JOIN TiposDeDivisas 						TD 		ON TD.Id 	= C.Divisa
 LEFT JOIN AfipMonedas 						AM 		ON AM.Id 	= TD.Afip
 
 LEFT JOIN FacturacionElectronicaAfip 			FEA 	ON C.Id 	= FEA.Comprobante 
 LEFT JOIN TiposDeComprobantes 				TC 		ON TC.Id 	= C.TipoDeComprobante
 LEFT JOIN Personas 							P 		ON P.Id 	= C.Persona

 LEFT JOIN ModalidadesIVA 						MI 		ON MI.Id 	= P.ModalidadIva
 LEFT JOIN TiposDeCondicionesDePago 			TCP 	ON TCP.Id 	= C.CondicionDePago
 
WHERE C.Id = IdComprobante 
LIMIT 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobanteElectronicoExportacion_Detalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobanteElectronicoExportacion_Detalle`(
 IN `IdComprobante` INTEGER(11)
 )
BEGIN

declare monedaComprobante INTEGER(1);

SELECT Divisa 
INTO monedaComprobante
FROM Comprobantes 
WHERE Id = IdComprobante;

IF (monedaComprobante = 1) THEN

 SELECT 
 C.Id AS IdComprobante,
 IF(ISNULL(CD.Articulo),CD.Observaciones,A.Descripcion) AS Articulo,
 CD.Cantidad AS Cantidad,
 
 CD.PrecioUnitario AS PrecioUnitario,
 UdM.Descripcion AS UnidadDeMedida,
 IF( ISNULL(CD.DescuentoEnPorcentaje),0,
 Round(CD.DescuentoEnPorcentaje/100,4)) AS DescuentoEnPorcentaje,
 (CD.Cantidad * CD.PrecioUnitario) AS MontoSinDescuento,
 IF( ISNULL(CD.DescuentoEnPorcentaje),0,
 (CD.Cantidad * CD.PrecioUnitario * CD.DescuentoEnPorcentaje / 100)) AS MontoDescuento,
 IF( ISNULL(CD.DescuentoEnPorcentaje),(CD.Cantidad * CD.PrecioUnitario), 
 (CD.Cantidad * CD.PrecioUnitario) - (CD.Cantidad * CD.PrecioUnitario * CD.DescuentoEnPorcentaje / 100)) AS MontoConDescuento
 FROM 
 ComprobantesDetalles CD 
 INNER JOIN Comprobantes C ON C.Id = CD.Comprobante 
 LEFT JOIN Articulos A ON A.Id = CD.Articulo 
 LEFT JOIN UnidadesDeMedidas UdM ON UdM.Id = A.UnidadDeMedida
 LEFT JOIN TiposDeComprobantes TC ON TC.Id = C.TipoDeComprobante
 where CD.Comprobante = IdComprobante
 order by Articulo;

ELSE

 SELECT 
 C.Id AS IdComprobante,
 IF(ISNULL(CD.Articulo),CD.Observaciones,A.Descripcion) AS Articulo,
 CD.Cantidad AS Cantidad,
 
 CD.PrecioUnitarioMExtranjera AS PrecioUnitario,
 UdM.Descripcion AS UnidadDeMedida,
 IF( ISNULL(CD.DescuentoEnPorcentaje),0,
 Round(CD.DescuentoEnPorcentaje/100,4)) AS DescuentoEnPorcentaje,
 (CD.Cantidad * CD.PrecioUnitarioMExtranjera) AS MontoSinDescuento,
 IF( ISNULL(CD.DescuentoEnPorcentaje),0,
 (CD.Cantidad * CD.PrecioUnitarioMExtranjera * CD.DescuentoEnPorcentaje / 100)) AS MontoDescuento,
 IF( ISNULL(CD.DescuentoEnPorcentaje),(CD.Cantidad * CD.PrecioUnitarioMExtranjera), 
 (CD.Cantidad * CD.PrecioUnitarioMExtranjera) - (CD.Cantidad * CD.PrecioUnitarioMExtranjera * CD.DescuentoEnPorcentaje / 100)) AS MontoConDescuento
 FROM 
 ComprobantesDetalles CD 
 INNER JOIN Comprobantes C ON C.Id = CD.Comprobante 
 LEFT JOIN Articulos A ON A.Id = CD.Articulo 
 LEFT JOIN UnidadesDeMedidas UdM ON UdM.Id = A.UnidadDeMedida
 LEFT JOIN TiposDeComprobantes TC ON TC.Id = C.TipoDeComprobante
 where CD.Comprobante = IdComprobante
 order by Articulo;

END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobanteElectronico_Cabecera` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobanteElectronico_Cabecera`(
        IN IdComprobante INTEGER(11)
    )
BEGIN
SELECT 
    C.Id 					AS IdComprobante,
    TC.Descripcion 			AS TipoDeComprobante,
    UPPER(P.RazonSocial)	AS RazonSocial,    
    UPPER(D.Direccion) 		AS Direccion,    
    UPPER(L.Descripcion) 	AS Localidad,   
    UPPER(PR.Descripcion) 	AS Provincia,    
    fNumeroCompleto(C.Id,'') AS NumeroCompleto,    
    C.FechaEmision 			AS FechaEmision,    
    UPPER(MI.Descripcion) 	AS ModalidadIVA,    
    P.Cuit 					AS Cuit,    
    UPPER(TCP.Descripcion) 	AS CondicionPago,
    C.ObservacionesImpresas AS ObservacionesImpresas,
    C.Persona 				AS Persona,
    fComprobante_Monto_Total(IdComprobante) AS MontoTotal,
	CONCAT(	SUBSTR(FEA.CAEFchVto,7,2),'/',
    		SUBSTR(FEA.CAEFchVto,5,2),'/',
			SUBSTR(FEA.CAEFchVto,1,4)) AS CAE_FechaVencimiento, 
    FEA.Obs 				AS CAE_Obs, 
    FEA.CAE 				AS CAE_Numero    
  FROM 
    Comprobantes C
    INNER JOIN  FacturacionElectronicaAfip FEA ON C.Id = FEA.Comprobante 
    INNER JOIN 	TiposDeComprobantes TC ON C.TipoDeComprobante = TC.Id     
    INNER JOIN 	Personas P ON C.Persona = P.Id 
    LEFT JOIN 	Direcciones D ON P.Id = D.Persona
    LEFT JOIN 	Localidades L ON D.Localidad = L.Id
    LEFT JOIN 	Provincias PR ON L.Provincia = PR.Id
    LEFT JOIN 	ModalidadesIVA MI ON P.ModalidadIva = MI.Id
    LEFT JOIN 	TiposDeCondicionesDePago TCP ON TCP.Id = C.CondicionDePago
     
WHERE C.Id = IdComprobante 
LIMIT 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobanteEmitido_NetosGravados` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobanteEmitido_NetosGravados`(
        IN IdComprobante INTEGER(11),
        IN Discriminado INTEGER(11)
    )
BEGIN
IF (Discriminado = 0) THEN
    SELECT
        'Neto Gravado' AS TipoNeto,
		SUM(((CD.Cantidad * CD.PrecioUnitario) * (1 - (IFNULL(CD.DescuentoEnPorcentaje,0) / 100)))) AS NetoGravado 
    FROM 
        ComprobantesDetalles CD 
        INNER JOIN Comprobantes C ON C.Id = CD.Comprobante
        INNER JOIN ConceptosImpositivos CI ON CI.Id = CD.ConceptoImpositivo
    WHERE  	C.Id = IdComprobante
    AND		CI.PorcentajeActual > 0
    GROUP BY C.Id
    
    UNION
    
    SELECT
        'Neto No Gravado' AS TipoNeto,
		SUM(((CD.Cantidad * CD.PrecioUnitario) * (1 - (IFNULL(CD.DescuentoEnPorcentaje,0) / 100)))) AS NetoGravado 
    FROM 
        ComprobantesDetalles CD 
        INNER JOIN Comprobantes C ON C.Id = CD.Comprobante
        INNER JOIN ConceptosImpositivos CI ON CI.Id = CD.ConceptoImpositivo
    WHERE  	C.Id = IdComprobante
    AND		CI.PorcentajeActual < 0.001
    GROUP BY C.Id;
END IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobantePago_Cabecera` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobantePago_Cabecera`(
        IN IdComprobante INTEGER(11)
    )
BEGIN
SELECT 
    C.Id 					AS IdComprobante,
    TC.Descripcion 			AS TipoDeComprobante,
    TC.Codigo				AS CodigoDeComprobante,
    UPPER(P.RazonSocial)	AS RazonSocial,    
    UPPER(D.Direccion) 		AS Direccion,    
    UPPER(L.Descripcion) 	AS Localidad,   
    UPPER(PR.Descripcion) 	AS Provincia,    
    fNumeroCompleto(C.Id,'') AS NumeroCompleto,    
    C.FechaEmision 			AS FechaEmision,    
    UPPER(MI.Descripcion) 	AS ModalidadIVA,    
    P.Cuit 					AS Cuit,    
    C.ObservacionesImpresas AS ObservacionesImpresas,
    C.Persona 				AS Persona   
  FROM 
    Comprobantes C
    INNER JOIN 	TiposDeComprobantes TC ON C.TipoDeComprobante = TC.Id     
    INNER JOIN 	Personas P ON C.Persona = P.Id 
    LEFT JOIN 	Direcciones D ON P.Id = D.Persona
    LEFT JOIN 	Localidades L ON D.Localidad = L.Id
    LEFT JOIN 	Provincias PR ON L.Provincia = PR.Id
    LEFT JOIN 	ModalidadesIVA MI ON P.ModalidadIva = MI.Id
WHERE C.Id = IdComprobante 
LIMIT 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobantePago_CompAsociados` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobantePago_CompAsociados`(
        IN idComprobante INTEGER(11)
    )
BEGIN
SELECT  C.Id                                         AS IdComprobante,
        TGC.Codigo                                   AS Codigo,
        TC.Descripcion                               AS Descripcion,
        fSigno_Comprobante_xID(CR.ComprobantePadre,CR.ComprobanteHijo)*fComprobante_Monto_Total(CR.ComprobanteHijo) AS Monto,
        fNumeroCompleto(CR.ComprobanteHijo,'CG')     AS NumeroCompleto,
		CR.ComprobanteHijo                           AS Hijo,
		CR.ComprobantePadre                          AS Padre,
        C.FechaEmision                               AS FechaEmision,
        C.FechaCierre                                AS FechaCierre
		
FROM    ComprobantesRelacionados CR
        INNER JOIN Comprobantes C                     ON C.Id   = CR.ComprobanteHijo
        INNER JOIN TiposDeComprobantes TC             ON TC.Id  = C.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC    ON TGC.Id = TC.Grupo
WHERE   CR.ComprobantePadre = idComprobante
AND     C.Cerrado = 1
AND     C.Anulado = 0
UNION
SELECT  C1.Id                                         AS IdComprobante,
        TGC1.Codigo                                   AS Codigo,
        CONCAT('.    ',TC1.Descripcion)               AS Descripcion,
        (fSigno_Comprobante_xID(CR1.ComprobantePadre,CR1.ComprobanteHijo)*(-1)*CR1.MontoAsociado) AS Monto,
        CONCAT( cast('.       Monto de '     AS CHAR CHARSET utf8),
                cast(fNumeroCompleto(CR1.ComprobanteHijo,'C') AS CHAR CHARSET utf8),
                cast(' liquidado en ' AS CHAR CHARSET utf8),
                cast(fNumeroCompleto(CR1.ComprobantePadre,'C') AS CHAR CHARSET utf8)
        )                                             AS NumeroCompleto,
        CR1.ComprobanteHijo                           AS Hijo,
        CR1.ComprobantePadre                          AS Padre,
        C1.FechaEmision                               AS FechaEmision,
        C1.FechaCierre                                AS FechaCierre
FROM    ComprobantesRelacionados CR1
        INNER JOIN Comprobantes C1                    ON C1.Id   = CR1.ComprobantePadre
        INNER JOIN TiposDeComprobantes TC1            ON TC1.Id  = C1.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC1   ON TGC1.Id = TC1.Grupo
WHERE   C1.FechaCierre <= ( SELECT IF (
                    (SELECT ifnull(CONVERT(FechaCierre,CHAR),'0000-00-00 00:00:00')
                     FROM 	Comprobantes 
                     WHERE 	Id = idComprobante) = '0000-00-00 00:00:00',
                    NOW(),
                    (SELECT FechaCierre FROM Comprobantes WHERE Id = idComprobante)
                      )
        )
AND     C1.Cerrado = 1
AND     C1.Anulado = 0
AND     CR1.ComprobanteHijo IN (SELECT ComprobanteHijo FROM ComprobantesRelacionados WHERE ComprobantePadre = idComprobante)
AND     CR1.ComprobantePadre <> idComprobante
ORDER BY Hijo,FechaCierre ASC, Padre ASC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobantePago_DetallePago` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobantePago_DetallePago`(
        IN `IdComprobante` INTEGER(11)
    )
BEGIN
SELECT 	CI.Descripcion 									AS FormaDePago,
		CAST(
        	CONCAT(
        		' Nº:', 	IF(  C.Numero = 0 or C.Numero is null ,
                				'S/D',
                				CAST(C.Numero AS CHAR CHARSET utf8)
                			),
            	' - Fecha:', 	DATE_FORMAT(C.FechaEmision,'%d/%m/%Y') 
        	) AS CHAR CHARSET utf8
        )												AS DetalleDePago,
        CD.PrecioUnitario 								AS PrecioUnitario 
FROM 	ComprobantesDetalles CD
    	INNER JOIN Comprobantes C   		ON C.Id = CD.ComprobanteRelacionado 
    	INNER JOIN ConceptosImpositivos CI  ON CI.Id = C.ConceptoImpositivo
WHERE	CD.Comprobante = IdComprobante
union
select 	'Efectivo' 			AS FormaDePago,
		''					AS DetalleDePago,
		CD.PrecioUnitario 	AS PrecioUnitario
FROM 	ComprobantesDetalles CD
WHERE	CD.Comprobante = IdComprobante
AND		CD.ComprobanteRelacionado is null
AND		CD.Cheque is null
AND		CD.TransaccionBancaria is null
AND		CD.TarjetaDeCreditoCupon is null
union
select 	TTB.Descripcion									AS FormaDePago,
		CAST( 
        	CONCAT(
        	' Nº:', 		IF( TB.Numero,
                				CAST(TB.Numero AS CHAR CHARSET utf8),
                                'S/D'
                			), ' ',
        	' - Fecha:', 	DATE_FORMAT(TB.Fecha,'%d/%m/%Y'),
        	
            IF(	TB.CtaOrigen,
                CONCAT(     ' - Origen: ',
                            BO.Descripcion, ' ',
                            TCO.Codigo, ' ',
                            CAST(CBO.Numero AS CHAR CHARSET utf8)
                ),
                ''
            ),
            
            IF(	TB.CtaDestino,
                CONCAT(	  	' - Destino: ',
                            BD.Descripcion, ' ',
                            TCD.Codigo, ' ',
                            CAST(CBD.Numero AS CHAR CHARSET utf8)
                ), 
                ''
            )                      
        ) AS CHAR CHARSET utf8)							AS DetalleDePago,
		CD.PrecioUnitario 								AS PrecioUnitario
FROM 	ComprobantesDetalles CD
        INNER JOIN TransaccionesBancarias TB 			ON TB.Id 	= CD.TransaccionBancaria 
        INNER JOIN TiposDeTransaccionesBancarias TTB 	ON TTB.Id 	= TB.TipoDeTransaccionBancaria
        
        LEFT JOIN CuentasBancarias CBO      			ON CBO.Id 	= TB.CtaOrigen
        LEFT JOIN TiposDeCuentas TCO					ON TCO.Id	= CBO.TipoDeCuenta 
        LEFT JOIN BancosSucursales BSO      			ON BSO.Id 	= CBO.BancoSucursal
        LEFT JOIN Bancos BO                				ON BO.Id 	= BSO.Banco
        
        LEFT JOIN CuentasBancarias CBD      			ON CBD.Id 	= TB.CtaDestino
        LEFT JOIN TiposDeCuentas TCD					ON TCD.Id	= CBD.TipoDeCuenta 
        LEFT JOIN BancosSucursales BSD      			ON BSD.Id 	= CBD.BancoSucursal
        LEFT JOIN Bancos BD                				ON BD.Id 	= BSD.Banco 
WHERE	CD.Comprobante = IdComprobante
union
select 	'Tarjeta' 			AS FormaDePago,
		CAST(
        	CONCAT(
            	TM.`Descripcion`, ' (', B.Descripcion, ') ',
        		' Nº:**** **** **** ', substr(CAST(TC.Numero AS CHAR CHARSET utf8),-4), ' Cupon Nº:',
                CAST(TCC.NumeroCupon AS CHAR CHARSET utf8)
        	) AS CHAR CHARSET utf8
        )												AS DetalleDePago,
		CD.PrecioUnitario 	AS PrecioUnitario
FROM 	ComprobantesDetalles CD
        INNER JOIN TarjetasDeCreditoCupones TCC on 	TCC.Id = CD.TarjetaDeCreditoCupon
        INNER JOIN TarjetasDeCredito TC on 			TC.Id = TCC.TarjetaDeCredito
        INNER JOIN TarjetasDeCreditoMarcas TM on 	TM.Id = TC.TarjetaCreditoMarca
        INNER JOIN Bancos B on 						B.Id = TC.EntidadEmisora
WHERE	CD.Comprobante = IdComprobante
and		CD.TarjetaDeCreditoCupon is not null
union
select 	'Cheque' 										AS FormaDePago,
		CAST(	
            CONCAT(
        	' Nº:', CAST(CH.Numero AS CHAR CHARSET utf8), 
            ' - ',	B.Descripcion,
            
            IF (	CH.TipoDeEmisorDeCheque = 1 and CH.Chequera,
 		           	(SELECT 	CONCAT( ' - ',
                    					TC1.Codigo, 
                                        ' ',
        	                			CAST(CB1.Numero AS CHAR CHARSET utf8)
            	    		)
                	FROM	Chequeras CHR
                			INNER JOIN CuentasBancarias CB1 	ON CB1.Id = CHR.CuentaBancaria
        					LEFT JOIN TiposDeCuentas TC1 		ON TC1.Id = CB1.TipoDeCuenta
                	WHERE	CHR.Id = CH.Chequera)
                ,
                ''
            ),
            ' - FE:', DATE_FORMAT(CH.FechaDeEmision,'%d/%m/%Y'),
            ' - FV:', DATE_FORMAT(CH.FechaDeVencimiento,'%d/%m/%Y')
        	) AS CHAR(250) CHARSET utf8
        )												AS DetalleDePago,
		CD.PrecioUnitario 								AS PrecioUnitario
FROM 	ComprobantesDetalles CD
		INNER JOIN Cheques CH 				ON CH.Id = CD.Cheque
        LEFT JOIN BancosSucursales BS       ON BS.Id = CH.BancoSucursal
        LEFT JOIN Bancos B                	ON B.Id = BS.Banco        
WHERE	Comprobante = IdComprobante
Order By FormaDePago, DetalleDePago;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_ComprobanteRemito_Cabecera` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_ComprobanteRemito_Cabecera`(
        IN IdComprobante INTEGER(11)
    )
BEGIN
SELECT 
    C.Id 					AS IdComprobante,
    TC.Descripcion 			AS TipoDeComprobante,
    TC.Codigo				AS CodigoDeComprobante,
    UPPER(P.RazonSocial)	AS RazonSocial,    
    UPPER(D.Direccion)		AS Direccion,    
    UPPER(L.Descripcion)	AS Localidad,   
    UPPER(PR.Descripcion) 	AS Provincia,    
    fNumeroCompleto(C.Id,'') AS NumeroCompleto,    
    C.FechaEmision 			AS FechaEmision,    
    UPPER(MI.Descripcion)	AS ModalidadIVA,    
    P.Cuit 					AS Cuit,    
    UPPER(TCP.Descripcion)	AS CondicionPago,
    C.ObservacionesImpresas AS ObservacionesImpresas,
    C.Persona 				AS Persona,
    fComprobante_Monto_Total(IdComprobante) AS MontoTotal,
    C.DescuentoEnMonto		AS DescuentoEnMonto,
    C.Anulado				AS Anulado,
    C.Cerrado				AS Cerrado,
    
    IF ( C.CuentaBancaria,
            (SELECT 	CONCAT( TC1.Codigo, 
                                ' ',
                                CAST(CB1.Numero AS CHAR CHARSET utf8)
                    )
            FROM	CuentasBancarias CB1
                    INNER JOIN TiposDeCuentas TC1 		ON TC1.Id = CB1.TipoDeCuenta
            WHERE	CB1.Id = C.CuentaBancaria)
        ,
        ''
    ) as CuentaBancaria,
    
    C.ValorDeclarado 		AS ValorDeclarado,
    C.FechaEntrega 			AS FechaEntrega,
    TRetira.RazonSocial		AS TransportistaRetira,
    TEntrega.RazonSocial	AS TransportistaEntrega,
    C.CotCodigo				AS CotCodigo,
    C.CotFechaValidez		AS CotFechaValidez,
    
    DE.Direccion 			As DireccionDeEntrega,
	DS.Direccion 			As DireccionDeSalida,
    
   	CONCAT(	SUBSTR(FEA.CAEFchVto,7,2),'/',
    		SUBSTR(FEA.CAEFchVto,5,2),'/',
			SUBSTR(FEA.CAEFchVto,1,4)) AS CAE_FechaVencimiento, 
    FEA.Obs 				AS CAE_Obs, 
    FEA.CAE 				AS CAE_Numero    
FROM 
    Comprobantes C 
    INNER JOIN 	TiposDeComprobantes TC 		ON C.TipoDeComprobante = TC.Id     
    INNER JOIN 	Personas P	 				ON C.Persona = P.Id 
    LEFT JOIN 	Direcciones D 				ON C.DepositoEntrega = D.Id
    LEFT JOIN 	Localidades L 				ON D.Localidad = L.Id
    LEFT JOIN 	Provincias PR 				ON L.Provincia = PR.Id
    LEFT JOIN 	ModalidadesIVA MI 			ON P.ModalidadIva = MI.Id
    LEFT JOIN 	TiposDeCondicionesDePago TCP ON TCP.Id = C.CondicionDePago
    LEFT JOIN 	Personas TRetira			ON TRetira.Id = C.TransportistaRetiroDeOrigen 
    LEFT JOIN 	Personas TEntrega			ON TEntrega.Id = C.TransportistaEntregoEnDestino 
    LEFT JOIN 	Direcciones DE 				ON DE.Id = C.DepositoEntrega 
	LEFT JOIN 	Direcciones DS 				ON DS.Id = C.DepositoSalida
    LEFT JOIN  	FacturacionElectronicaAfip FEA ON C.Id = FEA.Comprobante 
WHERE C.Id = IdComprobante 
LIMIT 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_Comprobante_Cabecera` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_Comprobante_Cabecera`(
        IN IdComprobante INTEGER(11)
    )
BEGIN
SELECT 
    C.Id 					AS IdComprobante,
    TC.Descripcion 			AS TipoDeComprobante,
    TC.Codigo				AS CodigoDeComprobante,
    UPPER(P.RazonSocial)	AS RazonSocial,    
    UPPER(D.Direccion)		AS Direccion,    
    UPPER(L.Descripcion)	AS Localidad,   
    UPPER(PR.Descripcion) 	AS Provincia,    
    fNumeroCompleto(C.Id,'') AS NumeroCompleto,    
    C.FechaEmision 			AS FechaEmision,    
    UPPER(MI.Descripcion)	AS ModalidadIVA,    
    P.Cuit 					AS Cuit,    
    UPPER(TCP.Descripcion)	AS CondicionPago,
    C.ObservacionesImpresas AS ObservacionesImpresas,
    C.Persona 				AS Persona,
    fComprobante_Monto_Total(IdComprobante) AS MontoTotal,
    C.DescuentoEnMonto		AS DescuentoEnMonto,
    C.Anulado				AS Anulado,
    C.Cerrado				AS Cerrado,
    
    IF ( C.CuentaBancaria,
            (SELECT 	CONCAT( TC1.Codigo, 
                                ' ',
                                CAST(CB1.Numero AS CHAR CHARSET utf8)
                    )
            FROM	CuentasBancarias CB1
                    INNER JOIN TiposDeCuentas TC1 		ON TC1.Id = CB1.TipoDeCuenta
            WHERE	CB1.Id = C.CuentaBancaria)
        ,
        ''
    ) as CuentaBancaria,
    
    C.ValorDeclarado 		AS ValorDeclarado,
    C.FechaEntrega 			AS FechaEntrega,
    TRetira.RazonSocial		AS TransportistaRetira,
    TEntrega.RazonSocial	AS TransportistaEntrega,
    C.CotCodigo				AS CotCodigo,
    C.CotFechaValidez		AS CotFechaValidez,
    
    UPPER(DE.Direccion)		AS DireccionDeEntrega,    
    UPPER(LE.Descripcion)	AS LocalidadDeEntrega,   
    UPPER(PRE.Descripcion) 	AS ProvinciaDeEntrega, 
    UPPER(DS.Direccion)		AS DireccionDeSalida,    
    UPPER(LS.Descripcion)	AS LocalidadDeSalida,   
    UPPER(PRS.Descripcion) 	AS ProvinciaDeSalida, 
    
   	CONCAT(	SUBSTR(FEA.CAEFchVto,7,2),'/',
    		SUBSTR(FEA.CAEFchVto,5,2),'/',
			SUBSTR(FEA.CAEFchVto,1,4)) AS CAE_FechaVencimiento, 
    FEA.Obs 				AS CAE_Obs, 
    FEA.CAE 				AS CAE_Numero    
FROM 
    Comprobantes C 
    INNER JOIN 	TiposDeComprobantes TC 		ON C.TipoDeComprobante = TC.Id     
    INNER JOIN 	Personas P	 				ON C.Persona = P.Id 
    LEFT JOIN 	Direcciones D 				ON P.Id = D.Persona
    LEFT JOIN 	Localidades L 				ON D.Localidad = L.Id
    LEFT JOIN 	Provincias PR 				ON L.Provincia = PR.Id
    LEFT JOIN 	ModalidadesIVA MI 			ON P.ModalidadIva = MI.Id
    LEFT JOIN 	TiposDeCondicionesDePago TCP ON TCP.Id = C.CondicionDePago
    LEFT JOIN 	Personas TRetira			ON TRetira.Id = C.TransportistaRetiroDeOrigen 
    LEFT JOIN 	Personas TEntrega			ON TEntrega.Id = C.TransportistaEntregoEnDestino 
    LEFT JOIN 	Direcciones DE 				ON DE.Id = C.DepositoEntrega 
    LEFT JOIN 	Localidades LE				ON DE.Localidad = LE.Id
    LEFT JOIN 	Provincias PRE				ON LE.Provincia = PRE.Id
	LEFT JOIN 	Direcciones DS 				ON DS.Id = C.DepositoSalida
    LEFT JOIN 	Localidades LS				ON DS.Localidad = LS.Id
    LEFT JOIN 	Provincias PRS				ON LS.Provincia = PRS.Id
    LEFT JOIN  	FacturacionElectronicaAfip FEA ON C.Id = FEA.Comprobante
WHERE C.Id = IdComprobante 
LIMIT 1;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_Comprobante_Detalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_Comprobante_Detalle`(
        IN `IdComprobante` INTEGER(11)
    )
BEGIN

/*
OJO!!!!!!!!!!!!!!!!!! el descuento redondea en 4 decimales 0.2850 = 28.50%
*/

SELECT 
    C.Id 								AS IdComprobante,
    IF(ISNULL(CD.Articulo),CD.Observaciones,A.Descripcion) AS Articulo,
    CD.Cantidad 						AS Cantidad,
    
    CD.PrecioUnitario 					AS PrecioUnitario,
    UdM.Descripcion 					AS UnidadDeMedida,
    IF(	ISNULL(CD.DescuentoEnPorcentaje),0,
    	Round(CD.DescuentoEnPorcentaje/100,4)) AS DescuentoEnPorcentaje,
    (CD.Cantidad * CD.PrecioUnitario) 	AS MontoSinDescuento,
    IF(	ISNULL(CD.DescuentoEnPorcentaje),0,
    	(CD.Cantidad * CD.PrecioUnitario  * CD.DescuentoEnPorcentaje / 100)) AS MontoDescuento,
    IF(	ISNULL(CD.DescuentoEnPorcentaje),(CD.Cantidad * CD.PrecioUnitario),	
    	(CD.Cantidad * CD.PrecioUnitario) - (CD.Cantidad * CD.PrecioUnitario  * CD.DescuentoEnPorcentaje / 100)) AS MontoConDescuento
  FROM 
    ComprobantesDetalles CD 
        INNER JOIN Comprobantes C       	ON C.Id = CD.Comprobante 
        LEFT JOIN Articulos     A       	ON A.Id = CD.Articulo  
        LEFT JOIN UnidadesDeMedidas UdM 	ON UdM.Id = A.UnidadDeMedida
        LEFT JOIN TiposDeComprobantes TC 	ON TC.Id = C.TipoDeComprobante
where CD.Comprobante = IdComprobante
order by Articulo;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_CtaCte_Detalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_CtaCte_Detalle`(
        IN idPersona INTEGER(11),
        IN desde DATE,
        IN hasta DATE
    )
BEGIN
if (desde = '1900/01/01' and hasta = '1900/01/01') THEN
    SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
            CC.FechaComprobante 					AS FechaComprobante, 
            CC.Debe 								AS Debe, 
            CC.Haber 								AS Haber,
            2										AS Orden
    FROM 	CuentasCorrientes CC
    WHERE 	CC.Persona = idPersona
    ORDER BY FechaComprobante;
End IF;
if (desde = '1900/01/01' and hasta <> '1900/01/01') THEN
    SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
            CC.FechaComprobante 				AS FechaComprobante, 
            CC.Debe 							AS Debe, 
            CC.Haber 							AS Haber,
            2									AS Orden
    FROM 	CuentasCorrientes CC
    WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante <= hasta
    ORDER BY FechaComprobante;
End IF;
if (desde <> '1900/01/01' and hasta = '1900/01/01') THEN
	
    SELECT 	CAST('Arqueo previo a fecha' AS CHAR CHARSET utf8) AS NroComprobante, 
            desde 								AS FechaComprobante, 
            ifnull(sum(CC.Debe),0)				AS Debe, 
            IFNULL(sum(CC.Haber),0)				AS Haber,
            1									AS Orden
    FROM 	CuentasCorrientes CC
    WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante < desde
    
    UNION
    SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
            CC.FechaComprobante 				AS FechaComprobante, 
            CC.Debe 							AS Debe, 
            CC.Haber 							AS Haber,
            2									AS Orden
    FROM 	CuentasCorrientes CC
    WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante >= desde
    ORDER BY FechaComprobante;
End IF;
if (desde <> '1900/01/01' and hasta <> '1900/01/01') THEN
	
    SELECT 	CAST('Arqueo previo a fecha' AS CHAR CHARSET utf8) AS NroComprobante, 
            desde 								AS FechaComprobante, 
            ifnull(sum(CC.Debe),0)				AS Debe, 
            IFNULL(sum(CC.Haber),0)				AS Haber,
            1									AS Orden
    FROM 	CuentasCorrientes CC
    WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante < desde
    
    UNION
    SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
            CC.FechaComprobante 				AS FechaComprobante, 
            CC.Debe 							AS Debe, 
            CC.Haber 							AS Haber,
            2									AS Orden
    FROM 	CuentasCorrientes CC
    WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante >= desde
    AND		CC.FechaComprobante <= hasta
    ORDER BY FechaComprobante;
End IF;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_OrdenDePago_CompAsociados` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_OrdenDePago_CompAsociados`(
        IN idComprobante INTEGER(11)
    )
BEGIN
SELECT 	C.Id 										AS IdComprobante,
		TGC.Codigo									AS Codigo,
    	TC.Descripcion 								AS Descripcion,
    	fSigno_Comprobante_xID(CR.ComprobantePadre,CR.ComprobanteHijo)*fComprobante_Monto_Total(CR.ComprobanteHijo) AS Monto, 
    	fNumeroCompleto(CR.ComprobanteHijo,'CG') 	AS NumeroCompleto,
    	CR.ComprobanteHijo 							AS Hijo,
    	CR.ComprobantePadre 						AS Padre,
    	C.FechaEmision 								AS FechaEmision, 
    	(IF (	ifnull(CONVERT(C.FechaCierre,CHAR),'0000-00-00 00:00:00') = '0000-00-00 00:00:00',
    			NOW(),
    			C.FechaCierre)
    	) 											AS FechaCierre	
FROM    ComprobantesRelacionados CR    
        INNER JOIN Comprobantes C 					ON C.Id 	= CR.ComprobanteHijo
        INNER JOIN TiposDeComprobantes TC 			ON TC.Id 	= C.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC 	ON TGC.Id 	= TC.Grupo
WHERE 	CR.ComprobantePadre = idComprobante
AND 	C.Cerrado = 1 
AND 	C.Anulado = 0
UNION
SELECT 	C1.Id 										AS IdComprobante,
		TGC1.Codigo									AS Codigo,
    	CONCAT('.    ',TC1.Descripcion) 			AS Descripcion,
    	(fSigno_Comprobante_xID(CR1.ComprobantePadre,CR1.ComprobanteHijo)*(-1)*CR1.MontoAsociado) AS Monto,   
    	CONCAT(	cast('.       Monto de ' 	AS CHAR CHARSET utf8),
    			cast(fNumeroCompleto(CR1.ComprobanteHijo,'C') AS CHAR CHARSET utf8),
		        cast(' liquidado en ' AS CHAR CHARSET utf8),
    			cast(fNumeroCompleto(CR1.ComprobantePadre,'C') AS CHAR CHARSET utf8)
    	) 											AS NumeroCompleto,
    	CR1.ComprobanteHijo  						AS Hijo,
    	CR1.ComprobantePadre 						AS Padre,
    	C1.FechaEmision 							AS FechaEmision, 
    	C1.FechaCierre 								AS FechaCierre
FROM    ComprobantesRelacionados CR1    
        INNER JOIN Comprobantes C1 					ON C1.Id 	= CR1.ComprobantePadre
        INNER JOIN TiposDeComprobantes TC1 			ON TC1.Id 	= C1.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC1 ON TGC1.Id 	= TC1.Grupo
WHERE 	C1.FechaCierre <= ( SELECT IF (
					(	SELECT ifnull(CONVERT(FechaCierre,CHAR),'0000-00-00 00:00:00') 
                    	FROM Comprobantes WHERE Id = idComprobante) = '0000-00-00 00:00:00',
					NOW(),
					(SELECT FechaCierre FROM Comprobantes WHERE Id = idComprobante)
				      )
		)
AND 	C1.Cerrado = 1 
AND 	C1.Anulado = 0
AND 	CR1.ComprobanteHijo IN (SELECT ComprobanteHijo FROM ComprobantesRelacionados WHERE ComprobantePadre = idComprobante)
AND 	CR1.ComprobantePadre <> idComprobante
ORDER BY Hijo,FechaCierre ASC, Padre ASC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_Persona_Detalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_Persona_Detalle`(
        IN idPersona INTEGER(11)
    )
BEGIN
	select 	RazonSocial 		AS RazonSocial,
    		Denominacion		AS Denominacion,
            Cuit				AS Cuit,
            Dni					AS Dni,
            EsProveedor			AS EsProveedor,
            EsCliente			AS EsCliente,
            EsVendedor			AS EsVendedor,
            EsTransporte		AS EsTransporte,
            EsEmpleado			AS EsEmpleado
    from 	Personas 
    where 	Id = idPersona;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_Recibo_CompAsociados` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_Recibo_CompAsociados`(
        IN idComprobante INTEGER(11)
    )
BEGIN
SELECT 	C.Id 										AS IdComprobante,
		TGC.Codigo									AS Codigo,
    	TC.Descripcion 								AS Descripcion,
        fSigno_Comprobante_xID(CR.ComprobantePadre,CR.ComprobanteHijo)*fComprobante_Monto_Total(CR.ComprobanteHijo) AS Monto, 
    	fNumeroCompleto(CR.ComprobanteHijo,'CG') 	AS NumeroCompleto,
        CR.ComprobanteHijo 							AS Hijo,
        CR.ComprobantePadre 						AS Padre,
    	C.FechaEmision 								AS FechaEmision, 
        C.FechaCierre 								AS FechaCierre
FROM    ComprobantesRelacionados CR    
        INNER JOIN Comprobantes C 					ON C.Id = CR.ComprobanteHijo
        INNER JOIN TiposDeComprobantes TC 			ON TC.Id = C.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC 	ON TGC.Id = TC.Grupo
WHERE 	CR.ComprobantePadre = idComprobante 
AND 	C.Cerrado = 1 
AND 	C.Anulado = 0
UNION
SELECT 	C1.Id 										AS IdComprobante,
		TGC1.Codigo									AS Codigo,
    	CONCAT('.    ',TC1.Descripcion) 			AS Descripcion,
        (fSigno_Comprobante_xID(CR1.ComprobantePadre,CR1.ComprobanteHijo)*(-1)*CR1.MontoAsociado) AS Monto,
    	CONCAT(	cast('.       Monto de ' 	AS CHAR CHARSET utf8),
    			cast(fNumeroCompleto(CR1.ComprobanteHijo,'C') AS CHAR CHARSET utf8),
		        cast(' liquidado en ' AS CHAR CHARSET utf8),
    			cast(fNumeroCompleto(CR1.ComprobantePadre,'C') AS CHAR CHARSET utf8)
    	) 											AS NumeroCompleto,        
        CR1.ComprobanteHijo  						AS Hijo,
        CR1.ComprobantePadre 						AS Padre,
    	C1.FechaEmision 							AS FechaEmision, 
        C1.FechaCierre 								AS FechaCierre
FROM    ComprobantesRelacionados CR1    
        INNER JOIN Comprobantes C1 					ON C1.Id = CR1.ComprobantePadre
        INNER JOIN TiposDeComprobantes TC1 			ON TC1.Id = C1.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC1 ON TGC1.Id = TC1.Grupo
WHERE 	C1.FechaCierre <= ( SELECT IF (
					(	SELECT ifnull(CONVERT(FechaCierre,CHAR),'0000-00-00 00:00:00') 
                    	FROM Comprobantes WHERE Id = idComprobante) = '0000-00-00 00:00:00',
					NOW(),
					(SELECT FechaCierre FROM Comprobantes WHERE Id = idComprobante)
				      )
		)
AND 	C1.Cerrado = 1 
AND 	C1.Anulado = 0
AND 	CR1.ComprobanteHijo IN (SELECT ComprobanteHijo FROM ComprobantesRelacionados WHERE ComprobantePadre = idComprobante)
AND 	CR1.ComprobantePadre <> idComprobante
ORDER BY Hijo,FechaCierre ASC, Padre ASC;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Birt_Stock_Valorizado` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `Birt_Stock_Valorizado`(
 )
BEGIN

select IdArticulo, Codigo, Articulo, UnidadDeMedida, Cantidad,
 if ( ifnull(pUV,0) > ifnull(pUI,0),
 if (ifnull(pUC,0) > ifnull(pUV,0),ifnull(pUC,0),ifnull(pUV,0)),
 if (ifnull(pUC,0) > ifnull(pUI,0),ifnull(pUC,0),ifnull(pUI,0))
 ) as precioUnitario
from (
 select 
 A.Id as IdArticulo,
 A.Codigo as Codigo,
 A.Descripcion as Articulo,
 UM.DescripcionR as UnidadDeMedida, 
 ifnull(sum(M.CantidadActual),0) as Cantidad,
 /* 
 ----------------------------------------------------------------- 
 Precio Ultima Venta
 -----------------------------------------------------------------
 */
 IF( ifnull(sum(M.CantidadActual),0)>0 ,
 (
 select ifnull(PRP.PrecioUltimo,0) 
 from PersonasRegistrosDePrecios PRP
 inner join Articulos A1 on A1.Id = PRP.Articulo
 inner join TiposDeDivisas TD on TD.Id = PRP.TipoDeDivisa
 where A.Id = A1.Id
 and PRP.TipoDeRegistroDePrecio = 2
 and PRP.Historico is null
 order by PRP.FechaPrecioUltimo desc 
 limit 1
 ) ,
 0
 ) as pUV,
 /* 
 ----------------------------------------------------------------- 
 Precio ultimo Informado
 -----------------------------------------------------------------
 */
 IF( ifnull(sum(M.CantidadActual),0)>0 ,
 (
 select ifnull(PRP.PrecioUltimo,0)
 from PersonasRegistrosDePrecios PRP
 inner join Articulos A2 on A2.Id = PRP.Articulo
 inner join TiposDeDivisas TD on TD.Id = PRP.TipoDeDivisa
 where A.Id = A2.Id
 and PRP.TipoDeRegistroDePrecio = 3
 and PRP.Historico is null
 order by PRP.FechaPrecioUltimo desc
 limit 1
 ),
 0
 ) as pUI,
 /*
 ----------------------------------------------------------------- 
 Precio ultimo Compra
 -----------------------------------------------------------------
 */
 IF( ifnull(sum(M.CantidadActual),0)>0 ,
 ( 
 select ifnull(PRP.PrecioUltimo,0)
 from PersonasRegistrosDePrecios PRP
 inner join Articulos A2 on A2.Id = PRP.Articulo
 inner join TiposDeDivisas TD on TD.Id = PRP.TipoDeDivisa
 where A.Id = A2.Id
 and PRP.TipoDeRegistroDePrecio = 1
 and PRP.Historico is null
 order by PRP.FechaPrecioUltimo desc
 limit 1
 ),
 0
 ) as pUC 
from Mmis M
 inner join Articulos A on A.Id = M.Articulo
 inner join UnidadesDeMedidas UM on UM.Id = M.UnidadDeMedida
 left join Almacenes AL on AL.Id = M.Almacen 
Where ( M.FechaCierre is null 
 or 
 M.FechaCierre > now() 
 )
and M.FechaIngreso <= now()
group by A.Id
) as Tabla
order by Articulo asc;


END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `generar931` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `generar931`(
        IN empresa INTEGER(11),
        IN periodo integer(11)
    )
BEGIN
        DECLARE done			INT DEFAULT FALSE;
        DECLARE fechainicio		datetime;	    
        DECLARE fechafin		datetime;
        DECLARE persona		    	INTEGER;
        DECLARE montoTotal		DECIMAL(11,2);
        DECLARE remImp			DECIMAL(11,2); 
        
        DECLARE cur1 CURSOR FOR SELECT cp.Id  FROM Personas cp 
		INNER JOIN Servicios cs ON cp.id = cs.Persona 
		WHERE cs.FechaAlta <= '2013-09-01' AND IFNULL(cs.FechaBaja,'2099-12-31') >= '2013-09-01'
		and cs.Empresa = empresa;         
        
        DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;    
  
	SET @@max_sp_recursion_depth=255; 
/*	    
	DROP TABLE IF EXISTS `Afip931_temp`; 
	    
	CREATE TEMPORARY TABLE Afip931_temp
	  (
	  IdTabla INT(11) NOT NULL AUTO_INCREMENT,
	  IdArticulo INT,
	  IdArticuloVersionPadre INT,
	  IdArticuloVersionDetalle INT,
	  IdArticuloVersion INT,
	  PRIMARY KEY  (`IdTabla`),
	  UNIQUE KEY `IdTabla` (`IdTabla`)
	  );      
*/	  
	DROP TABLE IF EXISTS `Afip931_temp`; 
    
	CREATE TEMPORARY TABLE Afip931_temp
	(	  
		Id INT(11) NOT NULL AUTO_INCREMENT, 
		Cuil varchar(11), 
		ApeNom VARCHAR(30), 
		Conyuge VARCHAR(1), 
		CantHijo VARCHAR(2), 
		CodSit VARCHAR(2), 
		CodCon VARCHAR(2), 
		CodAct VARCHAR(3), 
		CodZona VARCHAR(2), 
		PorAporAdicSS varchar(5), 
		CodModCont VARCHAR(3), 
		CodOS VARCHAR(6), 
		CantAdh VARCHAR(2), 
		RemTot VARCHAR(11), 
		RemImp1 VARCHAR(10), 
		AsigFamPag VARCHAR(9), 
		ImpAportVol VARCHAR(9), 
		ImpAdiOS VARCHAR(9), 
		ImpExcAportSS VARCHAR(9), 
		ImpExcAportOS VARCHAR(9),
		ProvLoc VARCHAR(50), 
		RemImp2 VARCHAR(10), 
		RemImp3 VARCHAR(10), 
		RemImp4 VARCHAR(10), 
		CodSin VARCHAR(2), 
		MarCorrRed VARCHAR(1), 
		CapRecLRT VARCHAR(9), 
		TipoEmp VARCHAR(1), 
		AporAdiOS VARCHAR(9), 
		Regimen VARCHAR(1), 
		SitRev1 VARCHAR(2), 
		DiaIniSitRev1 VARCHAR(2), 
		SitRev2 VARCHAR(2), 
		DiaIniSitRev2 VARCHAR(2), 
		SitRev3 VARCHAR(2), 
		DiaIniSitRev3 VARCHAR(2), 
		SuelAdic VARCHAR(10), 
		SAC VARCHAR(10), 
		HorasExtras VARCHAR(10), 
		ZonaDesf VARCHAR(10), 
		Vacaciones VARCHAR(10), 
		CantDiasTrab VARCHAR(9), 
		RemImp5 VARCHAR(10), 
		TrabConv VARCHAR(1), 
		RemImp6 VARCHAR(10), 
		TipoOper VARCHAR(1), 
		Adicionales VARCHAR(10), 
		Premios VARCHAR(10), 
		RemDec78805RemImp8 VARCHAR(10),
		RemImp7 VARCHAR(10),
		CantHorasExtras VARCHAR(3),
		ConcNoRem VARCHAR(10), 
		Maternidad VARCHAR(10), 
		RectRem VARCHAR(9), 
		RemImp9 VARCHAR(10), 
		ContTarDif VARCHAR(9), 
		HorasTrab VARCHAR(3), 
		SegColVidaOblig VARCHAR(1)
	);
	          
	SELECT FechaDesde, FechaHasta INTO fechaInicio, fechaFin FROM LiquidacionesPeriodos WHERE Id = periodo;
        
	OPEN cur1;
		REPEAT
			FETCH cur1 INTO persona;
			IF NOT done THEN 
			
				SELECT SUM(lrdMontoCalculado) into montoTotal FROM LiquidacionesRecibos lr 
				inner join Liquidaciones l on lr.Liquidacion = l.Id
				INNER JOIN LiquidacionesRecibosDetalles lrd ON lr.Id = lrd.LiquidacionRecibo 
				WHERE lr.Persona = persona AND l.periodo = periodo;				
/*
			
				INSERT INTO ArbolArticulos_temp (IdArticulo,  IdArticuloVersionPadre,  IdArticuloVersionDetalle, IdArticuloVersion)
				VALUES (Art, ArtVer, ArtVerDet, ArtVerHijo);
					
*/								
			END IF;
		UNTIL done END REPEAT;
	CLOSE cur1;             
    
	SET @@max_sp_recursion_depth=0;	
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liq_ReciboDetalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `Liq_ReciboDetalle`(
        IN idLiquidacion INTEGER(11),
        IN idPersona INTEGER(11)
    )
BEGIN

SELECT 		LR.Persona				AS Persona,
			VA.Codigo				AS Codigo, 
			VA.Descripcion			AS Nombre,
            TCL.DescripcionR		AS TipoDeConcepto,
            LRD.Monto				AS MontoPagado,
            LRD.MontoCalculado		AS MontoCalculado,
            LRD.Detalle				AS Detalle,
            TCL.OrdenEjecucion		AS Orden,
            1						AS Orden2            
FROM		LiquidacionesRecibosDetalles 	LRD
INNER JOIN	LiquidacionesRecibos 			LR 	on LR.Id 	= LRD.LiquidacionRecibo
INNER JOIN	VariablesDetalles 				VD	on VD.Id 	= LRD.VariableDetalle
INNER JOIN  Variables						VA 	on VA.Id 	= VD.Variable
INNER JOIN  TiposDeConceptosLiquidaciones 	TCL on TCL.Id 	= VA.TipoDeConceptoLiquidacion
WHERE 		LR.Liquidacion 	= idLiquidacion
AND			LR.Persona 		= idPersona
AND			LR.Periodo 		= LRD.PeriodoDevengado
AND			LRD.Monto 		<> 0

UNION

SELECT 		LR.Persona				AS Persona,
			VA.Codigo				AS Codigo, 
			VA.Descripcion			AS Nombre,
            TCL.DescripcionR		AS TipoDeConcepto,
            sum(LRD.Monto)			AS MontoPagado,
            sum(LRD.MontoCalculado)	AS MontoCalculado,
            '(R)' 					AS Detalle,
            TCL.OrdenEjecucion		AS Orden,
            2						AS Orden2
FROM		LiquidacionesRecibosDetalles 	LRD
INNER JOIN	LiquidacionesRecibos 			LR 	on LR.Id 	= LRD.LiquidacionRecibo
INNER JOIN	VariablesDetalles 				VD	on VD.Id 	= LRD.VariableDetalle
INNER JOIN  Variables						VA 	on VA.Id 	= VD.Variable
INNER JOIN  TiposDeConceptosLiquidaciones 	TCL on TCL.Id 	= VA.TipoDeConceptoLiquidacion
WHERE 		LR.Liquidacion 	= idLiquidacion
AND			LR.Persona 		= idPersona
AND			LR.Periodo 		<> LRD.PeriodoDevengado
GROUP BY	LRD.PeriodoDevengado, VA.Codigo
HAVING		sum(LRD.Monto) <> 0
ORDER BY	Orden, Codigo asc;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liq_ReciboDetalle_porEmpresa` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `Liq_ReciboDetalle_porEmpresa`(
 IN idLiquidacion INTEGER(11),
 IN idEmpresa INTEGER(11)
 )
BEGIN

SELECT LR.Persona AS Persona,
 VA.Codigo AS Codigo, 
 VA.Descripcion AS Nombre,
 TCL.DescripcionR AS TipoDeConcepto,
 LRD.Monto AS MontoPagado,
 LRD.MontoCalculado AS MontoCalculado,
 LRD.Detalle AS Detalle,
 TCL.OrdenEjecucion AS Orden,
 1 AS Orden2 
FROM LiquidacionesRecibosDetalles LRD
INNER JOIN LiquidacionesRecibos LR on LR.Id = LRD.LiquidacionRecibo
INNER JOIN VariablesDetalles VD on VD.Id = LRD.VariableDetalle
INNER JOIN Variables VA on VA.Id = VD.Variable
INNER JOIN TiposDeConceptosLiquidaciones TCL on TCL.Id = VA.TipoDeConceptoLiquidacion
INNER JOIN Servicios S on S.Id = LR.Servicio
WHERE LR.Liquidacion = idLiquidacion
AND S.Empresa = idEmpresa
AND LR.Periodo = LRD.PeriodoDevengado
AND LRD.Monto <> 0

UNION

SELECT LR.Persona AS Persona,
 VA.Codigo AS Codigo, 
 VA.Descripcion AS Nombre,
 TCL.DescripcionR AS TipoDeConcepto,
 sum(LRD.Monto) AS MontoPagado,
 sum(LRD.MontoCalculado) AS MontoCalculado,
 '(R)' AS Detalle,
 TCL.OrdenEjecucion AS Orden,
 2 AS Orden2
FROM LiquidacionesRecibosDetalles LRD
INNER JOIN LiquidacionesRecibos LR on LR.Id = LRD.LiquidacionRecibo
INNER JOIN VariablesDetalles VD on VD.Id = LRD.VariableDetalle
INNER JOIN Variables VA on VA.Id = VD.Variable
INNER JOIN TiposDeConceptosLiquidaciones TCL on TCL.Id = VA.TipoDeConceptoLiquidacion
INNER JOIN Servicios S on S.Id = LR.Servicio
WHERE LR.Liquidacion = idLiquidacion
AND S.Empresa = idEmpresa
AND LR.Periodo <> LRD.PeriodoDevengado
GROUP BY LRD.PeriodoDevengado, VA.Codigo
HAVING sum(LRD.Monto) <> 0
ORDER BY Orden, Codigo asc;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `Liq_ReciboDetalle_porEmprsa` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_unicode_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = '' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`localhost`*/ /*!50003 PROCEDURE `Liq_ReciboDetalle_porEmprsa`(
 IN idLiquidacion INTEGER(11),
 IN idEmpresa INTEGER(11)
 )
BEGIN

SELECT LR.Persona AS Persona,
 VA.Codigo AS Codigo, 
 VA.Descripcion AS Nombre,
 TCL.DescripcionR AS TipoDeConcepto,
 LRD.Monto AS MontoPagado,
 LRD.MontoCalculado AS MontoCalculado,
 LRD.Detalle AS Detalle,
 TCL.OrdenEjecucion AS Orden,
 1 AS Orden2 
FROM LiquidacionesRecibosDetalles LRD
INNER JOIN LiquidacionesRecibos LR on LR.Id = LRD.LiquidacionRecibo
INNER JOIN VariablesDetalles VD on VD.Id = LRD.VariableDetalle
INNER JOIN Variables VA on VA.Id = VD.Variable
INNER JOIN TiposDeConceptosLiquidaciones TCL on TCL.Id = VA.TipoDeConceptoLiquidacion
INNER JOIN Servicios S on S.Id = LR.Servicio
WHERE LR.Liquidacion = idLiquidacion
AND S.Empresa = idEmpresa
AND LR.Periodo = LRD.PeriodoDevengado
AND LRD.Monto <> 0

UNION

SELECT LR.Persona AS Persona,
 VA.Codigo AS Codigo, 
 VA.Descripcion AS Nombre,
 TCL.DescripcionR AS TipoDeConcepto,
 sum(LRD.Monto) AS MontoPagado,
 sum(LRD.MontoCalculado) AS MontoCalculado,
 '(R)' AS Detalle,
 TCL.OrdenEjecucion AS Orden,
 2 AS Orden2
FROM LiquidacionesRecibosDetalles LRD
INNER JOIN LiquidacionesRecibos LR on LR.Id = LRD.LiquidacionRecibo
INNER JOIN VariablesDetalles VD on VD.Id = LRD.VariableDetalle
INNER JOIN Variables VA on VA.Id = VD.Variable
INNER JOIN TiposDeConceptosLiquidaciones TCL on TCL.Id = VA.TipoDeConceptoLiquidacion
INNER JOIN Servicios S on S.Id = LR.Servicio
WHERE LR.Liquidacion = idLiquidacion
AND S.Empresa = idEmpresa
AND LR.Periodo <> LRD.PeriodoDevengado
GROUP BY LRD.PeriodoDevengado, VA.Codigo
HAVING sum(LRD.Monto) <> 0
ORDER BY Orden, Codigo asc;

END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `p30dias` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `p30dias`(fecha date)
BEGIN
create temporary table tUltimos30Dias as 
    select @rownum:=@rownum+1 as num,date(fecha) - interval @rownum day as fecha from
    (select 0 union all select 1 union all select 3 union all select 4 union all select 5 union all select 6 union all select 6 union all select 7 union all select 8 union all select 9) t,
    (select 0 union all select 1 union all select 3 ) t2,
    
    (SELECT @rownum:=0) r;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `pCompPago_Monto_Detalle` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `pCompPago_Monto_Detalle`(
        IN idComprobante INTEGER(11)
    )
BEGIN
      
      select 	'Ctdo' 					AS FormaPago,
              	sum(CD.PrecioUnitario)	AS MontoPago
      from 		ComprobantesDetalles CD
      where		CD.Comprobante = idComprobante
      and		CD.Caja is not null
      and		CD.Preciounitario is not null
      
      
      union
      select 	'Cheque' 				AS FormaPago,
              	sum(CD1.PrecioUnitario)	AS MontoPago
      from 		ComprobantesDetalles CD1
      where		CD1.Comprobante = idComprobante
      and		CD1.Cheque is not null
      
      
      union
      select 	'CompImp' 				AS FormaPago,
              sum(CD2.PrecioUnitario)	AS MontoPago
      from 	ComprobantesDetalles CD2
      where	CD2.Comprobante = idComprobante 
      and		CD2.ComprobanteRelacionado is not null
      and		ifnull(CD2.Preciounitario,0) > 0
      
      
      union
      select 	TTB.Codigo 				AS FormaPago,
              sum(CD3.PrecioUnitario)	AS MontoPago
      from 	ComprobantesDetalles CD3
      inner join TransaccionesBancarias TB 			on CD3.TransaccionBancaria = TB.Id
      inner join TiposDeTransaccionesBancarias TTB 	on TB.TipoDeTransaccionBancaria = TTB.Id
      where	CD3.Comprobante = idComprobante
      group by TTB.Codigo;
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `prueba` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `prueba`(IN IdMmi INTEGER,tipo INTEGER)
BEGIN
	    
	    DECLARE done INT DEFAULT FALSE;
            DECLARE padre 	INTEGER; 
            DECLARE idm		INTEGER;           
	    DECLARE cur CURSOR FOR SELECT Mmi,2,idm FROM OrdenesDeProduccionesMmis WHERE OrdenDeProduccionDetalle IN (SELECT Id FROM OrdenesDeProduccionesDetalles WHERE OrdenDeProduccion = idm);
	    DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;	    
	    SET @@max_sp_recursion_depth=255; 
        
            SET idm = IdMmi;
	    DROP TABLE IF EXISTS `MmiTraz_temp`; 
            CREATE TEMPORARY TABLE MmiTraz_temp
              (
              IdTabla INT(11) NOT NULL AUTO_INCREMENT,
              Id INT,
              Tipo INT,
              Padre INT,
              TipoPadre INT,
              PRIMARY KEY  (`IdTabla`),
              UNIQUE KEY `IdTabla` (`IdTabla`)
              );
              
        
	INSERT INTO MmiTraz_temp ( Id,  Tipo,  Padre, TipoPadre) VALUES (idm, tipo, null, null);
        
        CALL TrazabilidadRecuperoPadre(idm, tipo);       
                     
	SELECT * FROM MmiTraz_temp;
	SET @@max_sp_recursion_depth=0;	
	
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `RecorridoMmi` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `RecorridoMmi`(IN IdMmi INTEGER)
BEGIN
            DECLARE padre INTEGER;
            DECLARE fechaIngreso DATETIME;
            SELECT m.MmiPadre into padre FROM Mmis m WHERE m.Id = IdMmi;
            SELECT m.FechaIngreso into fechaIngreso FROM Mmis m WHERE m.Id = IdMmi;
	    DROP TABLE IF EXISTS  MmiMov_temp;
            CREATE TEMPORARY TABLE MmiMov_temp
              (
              Id INT(11) NOT NULL AUTO_INCREMENT,
              Mmi INT(11),
              Fecha DATETIme DEFAULT null,
              Descripcion VARCHAR(255),
              Cantidad INT(11),
              UbicacionOrigen INT(11),
              AlmacenOrigen INT(11),
              UbicacionDestino INT(11),
              AlmacenDestino INT(11),
              PRIMARY KEY  (`Id`),
              UNIQUE KEY `Id` (`Id`)
              );
            INSERT INTO MmiMov_temp (    Mmi,  Fecha,  Descripcion,  Cantidad,  UbicacionOrigen,  AlmacenOrigen,  UbicacionDestino,  AlmacenDestino)
                               SELECT  M.Mmi,M.Fecha,M.Descripcion,M.Cantidad,M.UbicacionOrigen,M.AlmacenOrigen,M.UbicacionDestino,M.AlmacenDestino
                               FROM MmisMovimientos M WHERE  M.Mmi = IdMmi;
            WHILE(padre IS NOT NULL) DO
                INSERT INTO MmiMov_temp (    Mmi,  Fecha,  Descripcion,  Cantidad,  UbicacionOrigen,  AlmacenOrigen,  UbicacionDestino,  AlmacenDestino)
                                   SELECT  M.Mmi,M.Fecha,M.Descripcion,M.Cantidad,M.UbicacionOrigen,M.AlmacenOrigen,M.UbicacionDestino,M.AlmacenDestino
                                   FROM MmisMovimientos M WHERE M.Mmi = padre AND M.Fecha <= fechaIngreso;
                SELECT m.FechaIngreso INTO fechaIngreso FROM Mmis m WHERE m.Id = padre;
                SELECT m.MmiPadre into padre FROM Mmis m WHERE m.Id = padre;
            END WHILE;
            
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `TrazabilidadPorMmi` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `TrazabilidadPorMmi`(IN IdMmi INTEGER,tipo integer)
BEGIN
	    
	    DECLARE done INT DEFAULT FALSE;
            DECLARE padre 	INTEGER; 
            DECLARE idm		INTEGER;           
  
	    SET @@max_sp_recursion_depth=255; 
        
            SET idm = IdMmi;
	    DROP TABLE IF EXISTS `MmiTraz_temp`; 
            CREATE TEMPORARY TABLE MmiTraz_temp
              (
              IdTabla INT(11) NOT NULL AUTO_INCREMENT,
              Id INT,
              Tipo INT,
              Padre INT,
              TipoPadre INT,
              PRIMARY KEY  (`IdTabla`),
              UNIQUE KEY `IdTabla` (`IdTabla`)
              );
              
        
	INSERT INTO MmiTraz_temp ( Id,  Tipo,  Padre, TipoPadre) VALUES (idm, tipo, NULL, NULL);
        
        CALL TrazabilidadRecuperoPadre(idm, tipo);       
                     
	SELECT * FROM MmiTraz_temp;
	SET @@max_sp_recursion_depth=0;	
END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50003 DROP PROCEDURE IF EXISTS `TrazabilidadRecuperoPadre` */;
/*!50003 SET @saved_cs_client      = @@character_set_client */ ;
/*!50003 SET @saved_cs_results     = @@character_set_results */ ;
/*!50003 SET @saved_col_connection = @@collation_connection */ ;
/*!50003 SET character_set_client  = utf8 */ ;
/*!50003 SET character_set_results = utf8 */ ;
/*!50003 SET collation_connection  = utf8_general_ci */ ;
/*!50003 SET @saved_sql_mode       = @@sql_mode */ ;
/*!50003 SET sql_mode              = 'NO_AUTO_VALUE_ON_ZERO' */ ;
DELIMITER ;;
/*!50003 CREATE*/ /*!50020 DEFINER=`root`@`127.0.0.1`*/ /*!50003 PROCEDURE `TrazabilidadRecuperoPadre`(IN idm integer, tipo INTEGER)
BEGIN
    
	
	
	DECLARE done INT DEFAULT FALSE;
	DECLARE padre 	INTEGER; 
	DECLARE idmh 	INTEGER;	
	DECLARE valor 	INTEGER;           
	DECLARE cur1 CURSOR FOR SELECT Mmi,2,idm FROM OrdenesDeProduccionesMmis WHERE OrdenDeProduccionDetalle IN (SELECT Id FROM OrdenesDeProduccionesDetalles WHERE OrdenDeProduccion = idm);
	DECLARE CONTINUE HANDLER FOR NOT FOUND SET done = TRUE;    
    
	 
	
        
        IF(tipo = 3) THEN 
		OPEN cur1;
			REPEAT
				FETCH cur1 INTO idmh, tipo, padre;
				IF NOT done THEN
					INSERT INTO MmiTraz_temp ( Id,  Tipo,  Padre, TipoPadre) VALUES (idmh, 2, idm, 3);
					CALL TrazabilidadRecuperoPadre(idmh, 2); 						
				END IF;
			UNTIL done END REPEAT;
		CLOSE cur1; 
	ELSE 
		IF(tipo = 2) THEN
			
			SET valor = 0;				
			SELECT IFNULL(COUNT(MmiPadre),0) INTO valor FROM Mmis WHERE Id = idm;
			IF (valor <> 0) THEN 
				SET padre 	= idm;
				SELECT MmiPadre INTO idm FROM  Mmis WHERE Id = idm; 
				INSERT INTO MmiTraz_temp ( Id,  Tipo,  Padre, TipoPadre) VALUES (idm, 2, padre, 2);
				CALL TrazabilidadRecuperoPadre(idm, 2); 									
			ELSE
				
				SET valor = 0;
				SELECT IFNULL(COUNT(M.RemitoArticulo),0) INTO valor FROM Mmis M WHERE M.Id = idm;
				IF (valor > 0) THEN 
					SET padre 	= idm;
					SELECT Comprobante INTO idm FROM ComprobantesDetalles WHERE Id IN (SELECT RemitoArticulo FROM Mmis WHERE Id = idm);
					
					INSERT INTO MmiTraz_temp ( Id,  Tipo,  Padre, TipoPadre) VALUES (idm, 1, padre, 2);
				ELSE	
					
					SET valor = 0;
					SELECT IFNULL(COUNT(Id),0) INTO valor FROM ProduccionesMmis WHERE Mmi = idm;
					IF (valor <> 0) THEN
						SET padre 	= idm;				
						SELECT OrdenDeProduccion INTO idm FROM Producciones WHERE Id IN (SELECT Produccion FROM ProduccionesMmis WHERE Mmi = idm);
						INSERT INTO MmiTraz_temp ( Id,  Tipo,  Padre, TipoPadre) VALUES (idm, 3, padre, 2);
						CALL TrazabilidadRecuperoPadre(idm, 3); 			
					END IF;
					
				END IF;
			END IF;					 
		END IF;
        END IF;	
        					
    END */;;
DELIMITER ;
/*!50003 SET sql_mode              = @saved_sql_mode */ ;
/*!50003 SET character_set_client  = @saved_cs_client */ ;
/*!50003 SET character_set_results = @saved_cs_results */ ;
/*!50003 SET collation_connection  = @saved_col_connection */ ;
/*!50001 DROP TABLE IF EXISTS `VBancosCuentas`*/;
/*!50001 DROP VIEW IF EXISTS `VBancosCuentas`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `VBancosCuentas` AS select concat(`TC`.`Codigo`,' : ',`B`.`Descripcion`,' : ',`CB`.`Numero`) AS `Descripcion`,`CB`.`Numero` AS `CuentaBancariaNumero`,`CB`.`Cbu` AS `Cbu`,`CB`.`Persona` AS `Persona`,`CB`.`Propia` AS `Propia`,`B`.`Descripcion` AS `Banco`,`CB`.`Id` AS `CuentaBancariaId`,`BS`.`Id` AS `BancoSucursalId` from (((`BancosSucursales` `BS` join `Bancos` `B` on((`BS`.`Banco` = `B`.`Id`))) join `CuentasBancarias` `CB` on((`BS`.`Id` = `CB`.`BancoSucursal`))) join `TiposDeCuentas` `TC` on((`TC`.`Id` = `CB`.`TipoDeCuenta`))) where (`CB`.`Id` is not null) order by `CB`.`TipoDeCuenta`,`B`.`Descripcion` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `VCuentasBancariasProveedores`*/;
/*!50001 DROP VIEW IF EXISTS `VCuentasBancariasProveedores`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `VCuentasBancariasProveedores` AS select `CB`.`Id` AS `Id`,`CB`.`Numero` AS `Numero`,`CB`.`Cbu` AS `Cbu`,`BS`.`Domicilio` AS `Domicilio`,`B`.`Descripcion` AS `Banco`,`BS`.`Descripcion` AS `Sucursal`,`L`.`Descripcion` AS `Localidad` from ((`CuentasBancarias` `CB` join (`BancosSucursales` `BS` join `Localidades` `L` on((`BS`.`Localidad` = `L`.`Id`))) on((`CB`.`BancoSucursal` = `BS`.`Id`))) join `Bancos` `B` on((`BS`.`Banco` = `B`.`Id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `VFCACantidades`*/;
/*!50001 DROP VIEW IF EXISTS `VFCACantidades`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `VFCACantidades` AS select concat(`FCA`.`Id`,ifnull(`CR`.`Id`,1)) AS `Id`,`FC`.`Id` AS `FacturaCompra`,`FCA`.`Id` AS `FacturaCompraArticulo`,`FCA`.`Articulo` AS `Articulo`,`FCA`.`Cantidad` AS `CantitadFactura`,`RA`.`Cantidad` AS `CantitadRemito`,`R`.`Numero` AS `NumeroRemito`,`R`.`Punto` AS `PuntoRemito` from ((((`ComprobantesDetalles` `FCA` join `Comprobantes` `FC` on((`FCA`.`Comprobante` = `FC`.`Id`))) left join `ComprobantesRelacionados` `CR` on((`CR`.`ComprobantePadre` = `FC`.`Id`))) left join `ComprobantesDetalles` `RA` on(((`RA`.`Comprobante` = `CR`.`ComprobanteHijo`) and (`RA`.`Articulo` = `FCA`.`Articulo`)))) left join `Comprobantes` `R` on((`RA`.`Comprobante` = `R`.`Id`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `VPersonasCuentasCorrientes`*/;
/*!50001 DROP VIEW IF EXISTS `VPersonasCuentasCorrientes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `VPersonasCuentasCorrientes` AS select `CC`.`Persona` AS `Persona`,`CL`.`RazonSocial` AS `RazonSocial`,`CC`.`DescripcionComprobante` AS `DescripcionComprobante`,`TC`.`Descripcion` AS `Descripcion`,`CC`.`FechaComprobante` AS `FechaComprobante`,`CC`.`Debe` AS `Debe`,`CC`.`Haber` AS `Haber` from ((`CuentasCorrientes` `CC` left join `Personas` `CL` on((`CC`.`Persona` = `CL`.`Id`))) left join `TiposDeComprobantes` `TC` on((`CC`.`TipoDeComprobante` = `TC`.`Id`))) where (`CC`.`Persona` <> 0) order by `CC`.`Persona` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `VSaldoCuentasCorrientes`*/;
/*!50001 DROP VIEW IF EXISTS `VSaldoCuentasCorrientes`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `VSaldoCuentasCorrientes` AS select `CC`.`Persona` AS `Persona`,sum(`CC`.`Debe`) AS `Debe`,sum(`CC`.`Haber`) AS `Haber`,(sum(`CC`.`Debe`) - sum(`CC`.`Haber`)) AS `Saldo` from `CuentasCorrientes` `CC` group by `CC`.`Persona` */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `vArticulosArbol`*/;
/*!50001 DROP VIEW IF EXISTS `vArticulosArbol`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vArticulosArbol` AS select `AV_Hijo`.`Id` AS `Id`,`AV_Hijo`.`Articulo` AS `Articulo`,`AV_Hijo`.`Version` AS `Version`,`AV_Hijo`.`Fecha` AS `Fecha`,`AV_Hijo`.`Descripcion` AS `Descripcion`,`AV_Padre`.`Id` AS `Padre` from (`ArticulosVersiones` `AV_Padre` left join (`ArticulosVersionesDetalles` `AVD` join `ArticulosVersiones` `AV_Hijo` on((`AVD`.`ArticuloVersionHijo` = `AV_Hijo`.`Id`))) on((`AVD`.`ArticuloVersionPadre` = `AV_Padre`.`Id`))) where (`AV_Hijo`.`Id` is not null) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `vRelFacturasArticulosOrdenesArticulos`*/;
/*!50001 DROP VIEW IF EXISTS `vRelFacturasArticulosOrdenesArticulos`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vRelFacturasArticulosOrdenesArticulos` AS (select `OA`.`Id` AS `Id`,`FA`.`Id` AS `FAID`,concat('Orden: ',`O`.`Numero`) AS `Numero`,`OA`.`Cantidad` AS `Cantidad`,`OA`.`PrecioUnitario` AS `PrecioUnitario` from (((((`ComprobantesDetalles` `FA` join `ComprobantesRelacionados` `FR` on((`FA`.`Comprobante` = `FR`.`ComprobantePadre`))) join `Comprobantes` `R` on((`R`.`Id` = `FR`.`ComprobanteHijo`))) join `ComprobantesRelacionados` `ORE` on((`R`.`Id` = `ORE`.`ComprobantePadre`))) join `Comprobantes` `O` on((`ORE`.`ComprobanteHijo` = `O`.`Id`))) join `ComprobantesDetalles` `OA` on(((`OA`.`Comprobante` = `O`.`Id`) and (`OA`.`Articulo` = `FA`.`Articulo`))))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!50001 DROP TABLE IF EXISTS `vTransportesUsados`*/;
/*!50001 DROP VIEW IF EXISTS `vTransportesUsados`*/;
/*!50001 SET @saved_cs_client          = @@character_set_client */;
/*!50001 SET @saved_cs_results         = @@character_set_results */;
/*!50001 SET @saved_col_connection     = @@collation_connection */;
/*!50001 SET character_set_client      = utf8 */;
/*!50001 SET character_set_results     = utf8 */;
/*!50001 SET collation_connection      = utf8_general_ci */;
/*!50001 CREATE ALGORITHM=UNDEFINED */
/*!50013 DEFINER=`root`@`localhost` SQL SECURITY DEFINER */
/*!50001 VIEW `vTransportesUsados` AS select `C`.`Id` AS `Id`,`P`.`RazonSocial` AS `RazonSocialRetira`,`P1`.`RazonSocial` AS `RazonSocialEntrega` from ((`Comprobantes` `C` left join `Personas` `P` on((`P`.`Id` = `C`.`TransportistaRetiroDeOrigen`))) left join `Personas` `P1` on((`P1`.`Id` = `C`.`TransportistaEntregoEnDestino`))) */;
/*!50001 SET character_set_client      = @saved_cs_client */;
/*!50001 SET character_set_results     = @saved_cs_results */;
/*!50001 SET collation_connection      = @saved_col_connection */;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

