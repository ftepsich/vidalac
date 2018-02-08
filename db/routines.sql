
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
/*!40103 SET TIME_ZONE=@OLD_TIME_ZONE */;

/*!40101 SET SQL_MODE=@OLD_SQL_MODE */;
/*!40014 SET FOREIGN_KEY_CHECKS=@OLD_FOREIGN_KEY_CHECKS */;
/*!40014 SET UNIQUE_CHECKS=@OLD_UNIQUE_CHECKS */;
/*!40101 SET CHARACTER_SET_CLIENT=@OLD_CHARACTER_SET_CLIENT */;
/*!40101 SET CHARACTER_SET_RESULTS=@OLD_CHARACTER_SET_RESULTS */;
/*!40101 SET COLLATION_CONNECTION=@OLD_COLLATION_CONNECTION */;
/*!40111 SET SQL_NOTES=@OLD_SQL_NOTES */;

