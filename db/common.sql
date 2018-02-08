
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

LOCK TABLES `MenuesPrincipales` WRITE;
/*!40000 ALTER TABLE `MenuesPrincipales` DISABLE KEYS */;
INSERT INTO `MenuesPrincipales` VALUES (1,'Almacenes',246,1,1,0,8,'ArticulosTipos',NULL,NULL),(11,'Caracteristicas genericas',379,11,1,0,6,'CaracteristicasGenericas',NULL,NULL),(12,'Chequeras',91,12,1,0,5,'CuadernoConLapiz',NULL,NULL),(13,'Cheques Propios',91,13,1,0,4,'Cheques',NULL,NULL),(14,'Bloqueos de fechas para Cheques',91,14,1,0,3,'ChequesBloqueos',NULL,NULL),(15,'Bloqueos de fechas - Motivos',91,15,1,0,2,'ChequesBloqueosTipos',NULL,NULL),(16,'Cheques Estados',91,16,0,0,1,'ChequesEstados',NULL,NULL),(21,'Depositos',246,21,1,0,5,'Depositos2',NULL,NULL),(25,'Estados Civiles',344,25,1,0,19,'EstadosCiviles',NULL,NULL),(30,'Formas De Pagos',121,30,1,0,18,'FormasDePagos',NULL,NULL),(33,'Localidades',209,33,1,0,17,'MundoRojo',NULL,NULL),(34,'Mmis',84,34,1,0,4,'Mmis',NULL,NULL),(35,'Mmis Movimientos',86,35,1,0,3,'MmisMovimientos',NULL,NULL),(38,'Menu Principal',220,38,1,0,16,'Menu',NULL,NULL),(39,'Modalidades De Pagos',210,39,1,0,15,'ModalidadesDePagos',NULL,NULL),(40,'Modalidades IVA',210,40,1,0,14,'ModalidadesIVA',NULL,NULL),(41,'Modulos',220,72,1,0,13,'MRoja',NULL,NULL),(51,'Paises',209,50,1,0,12,'MundoVerde',NULL,NULL),(56,'Provincias',209,55,1,0,10,'MundoAzul',NULL,NULL),(57,'Ubicaciones',246,56,1,0,2,'Zampi',NULL,NULL),(61,'Sexos',121,60,1,0,9,'Sexos',NULL,NULL),(64,'Tipos De Cheques',234,63,1,0,6,'TiposDeCheques',NULL,NULL),(65,'Tipos De Divisas',234,64,1,0,5,'TiposDeDivisas',NULL,NULL),(66,'Tipos De Emisores De Cheques',234,65,1,0,4,'TiposDeEmisoresDeCheques',NULL,NULL),(68,'Tipos De Palets',121,67,1,0,1,'TiposDePalets',NULL,NULL),(69,'Unidades De Medidas',121,68,1,0,2,'UnidadesDeMedidas',NULL,NULL),(71,'Zonas de Ventas',212,70,1,0,2,'ZonasdeVentas',NULL,NULL),(73,'Tipos de Telefonos',121,73,1,0,1,'TiposDeTelefonos',NULL,NULL),(84,'Produccion',NULL,NULL,1,1,4,'Produccion',600,450),(85,'Laboratorio',NULL,NULL,1,1,5,'xe',600,450),(86,'Adm Almacenes',NULL,NULL,1,1,6,'AdmAlmacenes',600,450),(88,'Personal',NULL,NULL,1,1,8,'Empleados',600,450),(89,'Adm Gerencial',NULL,NULL,1,1,9,'AdmGerencial',600,450),(90,'Adm Bancaria',120,NULL,1,1,1,'defaultFolder',600,450),(91,'Adm Cheques',120,NULL,1,1,2,'defaultFolder',600,450),(98,'Administrar Empleados',88,80,1,0,0,'AbmEmpleados',NULL,NULL),(105,'Mmis Tipos',270,87,1,0,0,'MmisTipos',NULL,NULL),(113,'Administrador de Almacenes',86,83,1,0,0,'gesalmacenes',NULL,NULL),(114,'Ordenes de Compras',163,95,1,0,0,'DocAzul',NULL,NULL),(117,'Ingreso de Comprobantes',NULL,NULL,1,1,0,'CarritoCompras',600,450),(118,'Ingreso de Comprobantes',117,150,1,0,0,'DocNaranja',NULL,NULL),(120,'Contable',NULL,NULL,1,1,0,'Pesos',600,450),(121,'Panel de Control',NULL,NULL,1,1,0,'PanelDeControlB',600,450),(122,'Adm Usuarios',121,NULL,1,1,0,'defaultFolder',600,450),(126,'Tipos de Bienes',233,10,1,0,0,'BienesTipos',NULL,NULL),(127,'Clases de bienes',233,6,1,0,0,'Bienes',NULL,NULL),(135,'Administrar Proveedores',117,107,1,0,0,'Proveedores',NULL,NULL),(140,'Tipos de analisis',245,112,1,0,0,'xb',NULL,NULL),(141,'Analisis',245,113,1,0,0,'Analisis',NULL,NULL),(142,'Asignar Analisis a Productos',245,114,1,0,0,'AnalisisArticulos',NULL,NULL),(143,'Usuarios',122,115,1,0,0,'MundoVerde',NULL,NULL),(145,'Muestras',85,117,1,0,0,'xd',NULL,NULL),(146,'Habilitar MMI para produccion',85,118,1,0,0,'ok',NULL,NULL),(147,'Asignar Analisis a Grupos',245,119,1,0,0,'xc',NULL,NULL),(148,'Resultados',85,120,1,0,0,'VistoVerde',NULL,NULL),(149,'Muestras analizadas',85,121,1,0,0,'VistoRojo',NULL,NULL),(153,'Gestionar planes de cuentas',211,580,1,0,NULL,'Tree',NULL,NULL),(154,'Grupos de planes de cuentas',211,126,1,0,NULL,'TresCuadros',NULL,NULL),(155,'Libros IVA',267,127,1,0,NULL,'Libros',NULL,NULL),(156,'Gestionar ambitos',211,130,1,0,NULL,'MundoAzul',NULL,NULL),(157,'Conceptos impositivos - General',208,128,1,0,NULL,'CI_Gral',NULL,NULL),(158,'Gestionar entes recaudadores',211,129,1,0,NULL,'CaritaDePlata',NULL,NULL),(162,'Administrar Clientes',163,575,1,0,NULL,'Personas',NULL,NULL),(163,'Emision de Comprobantes',NULL,NULL,1,1,NULL,'CaritaDePlata',600,450),(164,'Pedidos de Cotizaciones',163,137,1,0,NULL,'DocVerde',NULL,NULL),(171,'Motivo de Notas de Pagos',234,42,1,0,NULL,'NotasDePagosMotivo',NULL,NULL),(174,'Generador de Cheques',91,147,1,0,NULL,'printerCheque',NULL,NULL),(186,'Consultar Precios Insumos',84,160,0,0,NULL,'Archivador',NULL,NULL),(192,'Emision de Comprobantes',163,170,1,0,NULL,'FacturasVentas',NULL,NULL),(196,'Administrar Listas de Precios',163,172,1,0,NULL,'Tablaanotador',NULL,NULL),(200,'Ingreso de mercaderia sin remito',86,177,1,0,0,'DocNaranja',NULL,NULL),(201,'Modalidad de Ingresos Brutos',210,142,1,0,NULL,'',NULL,NULL),(202,'Modalidad de Ganancias',234,178,1,0,NULL,'',NULL,NULL),(203,'Recibos',163,179,1,0,NULL,'DocNaranja',NULL,NULL),(205,'Conceptos Facturacion - Servicios de terceros',208,182,1,0,NULL,'CF_Serv',NULL,NULL),(207,'Cheques de terceros',91,184,1,0,NULL,'cheques',NULL,NULL),(208,'Conceptos Facturacion',120,NULL,1,1,NULL,'defaultFolder',600,450),(209,'Varios Ubicacion',121,NULL,1,1,NULL,'defaultFolder',600,450),(210,'Varios Impositivos',121,NULL,1,1,NULL,'defaultFolder',600,450),(211,'Adm Contable',120,NULL,1,1,NULL,'defaultFolder',600,450),(212,'Adm Ventas',163,NULL,1,1,NULL,'defaultFolder',600,450),(213,'Pedidos de Clientes',117,185,1,0,NULL,'DocVerde',NULL,NULL),(214,'Remitos De Salidas',163,186,1,0,NULL,'kword',NULL,NULL),(215,'Crear Modelo',220,187,1,0,NULL,'Generador',NULL,NULL),(217,'Asignar Modelos a Modulos',122,547,1,0,NULL,'MarcasA',NULL,NULL),(218,'Roles',122,548,1,0,NULL,'MundoAzul',NULL,NULL),(219,'Asignar Modulos a Roles',122,549,1,0,NULL,'MarcasC',NULL,NULL),(220,'Desarrollo',NULL,NULL,1,1,NULL,'MmisTipos',600,450),(221,'Asignar Modelos a Roles',122,550,1,0,NULL,'MarcasB',NULL,NULL),(224,'Movimientos Bancarios',90,554,0,0,NULL,'',NULL,NULL),(228,'Formatos de Importacion',90,559,0,0,NULL,'',NULL,NULL),(232,'Marcas',244,100,1,0,NULL,'MarcasD',NULL,NULL),(233,'Panel Bienes',121,NULL,1,1,NULL,'defaultFolder',600,450),(234,'Panel Contable',121,NULL,1,1,NULL,'defaultFolder',600,450),(235,'Conceptos impositivos - Percepciones',208,562,1,0,NULL,'CI_Per',NULL,NULL),(236,'Conceptos impositivos - Retenciones',208,563,1,0,NULL,'CI_Ret',NULL,NULL),(237,'Old_Productos',84,564,0,0,NULL,'',NULL,NULL),(239,'Productos Caracteristicas',379,565,1,0,NULL,'',NULL,NULL),(240,'Caracteristicas Opciones Listas',379,566,1,0,NULL,'',NULL,NULL),(241,'Lotes De Terceros',244,567,1,0,NULL,'',NULL,NULL),(242,'Tipos De Direcciones',209,568,1,0,NULL,'',NULL,NULL),(244,'Configuración',84,NULL,1,1,NULL,'defaultFolder',600,450),(245,'Configuracion',85,NULL,1,0,NULL,'',NULL,NULL),(246,'Configuracion',86,NULL,1,1,NULL,'defaultFolder',600,450),(247,'Log Usuarios',122,570,1,0,NULL,'Anotador',NULL,NULL),(248,'Transacciones',90,NULL,1,1,NULL,'defaultFolder',600,450),(249,'Depositos Entrantes',248,571,1,0,NULL,'VistoVerde',NULL,NULL),(250,'Transferencias Entrantes',248,572,1,0,NULL,'VistoVerde',NULL,NULL),(251,'Transferencias Salientes',248,573,1,0,NULL,'VistoRojo',NULL,NULL),(252,'Depositos Salientes',248,574,1,0,NULL,'VistoRojo',NULL,NULL),(254,'Remitos de Ingresos',86,576,1,0,NULL,'DocVerde',NULL,NULL),(257,'Administrar Bancos y Cuentas Propias',90,578,1,0,NULL,'SucursalesBancarias',NULL,NULL),(258,'Administrar Vendedores',212,579,1,0,NULL,'AdmVendedores',NULL,NULL),(260,'Asociar Remitos',117,581,1,0,NULL,'',NULL,NULL),(261,'Libros de IVA Detalles Ventas',267,582,1,0,NULL,'LibrosIVA',NULL,NULL),(262,'Est. Pagos y Cobros Mensuales',89,583,1,0,NULL,'infGraficos',NULL,NULL),(263,'Est. Comprobantes Mensual',89,584,1,0,NULL,'infGraficos',NULL,NULL),(264,'Est. Venta mensual',89,585,1,0,NULL,'infGraficos',NULL,NULL),(265,'Rep. de Cheques',91,586,1,0,NULL,'reportes',NULL,NULL),(266,'Inf. Cuentas Corrientes de Clientes',89,587,1,0,NULL,'infGral',NULL,NULL),(267,'Adm Libros IVA',120,NULL,1,1,NULL,'defaultFolder',600,450),(268,'Rep. Libros de IVA',267,588,1,0,NULL,'reportes',NULL,NULL),(270,'Produccion',321,NULL,1,1,NULL,'defaultFolder',600,450),(271,'Tipos De Lineas De Produccion',244,590,1,0,NULL,'',NULL,NULL),(274,'Ordenes de Produccion',84,593,1,0,NULL,'',NULL,NULL),(276,'Producir',84,597,1,0,NULL,'',NULL,NULL),(277,'Codigos Actividades Afip',234,598,1,0,NULL,'Archivador',NULL,NULL),(278,'Actividades',244,599,1,0,NULL,'BienesDelInventario',NULL,NULL),(279,'Lineas de Producción',244,600,1,0,NULL,'',NULL,NULL),(280,'Areas De Trabajos',344,601,1,0,NULL,'',NULL,NULL),(281,'Rep. Producción',89,602,1,0,NULL,'reportes',NULL,NULL),(282,'Grupos de Usuarios',122,603,1,0,NULL,'MarcasD',NULL,NULL),(287,'Lotes Propios',84,610,1,0,NULL,'',NULL,NULL),(288,'Consola PHP',220,612,1,0,NULL,'Terminal',NULL,NULL),(289,'Analisis Valores Listas',245,616,1,0,NULL,'',NULL,NULL),(290,'Libros de IVA Detalles Compras',267,617,1,0,NULL,'LibrosIVA',NULL,NULL),(291,'Rep. de Clientes o Proveedores',89,618,1,0,NULL,'reportes',NULL,NULL),(292,'Rep. de montos de Ventas por Localidad',89,619,1,0,NULL,'reportes',NULL,NULL),(294,'Conceptos Facturacion - Bancarios',208,621,1,0,NULL,'CF_Banco',NULL,NULL),(295,'Conciliacion Bancaria',90,622,1,0,NULL,'Anotador',NULL,NULL),(296,'Dar destino a cheques',248,623,1,0,NULL,'',NULL,NULL),(297,'Rep. de Ventas y Compras ',89,624,1,0,NULL,'reportes',NULL,NULL),(299,'Control Stock',86,626,1,0,NULL,'AnalisisArticulos',NULL,NULL),(301,'Est. Ventas y Compras últimos tres años',89,628,1,0,NULL,'infGraficos',NULL,NULL),(304,'Extracciones Bancarias',248,630,1,0,NULL,'VistoRojo',NULL,NULL),(308,'Administrar Cajas',120,634,1,0,NULL,'',NULL,NULL),(309,'Rep. Detalle De Caja',89,635,0,0,NULL,'reportes',NULL,NULL),(310,'Ordenes de Compras Varios',163,636,1,0,NULL,'DocRosado',NULL,NULL),(311,'Ordenes de Pago',163,165,1,0,NULL,'DocRojo',NULL,NULL),(312,'Depositos desde caja a cuenta propia',248,637,1,0,NULL,'VistoVerde',NULL,NULL),(313,'Transferencias entre Cuentas Propias',248,638,1,0,NULL,'VistoVerde',NULL,NULL),(314,'Movimientos De Cuentas Bancarias',90,639,1,0,NULL,'',NULL,NULL),(316,'Rep. de Ventas y Compras por Articulos',89,641,1,0,NULL,'reportes',NULL,NULL),(317,'Rep. de Cuentas Corrientes',89,642,1,0,NULL,'reportes',NULL,NULL),(318,'Comprobantes Bancarios',117,643,1,0,NULL,'DocRosado',NULL,NULL),(319,'Articulos y SubArticulos',395,645,1,0,NULL,'TresCuadros',NULL,NULL),(320,'Remitos Despachados',86,647,1,0,NULL,'Remitidos',NULL,NULL),(321,'Administracion',220,NULL,1,1,NULL,'defaultFolder',600,450),(322,'Mmis Actividad',270,648,1,0,NULL,'',NULL,NULL),(323,'Dar destino a cheques',91,623,1,0,NULL,'',NULL,NULL),(324,'Recibo por entrega de Cheques',91,649,1,0,NULL,'infGralB',NULL,NULL),(325,'Rep. de Stock',86,651,1,0,NULL,'reportes',NULL,NULL),(326,'Productos',84,652,1,0,0,'',0,0),(328,'Tipos De Bajas',344,654,1,0,NULL,'',NULL,NULL),(329,'Liquidacion de Sueldos',NULL,NULL,1,1,NULL,'Dolares',600,450),(330,'Grupos de Categorias de Convenios',88,655,1,0,NULL,'Camisas',NULL,NULL),(331,'Empresas',329,656,1,0,NULL,'Depositos2',600,450),(332,'Tipos De Jornadas',344,657,1,0,NULL,'',NULL,NULL),(333,'Tipos de Conceptos',349,658,0,0,NULL,'',NULL,NULL),(335,'Tipos de Liquidaciones',349,660,1,0,NULL,'',NULL,NULL),(336,'Grupos de Personas a Liquidar',349,661,1,0,NULL,'',NULL,NULL),(337,'Tipos De Tablas para Liquidaciones',344,662,1,0,NULL,'',NULL,NULL),(343,'Administrar Convenios Colectivos de Trabajo',88,668,1,0,NULL,'Convenios',NULL,NULL),(344,'Referenciales',88,NULL,1,1,NULL,'Anotador',NULL,NULL),(345,'Tipos de Familiares',344,669,1,0,NULL,'',NULL,NULL),(346,'Tipos de Escolaridad',344,670,1,0,NULL,'',NULL,NULL),(347,'Organismos',344,671,1,0,NULL,'',NULL,NULL),(348,'Conceptos a Liquidar',329,673,1,0,NULL,'C',NULL,NULL),(349,'Referenciales',329,NULL,1,1,NULL,'Anotador',NULL,NULL),(351,'Variables Generales',329,676,1,0,NULL,'V',NULL,NULL),(352,'Parametros',329,677,1,0,NULL,'P',NULL,NULL),(353,'Categorias de Variables',349,678,1,0,NULL,'',NULL,NULL),(354,'Tipos De Organismos',344,679,1,0,NULL,'',NULL,NULL),(355,'Modalidades de Contrataciones',344,680,1,0,NULL,'',NULL,NULL),(356,'Situaciones De Revistas',344,681,1,0,NULL,'',NULL,NULL),(357,'Prueba Liquidacion',329,682,1,0,NULL,'',NULL,NULL),(358,'Rep. estadistico de ventas por Clientes',89,683,1,0,NULL,'reportes',NULL,NULL),(359,'Administrar Formulas',84,684,1,0,NULL,'FormulasA',NULL,NULL),(360,'Rep. estadistico de ventas por Localidad',89,685,1,0,NULL,'reporte',NULL,NULL),(361,'Rep. Recibos',163,686,1,0,NULL,'reportes',NULL,NULL),(362,'Rep. Comprobantes de Ventas',163,689,1,0,NULL,'reportes',NULL,NULL),(363,'Rep. Ordenes de Pagos',163,688,1,0,NULL,'reportes',NULL,NULL),(364,'Rep. Comprobantes de Compra',117,687,1,0,NULL,'reportes',NULL,NULL),(365,'Conceptos Facturacion - Servicios prestados',208,691,1,0,NULL,'',NULL,NULL),(366,'Grupos de Articulos',395,694,1,0,NULL,'',NULL,NULL),(368,'Tipos de Feriados',349,695,1,0,NULL,'',NULL,NULL),(369,'Feriados',349,696,1,0,NULL,'',NULL,NULL),(370,'TiposDeArticulos',244,697,0,0,NULL,'',NULL,NULL),(371,'Familiares',344,698,1,0,NULL,'',NULL,NULL),(376,'Debito Directo de Cta Bancaria',248,699,1,0,NULL,'VistoRojo',NULL,NULL),(377,'Calificaciones Profesionales',344,700,1,0,NULL,'',NULL,NULL),(378,'Tipos de Titulos',344,701,1,0,NULL,'',NULL,NULL),(379,'Caracteristicas',220,NULL,1,1,NULL,'defaultFolder',NULL,NULL),(380,'Modelos Caracteristicas',379,703,1,0,NULL,'',NULL,NULL),(381,'Tipos de Horas Extras',344,704,1,0,NULL,'',NULL,NULL),(382,'Titulos',344,705,1,0,NULL,'',NULL,NULL),(383,'Niveles Academicos de Titulos',344,706,1,0,NULL,'',NULL,NULL),(384,'Liquidaciones',329,708,1,0,NULL,'',NULL,NULL),(385,'Adm Tarjetas de Credito',120,NULL,1,1,NULL,'defaultFolder',600,450),(386,'Tarjetas de Credito Propias',385,710,1,0,NULL,'',NULL,NULL),(387,'Marcas de Tarjetas',385,711,1,0,NULL,'',NULL,NULL),(388,'Cupones Salientes',385,712,1,0,NULL,'',NULL,NULL),(389,'Cupones Entrantes',385,713,1,0,NULL,'',NULL,NULL),(390,'Administrar Descuentos',329,714,1,0,NULL,'',NULL,NULL),(391,'Deducciones Ganancias',349,715,1,0,NULL,'',NULL,NULL),(392,'Tipos de Deducciones Ganancias',349,716,1,0,NULL,'',NULL,NULL),(393,'Conceptos Extras',329,717,1,0,NULL,'',NULL,NULL),(394,'POS',163,718,1,0,NULL,'',NULL,NULL),(395,'Articulos',NULL,NULL,1,1,NULL,'TresCuadros',NULL,NULL),(397,'Articulos Comp/Vent',395,719,1,0,NULL,'',NULL,NULL),(398,'Tarjetas de Credito Terceros',385,720,1,0,NULL,'',NULL,NULL),(399,'Laborales',88,721,1,0,NULL,'',NULL,NULL),(400,'Tablas Afip',329,NULL,1,1,NULL,'',450,600),(401,'Afip - Deducciones De Ganancias',400,722,1,0,NULL,'',NULL,NULL),(402,'Afip - Detalles de Deducciones de Ganancias',400,723,1,0,NULL,'',NULL,NULL),(403,'Afip - Periodos de Deducciones de Ganancias',400,724,1,0,NULL,'',NULL,NULL),(404,'Cuit de Paises',209,725,1,0,NULL,'',NULL,NULL),(405,'Idiomas',121,726,1,0,NULL,'',NULL,NULL),(406,'Puntos de Ventas',212,727,1,0,NULL,'',NULL,NULL);
/*!40000 ALTER TABLE `MenuesPrincipales` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `Modulos` WRITE;
/*!40000 ALTER TABLE `Modulos` DISABLE KEYS */;
INSERT INTO `Modulos` VALUES (1,'Almacenesdefault','Almacenes','abm','index','/m/Almacenes/model/Almacenes','Window',NULL),(6,'Bienesdefault','Bienes','abm','index','/m/Base/model/Bienes','Window',NULL),(10,'BienesTiposdefault','Bienes Tipos','abm','index','/m/Base/model/BienesTipos','Window',NULL),(11,'Caracteristicasdefault','Caracteristicas','abm','index','/model/Caracteristicas','Window',''),(12,'Chequerasdefault','Chequeras','abm','index','/m/Base/model/Chequeras','Window',NULL),(13,'ChequesPropiosdefault','Cheques','abm','index','/m/Base/model/ChequesPropios','Window',NULL),(14,'ChequesBloqueosdefault','ChequesBloqueos','abm','index','/m/Base/model/ChequesBloqueos','Window',NULL),(15,'ChequesBloqueosTiposdefault','ChequesBloqueosTipos','abm','index','/m/Base/model/ChequesBloqueosTipos','Window',NULL),(16,'ChequesEstadosdefault','ChequesEstados','abm','index','/m/Base/model/ChequesEstados','Window',NULL),(21,'Depositosdefault','Depositos','abm','index','/m/Base/model/DepositosPropios','Window',NULL),(25,'EstadosCivilesdefault','EstadosCiviles','abm','index','/m/Base/model/EstadosCiviles','Window',NULL),(30,'FormasDePagosdefault','FormasDePagos','abm','index','/m/Base/model/FormasDePagos','Window',NULL),(33,'Localidadesdefault','Localidades','abm','index','/m/Base/model/Localidades','Window',NULL),(34,'Mmisdefault','Mmis','list','index','/m/Almacenes/model/Mmis','Window',NULL),(35,'MmisMovimientosdefault','MmisMovimientos','list','index','/m/Almacenes/model/MmisMovimientos','Window',NULL),(38,'MenuesPrincipalesdefault','Menuesprincipales','abm','index','/model/MenuesPrincipales','Window',NULL),(39,'ModalidadesDePagosdefault','Modalidades De Pagos','abm','index','/m/Base/model/ModalidadesDePagos','Window',NULL),(40,'ModalidadesIVAdefault','ModalidadesIVA','abm','index','/m/Base/model/ModalidadesIVA','Window',NULL),(42,'NotasDePagosMotivodefault','NotasDePagosMotivo','abm','index','/m/Facturacion/model/NotasDePagosMotivo','Window',NULL),(50,'Paisesdefault','Paises','abm','index','/m/Base/model/Paises','Window',NULL),(55,'Provinciasdefault','Provincias','abm','index','/m/Base/model/Provincias','Window',NULL),(56,'Ubicacionesdefault','Ubicaciones','abm','index','/m/Almacenes/model/Ubicaciones','Window',NULL),(60,'Sexosdefault','Sexos','abm','index','/m/Base/model/Sexos','Window',NULL),(63,'TiposDeChequesdefault','TiposDeCheques','abm','index','/m/Contable/model/TiposDeCheques','Window',NULL),(64,'TiposDeDivisasdefault','TiposDeDivisas','abm','index','/m/Base/model/TiposDeDivisas','Window',NULL),(65,'TiposDeEmisoresDeChequesdefault','TiposDeEmisoresDeCheques','abm','index','/m/Contable/model/TiposDeEmisoresDeCheques','Window',NULL),(67,'TiposDePaletsdefault','TiposDePalets','abm','index','/m/Almacenes/model/TiposDePalets','Window',NULL),(68,'UnidadesDeMedidasdefault','UnidadesDeMedidas','abm','index','/m/Base/model/UnidadesDeMedidas','Window',NULL),(70,'ZonasDeVentasdefault','Zonas de Ventas','abm','index','/m/Base/model/ZonasDeVentas','Window',NULL),(72,'Modulosdefault','Modulos','abm','index','/model/Modulos','Window',NULL),(73,'TiposDeTelefonosdefault','Tipos de Telefonos','abm','index','/m/Base/model/TiposDeTelefonos','Window',NULL),(80,'administrarEmpleados','Telefonos de Empleados','administrarEmpleados','','','Base',NULL),(83,'Almacenes','AdministraciÃ³n de Almacenes','Almacenes','','','Almacenes',NULL),(87,'MmisTiposdefault','Mmis Tipos','abm','index','/m/Almacenes/model/MmisTipos','Window',NULL),(95,'ordenesDeCompras','ordenesDeCompras','ordenesDeCompras','','','Facturacion',NULL),(100,'Marcasdefault',NULL,'abm','index','/m/Base/model/Marcas','Window',NULL),(107,'administrarProveedores',NULL,'administrarProveedores','','','Base',NULL),(112,'TiposDeAnalisisdefault',NULL,'abm','index','/m/Laboratorio/model/TiposDeAnalisis','Window',NULL),(113,'Analisisdefault',NULL,'abm','index','/m/Laboratorio/model/Analisis','Window',NULL),(114,'ProductosGruposAnalisisdefault',NULL,'abm','index','/m/Base/model/ProductosGruposAnalisis','Window',NULL),(115,'Usuariosdefault',NULL,'abm','index','/model/Usuarios','Window',NULL),(117,'AnalisisMuestrasdefault',NULL,'abm','index','/m/Laboratorio/model/AnalisisMuestras','Window',NULL),(118,'habilitarMmis',NULL,'habilitarMmis','','','Laboratorio',NULL),(119,'asignarAnalisisaGrupos',NULL,'asignarAnalisisaGrupos','','','Laboratorio',NULL),(120,'Asignarresultadosdeanalisis',NULL,'Asignarresultadosdeanalisis','','','Laboratorio',NULL),(121,'muestrasAnalizadas',NULL,'muestrasAnalizadas','','','Laboratorio',NULL),(126,'PlanesDeCuentasGruposdefault',NULL,'abm','index','/m/Contable/model/PlanesDeCuentasGrupos','Window',NULL),(127,'LibrosIVAdefault',NULL,'abm','index','/m/Contable/model/LibrosIVA','Window',NULL),(128,'ConceptosImpositivosdefault',NULL,'abm','index','/m/Base/model/ConceptosImpositivos','Window',NULL),(129,'EntesRecaudadoresdefault',NULL,'abm','index','/m/Base/model/EntesRecaudadores','Window',NULL),(130,'Ambitosdefault',NULL,'abm','index','/m/Base/model/Ambitos','Window',NULL),(137,'pedidosDeCotizaciones',NULL,'pedidosDeCotizaciones','','','Facturacion',NULL),(142,'TiposDeInscripcionesIBdefault',NULL,'abm','index','/m/Base/model/TiposDeInscripcionesIB','Window',NULL),(147,'GeneradorDeChequesdefault',NULL,'abm','index','/m/Base/model/GeneradorDeCheques','Window',NULL),(150,'facturasCompras',NULL,'facturasCompras','','','Facturacion',NULL),(160,'insumosPrecios',NULL,'insumosPrecios','','','Base',NULL),(165,'ordenesDePagos',NULL,'ordenesDePagos','','','Facturacion',NULL),(170,'facturasVentas',NULL,'facturasVentas','','','Facturacion',NULL),(172,'administrarListasDePrecios',NULL,'administrarListasDePrecios','','','Base',NULL),(177,'remitosSinRemito',NULL,'remitosSinRemito','','','Almacenes',NULL),(178,'TiposDeInscripcionesGananciasdefault',NULL,'abm','index','/m/Base/model/TiposDeInscripcionesGanancias','Window',NULL),(179,'recibos',NULL,'recibos','','','Facturacion',NULL),(182,'ConceptosFacturacionServiciosdefault',NULL,'abm','index','/m/Base/model/ConceptosFacturacionServicios','Window',NULL),(184,'ChequesDeTercerosdefault',NULL,'abm','index','/m/Base/model/ChequesDeTerceros','Window',NULL),(185,'ordenesDePedidos',NULL,'ordenesDePedidos','','','Facturacion',NULL),(186,'remitosDeSalidas',NULL,'remitosDeSalidas','','','Almacenes',NULL),(187,'buildModels',NULL,'buildModels','','','Develop',NULL),(547,'asignarModelosAModulos',NULL,'asignarModelosAModulos','','','Develop',NULL),(548,'Rolesdefault',NULL,'abm','index','/model/Roles','Window',NULL),(549,'asignarModulosARoles',NULL,'asignarModulosARoles','','','default',NULL),(550,'asignarModelosARoles',NULL,'asignarModelosARoles','','','default',NULL),(554,'BancosMovimientosdefault',NULL,'abm','index','/m/Contable/model/BancosMovimientos','Window',NULL),(559,'FormatosDeImportacionBancaria',NULL,'FormatosDeImportacionBancaria','','','Window',NULL),(562,'ConceptosDePercepcionesdefault',NULL,'abm','index','/m/Base/model/ConceptosDePercepciones','Window',NULL),(563,'ConceptosDeRetencionesdefault',NULL,'abm','index','/m/Base/model/ConceptosDeRetenciones','Window',NULL),(564,'Productos',NULL,'Productos','','','Base',NULL),(565,'ProductosCategoriasdefault',NULL,'abm','index','/m/Base/model/ProductosCategorias','Window',NULL),(566,'CaracteristicasListasdefault',NULL,'abm','index','/model/CaracteristicasListas','Window',''),(567,'LotesDeTercerosdefault',NULL,'abm','index','/m/Almacenes/model/LotesDeTerceros','Window',NULL),(568,'TiposDeDireccionesdefault',NULL,'abm','index','/m/Base/model/TiposDeDirecciones','Window',NULL),(570,'UsuariosLogsdefault',NULL,'list','index','/model/UsuariosLogs','Window',NULL),(571,'DepositosEntrantesdefault',NULL,'abm','index','/m/Base/model/DepositosEntrantes','Window',NULL),(572,'TransferenciasEntrantesdefault',NULL,'abm','index','/m/Base/model/TransferenciasEntrantes','Window',NULL),(573,'TransferenciasSalientesdefault',NULL,'abm','index','/m/Base/model/TransferenciasSalientes','Window',NULL),(574,'DepositosSalientesdefault',NULL,'abm','index','/m/Base/model/DepositosSalientes','Window',NULL),(575,'administrarClientes',NULL,'administrarClientes','','','Base',NULL),(576,'remitosDeIngresos',NULL,'remitosDeIngresos','','','Almacenes',NULL),(578,'administrarBancos',NULL,'administrarBancos','','','Base',NULL),(579,'administrarVendedores',NULL,'administrarVendedores','','','Base',NULL),(580,'planesDeCuentas',NULL,'planesDeCuentas','','','Contable',NULL),(581,'relacionarRemitosFacturasCompras',NULL,'relacionarRemitosFacturasCompras','','','Almacenes',NULL),(582,'LibrosIVADetallesVentasventas',NULL,'list','index','/m/Contable/model/LibrosIVADetalles/fetch/EsParaVenta/section/ventas','Window',NULL),(583,'pagoscobrosMensual',NULL,'pagoscobrosMensual','','','Contable',NULL),(584,'movimientosMensual',NULL,'movimientosMensual','','','Contable',NULL),(585,'resumenVentasMensual',NULL,'resumenVentasMensual','','','Contable',NULL),(586,'ReporteCheques',NULL,'ReporteCheques','','','Base',NULL),(587,'cuentaCorrienteClientes',NULL,'cuentaCorrienteClientes','','','Contable',NULL),(588,'ReporteLibroIva',NULL,'ReporteLibroIva','','','Contable',NULL),(590,'TiposDeLineasDeProduccionesdefault',NULL,'abm','Index','/m/Produccion/model/TiposDeLineasDeProducciones','Window',NULL),(593,'ordenesDeProducciones',NULL,'ordenesDeProducciones','','','Produccion',NULL),(594,'FacturasVentasMapper','FacturasVentasMapper','Facturacion_Model_FacturasVentasMapper',NULL,NULL,NULL,NULL),(595,'RecibosMapper',NULL,'Facturacion_Model_RecibosMapper','','','',NULL),(596,'RemitosDeSalidasMapper',NULL,'Almacenes_Model_RemitosDeSalidasMapper','','','',NULL),(597,'Produccion',NULL,'Produccion','','','Produccion',NULL),(598,'CodigosActividadesAfipdefault',NULL,'abm','index','/m/Base/model/CodigosActividadesAfip','Window',NULL),(599,'Actividadesdefault',NULL,'abm','index','/m/Produccion/model/Actividades','Window',NULL),(600,'LineasDeProduccion',NULL,'LineasDeProduccion','','','Produccion',NULL),(601,'AreasDeTrabajosdefault',NULL,'abm','index','/m/Base/model/AreasDeTrabajos','Window',NULL),(602,'ViewProduccion',NULL,'viewProduccion','','','Produccion',NULL),(603,'gruposDeUsuarios',NULL,'gruposDeUsuarios','','','default',NULL),(608,'OrdenesDeComprasMapper',NULL,'Facturacion_Model_OrdenesDeComprasMapper','','','',NULL),(609,'FacturasComprasMapper',NULL,'Facturacion_Model_FacturasComprasMapper','','','',NULL),(610,'LotesPropiosdefault',NULL,'abm','index','/m/Almacenes/model/LotesPropios','Window',NULL),(611,'ProduccionesMapper',NULL,'Produccion_Model_ProduccionesMapper','','','',NULL),(612,'debugconsole',NULL,'debugconsole','','','Develop',NULL),(616,'AnalisisValoresListasdefault',NULL,'abm','index','/m/Laboratorio/model/AnalisisValoresListas','Window',NULL),(617,'LibrosIVADetallesComprascompras',NULL,'list','index','/m/Contable/model/LibrosIVADetalles/fetch/EsParaCompra/section/compras','Window',NULL),(618,'ReporteClientes',NULL,'ReporteClientes','','','Base',NULL),(619,'ReporteMontosDeVentasxLocalidad',NULL,'ReporteMontosDeVentasxLocalidad','','','Base',''),(621,'ConceptosFacturacionBancariosdefault',NULL,'abm','index','/m/Base/model/ConceptosFacturacionBancarios','Window',NULL),(622,'ChequesConciliacionBancariadefault',NULL,'abm','index','/m/Base/model/ChequesConciliacionBancaria','Window',NULL),(623,'ChequesDarDestinosdefault',NULL,'abm','index','/m/Base/model/ChequesDarDestinos/fetch/DarDestino','Window',NULL),(624,'ReporteVentasCompras',NULL,'ReporteVentasCompras','','','Base',NULL),(626,'ControlStock',NULL,'ControlStock','','','Almacenes',NULL),(627,'Trazabilidad',NULL,'Trazabilidad','','','Almacenes',NULL),(628,'ReporteVentasxMes',NULL,'ReporteVentasxMes','','','Base',NULL),(630,'ExtraccionesBancariasdefault',NULL,'abm','index','/m/Base/model/ExtraccionesBancarias','Window',NULL),(631,'ComprobantesPagosdefault',NULL,'list','index','/model/ComprobantesPagos/m/Facturacion','Window',NULL),(634,'administrarCajas',NULL,'administrarCajas','','','Contable',NULL),(635,'ReporteCajaDetalle',NULL,'ReporteCajaDetalle','','','Base',NULL),(636,'ordenesDeComprasVarios',NULL,'ordenesDeComprasVarios','','','Facturacion',NULL),(637,'DepositosPropiosEntratesdefault',NULL,'abm','index','/m/Base/model/DepositosPropiosEntrantes','Window',NULL),(638,'TransferenciasPropiasSalientesdefault',NULL,'abm','index','/m/Base/model/TransferenciasPropiasSalientes','Window',NULL),(639,'CuentasBancariasMovimientosdefault',NULL,'abm','index','/m/Base/model/CuentasBancariasMovimientos','Window',NULL),(640,'colaimpresion',NULL,'colaimpresion','','','default',NULL),(641,'ReporteVentasComprasArticulos',NULL,'ReporteVentasComprasArticulos','','','Base',NULL),(642,'ReporteCuentasCorrientes',NULL,'ReporteCuentasCorrientes','','','Base',NULL),(643,'comprobantesBancarios',NULL,'comprobantesBancarios','','','Facturacion',NULL),(644,'configuracion',NULL,'configuracion','','','default',NULL),(645,'administrarArticulos',NULL,'administrarArticulos','','','Base',NULL),(646,'ArticulosVersionesdefault',NULL,'abm','index','/m/Base/module/ArticulosVersiones','Window',NULL),(647,'remitosDespachados',NULL,'remitosDespachados','','','Almacenes',NULL),(648,'MmisAccionesdefault',NULL,'abm','index','/m/Almacenes/model/MmisAcciones','Window',NULL),(649,'ReporteChequesEntregadosASocios',NULL,'ReporteChequesEntregadosASocios','','','Base',NULL),(650,'AlmacenesMapper',NULL,'Almacenes_Model_AlmacenesMapper','','','',NULL),(651,'ReporteDeStock',NULL,'ReporteDeStock','','','Almacenes',NULL),(652,'Productosdefault',NULL,'abm','index','/m/Base/model/Productos','Window',NULL),(653,'OrdenesDeProduccionesMapper',NULL,'Produccion_Model_OrdenesDeProduccionesMapper','','','',NULL),(654,'TiposDeBajasdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeBajas','Window',NULL),(655,'CategoriasGruposdefault',NULL,'abm','index','/m/Rrhh/model/CategoriasGrupos','Window',NULL),(656,'Empresasdefault',NULL,'abm','index','/m/Base/model/Empresas','Window',NULL),(657,'TiposDeJornadasdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeJornadas','Window',NULL),(658,'TiposDeConceptosLiquidacionesdefault',NULL,'abm','index','/m/Liquidacion/model/TiposDeConceptosLiquidaciones','Window',NULL),(660,'TiposDeLiquidacionesdefault',NULL,'abm','index','/m/Liquidacion/model/TiposDeLiquidaciones','Window',NULL),(661,'gruposDePersonasALiquidar',NULL,'gruposDePersonasALiquidar','','','Liquidacion',NULL),(662,'TiposDeLiquidacionesTablas',NULL,'abm','index','/m/Rrhh/model/TiposDeLiquidacionesTablas','Window',NULL),(665,'ConveniosTablasdefault',NULL,'abm','index','/m/Rrhh/model/ConveniosTablas','Window',NULL),(668,'administrarConvenios',NULL,'administrarConvenios','','','Rrhh',NULL),(669,'TiposDeFamiliaresdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeFamiliares','Window',NULL),(670,'TiposDeEscolaridades',NULL,'abm','index','/m/Rrhh/model/TiposDeEscolaridades','Window',NULL),(671,'Organismosdefault',NULL,'abm','index','/m/Rrhh/model/Organismos','Window',NULL),(673,'variablesConceptosLiquidaciones',NULL,'variablesConceptosLiquidaciones','','','Liquidacion',NULL),(676,'variablesGenerales',NULL,'variablesGenerales','','','Liquidacion',NULL),(677,'variablesParametros',NULL,'variablesParametros','','','Liquidacion',NULL),(678,'VariablesCategoriasdefault',NULL,'abm','index','/m/Liquidacion/model/VariablesCategorias','Window',NULL),(679,'TiposDeOrganismosdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeOrganismos','Window',NULL),(680,'ModalidadesDeContratacionesdefault',NULL,'abm','index','/m/Rrhh/model/ModalidadesDeContrataciones','Window',NULL),(681,'SituacionesDeRevistasdefault',NULL,'abm','index','/m/Rrhh/model/SituacionesDeRevistas','Window',NULL),(682,'Testliquidador',NULL,'Testliquidador','','','Liquidacion',NULL),(683,'ReporteVentasxClientes',NULL,'ReporteVentasxClientes','','','Base',''),(684,'productosFormulas',NULL,'productosFormulas','','','Base',''),(685,'ReporteVentasxLocalidades',NULL,'ReporteVentasxLocalidad','','','Base',''),(686,'ReporteComprobantesCobros',NULL,'ReporteComprobantesCobros','','','Facturacion',''),(687,'ReporteComprobantesCompras',NULL,'ReporteComprobantesCompras','','','Facturacion','<br>'),(688,'ReporteComprobantesPagos',NULL,'ReporteComprobantesPagos','','','Facturacion','<br>'),(689,'ReporteComprobantesVentas',NULL,'ReporteComprobantesVentas','','','Facturacion','<br>'),(690,'RemitosDeEntradasMapper',NULL,'Almacenes_Model_RemitosDeEntradasMapper','','','',NULL),(691,'ConceptosFacturacionServiciosPrestadosdefault',NULL,'abm','index','/m/Base/model/ConceptosFacturacionServiciosPrestados','Window',''),(692,'ArticulosGruposdefault',NULL,'abm','index','/m/Base/model/ArticulosGrupos','Window',''),(694,'gruposDeArticulos',NULL,'gruposDeArticulos','','','Base',''),(695,'TiposDeFeriadosdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeFeriados','Window',''),(696,'Feriadosdefault',NULL,'abm','index','/m/Rrhh/model/Feriados','Window',''),(697,'TiposDeArticulosdefault',NULL,'abm','index','/m/Base/model/TiposDeArticulos','Window',''),(698,'Familiaresdefault',NULL,'abm','index','/m/Rrhh/model/Familiares','Window',''),(699,'DebitosDirectosdefault',NULL,'abm','index','/m/Base/model/DebitoDirectoDeCuentaBancaria','Window',NULL),(700,'ServiciosCalificacionesProfesionalesdefault',NULL,'abm','index','/m/Rrhh/model/ServiciosCalificacionesProfesionales','Window',''),(701,'TiposDeTitulosdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeTitulos','Window',''),(702,'RemitosSinRemitoMapper',NULL,'Almacenes_Model_RemitosSinRemitoMapper','','','',NULL),(703,'CaracteristicasModelosdefault',NULL,'abm','index','/model/CaracteristicasModelos','Window',''),(704,'TiposDeHorasExtrasdefault',NULL,'abm','index','/m/Rrhh/model/TiposDeHorasExtras','Window',''),(705,'Titulosdefault',NULL,'abm','index','/m/Rrhh/model/Titulos','Window',''),(706,'TitulosNivelesAcademicosdefault',NULL,'abm','index','/m/Rrhh/model/TitulosNivelesAcademicos','Window',''),(707,'ordenesDeProducciones',NULL,'ordenesDeProducciones','getrequerimientosproductos','','Produccion',''),(708,'Liquidador',NULL,'Liquidador','','','Liquidacion',''),(709,'ArticulosConfiguracionesdefault',NULL,'abm','index','/m/Base/model/ArticulosConfiguraciones','Window',''),(710,'TarjetasDeCredito_Pripoaspropias',NULL,'abm','index','/m/Facturacion/model/TarjetasDeCredito_Propias/section/propias','Window',''),(711,'TarjetasDeCreditoMarcasdefault',NULL,'abm','index','/m/Facturacion/model/TarjetasDeCreditoMarcas','Window',''),(712,'TarjetasDeCreditoCuponesSalientesdefault',NULL,'abm','index','/m/Facturacion/model/TarjetasDeCreditoCuponesSalientes','Window',''),(713,'TarjetasDeCreditoCuponesEntrantesdefault',NULL,'abm','index','/m/Facturacion/model/TarjetasDeCreditoCuponesEntrantes','Window',''),(714,'administrarDescuentos',NULL,'administrarDescuentos','','','Liquidacion',''),(715,'DeduccionesGananciasdefault',NULL,'abm','index','/m/Liquidacion/model/DeduccionesGanancias','Window',''),(716,'DeduccionesGananciasTiposdedault',NULL,'abm','index','/m/Liquidacion/model/DeduccionesGananciasTipos','Window',''),(717,'variablesConceptosLiquidaciones',NULL,'variablesConceptosLiquidaciones','index','/Extras/1','Liquidacion',''),(718,'facturasVentasMinoristas',NULL,'facturasVentasMinoristas','','','Facturacion',''),(719,'Articulosfinales',NULL,'abm','index','/m/Base/model/ArticulosFinales/section/finales','Window',''),(720,'TarjetasDeCredito_Tercerosterceros',NULL,'abm','index','/m/Facturacion/model/TarjetasDeCredito_Terceros/section/terceros','Window',''),(721,'administrarEmpleadosLaborales',NULL,'administrarEmpleadosLaborales','','','Base',''),(722,'AfipGananciasDeduccionesDefault',NULL,'abm','index','/m/Afip/model/AfipGananciasDeducciones','Window',''),(723,'AfipGananciasDeduccionesDetallesDefault',NULL,'abm','index','/m/Afip/model/AfipGananciasDeduccionesDetalles','Window',''),(724,'AfipGananciasDeduccionesPeriodosDefault',NULL,'abm','index','/m/Afip/model/AfipGananciasDeduccionesPeriodos','Window',''),(725,'PaisesCuitdefault',NULL,'abm','index','/m/Base/model/PaisesCuit','Window',''),(726,'Idiomasdefault',NULL,'abm','index','/m/Base/model/Idiomas','Window',''),(727,'puntosDeVentas',NULL,'puntosDeVentas',NULL,NULL,'Facturacion',NULL);
/*!40000 ALTER TABLE `Modulos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Modelos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Modelos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) COLLATE utf8_unicode_ci NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=576 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Modelos` WRITE;
/*!40000 ALTER TABLE `Modelos` DISABLE KEYS */;
INSERT INTO `Modelos` VALUES (1,'Almacenes_Model_DbTable_Almacenes'),(2,'Almacenes_Model_DbTable_ArticulosStock'),(3,'Almacenes_Model_DbTable_Lotes'),(4,'Almacenes_Model_DbTable_LotesPropios'),(5,'Almacenes_Model_DbTable_Mmis'),(6,'Almacenes_Model_DbTable_MmisMovimientos'),(7,'Almacenes_Model_DbTable_MmisTipos'),(8,'Almacenes_Model_DbTable_Remitos'),(9,'Almacenes_Model_DbTable_RemitosArticulos'),(10,'Almacenes_Model_DbTable_RemitosArticulosDeEntradas'),(11,'Almacenes_Model_DbTable_RemitosArticulosDeSalidas'),(12,'Almacenes_Model_DbTable_RemitosDeEntradas'),(13,'Almacenes_Model_DbTable_RemitosDeSalidas'),(14,'Almacenes_Model_DbTable_RemitosEstados'),(15,'Almacenes_Model_DbTable_RemitosInternos'),(16,'Almacenes_Model_DbTable_RemitosSinRemito'),(17,'Almacenes_Model_DbTable_TiposDeAlmacenes'),(18,'Almacenes_Model_DbTable_TiposDeControlesDeStock'),(19,'Almacenes_Model_DbTable_TiposDePalets'),(20,'Almacenes_Model_DbTable_Ubicaciones'),(21,'Almacenes_Model_DbTable_VArticulosRemitos'),(22,'Base_Model_DbTable_AdaptadoresFiscalizaciones'),(23,'Base_Model_DbTable_Ambitos'),(24,'Base_Model_DbTable_Articulos'),(25,'Base_Model_DbTable_ArticulosGrupos'),(26,'Base_Model_DbTable_ArticulosListasDePrecios'),(27,'Base_Model_DbTable_ArticulosListasDePreciosDetalle'),(28,'Base_Model_DbTable_ArticulosSubGrupos'),(29,'Base_Model_DbTable_Bancos'),(30,'Base_Model_DbTable_BancosSucursales'),(31,'Base_Model_DbTable_Caracteristicas'),(32,'Base_Model_DbTable_CaracteristicasListas'),(33,'Base_Model_DbTable_Chequeras'),(34,'Base_Model_DbTable_ChequerasTipos'),(35,'Base_Model_DbTable_Cheques'),(36,'Base_Model_DbTable_ChequesBloqueos'),(37,'Base_Model_DbTable_ChequesBloqueosTipos'),(38,'Base_Model_DbTable_ChequesDeTerceros'),(39,'Base_Model_DbTable_ChequesEstados'),(40,'Base_Model_DbTable_ChequesPropios'),(41,'Base_Model_DbTable_Clientes'),(42,'Base_Model_DbTable_ClientesConceptosImpositivos'),(43,'Base_Model_DbTable_ClientesModalidadesDePagos'),(44,'Base_Model_DbTable_CodigosActividadesAfip'),(45,'Base_Model_DbTable_ConceptosDePercepciones'),(46,'Base_Model_DbTable_ConceptosDeRetenciones'),(47,'Base_Model_DbTable_ConceptosFacturacionServicios'),(48,'Base_Model_DbTable_ConceptosFacturacionVarios'),(49,'Base_Model_DbTable_ConceptosImpositivos'),(50,'Base_Model_DbTable_CuentasBancarias'),(51,'Base_Model_DbTable_CuentasBancariasPropias'),(52,'Base_Model_DbTable_Depositos'),(53,'Base_Model_DbTable_DepositosEntrantes'),(54,'Base_Model_DbTable_DepositosSalientes'),(55,'Base_Model_DbTable_Direcciones'),(56,'Base_Model_DbTable_Emails'),(57,'Base_Model_DbTable_Empleados'),(58,'Base_Model_DbTable_EntesRecaudadores'),(59,'Base_Model_DbTable_EstadosCiviles'),(60,'Base_Model_DbTable_FletesFormasPagos'),(61,'Base_Model_DbTable_FormasDePagos'),(62,'Base_Model_DbTable_GeneradorDeCheques'),(63,'Base_Model_DbTable_Localidades'),(64,'Base_Model_DbTable_Marcas'),(65,'Base_Model_DbTable_MarcasDeTerceros'),(66,'Base_Model_DbTable_MarcasProduccion'),(67,'Base_Model_DbTable_ModalidadesDePagos'),(68,'Base_Model_DbTable_ModalidadesIVA'),(69,'Base_Model_DbTable_Paises'),(70,'Base_Model_DbTable_Personas'),(71,'Base_Model_DbTable_PersonasActividades'),(72,'Base_Model_DbTable_PersonasConceptosImpositivos'),(73,'Base_Model_DbTable_PersonasListasDePrecios'),(74,'Base_Model_DbTable_PersonasListasDePreciosInformados'),(75,'Base_Model_DbTable_Productos'),(76,'Base_Model_DbTable_ProductosCategorias'),(77,'Base_Model_DbTable_ProductosCategoriasCaracteristicas'),(78,'Base_Model_DbTable_ProductosCategoriasCaracteristicasValores'),(79,'Base_Model_DbTable_ProductosSubCategorias'),(80,'Base_Model_DbTable_Proveedores'),(81,'Base_Model_DbTable_ProveedoresConceptosImpositivos'),(82,'Base_Model_DbTable_ProveedoresMarcas'),(83,'Base_Model_DbTable_ProveedoresModalidadesDePagos'),(84,'Base_Model_DbTable_Provincias'),(85,'Base_Model_DbTable_PuntosDeVentas'),(86,'Base_Model_DbTable_Sexos'),(87,'Base_Model_DbTable_Telefonos'),(88,'Base_Model_DbTable_TelefonosSucursales'),(89,'Base_Model_DbTable_TiposDeArticulos'),(90,'Base_Model_DbTable_TiposDeCampos'),(91,'Base_Model_DbTable_TiposDeConceptos'),(92,'Base_Model_DbTable_TiposDeCuentas'),(93,'Base_Model_DbTable_TiposDeDirecciones'),(94,'Base_Model_DbTable_TiposDeDivisas'),(95,'Base_Model_DbTable_TiposDeDocumentos'),(96,'Base_Model_DbTable_TiposDeInscripcionesGanancias'),(97,'Base_Model_DbTable_TiposDeInscripcionesIB'),(98,'Base_Model_DbTable_TiposDeMontosMinimos'),(99,'Base_Model_DbTable_TiposDeMovimientosBancarios'),(100,'Base_Model_DbTable_TiposDePrioridades'),(101,'Base_Model_DbTable_TiposDeTelefonos'),(102,'Base_Model_DbTable_TiposDeTransaccionesBancarias'),(103,'Base_Model_DbTable_TiposDeUnidades'),(104,'Base_Model_DbTable_TransaccionesBancarias'),(105,'Base_Model_DbTable_TransferenciasEntrantes'),(106,'Base_Model_DbTable_TransferenciasSalientes'),(107,'Base_Model_DbTable_Transportistas'),(108,'Base_Model_DbTable_UnidadesDeMedidas'),(109,'Base_Model_DbTable_UnidadesDeMedidas_Exception'),(110,'Base_Model_DbTable_VBancosCuentas'),(111,'Base_Model_DbTable_VOrdenesDeComprasArticulos'),(112,'Base_Model_DbTable_Vendedores'),(113,'Base_Model_DbTable_ZonasDeVentas'),(114,'Base_Model_DbTable_ZonasPorPersonas'),(115,'Base_Model_DbTable_ZonasPorVendedores'),(116,'Contable_Model_DbTable_Cajas'),(117,'Contable_Model_DbTable_CajasMovimientos'),(118,'Contable_Model_DbTable_CuentasCorrientes'),(119,'Contable_Model_DbTable_LibrosDiarios'),(120,'Contable_Model_DbTable_LibrosIVA'),(121,'Contable_Model_DbTable_LibrosIVADetalles'),(122,'Contable_Model_DbTable_PlanesDeCuentas'),(123,'Contable_Model_DbTable_PlanesDeCuentasGrupos'),(124,'Contable_Model_DbTable_TiposDeCheques'),(125,'Contable_Model_DbTable_TiposDeEmisoresDeCheques'),(126,'Contable_Model_DbTable_VSaldoCuentasCorrientes'),(127,'Facturacion_Model_DbTable_Comprobantes'),(128,'Facturacion_Model_DbTable_ComprobantesDetalles'),(129,'Facturacion_Model_DbTable_ComprobantesEstados'),(130,'Facturacion_Model_DbTable_ComprobantesImpositivos'),(131,'Facturacion_Model_DbTable_ComprobantesPagos'),(132,'Facturacion_Model_DbTable_ComprobantesPagosDetalles'),(133,'Facturacion_Model_DbTable_ComprobantesRelacionados'),(134,'Facturacion_Model_DbTable_ComprobantesRelacionadosDetalles'),(135,'Facturacion_Model_DbTable_FacturacionElectronicaAfip'),(136,'Facturacion_Model_DbTable_Facturas'),(137,'Facturacion_Model_DbTable_FacturasCompras'),(138,'Facturacion_Model_DbTable_FacturasComprasArticulos'),(139,'Facturacion_Model_DbTable_FacturasComprasConceptos'),(140,'Facturacion_Model_DbTable_FacturasComprasRemitos'),(141,'Facturacion_Model_DbTable_FacturasComprasRemitosDetalles'),(142,'Facturacion_Model_DbTable_FacturasVentas'),(143,'Facturacion_Model_DbTable_FacturasVentasArticulos'),(144,'Facturacion_Model_DbTable_FacturasVentasConceptos'),(145,'Facturacion_Model_DbTable_FacturasVentasRemitos'),(146,'Facturacion_Model_DbTable_FacturasVentasRemitosDetalles'),(147,'Facturacion_Model_DbTable_OrdenesDeCompras'),(148,'Facturacion_Model_DbTable_OrdenesDeComprasArticulos'),(149,'Facturacion_Model_DbTable_OrdenesDeComprasRemitos'),(150,'Facturacion_Model_DbTable_OrdenesDeComprasRemitosDetalles'),(151,'Facturacion_Model_DbTable_OrdenesDePagos'),(152,'Facturacion_Model_DbTable_OrdenesDePagosConceptos'),(153,'Facturacion_Model_DbTable_OrdenesDePagosDetalles'),(154,'Facturacion_Model_DbTable_OrdenesDePagosFacturas'),(155,'Facturacion_Model_DbTable_OrdenesDePedidos'),(156,'Facturacion_Model_DbTable_OrdenesDePedidosArticulos'),(157,'Facturacion_Model_DbTable_OrdenesDePedidosRemitos'),(158,'Facturacion_Model_DbTable_PedidosDeCotizaciones'),(159,'Facturacion_Model_DbTable_PedidosDeCotizacionesArticulos'),(160,'Facturacion_Model_DbTable_Recibos'),(161,'Facturacion_Model_DbTable_RecibosConceptos'),(162,'Facturacion_Model_DbTable_RecibosDetalles'),(163,'Facturacion_Model_DbTable_RecibosFacturas'),(164,'Facturacion_Model_DbTable_TiposDeComprobantes'),(165,'Facturacion_Model_DbTable_TiposDeCondicionesDePago'),(166,'Facturacion_Model_DbTable_TiposDeExportaciones'),(167,'Facturacion_Model_DbTable_TiposDeGruposDeComprobantes'),(168,'Facturacion_Model_DbTable_VFCACantidades'),(169,'Facturacion_Model_DbTable_VRemitosArticulos'),(170,'Facturacion_Model_DbTable_vComprobanteTotal'),(171,'Facturacion_Model_DbTable_vRelFacturasArticulosOrdenesArticulos'),(172,'Laboratorio_Model_DbTable_Analisis'),(173,'Laboratorio_Model_DbTable_AnalisisModelos'),(174,'Laboratorio_Model_DbTable_AnalisisMuestras'),(175,'Laboratorio_Model_DbTable_AnalisisProtocolo'),(176,'Laboratorio_Model_DbTable_AnalisisTiposModelos'),(177,'Laboratorio_Model_DbTable_AnalisisValoresListas'),(178,'Laboratorio_Model_DbTable_FormulasProductos'),(179,'Laboratorio_Model_DbTable_FormulasProductosOP'),(180,'Laboratorio_Model_DbTable_TiposDeAnalisis'),(181,'Model_DbTable_MenuesPrincipales'),(182,'Model_DbTable_Modelos'),(183,'Model_DbTable_Modulos'),(184,'Model_DbTable_ModulosModelos'),(185,'Model_DbTable_OrdenesDePedidos'),(186,'Model_DbTable_OrdenesDePedidosArticulos'),(187,'Model_DbTable_OrdenesDePedidosEstados'),(188,'Model_DbTable_OrdenesDePedidosPrioridad'),(189,'Model_DbTable_Roles'),(190,'Model_DbTable_RolesModelos'),(191,'Model_DbTable_RolesModulos'),(192,'Model_DbTable_TiposDeGruposDeComprobantes'),(193,'Model_DbTable_TiposDePrioridades'),(194,'Model_DbTable_Usuarios'),(195,'Model_DbTable_UsuariosEscritorio'),(196,'Model_DbTable_UsuariosLogs'),(197,'Model_DbTable_VBienesCaracteristicas'),(198,'Model_DbTable_VFCACantidades'),(199,'Model_DbTable_VInsumosYRepuestosExistentes'),(200,'Model_DbTable_VOrdenesDePedidosArticulos'),(201,'Model_DbTable_VPreciosDeArticulosPorProveedores'),(202,'Model_DbTable_VProveedoresListasDePreciosArticulos'),(203,'Model_DbTable_VRemitosArticulosVentas'),(204,'Produccion_Model_DbTable_LineasDeProducciones'),(205,'Produccion_Model_DbTable_OrdenesDeProducciones'),(206,'Produccion_Model_DbTable_OrdenesDeProduccionesDetalles'),(207,'Produccion_Model_DbTable_OrdenesDeProduccionesEstados'),(433,'Base_Model_DbTable_AreasDeTrabajos'),(434,'Base_Model_DbTable_AreasDeTrabajosPersonas'),(435,'Contable_Model_DbTable_LibrosDiariosDetalle'),(436,'Model_DbTable_GruposDeUsuarios'),(437,'Model_DbTable_GruposDeUsuariosRoles'),(438,'Model_DbTable_MarcasPropias'),(439,'Model_DbTable_TiposDeLineasDeProducciones'),(440,'Produccion_Model_DbTable_Actividades'),(441,'Produccion_Model_DbTable_ActividadesConfiguraciones'),(442,'Produccion_Model_DbTable_LineasDeProduccionesActividades'),(443,'Produccion_Model_DbTable_LineasDeProduccionesPersonas'),(444,'Produccion_Model_DbTable_OrdenesDeProduccionesMmis'),(445,'Produccion_Model_DbTable_Producciones'),(446,'Produccion_Model_DbTable_ProduccionesMmis'),(447,'Produccion_Model_DbTable_ProduccionesMmisMovimientos'),(448,'Produccion_Model_DbTable_ProduccionesMotivosDeFinalizaciones'),(449,'Produccion_Model_DbTable_TiposDeLineasDeProducciones'),(450,'Almacenes_Model_DbTable_AlmacenesPerspectivas'),(451,'Base_Model_DbTable_ArticulosGenericos'),(452,'Almacenes_Model_DbTable_ArticulosStockAlmacen'),(453,'Base_Model_DbTable_DepositosPropios'),(454,'Base_Model_DbTable_ArticulosVersiones'),(455,'Almacenes_Model_DbTable_TiposDeIncrementos'),(456,'Almacenes_Model_DbTable_LotesDeTerceros'),(457,'Almacenes_Model_DbTable_MmisAcciones'),(458,'Base_Model_DbTable_ArticulosVersionesDetalles'),(459,'Base_Model_DbTable_ChequesConciliacionBancaria'),(460,'Base_Model_DbTable_ChequesDarDestinos'),(461,'Base_Model_DbTable_ConceptosFacturacionBancarios'),(462,'Base_Model_DbTable_CuentasBancariasMovimientos'),(463,'Base_Model_DbTable_DepositosPropiosEntrantes'),(464,'Base_Model_DbTable_ExtraccionesBancarias'),(465,'Base_Model_DbTable_Meses'),(466,'Base_Model_DbTable_ProductosGruposAnalisis'),(467,'Base_Model_DbTable_PuntosDeRemitos'),(468,'Base_Model_DbTable_TransferenciasPropiasSalientes'),(469,'Base_Model_DbTable_vArticulosArbol'),(470,'Contable_Model_DbTable_CajasMovimientosDeEntradas'),(471,'Contable_Model_DbTable_CajasMovimientosDeSalidas'),(472,'Contable_Model_DbTable_TiposDeMovimientosCajas'),(473,'Facturacion_Model_DbTable_ComprobantesBancarios'),(474,'Facturacion_Model_DbTable_ComprobantesBancariosArticulos'),(475,'Facturacion_Model_DbTable_ComprobantesBancariosCheques'),(476,'Facturacion_Model_DbTable_ComprobantesBancariosConceptos'),(477,'Facturacion_Model_DbTable_ComprobantesCheques'),(478,'Facturacion_Model_DbTable_ComprobantesPagosFacturas'),(479,'Facturacion_Model_DbTable_OrdenesDeComprasArticulosVarios'),(480,'Facturacion_Model_DbTable_OrdenesDeComprasVarios'),(481,'Facturacion_Model_DbTable_vComprobanteTotalPagado'),(482,'Base_Model_DbTable_Empresas'),(483,'Liquidacion_Model_DbTable_Conceptos'),(484,'Liquidacion_Model_DbTable_ConceptosGenericos'),(485,'Liquidacion_Model_DbTable_GruposDePersonas'),(486,'Liquidacion_Model_DbTable_GruposDePersonasDetalles'),(487,'Liquidacion_Model_DbTable_TiposDeConceptosLiquidaciones'),(488,'Liquidacion_Model_DbTable_TiposDeLiquidaciones'),(489,'Rrhh_Model_DbTable_CategoriasGrupos'),(490,'Rrhh_Model_DbTable_Convenios'),(491,'Rrhh_Model_DbTable_ConveniosCategorias'),(492,'Rrhh_Model_DbTable_ConveniosCategoriasDetalles'),(493,'Rrhh_Model_DbTable_ConveniosLicencias'),(494,'Rrhh_Model_DbTable_ConveniosTablas'),(495,'Rrhh_Model_DbTable_ConveniosTablasDetalles'),(496,'Rrhh_Model_DbTable_Familiares'),(497,'Rrhh_Model_DbTable_Organismos'),(498,'Rrhh_Model_DbTable_PersonasAfiliaciones'),(499,'Rrhh_Model_DbTable_PersonasAfiliacionesAdherentes'),(500,'Rrhh_Model_DbTable_PersonasHorasExtras'),(501,'Rrhh_Model_DbTable_Servicios'),(502,'Rrhh_Model_DbTable_ServiciosLicencias'),(503,'Rrhh_Model_DbTable_TiposDeBajas'),(504,'Rrhh_Model_DbTable_TiposDeConveniosTablas'),(505,'Rrhh_Model_DbTable_TiposDeEscolaridades'),(506,'Rrhh_Model_DbTable_TiposDeFamiliares'),(507,'Rrhh_Model_DbTable_TiposDeHorasExtras'),(508,'Rrhh_Model_DbTable_TiposDeJornadas'),(509,'Rrhh_Model_DbTable_TiposDeOrganismos'),(510,'Base_Model_DbTable_CaracteristicasValores'),(511,'Liquidacion_Model_DbTable_ConceptosTiposDeLiquidaciones'),(512,'Liquidacion_Model_DbTable_Liquidaciones'),(513,'Liquidacion_Model_DbTable_LiquidacionesRecibos'),(514,'Liquidacion_Model_DbTable_LiquidacionesRecibosDetalles'),(515,'Liquidacion_Model_DbTable_LiquidacionesVariablesCalculadas'),(516,'Liquidacion_Model_DbTable_LiquidacionesVariablesDesactivadas'),(517,'Liquidacion_Model_DbTable_PeriodosLiquidaciones'),(518,'Liquidacion_Model_DbTable_TiposDePeriodosDeLiquidaciones'),(519,'Liquidacion_Model_DbTable_TiposDeVariables'),(520,'Liquidacion_Model_DbTable_VariablesAbstractas'),(521,'Liquidacion_Model_DbTable_VariablesCategorias'),(522,'Liquidacion_Model_DbTable_VariablesDetallesAbstractas'),(523,'Rrhh_Model_DbTable_LiquidacionesTablas'),(524,'Rrhh_Model_DbTable_LiquidacionesTablasDetalles'),(525,'Rrhh_Model_DbTable_LiquidacionesTablasEscalares'),(526,'Rrhh_Model_DbTable_LiquidacionesTablasEscalaresDetalles'),(527,'Rrhh_Model_DbTable_LiquidacionesTablasRangos'),(528,'Rrhh_Model_DbTable_LiquidacionesTablasRangosDetalles'),(529,'Rrhh_Model_DbTable_ModalidadesDeContrataciones'),(530,'Rrhh_Model_DbTable_PersonasCaracteristicasValores'),(531,'Rrhh_Model_DbTable_ServiciosSituacionesDeRevistas'),(532,'Rrhh_Model_DbTable_SituacionesDeRevistas'),(533,'Rrhh_Model_DbTable_TiposDeLiquidacionesTablas'),(534,'Rrhh_Model_DbTable_TiposDeSueldos'),(535,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetalles'),(536,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesCategorias'),(537,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesConvenios'),(538,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesEmpresas'),(539,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGenericos'),(540,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesGrupos'),(541,'Liquidacion_Model_DbTable_VariablesDetalles_ConceptosLiquidacionesDetallesPuestos'),(542,'Liquidacion_Model_DbTable_VariablesDetalles_ParametrosDetalles'),(543,'Liquidacion_Model_DbTable_VariablesDetalles_PrimitivasDetalles'),(544,'Liquidacion_Model_DbTable_VariablesDetalles_SelectoresDetalles'),(545,'Liquidacion_Model_DbTable_VariablesDetalles_VariablesDetalles'),(546,'Liquidacion_Model_DbTable_Variables_ConceptosLiquidaciones'),(547,'Liquidacion_Model_DbTable_Variables_Parametros'),(548,'Liquidacion_Model_DbTable_Variables_Primitivas'),(549,'Liquidacion_Model_DbTable_Variables_Selectores'),(550,'Liquidacion_Model_DbTable_Variables_Variables'),(552,'Base_Model_DbTable_ArticulosVersionesDetallesFormulas'),(553,'Base_Model_DbTable_ArticulosVersionesRaices'),(554,'Base_Model_DbTable_ConceptosFacturacionServiciosPrestados'),(555,'Base_Model_DbTable_DebitoDirectoDeCuentaBancaria'),(556,'Base_Model_DbTable_PersonasRegistrosDePrecios'),(557,'Base_Model_DbTable_PersonasRegistrosDePreciosInformados'),(558,'Base_Model_DbTable_TiposDeRegistrosDePrecios'),(559,'Base_Model_DbTable_TiposDeRelacionesArticulos'),(560,'Liquidacion_Model_DbTable_LiquidacionesPeriodos'),(561,'Liquidacion_Model_DbTable_NovedadesDeLiquidaciones'),(562,'Liquidacion_Model_DbTable_TiposDeLiquidacionesPeriodos'),(563,'Rrhh_Model_DbTable_FamiliaresPersonas'),(564,'Rrhh_Model_DbTable_Feriados'),(565,'Rrhh_Model_DbTable_LiquidacionesTablasCategoriasGrupos'),(566,'Rrhh_Model_DbTable_LiquidacionesTablasGrupos'),(567,'Rrhh_Model_DbTable_LiquidacionesTablasGruposDetalles'),(568,'Rrhh_Model_DbTable_PersonasTitulos'),(569,'Rrhh_Model_DbTable_ServiciosCalificacionesProfesionales'),(570,'Rrhh_Model_DbTable_ServiciosFeriados'),(571,'Rrhh_Model_DbTable_ServiciosHorasExtras'),(572,'Rrhh_Model_DbTable_TiposDeFeriados'),(573,'Rrhh_Model_DbTable_TiposDeTitulos'),(574,'Rrhh_Model_DbTable_Titulos'),(575,'Rrhh_Model_DbTable_TitulosNivelesAcademicos');
/*!40000 ALTER TABLE `Modelos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `ModulosModelos` WRITE;
/*!40000 ALTER TABLE `ModulosModelos` DISABLE KEYS */;
INSERT INTO `ModulosModelos` VALUES (10,170,142,1,1,1,1),(11,170,143,1,1,1,1),(12,170,144,1,1,1,1),(13,170,145,1,1,1,1),(14,170,41,1,0,0,0),(21,170,70,1,0,0,0),(27,170,94,1,0,0,0),(40,170,24,1,0,0,0),(41,170,49,1,0,0,0),(42,170,85,1,0,0,0),(44,170,170,1,0,0,0),(48,170,164,1,0,0,0),(49,575,95,1,0,0,0),(50,575,68,1,0,0,0),(51,575,96,1,0,0,0),(52,575,97,1,0,0,0),(53,575,107,1,0,0,0),(54,575,55,1,1,1,1),(55,575,93,1,0,0,0),(56,575,63,1,0,0,0),(57,575,101,1,0,0,0),(58,575,87,1,1,1,1),(59,575,42,1,1,1,1),(60,575,49,1,0,0,0),(61,575,72,1,1,1,1),(65,575,43,1,1,1,1),(66,575,29,1,0,0,0),(67,575,50,1,1,1,1),(68,575,30,1,0,0,0),(69,575,92,1,0,0,0),(70,575,118,1,0,0,0),(71,575,126,1,0,0,0),(72,575,114,1,1,1,1),(73,575,113,1,0,0,0),(74,575,71,1,1,1,1),(75,575,44,1,0,0,0),(76,575,41,1,1,1,1),(77,575,56,1,1,1,1),(78,575,67,1,0,0,0),(79,172,24,1,0,0,0),(80,172,26,1,1,1,1),(81,172,27,1,1,1,1),(82,172,94,1,0,0,0),(83,185,41,1,0,0,0),(84,185,55,1,0,0,0),(85,185,107,1,0,0,0),(86,185,26,1,0,0,0),(87,185,94,1,0,0,0),(88,185,155,1,1,1,1),(89,185,156,1,1,1,1),(90,185,24,1,0,0,0),(91,185,52,1,0,0,0),(92,179,164,1,0,0,0),(93,179,41,1,0,0,0),(94,179,160,1,1,1,1),(95,179,136,1,0,0,0),(96,179,163,1,1,1,1),(97,179,162,1,1,1,1),(98,179,161,1,1,1,1),(99,179,49,1,0,0,0),(100,179,35,1,0,0,0),(101,179,104,1,0,0,0),(102,179,116,1,0,0,0),(103,179,38,1,1,1,1),(104,179,39,1,0,0,0),(105,179,124,1,0,0,0),(106,179,30,1,0,0,0),(107,179,105,1,1,1,1),(108,179,110,1,0,0,0),(109,179,53,1,1,1,1),(110,186,41,1,0,0,0),(111,186,107,1,0,0,0),(112,186,164,1,0,0,0),(113,186,52,1,0,0,0),(114,186,11,1,1,1,1),(115,186,13,1,1,1,1),(116,186,24,1,0,0,0),(117,186,157,1,1,1,1),(118,186,155,1,0,0,0),(119,579,112,1,1,1,1),(120,579,115,1,1,1,1),(121,579,113,1,1,1,1),(122,579,95,1,0,0,0),(123,579,93,1,0,0,0),(124,579,86,1,0,0,0),(125,579,59,1,0,0,0),(126,579,63,1,0,0,0),(127,579,55,1,1,1,1),(128,579,87,1,1,1,1),(129,579,101,1,0,0,0),(130,579,50,1,1,1,1),(131,579,92,1,0,0,0),(132,579,56,1,1,1,1),(133,170,26,1,0,0,0),(134,170,165,1,0,0,0),(135,170,13,1,0,0,0),(136,170,122,1,0,0,0),(137,150,70,1,0,0,0),(138,150,120,1,0,0,0),(139,150,164,1,0,0,0),(140,150,165,1,0,0,0),(141,150,94,1,0,0,0),(142,150,12,1,0,0,0),(143,150,1,1,0,0,0),(144,150,138,1,0,0,0),(145,150,137,1,1,1,1),(146,150,140,1,0,0,0),(147,150,139,1,0,0,0),(148,150,141,1,0,0,0),(149,150,49,1,0,0,0),(150,150,122,1,0,0,0),(151,150,171,1,0,0,0),(152,150,169,1,0,0,0),(153,170,120,1,0,0,0),(154,107,80,1,1,1,1),(155,107,81,1,1,1,1),(156,107,82,1,1,1,1),(157,107,83,1,1,1,1),(158,107,201,1,1,1,1),(159,107,202,1,1,1,1),(162,137,159,1,0,0,0),(163,137,158,1,0,0,0),(164,95,147,1,1,1,1),(165,95,148,1,1,1,1),(166,95,150,1,1,1,1),(167,95,149,1,1,1,1),(168,122,127,1,0,0,0),(170,83,1,1,0,0,0),(172,83,2,1,0,0,0),(173,83,3,1,0,0,0),(174,83,4,1,0,0,0),(175,83,5,1,0,0,0),(176,83,6,1,0,0,0),(177,83,7,1,0,0,0),(178,83,8,1,0,0,0),(179,83,9,1,0,0,0),(180,83,10,1,0,0,0),(181,83,11,1,0,0,0),(182,83,13,1,0,0,0),(183,83,12,1,0,0,0),(184,83,14,1,0,0,0),(185,83,15,1,0,0,0),(186,83,16,1,0,0,0),(187,83,17,1,0,0,0),(188,83,18,1,0,0,0),(189,83,19,1,0,0,0),(190,83,20,1,0,0,0),(191,83,21,1,0,0,0),(192,83,24,1,0,0,0),(193,83,445,1,0,0,0),(196,83,52,1,0,0,0),(197,83,205,1,0,0,0),(198,83,206,1,0,0,0),(199,83,207,1,0,0,0),(200,83,75,1,0,0,0),(201,177,16,1,1,1,1),(209,83,450,1,0,0,0),(211,83,451,1,0,0,0),(212,626,452,1,0,0,0),(213,83,453,1,0,0,0),(214,83,454,1,0,0,0),(215,177,80,1,0,0,0),(216,177,453,1,0,0,0),(217,177,147,1,0,0,0),(218,177,148,1,0,0,0),(219,177,10,1,1,1,1),(220,177,24,1,0,0,0),(221,576,12,1,1,1,1),(222,576,80,1,0,0,0),(223,576,164,1,0,0,0),(224,576,107,1,0,0,0),(225,576,60,1,0,0,0),(228,576,52,1,0,0,0),(229,576,147,1,0,0,0),(230,576,148,1,0,0,0),(231,576,10,1,1,1,1),(233,576,451,1,0,0,0),(235,647,13,1,0,0,0),(236,647,5,1,0,0,0),(237,597,57,1,0,0,0),(238,597,440,1,0,0,0),(239,597,443,1,0,0,0),(240,597,444,1,0,0,0),(241,597,447,1,0,0,0),(242,597,446,1,0,0,0),(243,597,108,1,0,0,0),(244,597,448,1,0,0,0),(245,186,467,1,0,0,0),(246,618,68,1,0,0,0),(247,618,63,1,0,0,0),(248,618,84,1,0,0,0),(249,642,70,1,0,0,0),(250,624,120,1,0,0,0),(251,624,70,1,0,0,0),(252,641,70,1,0,0,0),(253,641,24,1,0,0,0),(254,602,205,1,0,0,0),(255,602,445,1,0,0,0),(256,602,444,1,0,0,0),(257,602,446,1,0,0,0),(258,602,447,1,0,0,0),(259,597,19,1,0,0,0),(260,80,434,1,1,1,1),(261,80,500,1,1,1,1),(262,80,507,1,0,0,0),(263,80,95,1,0,0,0),(264,80,86,1,0,0,0),(265,80,59,1,0,0,0),(266,80,63,1,0,0,0),(267,80,490,1,0,0,0),(268,80,491,1,0,0,0),(269,80,493,1,0,0,0),(270,80,482,1,0,0,0),(271,80,503,1,0,0,0),(272,80,508,1,0,0,0),(273,80,505,1,0,0,0),(274,80,497,1,0,0,0),(275,80,122,1,0,0,0),(276,668,490,1,1,1,1),(277,668,494,1,1,1,1),(278,668,495,1,1,1,1),(279,668,491,1,1,1,1),(280,668,493,1,1,1,1),(281,668,492,1,1,1,1),(282,668,489,1,0,0,0),(283,668,504,1,0,0,0),(284,668,108,1,0,0,0),(285,80,465,1,0,0,0),(286,80,531,1,1,1,1),(287,80,532,1,0,0,0),(288,668,525,1,1,1,1),(289,668,526,1,1,1,1),(290,668,527,1,1,1,1),(291,668,528,1,1,1,1),(292,673,546,1,1,1,1),(293,673,487,1,0,0,0),(294,673,539,1,1,1,1),(295,673,537,1,1,1,1),(296,673,538,1,1,1,1),(297,673,536,1,1,1,1),(298,673,540,1,1,1,1),(299,673,541,1,1,1,1),(300,673,490,1,0,0,0),(301,673,491,1,0,0,0),(302,673,482,1,0,0,0),(303,673,485,1,0,0,0),(304,673,501,1,0,0,0),(305,677,547,1,1,1,1),(306,677,542,1,1,1,1),(307,676,550,1,1,1,1),(308,676,521,1,0,0,0),(309,676,545,1,1,1,1),(310,80,57,1,1,1,1),(311,80,87,1,1,1,1),(312,80,55,1,1,1,1),(313,80,56,1,1,1,1),(314,80,501,1,1,1,1),(315,80,496,1,1,1,1),(316,80,506,1,0,0,0),(317,80,498,1,1,1,1),(318,80,499,1,1,1,1),(319,80,93,1,0,0,0),(320,80,101,1,0,0,0),(321,80,50,1,1,1,1),(322,80,92,1,0,0,0),(323,80,433,1,0,0,0),(324,683,41,1,0,0,0),(325,685,63,1,0,0,0),(326,685,84,1,0,0,0),(327,651,451,1,0,0,0),(328,651,453,1,0,0,0),(329,641,64,1,0,0,0),(330,641,28,1,0,0,0);
/*!40000 ALTER TABLE `ModulosModelos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AdaptadoresFiscalizaciones` WRITE;
/*!40000 ALTER TABLE `AdaptadoresFiscalizaciones` DISABLE KEYS */;
INSERT INTO `AdaptadoresFiscalizaciones` VALUES (1,'Facturacion_Model_Fiscalizar_Preimpreso','Pre Impreso'),(2,'Facturacion_Model_Fiscalizar_FactElectronica','F. Electronica'),(3,'Facturacion_Model_Fiscalizar_Null','Ninguno'),(4,'Facturacion_Model_Fiscalizar_ImpresoraFiscal','Imp. Fiscal'),(5,'Facturacion_Model_Fiscalizar_FactElectronicaExp','F. Elect. Exp.');
/*!40000 ALTER TABLE `AdaptadoresFiscalizaciones` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `Ambitos` WRITE;
/*!40000 ALTER TABLE `Ambitos` DISABLE KEYS */;
INSERT INTO `Ambitos` VALUES (1,'Nacional',1),(2,'Provincial',2),(3,'Municipal',3),(4,'Internos',4),(5,'Otros',99);
/*!40000 ALTER TABLE `Ambitos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ChequesEstados`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ChequesEstados` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=12 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Ingresado, Vendido, Cobrado, En cartera, Emitido para pago; ';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ChequesEstados` WRITE;
/*!40000 ALTER TABLE `ChequesEstados` DISABLE KEYS */;
INSERT INTO `ChequesEstados` VALUES (1,'Sin usar - Vacios'),(2,'En Cartera'),(3,'Utilizado para pago'),(4,'Vendido'),(5,'Anulado'),(6,'Disponible'),(7,'Historico'),(8,'Ingresado'),(9,'Cobrado'),(10,'Depositado'),(11,'Retirado Por Socio');
/*!40000 ALTER TABLE `ChequesEstados` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Meses`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Meses` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=13 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Meses` WRITE;
/*!40000 ALTER TABLE `Meses` DISABLE KEYS */;
INSERT INTO `Meses` VALUES (1,'Enero'),(2,'Febrero'),(3,'Marzo'),(4,'Abril'),(5,'Mayo'),(6,'Junio'),(7,'Julio'),(8,'Agosto'),(9,'Setiembre'),(10,'Octubre'),(11,'Noviembre'),(12,'Diciembre');
/*!40000 ALTER TABLE `Meses` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeComprobantes` WRITE;
/*!40000 ALTER TABLE `TiposDeComprobantes` DISABLE KEYS */;
INSERT INTO `TiposDeComprobantes` VALUES (1,'Saldo Inicial',1,1,2,NULL,NULL,'SI',NULL,NULL),(2,'Arqueo de Cuenta',1,1,2,NULL,NULL,'AC',NULL,NULL),(3,'Orden De Compra',0,1,5,NULL,NULL,'OC',NULL,NULL),(4,'Orden De Pedido',0,1,17,NULL,NULL,'PE',NULL,NULL),(5,'Recibos A',0,1,11,NULL,4,'RA',NULL,1),(6,'Recibos B',0,1,11,NULL,9,'RB',NULL,2),(7,'Orden de Pago',0,1,9,NULL,NULL,'OP',NULL,NULL),(8,'Recibos C',0,1,11,NULL,15,'RC',NULL,3),(9,'Recibos M',0,1,11,NULL,36,'RM',NULL,5),(10,'Comprobante de IVA Credito',1,1,3,NULL,66,'CI',NULL,NULL),(11,'Comprobante de IVA Debito',1,1,3,NULL,66,'CI',NULL,NULL),(12,'Comprobante de Impositivo Credito',0,1,3,NULL,66,'CI',NULL,NULL),(13,'Comprobante de Impositivo Debito',0,-1,3,NULL,66,'CI',NULL,NULL),(14,'Remito R',0,1,4,NULL,NULL,'RR',NULL,NULL),(15,'Remito Interno',0,1,10,NULL,NULL,'RI',NULL,NULL),(16,'Remito R',0,1,10,NULL,NULL,'RR',NULL,NULL),(17,'Mercaderia sin Remito',0,1,4,NULL,NULL,'SR',NULL,NULL),(18,'Pedido De Cotizacion',0,1,18,NULL,NULL,'CO',NULL,NULL),(19,'Factura A',0,1,1,1,1,'FA',33,1),(20,'Factura B',0,1,1,NULL,6,'FB',34,2),(21,'Factura C',0,1,1,NULL,11,'FC',35,3),(22,'Factura E',0,1,1,NULL,17,'FE',59,4),(23,'Factura M',0,1,1,1,33,'FM',36,5),(24,'Factura A',0,-1,6,1,1,'FA',29,1),(25,'Factura B',0,-1,6,NULL,6,'FB',30,2),(26,'Factura C',0,-1,6,NULL,11,'FC',31,3),(27,'Factura E',0,-1,6,NULL,17,'FE',59,4),(28,'Factura M',0,-1,6,1,33,'FM',32,5),(29,'Nota de Credito A',0,1,7,1,3,'NCA',NULL,1),(30,'Nota de Credito B',0,1,7,NULL,8,'NCB',NULL,2),(31,'Nota de Credito C',0,1,7,NULL,13,'NCC',NULL,3),(32,'Nota de Credito M',0,1,7,1,35,'NCM',NULL,5),(33,'Nota de Credito A',0,-1,8,1,3,'NCA',NULL,1),(34,'Nota de Credito B',0,-1,8,NULL,8,'NCB',NULL,2),(35,'Nota de Credito C',0,-1,8,NULL,13,'NCC',NULL,3),(36,'Nota de Credito M',0,-1,8,1,35,'NCM',NULL,5),(37,'Nota de Debito A',0,-1,12,1,2,'NDA',NULL,1),(38,'Nota de Debito B',0,-1,12,NULL,7,'NDB',NULL,2),(39,'Nota de Debito C',0,-1,12,NULL,12,'NDC',NULL,3),(40,'Nota de Debito M',0,-1,12,1,34,'NDM',NULL,5),(41,'Nota de Debito A',0,1,13,1,2,'NDA',NULL,1),(42,'Nota de Debito B',0,1,13,NULL,7,'NDB',NULL,2),(43,'Nota de Debito C',0,1,13,NULL,12,'NDC',NULL,3),(44,'Nota de Debito M',0,1,13,1,34,'NDM',NULL,5),(45,'Remito M',1,1,10,NULL,NULL,'rM',NULL,NULL),(46,'Remito M',1,1,4,NULL,NULL,'rM',NULL,NULL),(47,'Gastos Bancarios',0,-1,14,1,44,'GB',NULL,NULL),(48,'Recibo X',0,1,11,NULL,NULL,'RX',NULL,NULL),(49,'Gastos Bancarios por Cesion de factura',0,-1,15,1,44,'GB',NULL,NULL),(50,'Liquidacion Bancaria (Cheques)',0,1,16,1,44,'LB',NULL,NULL),(51,'Orden De Compra Varios',0,1,5,NULL,NULL,'OCV',NULL,NULL),(52,'Ticket ',0,-1,6,NULL,61,'T',31,3),(53,'Ticket',0,1,1,NULL,61,'T',35,3),(54,'Ticket Factura B',0,-1,6,NULL,60,'TB',30,2),(55,'Ticket Factura B',0,1,1,NULL,60,'TB',34,2),(56,'Ticket Factura A',0,-1,6,1,59,'TA',29,1),(57,'Ticket Factura A',0,1,1,1,59,'TA',33,1),(58,'Recibo Ficticio',1,1,11,NULL,NULL,'',NULL,NULL),(59,'Nota de Credito E',0,1,7,NULL,19,'NCE',NULL,4),(60,'Nota de Credito E',0,-1,8,NULL,19,'NCE',NULL,4),(61,'Nota de Debito E',0,-1,12,NULL,18,'NDE',NULL,4),(62,'Nota de Debito E',0,1,13,NULL,18,'NDE',NULL,4);
/*!40000 ALTER TABLE `TiposDeComprobantes` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeGruposDeComprobantes` WRITE;
/*!40000 ALTER TABLE `TiposDeGruposDeComprobantes` DISABLE KEYS */;
INSERT INTO `TiposDeGruposDeComprobantes` VALUES (1,'Factura Compra','FC',0,1,3,1,'Ingreso de Comprobantes'),(2,'Arqueos ','A',0,NULL,5,2,NULL),(3,'Comprobantes Impositivos','CI',0,1,4,2,NULL),(4,'Remitos Ingreso','RI',0,NULL,3,1,'Remitos que nos llegan con la mercaderia'),(5,'Ordenes de Compra','OC',1,NULL,2,5,NULL),(6,'Facturas Ventas','FV',1,1,1,3,'Emision de Comprobantes'),(7,'Notas de Credito Emitidas','NCE',1,1,1,3,NULL),(8,'Notas de Credito Recibidas','NCR',0,1,3,1,NULL),(9,'Pagos','OP',1,1,2,2,'Ordenes de Pagos'),(10,'Remitos de Salida','RS',1,NULL,2,4,NULL),(11,'Cobros','CO',1,1,2,2,'Recibos Emitidos'),(12,'Notas de Debito Emitidas','NDE',1,1,1,3,NULL),(13,'Notas de Debito Recibidas','NDR',0,1,3,1,NULL),(14,'Gastos Bancarios','GB',0,1,3,1,NULL),(15,'Liq. de Gastos Bancarios por Cesion de factura','GCF',0,NULL,3,1,NULL),(16,'Liquidacion de Cheques','LCH',0,NULL,3,1,NULL),(17,'Pedidos Recibidos','PR',0,NULL,2,5,NULL),(18,'Pedidos Realizados','NP',1,NULL,2,5,NULL);
/*!40000 ALTER TABLE `TiposDeGruposDeComprobantes` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeLibrosIVA`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeLibrosIVA` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(30) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeLibrosIVA` WRITE;
/*!40000 ALTER TABLE `TiposDeLibrosIVA` DISABLE KEYS */;
INSERT INTO `TiposDeLibrosIVA` VALUES (1,'Compra'),(2,'Venta');
/*!40000 ALTER TABLE `TiposDeLibrosIVA` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `WorkflowsEvents` WRITE;
/*!40000 ALTER TABLE `WorkflowsEvents` DISABLE KEYS */;
INSERT INTO `WorkflowsEvents` VALUES (1,'Cerrar Comprobante','Comprobante_Cerrar','Row\\Comprobante');
/*!40000 ALTER TABLE `WorkflowsEvents` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeInscripcionesIB`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeInscripcionesIB` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(60) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeInscripcionesIB` WRITE;
/*!40000 ALTER TABLE `TiposDeInscripcionesIB` DISABLE KEYS */;
INSERT INTO `TiposDeInscripcionesIB` VALUES (1,'Sin Datos'),(2,'Exento'),(3,'Convenio Multilateral (CM)'),(4,'Contribuyente Directo'),(5,'No Inscripto');
/*!40000 ALTER TABLE `TiposDeInscripcionesIB` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeInscripcionesGanancias`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeInscripcionesGanancias` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(40) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeInscripcionesGanancias` WRITE;
/*!40000 ALTER TABLE `TiposDeInscripcionesGanancias` DISABLE KEYS */;
INSERT INTO `TiposDeInscripcionesGanancias` VALUES (1,'Inscripto'),(2,'No Inscripto'),(3,'Exento');
/*!40000 ALTER TABLE `TiposDeInscripcionesGanancias` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `ModalidadesIVA` WRITE;
/*!40000 ALTER TABLE `ModalidadesIVA` DISABLE KEYS */;
INSERT INTO `ModalidadesIVA` VALUES (1,'No Inscripto',NULL),(2,'Sin Datos',NULL),(3,'Resp. Inscripto',NULL),(4,'Monotributista',NULL),(5,'Exento',NULL),(6,'Consumidor final',NULL),(7,'No Responsable',NULL);
/*!40000 ALTER TABLE `ModalidadesIVA` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeDivisas` WRITE;
/*!40000 ALTER TABLE `TiposDeDivisas` DISABLE KEYS */;
INSERT INTO `TiposDeDivisas` VALUES (1,'Pesos','1.00','$',62),(2,'Dolar','3.84','U$S',61),(3,'Euros','4.79','€',56),(4,'Reales','3.00','R',12);
/*!40000 ALTER TABLE `TiposDeDivisas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeCondicionesDePago`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCondicionesDePago` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeCondicionesDePago` WRITE;
/*!40000 ALTER TABLE `TiposDeCondicionesDePago` DISABLE KEYS */;
INSERT INTO `TiposDeCondicionesDePago` VALUES (1,'Cuenta corriente'),(2,'Contado');
/*!40000 ALTER TABLE `TiposDeCondicionesDePago` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeArticulos` WRITE;
/*!40000 ALTER TABLE `TiposDeArticulos` DISABLE KEYS */;
INSERT INTO `TiposDeArticulos` VALUES (1,'Articulos Genericos ','ARTG',0),(2,'Conceptos Varios','VARS',0),(3,'Servicios','SERV',0),(4,'Gastos Bancarios','GB',0),(5,'Bienes','BIEN',0);
/*!40000 ALTER TABLE `TiposDeArticulos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeCampos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCampos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='ej: varchar, int, datetime, etc.; InnoDB free: 4096 kB; Inno';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeCampos` WRITE;
/*!40000 ALTER TABLE `TiposDeCampos` DISABLE KEYS */;
INSERT INTO `TiposDeCampos` VALUES (1,'Entero'),(2,'Decimal'),(3,'Fecha'),(4,'Texto'),(5,'Lista'),(6,'Booleano'),(7,'Fecha y Hora');
/*!40000 ALTER TABLE `TiposDeCampos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeCheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeCheques` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='Contiene el tipo de cheque. Ejemplo: Posdatado, al dia, etc.';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeCheques` WRITE;
/*!40000 ALTER TABLE `TiposDeCheques` DISABLE KEYS */;
INSERT INTO `TiposDeCheques` VALUES (1,'Al Dia'),(2,'Posdatado');
/*!40000 ALTER TABLE `TiposDeCheques` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeConceptos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeConceptos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(150) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=7 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 69632 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeConceptos` WRITE;
/*!40000 ALTER TABLE `TiposDeConceptos` DISABLE KEYS */;
INSERT INTO `TiposDeConceptos` VALUES (1,'IVA'),(2,'GANANCIA'),(3,'INGRESOS BRUTOS'),(4,'Otros'),(5,'SUSS'),(6,'INTERNOS');
/*!40000 ALTER TABLE `TiposDeConceptos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeControlesDeStock`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeControlesDeStock` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeControlesDeStock` WRITE;
/*!40000 ALTER TABLE `TiposDeControlesDeStock` DISABLE KEYS */;
INSERT INTO `TiposDeControlesDeStock` VALUES (1,'Almacen'),(2,'Cantidad'),(3,'Ninguno');
/*!40000 ALTER TABLE `TiposDeControlesDeStock` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeCuentas` WRITE;
/*!40000 ALTER TABLE `TiposDeCuentas` DISABLE KEYS */;
INSERT INTO `TiposDeCuentas` VALUES (1,'Cuenta Corriente en Pesos','CC $'),(2,'Caja de Ahorro en Pesos','CA $'),(3,'Cuenta Corriente en Dolares','CC u$s'),(4,'Caja de Ahorro en Dolares','CA u$s'),(5,'Cuenta Ingreso en Pesos','CI $'),(6,'Cuenta Ingreso en Dolares','CI u$s');
/*!40000 ALTER TABLE `TiposDeCuentas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeDirecciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeDirecciones` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=6 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT='InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeDirecciones` WRITE;
/*!40000 ALTER TABLE `TiposDeDirecciones` DISABLE KEYS */;
INSERT INTO `TiposDeDirecciones` VALUES (1,'Domicilio Fiscal'),(2,'Deposito'),(3,'Taller'),(4,'Fabrica'),(5,'Direccion Principal');
/*!40000 ALTER TABLE `TiposDeDirecciones` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeDocumentos` WRITE;
/*!40000 ALTER TABLE `TiposDeDocumentos` DISABLE KEYS */;
INSERT INTO `TiposDeDocumentos` VALUES (1,'CUIT',26,1),(2,'CI Mendoza',8,0),(3,'CUIL',27,1),(4,'CI La Rioja',9,0),(5,'CDI',28,0),(6,'CI Salta',10,0),(7,'LE',30,0),(8,'CI San Juan',11,0),(9,'LC',31,0),(10,'CI San Luis',12,0),(11,'CI extranjera',32,0),(12,'CI Santa Fe',13,0),(13,'En trámite',33,0),(14,'CI Santiago del Estero',14,0),(15,'Acta nacimiento',34,0),(16,'CI Tucumán',15,0),(17,'CI Bs. As. RNP',36,0),(18,'CI Chaco',16,0),(19,'DNI',37,1),(20,'CI Chubut',17,0),(21,'Pasaporte',35,0),(22,'CI Formosa',18,0),(23,'CI Policía Federal',1,0),(24,'CI Misiones',19,0),(25,'CI Buenos Aires',2,0),(26,'CI Neuquén',20,0),(27,'CI Catamarca',3,0),(28,'CI La Pampa',21,0),(29,'CI Córdoba',4,0),(30,'CI Río Negro',22,0),(31,'CI Corrientes',5,0),(32,'CI Santa Cruz',23,0),(33,'CI Entre Ríos',6,0),(34,'CI Tierra del Fuego',24,0),(35,'CI Jujuy',7,0),(36,'Sin identificar/venta global diaria',38,0);
/*!40000 ALTER TABLE `TiposDeDocumentos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeEmisoresDeCheques`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeEmisoresDeCheques` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC COMMENT=' Propio, De cliente, de terceros; InnoDB free: 4096 kB; Inno';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeEmisoresDeCheques` WRITE;
/*!40000 ALTER TABLE `TiposDeEmisoresDeCheques` DISABLE KEYS */;
INSERT INTO `TiposDeEmisoresDeCheques` VALUES (1,'Propio'),(2,'De Terceros'),(3,'De Proveedores');
/*!40000 ALTER TABLE `TiposDeEmisoresDeCheques` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeMontosMinimos` WRITE;
/*!40000 ALTER TABLE `TiposDeMontosMinimos` DISABLE KEYS */;
INSERT INTO `TiposDeMontosMinimos` VALUES (3,'Anual'),(2,'Mensual'),(1,'Por Factura');
/*!40000 ALTER TABLE `TiposDeMontosMinimos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeMovimientosBancarios` WRITE;
/*!40000 ALTER TABLE `TiposDeMovimientosBancarios` DISABLE KEYS */;
INSERT INTO `TiposDeMovimientosBancarios` VALUES (1,'Entrante'),(3,'Interno'),(2,'Saliente');
/*!40000 ALTER TABLE `TiposDeMovimientosBancarios` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeMovimientosCajas` WRITE;
/*!40000 ALTER TABLE `TiposDeMovimientosCajas` DISABLE KEYS */;
INSERT INTO `TiposDeMovimientosCajas` VALUES (1,'Arqueo',1),(2,'Ajuste de Caja',0),(3,'Movimiento normal',0),(4,'Movimiento desde un comprobante',0),(5,'Movimiento generado por transaccion bancaria',0);
/*!40000 ALTER TABLE `TiposDeMovimientosCajas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDePalets` WRITE;
/*!40000 ALTER TABLE `TiposDePalets` DISABLE KEYS */;
INSERT INTO `TiposDePalets` VALUES (2,'Descartable Madera'),(4,'Doble Retornable Madera'),(3,'Retornable Madera');
/*!40000 ALTER TABLE `TiposDePalets` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDePrioridades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDePrioridades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(255) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDePrioridades` WRITE;
/*!40000 ALTER TABLE `TiposDePrioridades` DISABLE KEYS */;
INSERT INTO `TiposDePrioridades` VALUES (1,'Urgente'),(2,'Normal');
/*!40000 ALTER TABLE `TiposDePrioridades` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeTelefonos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeTelefonos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=4 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeTelefonos` WRITE;
/*!40000 ALTER TABLE `TiposDeTelefonos` DISABLE KEYS */;
INSERT INTO `TiposDeTelefonos` VALUES (1,'Telefono'),(2,'Fax'),(3,'Celular');
/*!40000 ALTER TABLE `TiposDeTelefonos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeTransaccionesBancarias` WRITE;
/*!40000 ALTER TABLE `TiposDeTransaccionesBancarias` DISABLE KEYS */;
INSERT INTO `TiposDeTransaccionesBancarias` VALUES (4,'Debito Directo'),(2,'Deposito'),(3,'Extraccion'),(1,'Transferencia');
/*!40000 ALTER TABLE `TiposDeTransaccionesBancarias` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeUnidades`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeUnidades` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(200) NOT NULL,
  PRIMARY KEY (`Id`,`Descripcion`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8 ROW_FORMAT=DYNAMIC;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeUnidades` WRITE;
/*!40000 ALTER TABLE `TiposDeUnidades` DISABLE KEYS */;
INSERT INTO `TiposDeUnidades` VALUES (1,'De Peso'),(2,'De Medida'),(3,'De Unidades'),(4,'De Volumen'),(5,'De Moneda'),(6,'Porcentaje'),(7,'De Tiempo');
/*!40000 ALTER TABLE `TiposDeUnidades` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `UnidadesDeMedidas` WRITE;
/*!40000 ALTER TABLE `UnidadesDeMedidas` DISABLE KEYS */;
INSERT INTO `UnidadesDeMedidas` VALUES (3,'Unidades','u','1.0000',1,3,8),(5,'Kilogramos','kg','1000.0000',0,1,2),(6,'Gramos','gr','1.0000',1,1,15),(7,'Metros','m','100.0000',0,2,3),(8,'Litros','Lit','1000.0000',0,4,6),(9,'Mililitros','ml','1.0000',1,2,40),(10,'Centimetros','cm','1.0000',1,2,20),(11,'Miligramos','mg','0.0010',0,1,39);
/*!40000 ALTER TABLE `UnidadesDeMedidas` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `MmisTipos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `MmisTipos` (
  `Id` int(11) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(50) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=3 DEFAULT CHARSET=utf8 COMMENT='InnoDB free: 4096 kB; InnoDB free: 12288 kB';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `MmisTipos` WRITE;
/*!40000 ALTER TABLE `MmisTipos` DISABLE KEYS */;
INSERT INTO `MmisTipos` VALUES (1,'Entrada'),(2,'Producido');
/*!40000 ALTER TABLE `MmisTipos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `OrdenesDeProduccionesEstados` WRITE;
/*!40000 ALTER TABLE `OrdenesDeProduccionesEstados` DISABLE KEYS */;
INSERT INTO `OrdenesDeProduccionesEstados` VALUES (1,'Ingresada',0),(2,'Aceptada',0),(3,'Anulada',1),(4,'Produccion',0),(5,'Cancelada',1),(6,'Detenida',0),(7,'Finalizada',1);
/*!40000 ALTER TABLE `OrdenesDeProduccionesEstados` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `Paises` WRITE;
/*!40000 ALTER TABLE `Paises` DISABLE KEYS */;
INSERT INTO `Paises` VALUES (1,'Argentina',54,'Argentino',61),(2,'Uruguay',51,'Uruguayo',86),(3,'Paraguay',58,'Paraguayo',82),(4,'Bolivia',60,'Boliviano',63),(5,'Otros Paises',0,'Otros Paises',309);
/*!40000 ALTER TABLE `Paises` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `Provincias` WRITE;
/*!40000 ALTER TABLE `Provincias` DISABLE KEYS */;
INSERT INTO `Provincias` VALUES (1,'Chubut',1,17),(2,'Buenos Aires',1,2),(3,'Catamarca',1,3),(4,'Chaco',1,16),(5,'Cordoba',1,4),(6,'Corrientes',1,5),(7,'Entre Rios',1,6),(8,'Formosa',1,18),(9,'Jujuy',1,7),(10,'La Pampa',1,21),(11,'La Rioja',1,9),(12,'Mendoza',1,8),(13,'Misiones',1,19),(14,'Neuquen',1,20),(15,'Rio Negro',1,22),(16,'Salta',1,10),(17,'Santa Cruz',1,23),(18,'San Juan',1,11),(19,'San Luis',1,12),(20,'Santa Fe',1,13),(21,'Santiago del Estero',1,14),(22,'Tierra del Fuego',1,24),(23,'Tucuman',1,15),(24,'S/D',5,NULL),(25,'Capital Federal',1,NULL);
/*!40000 ALTER TABLE `Provincias` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `Roles`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `Roles` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) NOT NULL DEFAULT '',
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=33 DEFAULT CHARSET=latin1 COMMENT='InnoDB free: 12288 kB; (`Rol`) REFER `vidalacFinal/Roles`(`I';
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `Roles` WRITE;
/*!40000 ALTER TABLE `Roles` DISABLE KEYS */;
INSERT INTO `Roles` VALUES (1,'Admin'),(2,'Pobre diablo'),(3,'Test'),(4,'Comprobantes Ventas'),(5,'Administrar Clientes'),(6,'Administrar Listas De Precios'),(7,'Ordenes De Pedidos Ingreso'),(8,'Recibos Ingreso'),(9,'Remitos De Salidas'),(10,'Vendedores Administrar'),(11,'Proveedores Administrar'),(12,'Remitos de Entradas'),(13,'Comprobantes Compras'),(14,'Ordenes de Pago Ingreso'),(15,'Proveedores Consulta'),(16,'Pedidos de Cotizaciones Ingreso'),(17,'Ordenes de Compras Ingreso'),(18,'Administrar Depositos'),(19,'Control De Stock'),(22,'Cargar Depositos'),(23,'Produccion'),(24,'Estadisticas y Reportes Gerenciales'),(25,'Administrar Convenios Colectivos de Trabajo'),(26,'Carga de referenciales de RRHH'),(27,'Carga de referenciales de Liquidaciones'),(28,'Carga de Conceptos a Liquidar'),(29,'Carga de Parametros'),(30,'Carga de Variables Generales'),(31,'Administrar Empleados'),(32,'Libro Iva');
/*!40000 ALTER TABLE `Roles` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `RolesModelos` WRITE;
/*!40000 ALTER TABLE `RolesModelos` DISABLE KEYS */;
INSERT INTO `RolesModelos` VALUES (1,6,26,1,1,1,0),(2,6,27,1,1,1,0),(3,11,80,1,0,0,0),(4,11,81,1,0,0,0),(5,11,82,1,0,0,0),(6,11,83,1,0,0,0),(7,11,201,1,0,0,0),(8,11,202,1,0,0,0),(9,16,158,1,1,1,1),(10,16,159,1,1,1,1),(11,16,52,1,0,0,0),(12,16,24,1,0,0,0),(13,17,147,1,1,1,1),(14,17,148,1,1,1,1),(15,17,149,1,1,1,1),(16,17,150,1,1,1,1),(17,17,107,1,0,0,0),(18,17,94,1,0,0,0),(19,17,111,1,0,0,0),(20,13,127,1,0,0,0),(21,13,138,1,0,0,0),(95,22,1,1,1,1,1),(96,22,453,1,1,1,1),(97,22,20,1,0,0,0),(98,22,455,1,0,0,0),(99,22,17,1,0,0,0),(100,22,450,1,0,0,0),(102,22,63,1,0,0,0),(103,22,93,1,0,0,0),(104,26,433,1,1,1,1),(105,26,489,1,1,1,1),(106,26,497,1,1,1,1),(107,26,504,1,1,1,1),(108,26,503,1,1,1,1),(109,26,505,1,1,1,1),(110,26,506,1,1,1,1),(111,26,508,1,1,1,1),(112,26,490,1,0,0,0),(113,26,509,1,1,1,1),(114,26,59,1,1,1,1),(115,26,529,1,1,1,1),(116,26,532,1,1,1,1),(117,27,521,1,1,1,1),(118,27,485,1,1,1,1),(119,27,487,1,1,1,1),(120,27,488,1,1,1,1),(121,27,482,1,1,1,1);
/*!40000 ALTER TABLE `RolesModelos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `RolesModulos` WRITE;
/*!40000 ALTER TABLE `RolesModulos` DISABLE KEYS */;
INSERT INTO `RolesModulos` VALUES (2,2,80,NULL),(3,2,164,NULL),(7,5,575,NULL),(8,6,172,NULL),(9,7,185,NULL),(10,8,179,NULL),(11,8,595,NULL),(12,9,186,NULL),(13,9,596,NULL),(14,10,579,NULL),(15,4,170,NULL),(16,4,594,NULL),(17,11,107,NULL),(18,16,137,NULL),(19,16,136,NULL),(21,17,95,NULL),(25,17,608,NULL),(26,13,27,NULL),(27,13,131,NULL),(28,13,173,NULL),(29,13,28,NULL),(30,13,26,NULL),(31,13,150,NULL),(32,13,609,NULL),(33,18,83,NULL),(65,18,650,NULL),(66,19,626,NULL),(68,12,647,NULL),(69,12,576,NULL),(70,12,177,NULL),(71,23,611,NULL),(72,23,597,NULL),(73,23,602,NULL),(76,24,584,NULL),(77,24,583,NULL),(78,24,585,NULL),(79,24,628,NULL),(80,24,587,NULL),(81,24,618,NULL),(82,24,642,NULL),(83,24,619,NULL),(84,24,624,NULL),(85,24,641,NULL),(86,24,602,NULL),(88,24,611,NULL),(89,18,651,NULL),(90,31,80,NULL),(91,25,668,NULL),(92,28,673,NULL),(93,29,677,NULL),(94,30,676,NULL),(95,24,683,NULL),(96,24,685,NULL),(97,24,651,NULL),(98,12,690,NULL),(99,12,702,NULL),(100,18,627,NULL),(101,18,653,NULL),(102,23,705,NULL),(103,32,588,NULL);
/*!40000 ALTER TABLE `RolesModulos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `Sexos` WRITE;
/*!40000 ALTER TABLE `Sexos` DISABLE KEYS */;
INSERT INTO `Sexos` VALUES (1,'Masculino','M'),(3,'Femenino','F');
/*!40000 ALTER TABLE `Sexos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeAlmacenes` WRITE;
/*!40000 ALTER TABLE `TiposDeAlmacenes` DISABLE KEYS */;
INSERT INTO `TiposDeAlmacenes` VALUES (1,'Comun'),(3,'Interdeposito'),(2,'Predeposito');
/*!40000 ALTER TABLE `TiposDeAlmacenes` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AlmacenesPerspectivas` WRITE;
/*!40000 ALTER TABLE `AlmacenesPerspectivas` DISABLE KEYS */;
INSERT INTO `AlmacenesPerspectivas` VALUES (2,'Arriba'),(1,'Frente'),(3,'Lateral');
/*!40000 ALTER TABLE `AlmacenesPerspectivas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `MmisAcciones` WRITE;
/*!40000 ALTER TABLE `MmisAcciones` DISABLE KEYS */;
INSERT INTO `MmisAcciones` VALUES (1,'Crear Mmi desde Remito','Mmi <<N>> creado desde el remito <<R>>.'),(2,'Crear Mmi desde otro Mmi','Mmi <<N>> creado desde Mmi <<N2>>.'),(3,'Partir Mmi','Mmi <<N>> partido, generando el mmi <<N2>>.'),(4,'Modificar Cantidad del Contenido','Mmi <<N>> se <<A>> en <<C>> <<U>> quedando con <<C2>> <<U>>.'),(5,'Modificar Articulo o Remito Ingreso','Mmi <<N>> se cambio el origen de <<R1>> a <<R2>> o <<A1>> a <<A2>>.'),(6,'Mover Mmi','Mmi <<N>> movido de <<X>> a <<Y>>.'),(7,'Cerrar Mmi','Mmi <<N>> cerrado en fecha <<F>>.'),(8,'Asignar Mmi a Remito','Mmi <<N>> asignado al remito <<R>>.'),(9,'Movido a Produccion','Mmi <<N>> movido a produccion en orden <<O>>.'),(10,'Unir Mmis','Mmi <<N>> se le incorpora Mmi <<N2>> (Cantidad <<C>> <<U>>).'),(11,'Habilitar para Produccion','Mmi <<N>> <<A>> para produccion. '),(12,'Modificacion no relevante','Mmi <<N>> modificados datos no relevantes.'),(13,'Abrir Mmi','Mmi <<N>> reabierto.'),(14,'Modificar Cantidad Original','Mmi <<N>> pasa a tener ahora <<C>> <<U>>.'),(15,'Desasignar Mmi de Remito','Mmi <<N>> desasignado del remito <<R>>.'),(16,'Crear Mmi desde Produccion','Mmi <<N>> creado desde Produccion.'),(17,'Conversion a SubArticulo','Mmi <<N>> convertido a un SubArticulo.');
/*!40000 ALTER TABLE `MmisAcciones` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeIncrementos` WRITE;
/*!40000 ALTER TABLE `TiposDeIncrementos` DISABLE KEYS */;
INSERT INTO `TiposDeIncrementos` VALUES (2,'Caracteres'),(1,'Numerico');
/*!40000 ALTER TABLE `TiposDeIncrementos` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `ProduccionesMotivosDeFinalizaciones`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `ProduccionesMotivosDeFinalizaciones` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(45) CHARACTER SET utf8 COLLATE utf8_unicode_ci DEFAULT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=8 DEFAULT CHARSET=utf8;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `ProduccionesMotivosDeFinalizaciones` WRITE;
/*!40000 ALTER TABLE `ProduccionesMotivosDeFinalizaciones` DISABLE KEYS */;
INSERT INTO `ProduccionesMotivosDeFinalizaciones` VALUES (1,'Fin de turno'),(2,'Limpieza'),(3,'Accidente'),(4,'Fumigacion'),(5,'Cambio de idea'),(6,'Inspeccion de SENASA'),(7,'Otros');
/*!40000 ALTER TABLE `ProduccionesMotivosDeFinalizaciones` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeLiquidacionesTablas` WRITE;
/*!40000 ALTER TABLE `TiposDeLiquidacionesTablas` DISABLE KEYS */;
INSERT INTO `TiposDeLiquidacionesTablas` VALUES (2,'Escalar'),(1,'Por Rango');
/*!40000 ALTER TABLE `TiposDeLiquidacionesTablas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeVariables` WRITE;
/*!40000 ALTER TABLE `TiposDeVariables` DISABLE KEYS */;
INSERT INTO `TiposDeVariables` VALUES (1,'Conceptos','Conceptos de los Convenios colectivos'),(2,'Variables','Variables generales'),(3,'Parametros','No se pueden eliminar y el usuario puede cambiar el valor o agregar valores para nuevos periodos de tiempo.\r\nEj: Salario Minimo Vital y movil.'),(4,'Primitivas','Las modificamos solo nosotros y llaman a un store (SQL)'),(5,'Conceptos Extras','Son primitivas que devuelven un booleano.');
/*!40000 ALTER TABLE `TiposDeVariables` ENABLE KEYS */;
UNLOCK TABLES;
DROP TABLE IF EXISTS `TiposDeRelacionesArticulos`;
/*!40101 SET @saved_cs_client     = @@character_set_client */;
/*!40101 SET character_set_client = utf8 */;
CREATE TABLE `TiposDeRelacionesArticulos` (
  `Id` int(10) unsigned NOT NULL AUTO_INCREMENT,
  `Descripcion` varchar(100) COLLATE utf8_unicode_ci NOT NULL,
  PRIMARY KEY (`Id`)
) ENGINE=InnoDB AUTO_INCREMENT=5 DEFAULT CHARSET=utf8 COLLATE=utf8_unicode_ci;
/*!40101 SET character_set_client = @saved_cs_client */;

LOCK TABLES `TiposDeRelacionesArticulos` WRITE;
/*!40000 ALTER TABLE `TiposDeRelacionesArticulos` DISABLE KEYS */;
INSERT INTO `TiposDeRelacionesArticulos` VALUES (1,'Formula'),(2,'Producto'),(3,'Packaging'),(4,'SubArticulo');
/*!40000 ALTER TABLE `TiposDeRelacionesArticulos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `PlanesDeCuentasGrupos` WRITE;
/*!40000 ALTER TABLE `PlanesDeCuentasGrupos` DISABLE KEYS */;
INSERT INTO `PlanesDeCuentasGrupos` VALUES (1,'Sin Datos');
/*!40000 ALTER TABLE `PlanesDeCuentasGrupos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeMovimientosTarjetas` WRITE;
/*!40000 ALTER TABLE `TiposDeMovimientosTarjetas` DISABLE KEYS */;
INSERT INTO `TiposDeMovimientosTarjetas` VALUES (1,'Entrante'),(2,'Saliente');
/*!40000 ALTER TABLE `TiposDeMovimientosTarjetas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `TiposDeRegistrosDePrecios` WRITE;
/*!40000 ALTER TABLE `TiposDeRegistrosDePrecios` DISABLE KEYS */;
/*!40000 ALTER TABLE `TiposDeRegistrosDePrecios` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipConceptosIncluidos` WRITE;
/*!40000 ALTER TABLE `AfipConceptosIncluidos` DISABLE KEYS */;
INSERT INTO `AfipConceptosIncluidos` VALUES (1,'Producto / Exportación definitiva de bienes','1'),(2,'Servicios','2'),(3,'Productos y Servicios','3'),(4,'Otro','4');
/*!40000 ALTER TABLE `AfipConceptosIncluidos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipCondicionIva` WRITE;
/*!40000 ALTER TABLE `AfipCondicionIva` DISABLE KEYS */;
INSERT INTO `AfipCondicionIva` VALUES (1,'No Gravado','1'),(2,'Exento\r\n','2'),(3,'0%','3'),(4,'10.5%','4'),(5,'21%','5'),(6,'27%','6');
/*!40000 ALTER TABLE `AfipCondicionIva` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipCuitPaises` WRITE;
/*!40000 ALTER TABLE `AfipCuitPaises` DISABLE KEYS */;
INSERT INTO `AfipCuitPaises` VALUES (1,'51600000016','URUGUAY',2,3),(2,'55000000018','URUGUAY',0,1),(3,'50000000024','PARAGUAY',1,2),(4,'51600000024','PARAGUAY',2,3),(5,'55000000026','PARAGUAY',0,1),(6,'50000000032','CHILE',1,2),(7,'51600000032','CHILE',2,3),(8,'55000000034','CHILE',0,1),(9,'50000000040','BOLIVIA',1,2),(10,'51600000040','BOLIVIA',2,3),(11,'55000000042','BOLIVIA',0,1),(12,'50000000059','BRASIL',1,2),(13,'51600000059','BRASIL',2,3),(14,'55000000050','BRASIL',0,1),(15,'50000001012','BURKINA FASO',1,2),(16,'51600001012','BURKINA FASO',2,3),(17,'55000001014','BURKINA FASO',0,1),(18,'50000001020','ARGELIA',1,2),(19,'51600001020','ARGELIA',2,3),(20,'55000001022','ARGELIA',0,1),(21,'50000001039','BOTSWANA',1,2),(22,'51600001039','BOTSWANA',2,3),(23,'55000001030','BOTSWANA',0,1),(24,'50000001047','BURUNDI',1,2),(25,'51600001047','BURUNDI',2,3),(26,'55000001049','BURUNDI',0,1),(27,'50000001055','CAMERUN',1,2),(28,'51600001055','CAMERUN',2,3),(29,'55000001057','CAMERUN',0,1),(30,'50000001071','CENTRO AFRICANO, REP.',1,2),(31,'51600001071','CENTRO AFRICANO, REP.',2,3),(32,'55000001073','CENTRO AFRICANO, REP.',0,1),(33,'50000001101','COSTA DE MARFIL',1,2),(34,'51600001101','COSTA DE MARFIL',2,3),(35,'55000001103','COSTA DE MARFIL',0,1),(36,'50000001136','EGIPTO',1,2),(37,'51600001136','EGIPTO',2,3),(38,'55000001138','EGIPTO',0,1),(39,'50000001144','ETIOPIA',1,2),(40,'51600001144','ETIOPIA',2,3),(41,'55000001146','ETIOPIA',0,1),(42,'50000001152','GABON',1,2),(43,'51600001152','GABON',2,3),(44,'55000001154','GABON',0,1),(45,'50000001160','GAMBIA',1,2),(46,'51600001160','GAMBIA',2,3),(47,'55000001162','GAMBIA',0,1),(48,'50000001179','GHANA',1,2),(49,'51600001179','GHANA',2,3),(50,'55000001170','GHANA',0,1),(51,'50000001187','GUINEA',1,2),(52,'51600001187','GUINEA',2,3),(53,'55000001189','GUINEA',0,1),(54,'50000001195','GUINEA ECUATORIAL',1,2),(55,'51600001195','GUINEA ECUATORIAL',2,3),(56,'55000001197','GUINEA ECUATORIAL',0,1),(57,'50000001209','KENIA',1,2),(58,'51600001209','KENIA',2,3),(59,'55000001200','KENIA',0,1),(60,'50000001217','LESOTHO',1,2),(61,'51600001217','LESOTHO',2,3),(62,'55000001219','LESOTHO',0,1),(63,'50000001225','REPUBLICA DE LIBERIA (Estado independiente)',1,2),(64,'51600001225','REPUBLICA DE LIBERIA (Estado independiente)',2,3),(65,'55000001227','REPUBLICA DE LIBERIA (Estado independiente)',0,1),(66,'50000001233','LIBIA',1,2),(67,'51600001233','LIBIA',2,3),(68,'55000001235','LIBIA',0,1),(69,'50000001241','MADAGASCAR',1,2),(70,'51600001241','MADAGASCAR',2,3),(71,'55000001243','MADAGASCAR',0,1),(72,'50000001276','MARRUECOS',1,2),(73,'51600001276','MARRUECOS',2,3),(74,'55000001278','MARRUECOS',0,1),(75,'50000001284','REPUBLICA DE MAURICIO',1,2),(76,'51600001284','REPUBLICA DE MAURICIO',2,3),(77,'55000001286','REPUBLICA DE MAURICIO',0,1),(78,'50000001292','MAURITANIA',1,2),(79,'51600001292','MAURITANIA',2,3),(80,'55000001294','MAURITANIA',0,1),(81,'50000001306','NIGER',1,2),(82,'51600001306','NIGER',2,3),(83,'55000001308','NIGER',0,1),(84,'50000001314','NIGERIA',1,2),(85,'51600001314','NIGERIA',2,3),(86,'55000001316','NIGERIA',0,1),(87,'50000001322','ZIMBABWE',1,2),(88,'51600001322','ZIMBABWE',2,3),(89,'55000001324','ZIMBABWE',0,1),(90,'50000001330','RUANDA',1,2),(91,'51600001330','RUANDA',2,3),(92,'55000001332','RUANDA',0,1),(93,'50000001349','SENEGAL',1,2),(94,'51600001349','SENEGAL',2,3),(95,'55000001340','SENEGAL',0,1),(96,'50000001357','SIERRA LEONA',1,2),(97,'51600001357','SIERRA LEONA',2,3),(98,'55000001359','SIERRA LEONA',0,1),(99,'50000001365','SOMALIA',1,2),(100,'51600001365','SOMALIA',2,3),(101,'55000001367','SOMALIA',0,1),(102,'50000001373','REINO DE SWAZILANDIA (Estado independiente)',1,2),(103,'51600001373','REINO DE SWAZILANDIA (Estado independiente)',2,3),(104,'55000001375','REINO DE SWAZILANDIA (Estado independiente)',0,1),(105,'50000001381','SUDAN',1,2),(106,'51600001381','SUDAN',2,3),(107,'55000001383','SUDAN',0,1),(108,'50000001403','TOGO',1,2),(109,'51600001403','TOGO',2,3),(110,'55000001405','TOGO',0,1),(111,'50000001411','REPUBLICA TUNECINA',1,2),(112,'51600001411','REPUBLICA TUNECINA',2,3),(113,'55000001413','REPUBLICA TUNECINA',0,1),(114,'50000001446','ZAMBIA',1,2),(115,'51600001446','ZAMBIA',2,3),(116,'55000001448','ZAMBIA',0,1),(117,'50000001454','POS.BRITANICA (AFRICA)',1,2),(118,'51600001454','POS.BRITANICA (AFRICA)',2,3),(119,'55000001456','POS.BRITANICA (AFRICA)',0,1),(120,'50000001462','POS.ESPAÑOLA (AFRICA)',1,2),(121,'51600001462','POS.ESPAÑOLA (AFRICA)',2,3),(122,'55000001464','POS.ESPAÑOLA (AFRICA)',0,1),(123,'50000001470','POS.FRANCESA (AFRICA)',1,2),(124,'51600001470','POS.FRANCESA (AFRICA)',2,3),(125,'55000001472','POS.FRANCESA (AFRICA)',0,1),(126,'50000001489','POS.PORTUGUESA (AFRICA)',1,2),(127,'51600001489','POS.PORTUGUESA (AFRICA)',2,3),(128,'55000001480','POS.PORTUGUESA (AFRICA)',0,1),(129,'50000001497','REPUBLICA DE ANGOLA',1,2),(130,'51600001497','REPUBLICA DE ANGOLA',2,3),(131,'55000001499','REPUBLICA DE ANGOLA',0,1),(132,'50000001500','REPUBLICA DE CABO VERDE (Estado independiente)',1,2),(133,'51600001500','REPUBLICA DE CABO VERDE (Estado independiente)',2,3),(134,'55000001502','REPUBLICA DE CABO VERDE (Estado independiente)',0,1),(135,'50000001519','MOZAMBIQUE',1,2),(136,'51600001519','MOZAMBIQUE',2,3),(137,'55000001510','MOZAMBIQUE',0,1),(138,'50000001527','CONGO REP.POPULAR',1,2),(139,'51600001527','CONGO REP.POPULAR',2,3),(140,'55000001529','CONGO REP.POPULAR',0,1),(141,'50000001535','CHAD',1,2),(142,'51600001535','CHAD',2,3),(143,'55000001537','CHAD',0,1),(144,'50000001543','MALAWI',1,2),(145,'51600001543','MALAWI',2,3),(146,'55000001545','MALAWI',0,1),(147,'50000001551','TANZANIA',1,2),(148,'51600001551','TANZANIA',2,3),(149,'55000001553','TANZANIA',0,1),(150,'50000001586','COSTA RICA',1,2),(151,'51600001586','COSTA RICA',2,3),(152,'55000001588','COSTA RICA',0,1),(153,'50000001616','ZAIRE',1,2),(154,'51600001616','ZAIRE',2,3),(155,'55000001618','ZAIRE',0,1),(156,'50000001624','BENIN',1,2),(157,'51600001624','BENIN',2,3),(158,'55000001626','BENIN',0,1),(159,'50000001632','MALI',1,2),(160,'51600001632','MALI',2,3),(161,'55000001634','MALI',0,1),(162,'50000001705','UGANDA',1,2),(163,'51600001705','UGANDA',2,3),(164,'55000001707','UGANDA',0,1),(165,'50000001713','SUDAFRICA, REP. DE',1,2),(166,'51600001713','SUDAFRICA, REP. DE',2,3),(167,'55000001715','SUDAFRICA, REP. DE',0,1),(168,'50000001810','REPUBLICA DE SEYCHELLES (Estado independiente)',1,2),(169,'51600001810','REPUBLICA DE SEYCHELLES (Estado independiente)',2,3),(170,'55000001812','REPUBLICA DE SEYCHELLES (Estado independiente)',0,1),(171,'50000001829','SANTO TOME Y PRINCIPE',1,2),(172,'51600001829','SANTO TOME Y PRINCIPE',2,3),(173,'55000001820','SANTO TOME Y PRINCIPE',0,1),(174,'50000001837','NAMIBIA',1,2),(175,'51600001837','NAMIBIA',2,3),(176,'55000001839','NAMIBIA',0,1),(177,'50000001845','GUINEA BISSAU',1,2),(178,'51600001845','GUINEA BISSAU',2,3),(179,'55000001847','GUINEA BISSAU',0,1),(180,'50000001853','ERITREA',1,2),(181,'51600001853','ERITREA',2,3),(182,'55000001855','ERITREA',0,1),(183,'50000001861','REPUBLICA DE DJIBUTI (Estado independiente)',1,2),(184,'51600001861','REPUBLICA DE DJIBUTI (Estado independiente)',2,3),(185,'55000001863','REPUBLICA DE DJIBUTI (Estado independiente)',0,1),(186,'50000001896','COMORAS',1,2),(187,'51600001896','COMORAS',2,3),(188,'55000001898','COMORAS',0,1),(189,'50000001985','INDETERMINADO (AFRICA)',1,2),(190,'51600001985','INDETERMINADO (AFRICA)',2,3),(191,'55000001987','INDETERMINADO (AFRICA)',0,1),(192,'50000002019','BARBADOS (Estado independiente)',1,2),(193,'51600002019','BARBADOS (Estado independiente)',2,3),(194,'55000002010','BARBADOS (Estado independiente)',0,1),(195,'50000002043','CANADA',1,2),(196,'51600002043','CANADA',2,3),(197,'55000002045','CANADA',0,1),(198,'50000002051','COLOMBIA',1,2),(199,'51600002051','COLOMBIA',2,3),(200,'55000002053','COLOMBIA',0,1),(201,'50000002094','DOMINICANA, REPUBLICA',1,2),(202,'51600002094','DOMINICANA, REPUBLICA',2,3),(203,'55000002096','DOMINICANA, REPUBLICA',0,1),(204,'50000002116','EL SALVADOR',1,2),(205,'51600002116','EL SALVADOR',2,3),(206,'55000002118','EL SALVADOR',0,1),(207,'50000002124','ESTADOS UNIDOS',1,2),(208,'51600002124','ESTADOS UNIDOS',2,3),(209,'55000002126','ESTADOS UNIDOS',0,1),(210,'50000002132','GUATEMALA',1,2),(211,'51600002132','GUATEMALA',2,3),(212,'55000002134','GUATEMALA',0,1),(213,'50000002140','REPUBLICA COOPERATIVA DE GUYANA (Estado independiente)',1,2),(214,'51600002140','REPUBLICA COOPERATIVA DE GUYANA (Estado independiente)',2,3),(215,'55000002142','REPUBLICA COOPERATIVA DE GUYANA (Estado independiente)',0,1),(216,'50000002159','HAITI',1,2),(217,'51600002159','HAITI',2,3),(218,'55000002150','HAITI',0,1),(219,'50000002167','HONDURAS',1,2),(220,'51600002167','HONDURAS',2,3),(221,'55000002169','HONDURAS',0,1),(222,'50000002175','JAMAICA',1,2),(223,'51600002175','JAMAICA',2,3),(224,'55000002177','JAMAICA',0,1),(225,'50000002183','MEXICO',1,2),(226,'51600002183','MEXICO',2,3),(227,'55000002185','MEXICO',0,1),(228,'50000002191','NICARAGUA',1,2),(229,'51600002191','NICARAGUA',2,3),(230,'55000002193','NICARAGUA',0,1),(231,'50000002205','REPUBLICA DE PANAMA (Estado independiente)',1,2),(232,'51600002205','REPUBLICA DE PANAMA (Estado independiente)',2,3),(233,'55000002207','REPUBLICA DE PANAMA (Estado independiente)',0,1),(234,'50000002213','ESTADO LIBRE ASOCIADO DE PUERTO RICO (Estado asoc. a EEUU)',1,2),(235,'51600002213','ESTADO LIBRE ASOCIADO DE PUERTO RICO (Estado asoc. a EEUU)',2,3),(236,'55000002215','ESTADO LIBRE ASOCIADO DE PUERTO RICO (Estado asoc. a EEUU)',0,1),(237,'50000002221','PERU',1,2),(238,'51600002221','PERU',2,3),(239,'55000002223','PERU',0,1),(240,'50000002256','ANTIGUA Y BARBUDA (Estado independiente)',1,2),(241,'51600002256','ANTIGUA Y BARBUDA (Estado independiente)',2,3),(242,'55000002258','ANTIGUA Y BARBUDA (Estado independiente)',0,1),(243,'50000002264','VENEZUELA',1,2),(244,'51600002264','VENEZUELA',2,3),(245,'55000002266','VENEZUELA',0,1),(246,'50000002272','POS.BRITANICA (AMERICA)',1,2),(247,'51600002272','POS.BRITANICA (AMERICA)',2,3),(248,'55000002274','POS.BRITANICA (AMERICA)',0,1),(249,'50000002280','POS.DANESA (AMERICA)',1,2),(250,'51600002280','POS.DANESA (AMERICA)',2,3),(251,'55000002282','POS.DANESA (AMERICA)',0,1),(252,'50000002299','POS.FRANCESA (AMERICA)',1,2),(253,'51600002299','POS.FRANCESA (AMERICA)',2,3),(254,'55000002290','POS.FRANCESA (AMERICA)',0,1),(255,'50000002302','POS.PAISES BAJOS (AMERICA)',1,2),(256,'51600002302','POS.PAISES BAJOS (AMERICA)',2,3),(257,'55000002304','POS.PAISES BAJOS (AMERICA)',0,1),(258,'50000002310','POS.E.E.U.U. (AMERICA)',1,2),(259,'51600002310','POS.E.E.U.U. (AMERICA)',2,3),(260,'55000002312','POS.E.E.U.U. (AMERICA)',0,1),(261,'50000002329','SURINAME',1,2),(262,'51600002329','SURINAME',2,3),(263,'55000002320','SURINAME',0,1),(264,'50000002337','EL COMMONWEALTH DE DOMINICA (Estado Asociado)',1,2),(265,'51600002337','EL COMMONWEALTH DE DOMINICA (Estado Asociado)',2,3),(266,'55000002339','EL COMMONWEALTH DE DOMINICA (Estado Asociado)',0,1),(267,'50000002345','SANTA LUCIA',1,2),(268,'51600002345','SANTA LUCIA',2,3),(269,'55000002347','SANTA LUCIA',0,1),(270,'50000002353','SAN VICENTE Y LAS GRANADINAS (Estado independiente)',1,2),(271,'51600002353','SAN VICENTE Y LAS GRANADINAS (Estado independiente)',2,3),(272,'55000002355','SAN VICENTE Y LAS GRANADINAS (Estado independiente)',0,1),(273,'50000002361','BELICE (Estado independiente)',1,2),(274,'51600002361','BELICE (Estado independiente)',2,3),(275,'55000002363','BELICE (Estado independiente)',0,1),(276,'50000002396','CUBA',1,2),(277,'51600002396','CUBA',2,3),(278,'55000002398','CUBA',0,1),(279,'50000002426','ECUADOR',1,2),(280,'51600002426','ECUADOR',2,3),(281,'55000002428','ECUADOR',0,1),(282,'50000002434','REPUBLICA DE TRINIDAD Y TOBAGO',1,2),(283,'51600002434','REPUBLICA DE TRINIDAD Y TOBAGO',2,3),(284,'55000002436','REPUBLICA DE TRINIDAD Y TOBAGO',0,1),(285,'50000002825','BUTAN',1,2),(286,'51600002825','BUTAN',2,3),(287,'55000002827','BUTAN',0,1),(288,'50000002841','MYANMAR (EX BIRMANIA)',1,2),(289,'51600002841','MYANMAR (EX BIRMANIA)',2,3),(290,'55000002843','MYANMAR (EX BIRMANIA)',0,1),(291,'50000002876','ISRAEL',1,2),(292,'51600002876','ISRAEL',2,3),(293,'55000002878','ISRAEL',0,1),(294,'50000002882','ESTADO ASOCIADO DE GRANADA (Estado independiente)',1,2),(295,'51600002884','ESTADO ASOCIADO DE GRANADA (Estado independiente)',2,3),(296,'55000002884','ESTADO ASOCIADO DE GRANADA (Estado independiente)',0,1),(297,'50000002892','FEDERACION DE SAN CRISTOBAL (Islas Saint Kitts and Nevis)',1,2),(298,'51600002892','FEDERACION DE SAN CRISTOBAL (Islas Saint Kitts and Nevis)',2,3),(299,'55000002894','FEDERACION DE SAN CRISTOBAL (Islas Saint Kitts and Nevis)',0,1),(300,'50000002906','COMUNIDAD DE LAS BAHAMAS (Estado independiente)',1,2),(301,'51600002906','COMUNIDAD DE LAS BAHAMAS (Estado independiente)',2,3),(302,'55000002908','COMUNIDAD DE LAS BAHAMAS (Estado independiente)',0,1),(303,'50000002914','TAILANDIA',1,2),(304,'51600002914','TAILANDIA',2,3),(305,'55000002916','TAILANDIA',0,1),(306,'50000002922','INDETERMINADO (AMERICA)',1,2),(307,'51600002922','INDETERMINADO (AMERICA)',2,3),(308,'55000002924','INDETERMINADO (AMERICA)',0,1),(309,'50000002930','IRAN',1,2),(310,'51600002930','IRAN',2,3),(311,'55000002932','IRAN',0,1),(312,'50000002981','ESTADO DE QATAR (Estado independiente)',1,2),(313,'51600002981','ESTADO DE QATAR (Estado independiente)',2,3),(314,'55000002983','ESTADO DE QATAR (Estado independiente)',0,1),(315,'50000003007','REINO HACHEMITA DE JORDANIA',1,2),(316,'51600003007','REINO HACHEMITA DE JORDANIA',2,3),(317,'55000003009','REINO HACHEMITA DE JORDANIA',0,1),(318,'50000003015','AFGANISTAN',1,2),(319,'51600003015','AFGANISTAN',2,3),(320,'55000003017','AFGANISTAN',0,1),(321,'50000003023','ARABIA SAUDITA',1,2),(322,'51600003023','ARABIA SAUDITA',2,3),(323,'55000003025','ARABIA SAUDITA',0,1),(324,'50000003031','ESTADO DE BAHREIN (Estado independiente)',1,2),(325,'51600003031','ESTADO DE BAHREIN (Estado independiente)',2,3),(326,'55000003033','ESTADO DE BAHREIN (Estado independiente)',0,1),(327,'50000003066','CAMBOYA (EX KAMPUCHEA)',1,2),(328,'51600003066','CAMBOYA (EX KAMPUCHEA)',2,3),(329,'55000003068','CAMBOYA (EX KAMPUCHEA)',0,1),(330,'50000003074','REPUBLICA DEMOCRATICA SOCIALISTA DE SRI LANKA',1,2),(331,'51600003074','REPUBLICA DEMOCRATICA SOCIALISTA DE SRI LANKA',2,3),(332,'55000003076','REPUBLICA DEMOCRATICA SOCIALISTA DE SRI LANKA',0,1),(333,'50000003082','COREA DEMOCRATICA ',1,2),(334,'51600003082','COREA DEMOCRATICA ',2,3),(335,'55000003084','COREA DEMOCRATICA ',0,1),(336,'50000003090','COREA REPUBLICANA',1,2),(337,'51600003090','COREA REPUBLICANA',2,3),(338,'55000003092','COREA REPUBLICANA',0,1),(339,'50000003104','CHINA REP.POPULAR',1,2),(340,'51600003104','CHINA REP.POPULAR',2,3),(341,'55000003106','CHINA REP.POPULAR',0,1),(342,'50000003112','REPUBLICA DE CHIPRE (Estado independiente)',1,2),(343,'51600003112','REPUBLICA DE CHIPRE (Estado independiente)',2,3),(344,'55000003114','REPUBLICA DE CHIPRE (Estado independiente)',0,1),(345,'50000003120','FILIPINAS',1,2),(346,'51600003120','FILIPINAS',2,3),(347,'55000003122','FILIPINAS',0,1),(348,'50000003139','TAIWAN',1,2),(349,'51600003139','TAIWAN',2,3),(350,'55000003130','TAIWAN',0,1),(351,'50000003147','GAZA',1,2),(352,'51600003147','GAZA',2,3),(353,'55000003149','GAZA',0,1),(354,'50000003155','INDIA',1,2),(355,'51600003155','INDIA',2,3),(356,'55000003157','INDIA',0,1),(357,'50000003163','INDONESIA',1,2),(358,'51600003163','INDONESIA',2,3),(359,'55000003165','INDONESIA',0,1),(360,'50000003171','IRAK',1,2),(361,'51600003171','IRAK',2,3),(362,'55000003173','IRAK',0,1),(363,'50000003201','JAPON',1,2),(364,'51600003201','JAPON',2,3),(365,'55000003203','JAPON',0,1),(366,'50000003236','ESTADO DE KUWAIT (Estado independiente)',1,2),(367,'51600003236','ESTADO DE KUWAIT (Estado independiente)',2,3),(368,'55000003238','ESTADO DE KUWAIT (Estado independiente)',0,1),(369,'50000003244','LAOS',1,2),(370,'51600003244','LAOS',2,3),(371,'55000003246','LAOS',0,1),(372,'50000003252','LIBANO',1,2),(373,'51600003252','LIBANO',2,3),(374,'55000003254','LIBANO',0,1),(375,'50000003260','MALASIA',1,2),(376,'51600003260','MALASIA',2,3),(377,'55000003262','MALASIA',0,1),(378,'50000003279','REPUBLICA DE MALDIVAS (Estado independiente)',1,2),(379,'51600003279','REPUBLICA DE MALDIVAS (Estado independiente)',2,3),(380,'55000003270','REPUBLICA DE MALDIVAS (Estado independiente)',0,1),(381,'50000003287','SULTANATO DE OMAN',1,2),(382,'51600003287','SULTANATO DE OMAN',2,3),(383,'55000003289','SULTANATO DE OMAN',0,1),(384,'50000003295','MONGOLIA',1,2),(385,'51600003295','MONGOLIA',2,3),(386,'55000003297','MONGOLIA',0,1),(387,'50000003309','NEPAL',1,2),(388,'51600003309','NEPAL',2,3),(389,'55000003300','NEPAL',0,1),(390,'50000003317','EMIRATOS ARABES UNIDOS (Estado independiente)',1,2),(391,'51600003317','EMIRATOS ARABES UNIDOS (Estado independiente)',2,3),(392,'55000003319','EMIRATOS ARABES UNIDOS (Estado independiente)',0,1),(393,'50000003325','PAKISTAN',1,2),(394,'51600003325','PAKISTAN',2,3),(395,'55000003327','PAKISTAN',0,1),(396,'50000003333','SINGAPUR',1,2),(397,'51600003333','SINGAPUR',2,3),(398,'55000003335','SINGAPUR',0,1),(399,'50000003341','SIRIA',1,2),(400,'51600003341','SIRIA',2,3),(401,'55000003343','SIRIA',0,1),(402,'50000003376','VIETNAM',1,2),(403,'51600003376','VIETNAM',2,3),(404,'55000003378','VIETNAM',0,1),(405,'50000003392','REPUBLICA DEL YEMEN',1,2),(406,'51600003392','REPUBLICA DEL YEMEN',2,3),(407,'55000003394','REPUBLICA DEL YEMEN',0,1),(408,'50000003414','POS.BRITANICA (HONG KONG)',1,2),(409,'51600003414','POS.BRITANICA (HONG KONG)',2,3),(410,'55000003416','POS.BRITANICA (HONG KONG)',0,1),(411,'50000003422','POS.JAPONESA (ASIA)',1,2),(412,'51600003422','POS.JAPONESA (ASIA)',2,3),(413,'55000003424','POS.JAPONESA (ASIA)',0,1),(414,'50000003449','MACAO',1,2),(415,'51600003449','MACAO',2,3),(416,'55000003440','MACAO',0,1),(417,'50000003457','BANGLADESH',1,2),(418,'51600003457','BANGLADESH',2,3),(419,'55000003459','BANGLADESH',0,1),(420,'50000003503','TURQUIA',1,2),(421,'51600003503','TURQUIA',2,3),(422,'55000003505','TURQUIA',0,1),(423,'50000003546','ITALIA',1,2),(424,'51600003546','ITALIA',2,3),(425,'55000003548','ITALIA',0,1),(426,'50000003554','TURKMENISTAN',1,2),(427,'51600003554','TURKMENISTAN',2,3),(428,'55000003556','TURKMENISTAN',0,1),(429,'50000003562','UZBEKISTAN',1,2),(430,'51600003562','UZBEKISTAN',2,3),(431,'55000003564','UZBEKISTAN',0,1),(432,'50000003570','TERRITORIOS AUTONOMOS PALESTINOS',1,2),(433,'51600003570','TERRITORIOS AUTONOMOS PALESTINOS',2,3),(434,'55000003572','TERRITORIOS AUTONOMOS PALESTINOS',0,1),(435,'50000003813','ISLANDIA',1,2),(436,'51600003813','ISLANDIA',2,3),(437,'55000003815','ISLANDIA',0,1),(438,'50000003880','GEORGIA',1,2),(439,'51600003880','GEORGIA',2,3),(440,'55000003882','GEORGIA',0,1),(441,'50000003899','TAYIKISTAN',1,2),(442,'51600003899','TAYIKISTAN',2,3),(443,'55000003890','TAYIKISTAN',0,1),(444,'50000003902','AZERBAIDZHAN',1,2),(445,'51600003902','AZERBAIDZHAN',2,3),(446,'55000003904','AZERBAIDZHAN',0,1),(447,'50000003910','BRUNEI DARUSSALAM (Estado independiente)',1,2),(448,'51600003910','BRUNEI DARUSSALAM (Estado independiente)',2,3),(449,'55000003912','BRUNEI DARUSSALAM (Estado independiente)',0,1),(450,'50000003929','KAZAJSTAN',1,2),(451,'51600003929','KAZAJSTAN',2,3),(452,'55000003920','KAZAJSTAN',0,1),(453,'50000003937','KIRGUISTAN',1,2),(454,'51600003937','KIRGUISTAN',2,3),(455,'55000003939','KIRGUISTAN',0,1),(456,'50000003961','INDETERMINADO (ASIA)',1,2),(457,'51600003961','INDETERMINADO (ASIA)',2,3),(458,'55000003963','INDETERMINADO (ASIA)',0,1),(459,'50000004011','REPUBLICA DE ALBANIA',1,2),(460,'51600004011','REPUBLICA DE ALBANIA',2,3),(461,'55000004013','REPUBLICA DE ALBANIA',0,1),(462,'50000004046','PRINCIPADO DEL VALLE DE ANDORRA',1,2),(463,'51600004046','PRINCIPADO DEL VALLE DE ANDORRA',2,3),(464,'55000004048','PRINCIPADO DEL VALLE DE ANDORRA',0,1),(465,'50000004054','AUSTRIA',1,2),(466,'51600004054','AUSTRIA',2,3),(467,'55000004056','AUSTRIA',0,1),(468,'50000004062','BELGICA',1,2),(469,'51600004062','BELGICA',2,3),(470,'55000004064','BELGICA',0,1),(471,'50000004070','BULGARIA',1,2),(472,'51600004070','BULGARIA',2,3),(473,'55000004072','BULGARIA',0,1),(474,'50000004097','DINAMARCA',1,2),(475,'51600004097','DINAMARCA',2,3),(476,'55000004099','DINAMARCA',0,1),(477,'50000004100','ESPAÑA',1,2),(478,'51600004100','ESPAÑA',2,3),(479,'55000004102','ESPAÑA',0,1),(480,'50000004119','FINLANDIA',1,2),(481,'51600004119','FINLANDIA',2,3),(482,'55000004110','FINLANDIA',0,1),(483,'50000004127','FRANCIA',1,2),(484,'51600004127','FRANCIA',2,3),(485,'55000004129','FRANCIA',0,1),(486,'50000004135','GRECIA',1,2),(487,'51600004135','GRECIA',2,3),(488,'55000004137','GRECIA',0,1),(489,'50000004143','HUNGRIA',1,2),(490,'51600004143','HUNGRIA',2,3),(491,'55000004145','HUNGRIA',0,1),(492,'50000004151','IRLANDA (EIRE)',1,2),(493,'51600004151','IRLANDA (EIRE)',2,3),(494,'55000004153','IRLANDA (EIRE)',0,1),(495,'50000004186','PRINCIPADO DE LIECHTENSTEIN (Estado independiente)',1,2),(496,'51600004186','PRINCIPADO DE LIECHTENSTEIN (Estado independiente)',2,3),(497,'55000004188','PRINCIPADO DE LIECHTENSTEIN (Estado independiente)',0,1),(498,'50000004194','GRAN DUCADO DE LUXEMBURGO',1,2),(499,'51600004194','GRAN DUCADO DE LUXEMBURGO',2,3),(500,'55000004196','GRAN DUCADO DE LUXEMBURGO',0,1),(501,'50000004216','PRINCIPADO DE MONACO',1,2),(502,'51600004216','PRINCIPADO DE MONACO',2,3),(503,'55000004218','PRINCIPADO DE MONACO',0,1),(504,'50000004224','NORUEGA',1,2),(505,'51600004224','NORUEGA',2,3),(506,'55000004226','NORUEGA',0,1),(507,'50000004232','PAISES BAJOS',1,2),(508,'51600004232','PAISES BAJOS',2,3),(509,'55000004234','PAISES BAJOS',0,1),(510,'50000004240','POLONIA',1,2),(511,'51600004240','POLONIA',2,3),(512,'55000004242','POLONIA',0,1),(513,'50000004259','PORTUGAL',1,2),(514,'51600004259','PORTUGAL',2,3),(515,'55000004250','PORTUGAL',0,1),(516,'50000004267','REINO UNIDO',1,2),(517,'51600004267','REINO UNIDO',2,3),(518,'55000004269','REINO UNIDO',0,1),(519,'50000004275','RUMANIA',1,2),(520,'51600004275','RUMANIA',2,3),(521,'55000004277','RUMANIA',0,1),(522,'50000004283','SERENISIMA REPUBLICA DE SAN MARINO (Estado independiente)',1,2),(523,'51600004283','SERENISIMA REPUBLICA DE SAN MARINO (Estado independiente)',2,3),(524,'55000004285','SERENISIMA REPUBLICA DE SAN MARINO (Estado independiente)',0,1),(525,'50000004291','SUECIA',1,2),(526,'51600004291','SUECIA',2,3),(527,'55000004293','SUECIA',0,1),(528,'50000004305','SUIZA',1,2),(529,'51600004305','SUIZA',2,3),(530,'55000004307','SUIZA',0,1),(531,'50000004313','SANTA SEDE (VATICANO)',1,2),(532,'51600004313','SANTA SEDE (VATICANO)',2,3),(533,'55000004315','SANTA SEDE (VATICANO)',0,1),(534,'50000004321','YUGOSLAVIA',1,2),(535,'51600004321','YUGOSLAVIA',2,3),(536,'55000004323','YUGOSLAVIA',0,1),(537,'50000004364','REPUBLICA DE MALTA (Estado independiente)',1,2),(538,'51600004364','REPUBLICA DE MALTA (Estado independiente)',2,3),(539,'55000004366','REPUBLICA DE MALTA (Estado independiente)',0,1),(540,'50000004380','ALEMANIA, REP. FED.',1,2),(541,'51600004380','ALEMANIA, REP. FED.',2,3),(542,'55000004382','ALEMANIA, REP. FED.',0,1),(543,'50000004399','BIELORUSIA',1,2),(544,'51600004399','BIELORUSIA',2,3),(545,'55000004390','BIELORUSIA',0,1),(546,'50000004402','ESTONIA',1,2),(547,'51600004402','ESTONIA',2,3),(548,'55000004404','ESTONIA',0,1),(549,'50000004410','LETONIA',1,2),(550,'51600004410','LETONIA',2,3),(551,'55000004412','LETONIA',0,1),(552,'50000004429','LITUANIA',1,2),(553,'51600004429','LITUANIA',2,3),(554,'55000004420','LITUANIA',0,1),(555,'50000004437','MOLDOVA',1,2),(556,'51600004437','MOLDOVA',2,3),(557,'55000004439','MOLDOVA',0,1),(558,'50000004461','BOSNIA HERZEGOVINA',1,2),(559,'51600004461','BOSNIA HERZEGOVINA',2,3),(560,'55000004463','BOSNIA HERZEGOVINA',0,1),(561,'50000004496','ESLOVENIA',1,2),(562,'51600004496','ESLOVENIA',2,3),(563,'55000004498','ESLOVENIA',0,1),(564,'50000004909','MACEDONIA',1,2),(565,'51600004909','MACEDONIA',2,3),(566,'55000004900','MACEDONIA',0,1),(567,'50000004917','POS.BRITANICA (EUROPA)',1,2),(568,'51600004917','POS.BRITANICA (EUROPA)',2,3),(569,'55000004919','POS.BRITANICA (EUROPA)',0,1),(570,'50000004984','INDETERMINADO (EUROPA)',1,2),(571,'51600004984','INDETERMINADO (EUROPA)',2,3),(572,'55000004986','INDETERMINADO (EUROPA)',0,1),(573,'50000004992','AUSTRALIA',1,2),(574,'51600004992','AUSTRALIA',2,3),(575,'55000004994','AUSTRALIA',0,1),(576,'50000005034','REPUBLICA DE NAURU (Estado independiente)',1,2),(577,'51600005034','REPUBLICA DE NAURU (Estado independiente)',2,3),(578,'55000005036','REPUBLICA DE NAURU (Estado independiente)',0,1),(579,'50000005042','NUEVA ZELANDA',1,2),(580,'51600005042','NUEVA ZELANDA',2,3),(581,'55000005044','NUEVA ZELANDA',0,1),(582,'50000005050','REPUBLICA DE VANUATU',1,2),(583,'51600005050','REPUBLICA DE VANUATU',2,3),(584,'55000005052','REPUBLICA DE VANUATU',0,1),(585,'50000005069','SAMOA OCCIDENTAL',1,2),(586,'51600005069','SAMOA OCCIDENTAL',2,3),(587,'55000005069','SAMOA OCCIDENTAL',0,1),(588,'50000005077','POS.AUSTRALIANA (OCEANIA)',1,2),(589,'51600005077','POS.AUSTRALIANA (OCEANIA)',2,3),(590,'55000005079','POS.AUSTRALIANA (OCEANIA)',0,1),(591,'50000005085','POS.BRITANICA (OCEANIA)',1,2),(592,'51600005085','POS.BRITANICA (OCEANIA)',2,3),(593,'55000005087','POS.BRITANICA (OCEANIA)',0,1),(594,'50000005093','POS.FRANCESA (OCEANIA)',1,2),(595,'51600005093','POS.FRANCESA (OCEANIA)',2,3),(596,'55000005095','POS.FRANCESA (OCEANIA)',0,1),(597,'50000005107','POS.NEOCELANDESA (OCEANIA)',1,2),(598,'51600005107','POS.NEOCELANDESA (OCEANIA)',2,3),(599,'55000005109','POS.NEOCELANDESA (OCEANIA)',0,1),(600,'50000005115','POS.E.E.U.U. (OCEANIA)',1,2),(601,'51600005115','POS.E.E.U.U. (OCEANIA)',2,3),(602,'55000005117','POS.E.E.U.U. (OCEANIA)',0,1),(603,'50000005123','FIJI, ISLAS',1,2),(604,'51600005123','FIJI, ISLAS',2,3),(605,'55000005125','FIJI, ISLAS',0,1),(606,'50000005131','PAPUA, ISLAS',1,2),(607,'51600005131','PAPUA, ISLAS',2,3),(608,'55000005133','PAPUA, ISLAS',0,1),(609,'50000005166','KIRIBATI',1,2),(610,'51600005166','KIRIBATI',2,3),(611,'55000005168','KIRIBATI',0,1),(612,'50000005174','TUVALU',1,2),(613,'51600005174','TUVALU',2,3),(614,'55000005176','TUVALU',0,1),(615,'50000005182','ISLAS SALOMON',1,2),(616,'51600005182','ISLAS SALOMON',2,3),(617,'55000005184','ISLAS SALOMON',0,1),(618,'50000005190','REINO DE TONGA (Estado independiente)',1,2),(619,'51600005190','REINO DE TONGA (Estado independiente)',2,3),(620,'55000005192','REINO DE TONGA (Estado independiente)',0,1),(621,'50000005204','REPUBLICA DE LAS ISLAS MARSHALL (Estado independiente)',1,2),(622,'51600005204','REPUBLICA DE LAS ISLAS MARSHALL (Estado independiente)',2,3),(623,'55000005206','REPUBLICA DE LAS ISLAS MARSHALL (Estado independiente)',0,1),(624,'50000005212','ISLAS MARIANAS',1,2),(625,'51600005212','ISLAS MARIANAS',2,3),(626,'55000005214','ISLAS MARIANAS',0,1),(627,'50000005905','MICRONESIA ESTADOS FED.',1,2),(628,'51600005905','MICRONESIA ESTADOS FEDERADOS',2,3),(629,'55000005907','MICRONESIA ESTADOS FED.',0,1),(630,'50000005913','PALAU',1,2),(631,'51600005913','PALAU',2,3),(632,'55000005915','PALAU',0,1),(633,'50000005980','INDETERMINADO (OCEANIA)',1,2),(634,'51600005980','INDETERMINADO (OCEANIA)',2,3),(635,'55000005982','INDETERMINADO (OCEANIA)',0,1),(636,'50000006014','RUSA, FEDERACION',1,2),(637,'51600006014','RUSA, FEDERACION',2,3),(638,'55000006016','RUSA, FEDERACION',0,1),(639,'50000006022','ARMENIA',1,2),(640,'51600006022','ARMENIA',2,3),(641,'55000006024','ARMENIA',0,1),(642,'50000006030','CROACIA',1,2),(643,'51600006030','CROACIA',2,3),(644,'55000006032','CROACIA',0,1),(645,'50000006049','UCRANIA',1,2),(646,'51600006049','UCRANIA',2,3),(647,'55000006040','UCRANIA',0,1),(648,'50000006057','CHECA, REPUBLICA',1,2),(649,'51600006057','CHECA, REPUBLICA',2,3),(650,'55000006059','CHECA, REPUBLICA',0,1),(651,'50000006065','ESLOVACA, REPUBLICA',1,2),(652,'51600006065','ESLOVACA, REPUBLICA',2,3),(653,'55000006067','ESLOVACA, REPUBLICA',0,1),(654,'50000006529','ANGUILA (Territorio no autónomo del Reino Unido)',1,2),(655,'51600006529','ANGUILA (Territorio no autónomo del Reino Unido)',2,3),(656,'55000006520','ANGUILA (Territorio no autónomo del Reino Unido)',0,1),(657,'50000006537','ARUBA (Territorio de Países Bajos)',1,2),(658,'51600006537','ARUBA (Territorio de Países Bajos)',2,3),(659,'55000006539','ARUBA (Territorio de Países Bajos)',0,1),(660,'50000006545','ISLAS DE COOK (Territorio autónomo asociado a Nueva Zelanda)',1,2),(661,'51600006545','ISLAS DE COOK (Territorio autónomo asociado a Nueva Zelanda)',2,3),(662,'55000006547','ISLAS DE COOK (Territorio autónomo asociado a Nueva Zelanda)',0,1),(663,'50000006553','PATAU',1,2),(664,'51600006553','PATAU',2,3),(665,'55000006555','PATAU',0,1),(666,'50000006561','POLINESIA FRANCESA (Territorio de Ultramar de Francia)',1,2),(667,'51600006561','POLINESIA FRANCESA (Territorio de Ultramar de Francia)',2,3),(668,'55000006563','POLINESIA FRANCESA (Territorio de Ultramar de Francia)',0,1),(669,'50000006596','ANTILLAS HOLANDESAS (Territorio de Países Bajos)',1,2),(670,'51600006596','ANTILLAS HOLANDESAS (Territorio de Países Bajos)',2,3),(671,'55000006598','ANTILLAS HOLANDESAS (Territorio de Países Bajos)',0,1),(672,'50000006626','ASCENCION',1,2),(673,'51600006626','ASCENCION',2,3),(674,'55000006628','ASCENCION',0,1),(675,'50000006634','BERMUDAS (Territorio no autónomo del Reino Unido)',1,2),(676,'51600006634','BERMUDAS (Territorio no autónomo del Reino Unido)',2,3),(677,'55000006636','BERMUDAS (Territorio no autónomo del Reino Unido)',0,1),(678,'50000006642','CAMPIONE D@ITALIA',1,2),(679,'51600006642','CAMPIONE D@ITALIA',2,3),(680,'55000006644','CAMPIONE D@ITALIA',0,1),(681,'50000006650','COLONIA DE GIBRALTAR',1,2),(682,'51600006650','COLONIA DE GIBRALTAR',2,3),(683,'55000006652','COLONIA DE GIBRALTAR',0,1),(684,'50000006669','GROENLANDIA',1,2),(685,'51600006669','GROENLANDIA',2,3),(686,'55000006660','GROENLANDIA',0,1),(687,'50000006677','GUAM (Territorio no autónomo de los EEUU)',1,2),(688,'51600006677','GUAM (Territorio no autónomo de los EEUU)',2,3),(689,'55000006679','GUAM (Territorio no autónomo de los EEUU)',0,1),(690,'50000006685','HONK KONG (Territorio de China)',1,2),(691,'51600006685','HONK KONG (Territorio de China)',2,3),(692,'55000006687','HONK KONG (Territorio de China)',0,1),(693,'50000006693','ISLAS AZORES',1,2),(694,'51600006693','ISLAS AZORES',2,3),(695,'55000006695','ISLAS AZORES',0,1),(696,'50000006707','ISLAS DEL CANAL:Guernesey,Jersey,Alderney,G.Stark,L.Sark,etc',1,2),(697,'51600006707','ISLAS DEL CANAL:Guernesey,Jersey,Alderney,G.Stark,L.Sark,etc',2,3),(698,'55000006709','ISLAS DEL CANAL:Guernesey,Jersey,Alderney,G.Stark,L.Sark,etc',0,1),(699,'50000006715','ISLAS CAIMAN (Territorio no autónomo del Reino Unido)',1,2),(700,'51600006715','ISLAS CAIMAN (Territorio no autónomo del Reino Unido)',2,3),(701,'55000006717','ISLAS CAIMAN (Territorio no autónomo del Reino Unido)',0,1),(702,'50000006723','ISLA CHRISTMAS',1,2),(703,'51600006723','ISLA CHRISTMAS',2,3),(704,'55000006725','ISLA CHRISTMAS',0,1),(705,'50000006731','ISLA DE COCOS O KEELING',1,2),(706,'51600006731','ISLA DE COCOS O KEELING',2,3),(707,'55000006733','ISLA DE COCOS O KEELING',0,1),(708,'50000006766','ISLA DE MAN (Territorio del Reino Unido)',1,2),(709,'51600006766','ISLA DE MAN (Territorio del Reino Unido)',2,3),(710,'55000006768','ISLA DE MAN (Territorio del Reino Unido)',0,1),(711,'50000006774','ISLA DE NORFOLK',1,2),(712,'51600006774','ISLA DE NORFOLK',2,3),(713,'55000006776','ISLA DE NORFOLK',0,1),(714,'50000006782','ISLAS TURKAS Y CAICOS (Territorio no autónomo del R. Unido)',1,2),(715,'51600006782','ISLAS TURKAS Y CAICOS (Territorio no autónomo del R. Unido)',2,3),(716,'55000006784','ISLAS TURKAS Y CAICOS (Territorio no autónomo del R. Unido)',0,1),(717,'50000006790','ISLAS PACIFICO',1,2),(718,'51600006790','ISLAS PACIFICO',2,3),(719,'55000006792','ISLAS PACIFICO',0,1),(720,'50000006804','ISLA DE SAN PEDRO Y MIGUELON',1,2),(721,'51600006804','ISLA DE SAN PEDRO Y MIGUELON',2,3),(722,'55000006806','ISLA DE SAN PEDRO Y MIGUELON',0,1),(723,'50000006812','ISLA QESHM',1,2),(724,'51600006812','ISLA QESHM',2,3),(725,'55000006814','ISLA QESHM',0,1),(726,'50000006820','ISLAS VIRGENES BRITANICAS(Territorio no autónomo de R.UNIDO)',1,2),(727,'51600006820','ISLAS VIRGENES BRITANICAS(Territorio no autónomo de R.UNIDO)',2,3),(728,'55000006822','ISLAS VIRGENES BRITANICAS(Territorio no autónomo de R.UNIDO)',0,1),(729,'50000006839','ISLAS VIRGENES DE ESTADOS UNIDOS DE AMERICA',1,2),(730,'51600006839','ISLAS VIRGENES DE ESTADOS UNIDOS DE AMERICA',2,3),(731,'55000006830','ISLAS VIRGENES DE ESTADOS UNIDOS DE AMERICA',0,1),(732,'50000006847','LABUAN',1,2),(733,'51600006847','LABUAN',2,3),(734,'55000006849','LABUAN',0,1),(735,'50000006855','MADEIRA (Territorio de Portugal)',1,2),(736,'51600006855','MADEIRA (Territorio de Portugal)',2,3),(737,'55000006857','MADEIRA (Territorio de Portugal)',0,1),(738,'50000006863','MONTSERRAT (Territorio no autónomo del Reino Unido)',1,2),(739,'51600006863','MONTSERRAT (Territorio no autónomo del Reino Unido)',2,3),(740,'55000006865','MONTSERRAT (Territorio no autónomo del Reino Unido)',0,1),(741,'50000006871','NIUE',1,2),(742,'51600006871','NIUE',2,3),(743,'55000006873','NIUE',0,1),(744,'50000006901','PITCAIRN',1,2),(745,'51600006901','PITCAIRN',2,3),(746,'55000006903','PITCAIRN',0,1),(747,'50000006936','REGIMEN APLICABLE A LAS SA FINANCIERAS(ley 11.073 de la ROU)',1,2),(748,'51600006936','REGIMEN APLICABLE A LAS SA FINANCIERAS(ley 11.073 de la ROU)',2,3),(749,'55000006938','REGIMEN APLICABLE A LAS SA FINANCIERAS(ley 11.073 de la ROU)',0,1),(750,'50000006944','SANTA ELENA',1,2),(751,'51600006944','SANTA ELENA',2,3),(752,'55000006946','SANTA ELENA',0,1),(753,'50000006952','SAMOA AMERICANA (Territorio no autónomo de los EEUU)',1,2),(754,'51600006952','SAMOA AMERICANA (Territorio no autónomo de los EEUU)',2,3),(755,'55000006954','SAMOA AMERICANA (Territorio no autónomo de los EEUU)',0,1),(756,'50000006960','ARCHIPIELAGO DE SVBALBARD',1,2),(757,'51600006960','ARCHIPIELAGO DE SVBALBARD',2,3),(758,'55000006962','ARCHIPIELAGO DE SVBALBARD',0,1),(759,'50000006979','TRISTAN DA CUNHA',1,2),(760,'51600006979','TRISTAN DA CUNHA',2,3),(761,'55000006970','TRISTAN DA CUNHA',0,1),(762,'50000006987','TRIESTE (Italia)',1,2),(763,'51600006987','TRIESTE (Italia)',2,3),(764,'55000006989','TRIESTE (Italia)',0,1),(765,'50000006995','TOKELAU',1,2),(766,'51600006995','TOKELAU',2,3),(767,'55000006997','TOKELAU',0,1),(768,'50000007002','ZONA LIBRE DE OSTRAVA (ciudad de la antigua Checoeslovaquia)',1,2),(769,'51600007002','ZONA LIBRE DE OSTRAVA (ciudad de la antigua Checoeslovaquia)',2,3),(770,'55000007004','ZONA LIBRE DE OSTRAVA (ciudad de la antigua Checoeslovaquia)',0,1),(771,'50000009986','PARA PERSONAS FISICAS DE INDETERMINADO (CONTINENTE)',1,2),(772,'51600009986','PARA PERSONAS FISICAS DE INDETERMINADO (CONTINENTE)',2,3),(773,'55000009988','PARA PERSONAS FISICAS DE INDETERMINADO (CONTINENTE)',0,1),(774,'50000009994','PARA PERSONAS FISICAS DE OTROS PAISES',1,2),(775,'51600009994','PARA PERSONAS FISICAS DE OTROS PAISES',2,3),(776,'55000009996','PARA PERSONAS FISICAS DE OTROS PAISES',0,1);
/*!40000 ALTER TABLE `AfipCuitPaises` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipGananciasDeducciones` WRITE;
/*!40000 ALTER TABLE `AfipGananciasDeducciones` DISABLE KEYS */;
INSERT INTO `AfipGananciasDeducciones` VALUES (1,'Ganancias no imponibles','A','art. 23, inc. a).',1),(2,'Cargas de Familia - Conyuge','B 1','art. 23, inc. b), pto. 1',2),(3,'Cargas de Familia - Hijos','B 2','art. 23, inc. b), pto. 2',2),(4,'Cargas de Familia - Otros a Cargo','B 3','art. 23, inc. b), pto. 3',2),(5,'Deduccion Especial s/beneficios cuando: Empresas, siempre que trabaje personalmente en las mismas','C 1','art. 23, inc. c), 1° párrafo y art. 49',1),(6,'Deduccion Especial s/beneficios cuando: El cumplimiento de los requisitos de los planes de seguro de retiro privado, de los servicios personales prestados por los socios en las sociedades cooperativas y del ejercicio de profesiones liberales u oficios, de las funciones de albacea, síndico, mandatario, gestor de negocios, director de S.A., socios administradores de S.R.L., S.C.S. y S.C.A. y fideicomisario','C 2','art. 23, inc, c), 2° párrafo y art. 79, incs. d), e) y f)',1),(7,'Deduccion Especial s/beneficios cuando: El desempeño de cargos públicos, del trabajo personal en relación de dependencia y de las jubilaciones, pensiones, retiros y subsidios','C 3','art. 23, inc. c), 3° párrafo y art. 79, incs. a), b) y c)',1),(8,'Gastos de sepelio','D','art. 22',3),(9,'Primas de seguros de vida','E','art. 81, inc. b).',3),(10,'Aportes (personales) correspondientes a planes de seguro de retiro privado.','F','art. 81, inc. e).',4),(11,'Honorarios médicos y paramédicos.','G','art. 81, inc. h).',4),(12,'Cuotas a instituciones que presten cobertura médico asistenciales.','H','art. 81, inc. g).',4),(13,'Servicio Doméstico','I','art. 16, Ley N° 26.063',1);
/*!40000 ALTER TABLE `AfipGananciasDeducciones` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipGananciasDeduccionesDetalles` WRITE;
/*!40000 ALTER TABLE `AfipGananciasDeduccionesDetalles` DISABLE KEYS */;
INSERT INTO `AfipGananciasDeduccionesDetalles` VALUES (1,1,1,'9000.00'),(2,1,2,'10800.00'),(3,1,3,'12960.00'),(4,1,4,'15552.00'),(5,2,1,'10000.00'),(6,2,2,'12000.00'),(7,2,3,'14400.00'),(8,2,4,'17280.00'),(9,3,1,'5000.00'),(10,3,2,'6000.00'),(11,3,3,'7200.00'),(12,3,4,'8640.00'),(13,4,1,'3750.00'),(14,4,2,'4500.00'),(15,4,3,'5400.00'),(16,4,4,'6480.00'),(17,5,1,'9000.00'),(18,5,2,'10800.00'),(19,5,3,'12960.00'),(20,5,4,'15552.00'),(21,6,1,'9000.00'),(22,6,2,'10800.00'),(23,6,3,'12960.00'),(24,6,4,'15552.00'),(25,7,1,'43200.00'),(26,7,2,'51840.00'),(27,7,3,'62208.00'),(28,7,4,'74649.60'),(29,8,1,'996.23'),(30,8,2,'996.23'),(31,8,3,'996.23'),(32,8,4,'996.23'),(33,9,1,'996.23'),(34,9,2,'996.23'),(35,9,3,'996.23'),(36,9,4,'996.23'),(37,13,1,'9000.00'),(38,13,2,'10800.00'),(39,13,3,'12960.00'),(40,13,4,'15552.00');
/*!40000 ALTER TABLE `AfipGananciasDeduccionesDetalles` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipGananciasDeduccionesPeriodos` WRITE;
/*!40000 ALTER TABLE `AfipGananciasDeduccionesPeriodos` DISABLE KEYS */;
INSERT INTO `AfipGananciasDeduccionesPeriodos` VALUES (1,'2009-01-01','2009-12-31'),(2,'2010-01-01','2010-12-31'),(3,'2011-01-01','2013-02-28'),(4,'2013-03-01',NULL);
/*!40000 ALTER TABLE `AfipGananciasDeduccionesPeriodos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipGananciasDeduccionesTipos` WRITE;
/*!40000 ALTER TABLE `AfipGananciasDeduccionesTipos` DISABLE KEYS */;
INSERT INTO `AfipGananciasDeduccionesTipos` VALUES (4,'Formula Particular'),(1,'Monto Fijo Anual'),(2,'Monto Fijo por suceso'),(3,'Monto Tope Anual');
/*!40000 ALTER TABLE `AfipGananciasDeduccionesTipos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipGananciasEscalas` WRITE;
/*!40000 ALTER TABLE `AfipGananciasEscalas` DISABLE KEYS */;
INSERT INTO `AfipGananciasEscalas` VALUES (1,0,10000,0,9,1),(2,10000,20000,900,14,1),(3,20000,30000,2300,19,1),(4,30000,60000,4200,23,1),(5,60000,90000,11100,27,1),(6,90000,120000,19200,31,1),(7,120000,9999999,28500,35,1);
/*!40000 ALTER TABLE `AfipGananciasEscalas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipGananciasEscalasPeriodos` WRITE;
/*!40000 ALTER TABLE `AfipGananciasEscalasPeriodos` DISABLE KEYS */;
INSERT INTO `AfipGananciasEscalasPeriodos` VALUES (1,'2000-01-01',NULL);
/*!40000 ALTER TABLE `AfipGananciasEscalasPeriodos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipIdiomas` WRITE;
/*!40000 ALTER TABLE `AfipIdiomas` DISABLE KEYS */;
INSERT INTO `AfipIdiomas` VALUES (1,'Español','1'),(2,'Inglés','2'),(3,'Portugués','3');
/*!40000 ALTER TABLE `AfipIdiomas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipIncoterms` WRITE;
/*!40000 ALTER TABLE `AfipIncoterms` DISABLE KEYS */;
INSERT INTO `AfipIncoterms` VALUES (1,'Cost and Freight','CFR'),(2,'Cost, Insurance and Freight','CIF'),(3,'Carriage and Insurance Paid To','CIP'),(4,'Carriage Paid To','CPT'),(5,'Delivered At Frontier','DAF'),(6,'Delivered At Port','DAP'),(7,'Delivered Duty Paid','DDP'),(8,'Delivered Duty Unpaid','DDU'),(9,'Delivered Ex Quay','DEQ'),(10,'Delivered Ex Ship','DES'),(11,'Ex Works','EXW'),(12,'Free Alongside Ship','FAS'),(13,'Free Carrier','FCA'),(14,'Free On Board','FOB');
/*!40000 ALTER TABLE `AfipIncoterms` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipMonedas` WRITE;
/*!40000 ALTER TABLE `AfipMonedas` DISABLE KEYS */;
INSERT INTO `AfipMonedas` VALUES (1,'OTRAS MONEDAS ','000'),(2,'Dólar EEUU LIBRE ','002'),(3,'FRANCOS FRANCESES ','003'),(4,'LIRAS ITALIANAS ','004'),(5,'PESETAS ','005'),(6,'MARCOS ALEMANES ','006'),(7,'FLORINES HOLANDESES ','007'),(8,'FRANCOS BELGAS ','008'),(9,'FRANCOS SUIZOS ','009'),(10,'PESOS MEJICANOS ','010'),(11,'PESOS URUGUAYOS ','011'),(12,'REAL ','012'),(13,'ESCUDOS PORTUGUESES ','013'),(14,'CORONAS DANESAS ','014'),(15,'CORONAS NORUEGAS ','015'),(16,'CORONAS SUECAS ','016'),(17,'CHELINES AUTRIACOS ','017'),(18,'Dólar CANADIENSE ','018'),(19,'YENS ','019'),(20,'LIBRA ESTERLINA ','021'),(21,'MARCOS FINLANDESES ','022'),(22,'BOLIVAR (VENEZOLANO)','023'),(23,'CORONA CHECA ','024'),(24,'DINAR (YUGOSLAVO) ','025'),(25,'Dólar AUSTRALIANO ','026'),(26,'DRACMA (GRIEGO) ','027'),(27,'FLORIN (ANTILLAS HOLA ','028'),(28,'GUARANI ','029'),(29,'SHEKEL (ISRAEL) ','030'),(30,'PESO BOLIVIANO ','031'),(31,'PESO COLOMBIANO ','032'),(32,'PESO CHILENO ','033'),(33,'RAND (SUDAFRICANO)','034'),(34,'NUEVO SOL PERUANO ','035'),(35,'SUCRE (ECUATORIANO) ','036'),(36,'LEI RUMANOS ','040'),(37,'DERECHOS ESPECIALES DE GIRO ','041'),(38,'PESOS DOMINICANOS ','042'),(39,'BALBOAS PANAMEÑAS ','043'),(40,'CORDOBAS NICARAGÛENSES ','044'),(41,'DIRHAM MARROQUÍES ','045'),(42,'LIBRAS EGIPCIAS ','046'),(43,'RIYALS SAUDITAS ','047'),(44,'BRANCOS BELGAS FINANCIERAS','048'),(45,'GRAMOS DE ORO FINO ','049'),(46,'LIBRAS IRLANDESAS ','050'),(47,'Dólar DE HONG KONG ','051'),(48,'Dólar DE SINGAPUR ','052'),(49,'Dólar DE JAMAICA ','053'),(50,'Dólar DE TAIWAN ','054'),(51,'QUETZAL (GUATEMALTECOS) ','055'),(52,'FORINT (HUNGRIA) ','056'),(53,'BAHT (TAILANDIA) ','057'),(54,'ECU ','058'),(55,'DINAR KUWAITI ','059'),(56,'EURO ','060'),(57,'ZLTYS POLACOS ','061'),(58,'RUPIAS HINDÚES ','062'),(59,'LEMPIRAS HONDUREÑAS ','063'),(60,'YUAN (Rep. Pop. China)','064'),(61,'Dólar ESTADOUNIDENSE ','DOL'),(62,'PESOS ','PES');
/*!40000 ALTER TABLE `AfipMonedas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipOtrosTributos` WRITE;
/*!40000 ALTER TABLE `AfipOtrosTributos` DISABLE KEYS */;
INSERT INTO `AfipOtrosTributos` VALUES (1,'Impuestos nacionales','01'),(2,'Impuestos provinciales','02'),(3,'Impuestos municipales','03'),(4,'Impuestos internos','04'),(5,'Otros','99');
/*!40000 ALTER TABLE `AfipOtrosTributos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipPaises` WRITE;
/*!40000 ALTER TABLE `AfipPaises` DISABLE KEYS */;
INSERT INTO `AfipPaises` VALUES (1,'BURKINA FASO','101','BURKINA FASO'),(2,'ARGELIA','102','ARGELIA'),(3,'BOTSWANA','103','BOTSWANA'),(4,'BURUNDI','104','BURUNDI'),(5,'CAMERUN','105','CAMERUN'),(6,'REP. CENTROAFRICANA.','107','REP.CENTROAFRICANA'),(7,'CONGO','108','CONGO'),(8,'REP.DEMOCRAT.DEL CONGO EX ZAIRE','109','REP. DEMOCRAT. DEL CONGO EX ZAIRE'),(9,'COSTA DE MARFIL','110','COSTA DE MARFIL'),(10,'CHAD','111','CHAD'),(11,'BENIN','112','BENIN'),(12,'EGIPTO','113','EGIPTO'),(13,'GABON','115','GABON'),(14,'GAMBIA','116','GAMBIA'),(15,'GHANA','117','GHANA'),(16,'GUINEA','118','GUINEA'),(17,'GUINEA ECUATORIAL','119','GUINEA ECUATORIAL'),(18,'KENYA','120','KENYA'),(19,'LESOTHO','121','LESOTHO'),(20,'LIBERIA','122','LIBERIA'),(21,'LIBIA','123','LIBIA'),(22,'MADAGASCAR','124','MADAGASCAR'),(23,'MALAWI','125','MALAWI'),(24,'MALI','126','MALI'),(25,'MARRUECOS','127','MARRUECOS'),(26,'MAURICIO,ISLAS','128','MAURICIO,ISLAS'),(27,'MAURITANIA','129','MAURITANIA'),(28,'NIGER','130','NIGER'),(29,'NIGERIA','131','NIGERIA'),(30,'ZIMBABWE','132','ZIMBABWE'),(31,'RWANDA','133','RWANDA'),(32,'SENEGAL','134','SENEGAL'),(33,'SIERRA LEONA','135','SIERRA LEONA'),(34,'SOMALIA','136','SOMALIA'),(35,'SWAZILANDIA','137','SWAZILANDIA'),(36,'SUDAN','138','SUDAN'),(37,'TANZANIA','139','TANZANIA'),(38,'TOGO','140','TOGO'),(39,'TUNEZ','141','TUNEZ'),(40,'UGANDA','142','UGANDA'),(41,'REPUBLICA DE SUDAFRICA','143','REPUBLICA DE SUDAFRICA'),(42,'ZAMBIA','144','ZAMBIA'),(43,'TERRIT.VINCULADOS AL R UNIDO','145','AFRICA'),(44,'TERRIT.VINCULADOS A ESPAÑA','146','AFRICA'),(45,'TERRIT.VINCULADOS A FRANCIA','147','AFRICA'),(46,'TERRIT.VINCULADOS A PORTUGAL','148','AFRICA'),(47,'ANGOLA','149','ANGOLA'),(48,'CABO VERDE','150','ISLAS'),(49,'MOZAMBIQUE','151','MOZAMBIQUE'),(50,'SEYCHELLES','152','SEYCHELLES'),(51,'DJIBOUTI','153','DJIBOUTI'),(52,'COMORAS','155','COMORAS'),(53,'GUINEA BISSAU','156','GUINEA BISSAU'),(54,'STO.TOME Y PRINCIPE','157','STO.TOME Y PRINCIPE'),(55,'NAMIBIA','158','NAMIBIA'),(56,'SUDAFRICA','159','SUDAFRICA'),(57,'ERITREA','160','ERITREA'),(58,'ETIOPIA','161','ETIOPIA'),(59,'RESTO (AFRICA)','197','RESTO (AFRICA)'),(60,'INDETERMINADO (AFRICA)','198','INDETERMINADO AFRICA)'),(61,'ARGENTINA','200','ARGENTINA'),(62,'BARBADOS','201','BARBADOS'),(63,'BOLIVIA','202','BOLIVIA'),(64,'BRASIL','203','BRASIL'),(65,'CANADA','204','CANADA'),(66,'COLOMBIA','205','COLOMBIA'),(67,'COSTA RICA','206','COSTA RICA'),(68,'CUBA','207','CUBA'),(69,'CHILE','208','CHILE'),(70,'DOMINICANA,REP.','209','DOMINICANA,REP.'),(71,'ECUADOR','210','ECUADOR'),(72,'EL SALVADOR','211','EL SALVADOR'),(73,'ESTADOS UNIDOS','212','ESTADOS UNIDOS'),(74,'GUATEMALA','213','GUATEMALA'),(75,'GUYANA','214','GUYANA'),(76,'HAITI','215','HAITI'),(77,'HONDURAS','216','HONDURAS'),(78,'JAMAICA','217','JAMAICA'),(79,'MEXICO','218','MEXICO'),(80,'NICARAGUA','219','NICARAGUA'),(81,'PANAMA','220','PANAMA'),(82,'PARAGUAY','221','PARAGUAY'),(83,'PERU','222','PERU'),(84,'PUERTO RICO','223','ESTADO ASOCIADO'),(85,'TRINIDAD Y -TOBAGO','224','TRINIDAD Y TOBAGO'),(86,'URUGUAY','225','URUGUAY'),(87,'VENEZUELA','226','VENEZUELA'),(88,'TERRIT.VINCULADO AL R.UNIDO','227','AMERICA'),(89,'TER.VINCULADOS A DINAMARCA','228','AMERICA'),(90,'TERRIT.VINCULADOS A FRANCIA AMERIC.','229','AMERICA'),(91,'TERRIT. HOLANDESES','230','TERRIT. HOLANDESES'),(92,'TER.VINCULADOS A ESTADOS UNIDOS','231','AMERICA'),(93,'SURINAME','232','SURINAME'),(94,'DOMINICA','233','DOMINICA'),(95,'SANTA LUCIA','234','SANTA LUCIA'),(96,'SAN VICENTE Y LAS GRANADINS','235','SAN VICENTE Y LAS GRANADINAS'),(97,'BELICE','236','BELICE'),(98,'ANTIGUA Y BARBUDA','237','ANTIGUA Y BARBUDA'),(99,'S.CRISTOBAL Y NEVIS','238','S.CRISTOBAL Y NEVIS'),(100,'BAHAMAS','239','BAHAMAS'),(101,'GRANADA','240','GRANADA'),(102,'ANTILLAS HOLANDESAS','241','TERRI.VINC.A PAISES BAJOS'),(103,'ARUBA','242',NULL),(104,'TIERRA DEL FUEGO','250','(AAE)'),(105,'ZF LA PLATA','251','BUENOS AIRES'),(106,'ZF JUSTO DARACT','252','SAN LUIS'),(107,'ZF RIO GALLEGOS','253','SANTA CRUZ'),(108,'ISLAS MALVINAS','254','ISLAS MALVINAS'),(109,'ZF TUCUMAN','255','TUCUMAN'),(110,'ZF CORDOBA','256','CORDOBA'),(111,'ZF MENDOZA','257','MENDOZA'),(112,'ZF GENERAL PICO','258','LA PAMPA'),(113,'ZF COMODORO RIVADAVIA','259','CHUBUT'),(114,'ZF IQUIQUE','260','CHILE'),(115,'ZF PUNTA ARENAS','261','CHILE'),(116,'ZF SALTA','262','SALTA'),(117,'ZF PASO DE LOS LIBRES','263','CORRIENTES'),(118,'ZF PUERTO IGUAZU','264','MISIONES'),(119,'SECTOR ANTARTICO ARG.','265','SECTOR ANTARTICO ARG.'),(120,'ZF COLON','270','PANAMA'),(121,'ZF WINNER (STA. C.DE LA SIERRA','271','BOLIVIA'),(122,'ZF COLONIA','280','URUGUAY'),(123,'ZF FLORIDA','281','URUGUAY'),(124,'ZF LIBERTAD','282','URUGUAY'),(125,'ZF ZONAMERICA','283','EX MONTEVIDEO URUGUAY'),(126,'ZF NUEVA HELVECIA','284','URUGUAY'),(127,'ZF NUEVA PALMIRA','285','URUGUAY'),(128,'ZF RIO NEGRO','286','URUGUAY'),(129,'ZF RIVERA','287','URUGUAY'),(130,'ZF SAN JOSE','288','URUGUAY'),(131,'ZF MANAOS','291','BRASIL'),(132,'MAR ARG ZONA ECO.EX','295','ARGENTINA'),(133,'RIOS ARG NAVEG INTER','296','ARGENTINA'),(134,'RESTO AMERICA','297','RESTO AMERICA'),(135,'INDETERMINADO.(AMERICA)','298','INDETERMINADO.(AMERICA)'),(136,'AFGANISTAN','301','AFGANISTAN'),(137,'ARABIA SAUDITA','302','ARABIA SAUDITA'),(138,'BAHREIN','303','BAHREIN'),(139,'MYANMAR(EX-BIRMANIA)','304','MYANMAR(EX-BIRMANIA)'),(140,'BUTAN','305','BUTAN'),(141,'CAMBODYA(EX-KAMPUCHE','306','CAMBODYA(EX-KAMPUCHE'),(142,'SRI LANKA','307','SRI LANKA'),(143,'COREA DEMOCRATICA','308','COREA DEMOCRATICA'),(144,'COREA REPUBLICANA','309','COREA REPUBLICANA'),(145,'CHINA','310','CHINA'),(146,'CHIPRE','311','CHIPRE'),(147,'FILIPINAS','312','FILIPINAS'),(148,'TAIWAN','313','TAIWAN'),(149,'GAZA','314','GAZA'),(150,'INDIA','315','INDIA'),(151,'INDONESIA','316','INDONESIA'),(152,'IRAK','317','IRAK'),(153,'IRAN','318','IRAN'),(154,'ISRAEL','319','ISRAEL'),(155,'JAPON','320','JAPON'),(156,'JORDANIA','321','JORDANIA'),(157,'QATAR','322','QATAR'),(158,'KUWAIT','323','KUWAIT'),(159,'LAOS','324','LAOS'),(160,'LIBANO','325','LIBANO'),(161,'MALASIA','326','MALASIA'),(162,'MALDIVAS ISLAS','327','MALDIVAS ISLAS'),(163,'OMAN','328','OMAN'),(164,'MONGOLIA','329','MONGOLIA'),(165,'NEPAL','330','NEPAL'),(166,'EMIRATOS ARABES,UNID','331','EMIRATOS ARABES,UNID'),(167,'PAKISTAN','332','PAKISTAN'),(168,'SINGAPUR','333','SINGAPUR'),(169,'SIRIA','334','SIRIA'),(170,'THAILANDIA','335','THAILANDIA'),(171,'TURQUIA','336','TURQUIA'),(172,'VIETNAM','337','VIETNAM'),(173,'HONG KONG','341','REG.ADM.ESP. DE CHINA'),(174,'MACAO','344','MACAO(REG.ADM.ESPEC)'),(175,'BANGLADESH','345','BANGLADESH'),(176,'BRUNEI','346','BRUNEI'),(177,'REPUBLICA DE YEMEN','348','REPUBLICA DE YEMEN'),(178,'ARMENIA','349','ARMENIA'),(179,'AZERBAIJAN','350','AZERBAIJAN'),(180,'GEORGIA','351','GEORGIA'),(181,'KAZAJSTAN','352','KAZAJSTAN'),(182,'KIRGUIZISTAN','353','KIRGUIZISTAN'),(183,'TAYIKISTAN','354','TAYIKISTAN'),(184,'TURKMENISTAN','355','TURKMENISTAN'),(185,'UZBEKISTAN','356','UZBEKISTAN'),(186,'TERR. AU. PALESTINOS','357','GAZA Y JERICO'),(187,'TIMOR ORIENTAL','358',NULL),(188,'RESTO DE ASIA','397','RESTO DE ASIA'),(189,'INDET.(ASIA)','398','INDET.(ASIA)'),(190,'ALBANIA','401','ALBANIA'),(191,'ALEMANIA FEDERAL','402','ALEMANIA FEDERAL'),(192,'ALEMANIA ORIENTAL','403','ALEMANIA ORIENTAL'),(193,'ANDORRA','404','ANDORRA'),(194,'AUSTRIA','405','AUSTRIA'),(195,'BELGICA','406','BELGICA'),(196,'BULGARIA','407','BULGARIA'),(197,'CHECOSLOVAQUIA','408','CHECOSLOVAQUIA'),(198,'DINAMARCA','409','DINAMARCA'),(199,'ESPAÑA','410','ESPAÑA'),(200,'FINLANDIA','411','FINLANDIA'),(201,'FRANCIA','412','FRANCIA'),(202,'GRECIA','413','GRECIA'),(203,'HUNGRIA','414','HUNGRIA'),(204,'IRLANDA','415','IRLANDA'),(205,'ISLANDIA','416','ISLANDIA'),(206,'ITALIA','417','ITALIA'),(207,'LIECHTENSTEIN','418','LIECHTENSTEIN'),(208,'LUXEMBURGO','419','LUXEMBURGO'),(209,'MALTA','420','MALTA'),(210,'MONACO','421','MONACO'),(211,'NORUEGA','422','NORUEGA'),(212,'PAISES BAJOS','423','PAISES BAJOS'),(213,'POLONIA','424','POLONIA'),(214,'PORTUGAL','425','PORTUGAL'),(215,'REINO UNIDO','426','REINO UNIDO'),(216,'RUMANIA','427','RUMANIA'),(217,'SAN MARINO','428','SAN MARINO'),(218,'SUECIA','429','SUECIA'),(219,'SUIZA','430','SUIZA'),(220,'VATICANO(SANTA SEDE)','431','VATICANO(SENTA SEDE)'),(221,'YUGOSLAVIA','432',NULL),(222,'POS.BRIT.(EUROPA)','433','POS.BRIT.(EUROPA)'),(223,'HOLANDA','434','HOLANDA'),(224,'CHIPRE','435','CHIPRE'),(225,'TURQUIA','436','TURQUIA'),(226,'ALEMANIA,REP.FED.','438','ALEMANIA,REP.FED.'),(227,'BIELORRUSIA','439','BIELORRUSIA'),(228,'ESTONIA','440','ESTONIA'),(229,'LETONIA','441','LETONIA'),(230,'LITUANIA','442','LITUANIA'),(231,'MOLDAVIA','443','MOLDAVIA'),(232,'RUSIA','444','RUSIA'),(233,'UCRANIA','445','UCRANIA'),(234,'BOSNIA HERZEGOVINA','446','BOSNIA HERZEGOVINA'),(235,'CROACIA','447','CROACIA'),(236,'ESLOVAQUIA','448','ESLOVAQUIA'),(237,'ESLOVENIA','449','ESLOVENIA'),(238,'MACEDONIA','450','MACEDONIA'),(239,'REP. CHECA','451','REP. CHECA'),(240,'FED. SER Y MONT YOGOE','452',NULL),(241,'MONTENEGRO','453','MONTENEGRO'),(242,'SERBIA','454','SERBIA'),(243,'RESTO EUROPA','497','RESTO EUROPA'),(244,'INDET.(EUROPA)','498','INDET.(EUROPA)'),(245,'AUSTRALIA','501','AUSTRALIA'),(246,'NAURU','503','NAURU'),(247,'NUEVA ZELANDIA','504','NUEVA ZELANDIA'),(248,'VANATU','505','VANATU'),(249,'SAMOA OCCIDENTAL','506','SAMOA OCCIDENTAL'),(250,'TERRITORIO VINCULADOS A AUSTRALIA','507','OCEANIA'),(251,'TERRITORIOS VINCULADOS AL R. UNIDO','508','OCEANIA'),(252,'TERRITORIOS VINCULADOS A FRANCIA','509','OCEANIA'),(253,'TER VINCULADOS A NUEVA. ZELANDA','510','OCEANIA'),(254,'TER. VINCULADOS A ESTADOS UNIDOS','511','OCEANIA'),(255,'FIJI, ISLAS','512','FIJI, ISLAS'),(256,'PAPUA NUEVA GUINEA','513','PAPUA NUEVA GUINEA'),(257,'KIRIBATI, ISLAS','514','KIRIBATI, ISLAS'),(258,'MICRONESIA,EST.FEDER','515','MICRONESIA,EST.FEDER'),(259,'PALAU','516','PALAU'),(260,'TUVALU','517','TUVALU'),(261,'SALOMON,ISLAS','518','SALOMON,ISLAS'),(262,'TONGA','519','TONGA'),(263,'MARSHALL,ISLAS','520','MARSHALL,ISLAS'),(264,'MARIANAS,ISLAS','521','MARIANAS,ISLAS'),(265,'RESTO OCEANIA','597','RESTO OCEANIA'),(266,'INDET.(OCEANIA)','598','INDET.(OCEANIA)'),(267,'URSS','601','URSS'),(268,'ANGUILA (TERRITORIO NO AUTONOMO DEL R. UNIDO)','652','ANGUILA (TERRITORIO NO AUTONOMO DEL R. UNIDO)'),(269,'ARUBA (TERRITORIO DE PAISES BAJOS)','653',NULL),(270,'ISLA DE COOK (TERRITORIO AUTONOMO ASOCIADO A ','654',NULL),(271,'PATAU','655',NULL),(272,'POLINESI FRANCESA (TERRITORIO DE ULTRAMAR DE ','656',NULL),(273,'ANTILLAS HOLANDESAS (TERRITORIO DE PAISES BAJ','659',NULL),(274,'ASCENCION','662',NULL),(275,'BERMUDAS (TERRITORIO NO AUTONOMO DEL R UNIDO)','663',NULL),(276,'CAMPIONE DITALIA','664',NULL),(277,'COLONIA DE GIBRALTAR','665',NULL),(278,'GROENLANDIA','666',NULL),(279,'GUAM (TERRITORIO NO AUTONOMO DE LOS ESTADO UN','667',NULL),(280,'HONG KONG (TERRITORIO DE CHINA)','668',NULL),(281,'ISLAS AZORES','669',NULL),(282,'ISLAS DEL CANAL (GUERNESEY, JERSEY, ALDERNEY,','670',NULL),(283,'ISLAS CAIMAN (TERRITORIO NO AUTONOMO DE R UNI','671',NULL),(284,'ISLA CHRISTMAS','672',NULL),(285,'ISLA DE COCOS O KEELING','673',NULL),(286,'ISLA DE MAN (TERRITORIO DEL REINO UNIDO)','676',NULL),(287,'ISLA DE NORFOLK (TERRITORIO DEL R UNIDO)','677',NULL),(288,'ISALAS TURKAS Y CAICOS (TERRITORIO NO AUTONOM','678',NULL),(289,'ISLAS PACIFICO','679',NULL),(290,'ISLAS DE SAN PEDRO Y MIGUELON','680',NULL),(291,'ISLA QESHM','681',NULL),(292,'ISLAS VIRGENES BRITANICAS (TERRITORIO NO AUTO','682',NULL),(293,'ISLAS VIRGENES DE ESTADOS UNIDOS DE AMERICA','683',NULL),(294,'LABUAM','684',NULL),(295,'MADEIRA (TERRITORIO DE PORTUGAL)','685',NULL),(296,'MONSERRAT (TERRITORIO NO AUTONOMO DEL REINO U','686',NULL),(297,'NIUE ','687',NULL),(298,'PITCAIRN','690',NULL),(299,'REGIMEN APLICABLE A LAS SA FINANCIERAS (LEY 1','693',NULL),(300,'SANTA ELENA','694',NULL),(301,'SAMAO AMERICANA (TERRITORIO NO AUTONOMO DE LO','695',NULL),(302,'ARCHIPIELAGO DE SVBALBARD','696',NULL),(303,'TRISTAN DACUNHA','697',NULL),(304,'TRIESTE (ITALIA)','698',NULL),(305,'TOKELAU','699',NULL),(306,'ZONA LIBRE DE OSTRAVA (CIUDAD DE LA ATIGUA CH','700',NULL),(307,'RESTO CONTINENTE','997','RESTO CONTINENTE'),(308,'INDET.(CONTINENTE)','998','INDET.(CONTINENTE)'),(309,'OTROS PAISES','999',NULL);
/*!40000 ALTER TABLE `AfipPaises` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipProvincias` WRITE;
/*!40000 ALTER TABLE `AfipProvincias` DISABLE KEYS */;
INSERT INTO `AfipProvincias` VALUES (1,'Ciudad Autónoma de Buenos Aires','0'),(2,'Buenos Aires','1'),(3,'Catamara','2'),(4,'Córdoba','3'),(5,'Corrientes','4'),(6,'Entre Ríos','5'),(7,'Jujuy','6'),(8,'Mendoza','7'),(9,'La Rioja','8'),(10,'Salta','9'),(11,'San Juan','10'),(12,'San Luis','11'),(13,'Santa Fe','12'),(14,'Santiago del Estero','13'),(15,'Tucumán','14'),(16,'Chaco','16'),(17,'Chubut','17'),(18,'Formosa','18'),(19,'Misiones','19'),(20,'Neuquén','20'),(21,'La Pampa','21'),(22,'Río Negro','22'),(23,'Santa Cruz','23'),(24,'Tierra del Fuego','24');
/*!40000 ALTER TABLE `AfipProvincias` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipSituacionesDeRevistas` WRITE;
/*!40000 ALTER TABLE `AfipSituacionesDeRevistas` DISABLE KEYS */;
INSERT INTO `AfipSituacionesDeRevistas` VALUES (1,'Activo',1,'01'),(2,'Baja por Fallecimiento',1,'00'),(3,'Baja otras causales',1,'02'),(4,'Activo Decreto N° 796/97',1,'03'),(5,'Bajas otras causales Decreto N° 796/97',1,'04'),(6,'Licencia por maternidad',1,'05'),(7,'Suspensiones otras causales',1,'06'),(8,'Baja por despido',1,'07'),(9,'Baja por despido Decreto N° 796/97',1,'08'),(10,'Suspendido. Art. 223 bis de la Ley N° 20.744.',1,'09'),(11,'Licencia por excedencia ',0,'10'),(12,'Licencia por maternidad Down',1,'11'),(13,'Licencia por vacaciones ',0,'12'),(14,'Licencia sin goce de haberes ',1,'13'),(15,'Reserva de puesto',1,'14'),(16,'E.S.E. Cese transitorio de servicios (Art. 6°, incisos 6 y 7 del Decreto N° 342/92)',1,'15'),(17,'Personal Siniestrado de terceros',1,'16'),(18,'Reingreso por disposición judicial',1,'17'),(19,'ILT (Incapacidad Laboral Transitoria) primeros DIEZ (10) días',1,'18'),(20,'ILT (Incapacidad Laboral Transitoria) días ONCE (11) y siguientes',1,'19'),(21,'Trabajador siniestrado en nómina de A.R.T',1,'20');
/*!40000 ALTER TABLE `AfipSituacionesDeRevistas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipTiposDeComprobantes` WRITE;
/*!40000 ALTER TABLE `AfipTiposDeComprobantes` DISABLE KEYS */;
INSERT INTO `AfipTiposDeComprobantes` VALUES (1,'FACTURAS A',1),(2,'NOTAS DE DEBITO A',2),(3,'NOTAS DE CREDITO A',3),(4,'RECIBOS A',4),(5,'NOTAS DE VENTA AL CONTADO A',5),(6,'FACTURAS B',6),(7,'NOTAS DE DEBITO B',7),(8,'NOTAS DE CREDITO B',8),(9,'RECIBOS B',9),(10,'NOTAS DE VENTA AL CONTADO B',10),(11,'FACTURAS C',11),(12,'NOTAS DE DEBITO C',12),(13,'NOTAS DE CREDITO C',13),(14,'DOCUMENTO ADUANERO',14),(15,'RECIBOS C',15),(16,'NOTAS DE VENTA AL CONTADO C',16),(17,'FACTURAS DE EXPORTACION',19),(18,'NOTAS DE DEBITO POR OPERACIONES CON EL EXTERIOR',20),(19,'NOTAS DE CREDITO POR OPERACIONES CON EL EXTERIOR',21),(20,'FACTURAS - PERMISO EXPORTACION SIMPLIFICADO - DTO. 855/97',22),(21,'COMPROBANTES DE COMPRA DE BIENES USADOS',30),(22,'MANDATO - CONSIGNACION',31),(23,'COMPROBANTES PARA RECICLAR MATERIALES',32),(24,'COMPROBANTES A DEL APARTADO A  INCISO F  R G  N  1415',34),(25,'COMPROBANTES B DEL ANEXO I, APARTADO A, INC. F), RG N° 1415',35),(26,'COMPROBANTES C DEL Anexo I, Apartado A, INC.F), R.G. N° 1415',36),(27,'NOTAS DE DEBITO O DOCUMENTO EQUIVALENTE QUE CUMPLAN CON LA R.G. N° 1415',37),(28,'NOTAS DE CREDITO O DOCMENTO EQUIVALENTE QUE CUMPLAN CON LA R.G. N° 1415',38),(29,'OTROS COMPROBANTES A QUE CUMPLEN CON LA R G  1415',39),(30,'OTROS COMPROBANTES B QUE CUMPLAN CON LA R.G. N° 1415',40),(31,'OTROS COMPROBANTES C QUE CUMPLAN CON LA R.G. N° 1415',41),(32,'RECIBO FACTURA A  REGIMEN DE FACTURA DE CREDITO ',50),(33,'FACTURAS M',51),(34,'NOTAS DE DEBITO M',52),(35,'NOTAS DE CREDITO M',53),(36,'RECIBOS M',54),(37,'NOTAS DE VENTA AL CONTADO M',55),(38,'COMPROBANTES M DEL ANEXO I  APARTADO A  INC F   R G  N  1415',56),(39,'OTROS COMPROBANTES M QUE CUMPLAN CON LA R G  N  1415',57),(40,'CUENTAS DE VENTA Y LIQUIDO PRODUCTO M',58),(41,'LIQUIDACIONES M',59),(42,'CUENTAS DE VENTA Y LIQUIDO PRODUCTO A',60),(43,'CUENTAS DE VENTA Y LIQUIDO PRODUCTO B',61),(44,'LIQUIDACIONES A',63),(45,'LIQUIDACIONES B',64),(46,'NOTAS DE CREDITO DE COMPROBANTES CON COD. 34, 39, 58, 59, 60, 63, 96, 97 ',65),(47,'DESPACHO DE IMPORTACION',66),(48,'IMPORTACION DE SERVICIOS',67),(49,'LIQUIDACION C',68),(50,'RECIBOS FACTURA DE CREDITO',70),(51,'CREDITO FISCAL POR CONTRIBUCIONES PATRONALES',71),(52,'FORMULARIO 1116 RT',73),(53,'CARTA DE PORTE PARA EL TRANSPORTE AUTOMOTOR PARA GRANOS',74),(54,'CARTA DE PORTE PARA EL TRANSPORTE FERROVIARIO PARA GRANOS',75),(55,NULL,77),(56,NULL,78),(57,NULL,79),(58,'COMPROBANTE DIARIO DE CIERRE (ZETA)',80),(59,'TIQUE FACTURA A   CONTROLADORES FISCALES',81),(60,'TIQUE - FACTURA B',82),(61,'TIQUE',83),(62,'COMPROBANTE   FACTURA DE SERVICIOS PUBLICOS   INTERESES FINANCIEROS',84),(63,'NOTA DE CREDITO   SERVICIOS PUBLICOS   NOTA DE CREDITO CONTROLADORES FISCALES',85),(64,'NOTA DE DEBITO   SERVICIOS PUBLICOS',86),(65,'OTROS COMPROBANTES - SERVICIOS DEL EXTERIOR',87),(66,'OTROS COMPROBANTES - DOCUMENTOS EXCEPTUADOS / REMITO ELECTRONICO ',88),(67,'OTROS COMPROBANTES - DOCUMENTOS EXCEPTUADOS - NOTAS DE DEBITO / RESUMEN DE DATOS',89),(68,'OTROS COMPROBANTES - DOCUMENTOS EXCEPTUADOS - NOTAS DE CREDITO',90),(69,'REMITOS R',91),(70,'AJUSTES CONTABLES QUE INCREMENTAN EL DEBITO FISCAL',92),(71,'AJUSTES CONTABLES QUE DISMINUYEN EL DEBITO FISCAL',93),(72,'AJUSTES CONTABLES QUE INCREMENTAN EL CREDITO FISCAL',94),(73,'AJUSTES CONTABLES QUE DISMINUYEN EL CREDITO FISCAL',95),(74,'FORMULARIO 1116 B',96),(75,'FORMULARIO 1116 C',97),(76,'OTROS COMP  QUE NO CUMPLEN CON LA R G  3419 Y SUS MODIF ',99),(77,'AJUSTE ANUAL PROVENIENTE DE LA  D J  DEL IVA  POSITIVO ',101),(78,'AJUSTE ANUAL PROVENIENTE DE LA  D J  DEL IVA  NEGATIVO ',102),(79,'NOTA DE ASIGNACION',103),(80,'NOTA DE CREDITO DE ASIGNACION',104);
/*!40000 ALTER TABLE `AfipTiposDeComprobantes` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipTiposDeDocumentos` WRITE;
/*!40000 ALTER TABLE `AfipTiposDeDocumentos` DISABLE KEYS */;
INSERT INTO `AfipTiposDeDocumentos` VALUES (1,'CI Policía Federal','0'),(2,'CI Buenos Aires','1'),(3,'CI Catamarca','2'),(4,'CI Córdoba','3'),(5,'CI Corrientes','4'),(6,'CI Entre Ríos','5'),(7,'CI Jujuy','6'),(8,'CI Mendoza','7'),(9,'CI La Rioja','8'),(10,'CI Salta','9'),(11,'CI San Juan','10'),(12,'CI San Luis','11'),(13,'CI Santa Fe','12'),(14,'CI Santiago del Estero','13'),(15,'CI Tucumán','14'),(16,'CI Chaco','16'),(17,'CI Chubut','17'),(18,'CI Formosa','18'),(19,'CI Misiones','19'),(20,'CI Neuquén','20'),(21,'CI La Pampa','21'),(22,'CI Río Negro','22'),(23,'CI Santa Cruz','23'),(24,'CI Tierra del Fuego','24'),(25,'Certificado de Migración','30'),(26,'CUIT','80'),(27,'CUIL','86'),(28,'CDI','87'),(29,'Usado por Anses para Padrón','88'),(30,'LE','89'),(31,'LC','90'),(32,'CI extranjera','91'),(33,'en trámite','92'),(34,'Acta nacimiento','93'),(35,'Pasaporte','94'),(36,'CI Bs. As. RNP','95'),(37,'DNI','96'),(38,'Sin identificar/venta global diaria','99');
/*!40000 ALTER TABLE `AfipTiposDeDocumentos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipTiposDeResponsables` WRITE;
/*!40000 ALTER TABLE `AfipTiposDeResponsables` DISABLE KEYS */;
INSERT INTO `AfipTiposDeResponsables` VALUES (1,'IVA Responsable Inscripto','1'),(2,'IVA Responsable no Inscripto','2'),(3,'IVA no Responsable','3'),(4,'IVA Sujeto Exento','4'),(5,'Consumidor Final','5'),(6,'Responsable Monotributo','6'),(7,'Sujeto no Categorizado','7'),(8,'Proveedor del Exterior','8'),(9,'Cliente del Exterior','9'),(10,'IVA Liberado – Ley Nº 19.640','10'),(11,'IVA Responsable Inscripto – Agente de Percepc','11'),(12,'Pequeño Contribuyente Eventual','12'),(13,'Monotributista Social','13'),(14,'Pequeño Contribuyente Eventual Social','14');
/*!40000 ALTER TABLE `AfipTiposDeResponsables` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipTiposDeSujetos` WRITE;
/*!40000 ALTER TABLE `AfipTiposDeSujetos` DISABLE KEYS */;
INSERT INTO `AfipTiposDeSujetos` VALUES (1,'Juridica','0'),(2,'Fisica','1'),(3,'Otro tipo de Entidad','2');
/*!40000 ALTER TABLE `AfipTiposDeSujetos` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `AfipUnidadesDeMedidas` WRITE;
/*!40000 ALTER TABLE `AfipUnidadesDeMedidas` DISABLE KEYS */;
INSERT INTO `AfipUnidadesDeMedidas` VALUES (1,'SIN DESCRIPCION','00'),(2,'KILOGRAMO','01'),(3,'METROS','02'),(4,'METRO CUADRADO','03'),(5,'METRO CUBICO','04'),(6,'LITROS','05'),(7,'1000 KILOWATT HORA','06'),(8,'UNIDAD','07'),(9,'PAR','08'),(10,'DOCENA','09'),(11,'QUILATE','10'),(12,'MILLAR','11'),(13,'MEGA U. INTER. ACT. ANTIB','12'),(14,'UNIDAD INT. ACT. INMUNG','13'),(15,'GRAMO','14'),(16,'MILIMETRO','15'),(17,'MILIMETRO CUBICO','16'),(18,'KILOMETRO','17'),(19,'HECTOLITRO','18'),(20,'MEGA UNIDAD INT. ACT. INMUNG','19'),(21,'CENTIMETRO','20'),(22,'KILOGRAMO ACTIVO','21'),(23,'GRAMO ACTIVO','22'),(24,'GRAMO BASE','23'),(25,'UIACTHOR','24'),(26,'JGO.PQT. MAZO NAIPES','25'),(27,'MUIACTHOR','26'),(28,'CENTIMETRO CUBICO','27'),(29,'UIACTANT','28'),(30,'TONELADA','29'),(31,'DECAMETRO CUBICO','30'),(32,'HECTOMETRO CUBICO','31'),(33,'KILOMETRO CUBICO','32'),(34,'MICROGRAMO','33'),(35,'NANOGRAMO','34'),(36,'PICOGRAMO','35'),(37,'MUIACTANT','36'),(38,'UIACTIG','37'),(39,'MILIGRAMO','41'),(40,'MILILITRO','47'),(41,'CURIE','48'),(42,'MILICURIE','49'),(43,'MICROCURIE','50'),(44,'U.INTER. ACT. HORMONAL','51'),(45,'MEGA U. INTER. ACT. HOR.','52'),(46,'KILOGRAMO BASE','53'),(47,'GRUESA','54'),(48,'MUIACTIG','55'),(49,'KILOGRAMO BRUTO','61'),(50,'PACK','62'),(51,'HORMA','63'),(52,'SEÑAS/ANTICIPOS','97'),(53,'OTRAS UNIDADES','98'),(54,'BONIFICACION','99');
/*!40000 ALTER TABLE `AfipUnidadesDeMedidas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `Idiomas` WRITE;
/*!40000 ALTER TABLE `Idiomas` DISABLE KEYS */;
INSERT INTO `Idiomas` VALUES (1,'Español',1),(2,'Ingles',2),(3,'Portugues',3);
/*!40000 ALTER TABLE `Idiomas` ENABLE KEYS */;
UNLOCK TABLES;
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

LOCK TABLES `PaisesCuit` WRITE;
/*!40000 ALTER TABLE `PaisesCuit` DISABLE KEYS */;
INSERT INTO `PaisesCuit` VALUES (1,2,2);
/*!40000 ALTER TABLE `PaisesCuit` ENABLE KEYS */;
UNLOCK TABLES;
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

