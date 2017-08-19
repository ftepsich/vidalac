DROP FUNCTION IF EXISTS fCompPago_MontoNG_aPagar;

delimiter //

CREATE DEFINER = 'root'@'127.0.0.1' FUNCTION fCompPago_MontoNG_aPagar(idComprobante INTEGER(11))
 RETURNS decimal(16,4)
BEGIN
DECLARE Monto               DECIMAL(16,4);
DECLARE GrupoPadre          INTEGER;
DECLARE MultiplicadorPadre  INTEGER;
DECLARE Cerrado             INTEGER;
    
SELECT  TC.Grupo, C.Cerrado 
INTO    GrupoPadre, Cerrado
FROM    TiposDeComprobantes TC
        INNER JOIN Comprobantes C on C.TipoDeComprobante = TC.Id
WHERE   C.Id = idComprobante;

IF (GrupoPadre = 11) THEN 
    set MultiplicadorPadre = -1;
ELSE
    IF (GrupoPadre = 9) THEN 
        set MultiplicadorPadre = 1;
    ELSE
        set MultiplicadorPadre = 0;
    END IF;
END IF;

IF ( Cerrado = 0 ) THEN
        SELECT  SUM(CASE WHEN TC.Id = 49  THEN (fComprobante_NetoGravado(CR.ComprobanteHijo)*TC.Multiplicador) ELSE (fComprobante_NetoGravado(CR.ComprobanteHijo)*TC.Multiplicador*MultiplicadorPadre) END)
        INTO    Monto
        FROM    ComprobantesRelacionados CR
                INNER JOIN Comprobantes CH          on CH.Id = CR.ComprobanteHijo
                INNER JOIN TiposDeComprobantes TC   on TC.Id = CH.TipoDeComprobante
        WHERE   CR.ComprobantePadre = idComprobante;
ELSE
        
        SELECT  SUM(fComprobante_NetoGravado(CR.ComprobanteHijo)*TC.Multiplicador*MultiplicadorPadre)
        INTO    Monto
        FROM    ComprobantesRelacionados CR
                INNER JOIN Comprobantes CH          on CH.Id = CR.ComprobanteHijo
                INNER JOIN TiposDeComprobantes TC   on TC.Id = CH.TipoDeComprobante
        WHERE   CR.ComprobantePadre = idComprobante;
END IF;

RETURN ROUND(Monto,4);

END//

delimiter ;

DROP TABLE IF EXISTS `ComprobantesRetenciones`;

CREATE TABLE `ComprobantesRetenciones` (
  `Periodo` int(11) unsigned NOT NULL,
  `Numero`  int(11) unsigned NOT NULL,
  `Comprobante` int(11) unsigned NOT NULL,
  `ComprobantePadre` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Periodo`,`Numero`),
  KEY `Comprobante` (`Comprobante`),
  KEY `ComprobantePadre` (`ComprobantePadre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;

DROP PROCEDURE IF EXISTS `Birt_Retenciones_Cabecera`;

delimiter //

CREATE DEFINER = `root`@`127.0.0.1` PROCEDURE `Birt_Retenciones_Cabecera`(IN idComprobante INTEGER(11))
BEGIN 

DECLARE _found int;

SELECT COUNT(*) INTO _found FROM  ComprobantesRetenciones WHERE ComprobantePadre = idComprobante;

IF ( NOT _found ) THEN 
INSERT INTO ComprobantesRetenciones ( Periodo, Numero, Comprobante, ComprobantePadre ) 
SELECT YEAR(c.FechaEmision) as Periodo,
       ( SELECT IFNULL(MAX(Numero),0)+1 
       FROM ComprobantesRetenciones
       WHERE Periodo = YEAR(c.FechaEmision) ) as Numero, 
       c.Id as Comprobante, 
       c.ComprobantePadre
FROM Comprobantes c
INNER JOIN Comprobantes c2         ON c.ComprobantePadre = c2.Id
LEFT  JOIN ConceptosImpositivos ci ON c.ConceptoImpositivo = ci.Id
WHERE c2.id = idComprobante
  AND ci.EsRetencion = 1
  AND c2.Cerrado = 1 
  AND c2.Anulado = 0
ORDER BY c.FechaEmision
LIMIT 1;
END IF;

SELECT p.RazonSocial,
       p.Denominacion,
       p.Cuit, 
       p.ModalidadIva, 
       miva.Descripcion as ModalidadIvaDescripcion,
       p.ModalidadGanancia, 
       tig.Descripcion  as ModalidadGananciaDecripcion, 
       p.TipoInscripcionIB, 
       tib.Descripcion  as TipoInscripcionIBDescripcion,
       CASE WHEN p.TipoInscripcionIB = '3' THEN 'X' ELSE ' ' END as ConvenioMultilateral,
       p.NroInscripcionIB,
       c.ConceptoImpositivo, 
       ci.Descripcion as ConceptoImpositivoDescripcion,
       c.ConceptoImpositivoPorcentaje,
       DATE_FORMAT(c.FechaEmision,'%d/%m/%Y') as FechaEmision,
       c.MontoImponible,
       c.Monto,
       CONCAT(r.Periodo,"-",r.Numero) as NroConstancia
FROM Comprobantes c
INNER JOIN Comprobantes c2                   ON c.ComprobantePadre = c2.Id
INNER JOIN Personas p                        ON c.Persona = p.Id 
LEFT  JOIN TiposDeInscripcionesIB tib        ON p.TipoInscripcionIB = tib.id 
LEFT  JOIN TiposDeInscripcionesGanancias tig ON p.ModalidadGanancia = tig.Id
LEFT  JOIN ModalidadesIVA miva               ON p.ModalidadIva = miva.Id
LEFT  JOIN ConceptosImpositivos ci           ON c.ConceptoImpositivo = ci.Id
INNER JOIN ComprobantesRetenciones r         ON r.Comprobante = c.Id 
WHERE c2.id = idComprobante
  AND ci.EsRetencion = 1
  AND c2.Cerrado = 1 
  AND c2.Anulado = 0
ORDER BY c.FechaEmision
LIMIT 1;

END//

delimiter ;

DROP PROCEDURE IF EXISTS `Birt_Retenciones_CompAsociados`;

delimiter //

CREATE DEFINER = `root`@`127.0.0.1` PROCEDURE `Birt_Retenciones_CompAsociados`(IN idComprobante INTEGER(11))
BEGIN 
SELECT  C.Id                                        AS IdComprobante, 
        TGC.Codigo                                  AS Codigo,
        TC.Descripcion                              AS Descripcion,
        fSigno_Comprobante_xID(CR.ComprobantePadre,CR.ComprobanteHijo)*fComprobante_Monto_Total(CR.ComprobanteHijo) AS Monto,
        CONCAT(LPAD(C.Punto , 4, '0'),'-',LPAD(C.Numero, 8, '0')) AS Numero,
        fNumeroCompleto(CR.ComprobanteHijo,'CG')    AS NumeroCompleto,
        CR.ComprobanteHijo                          AS Hijo,
        CR.ComprobantePadre                         AS Padre,
        DATE_FORMAT(C.FechaEmision,'%d/%m/%Y')      AS FechaEmision,
        (IF (   ifnull(CONVERT(C.FechaCierre,CHAR),'0000-00-00 00:00:00') = '0000-00-00 00:00:00',
                NOW(),
                C.FechaCierre)
        )                                           AS FechaCierre  
FROM    ComprobantesRelacionados CR    
        INNER JOIN Comprobantes C                   ON C.Id     = CR.ComprobanteHijo
        INNER JOIN TiposDeComprobantes TC           ON TC.Id    = C.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC  ON TGC.Id   = TC.Grupo
WHERE   CR.ComprobantePadre = idComprobante
AND     C.Cerrado = 1 
AND     C.Anulado = 0
UNION
SELECT  C1.Id                                       AS IdComprobante,
        TGC1.Codigo                                 AS Codigo,
        CONCAT('.    ',TC1.Descripcion)             AS Descripcion,
        (fSigno_Comprobante_xID(CR1.ComprobantePadre,CR1.ComprobanteHijo)*(-1)*CR1.MontoAsociado) AS Monto,
        CONCAT(LPAD(C1.Punto , 4, '0'),'-',LPAD(C1.Numero, 8, '0')) AS Numero,
        CONCAT( cast('.       Monto de '    AS CHAR CHARSET utf8),
                cast(fNumeroCompleto(CR1.ComprobanteHijo,'C') AS CHAR CHARSET utf8),
                cast(' liquidado en ' AS CHAR CHARSET utf8),
                cast(fNumeroCompleto(CR1.ComprobantePadre,'C') AS CHAR CHARSET utf8)
        )                                           AS NumeroCompleto,
        CR1.ComprobanteHijo                         AS Hijo,
        CR1.ComprobantePadre                        AS Padre,
        DATE_FORMAT(C1.FechaEmision,'%d/%m/%Y')     AS FechaEmision,
        C1.FechaCierre                              AS FechaCierre
FROM    ComprobantesRelacionados CR1    
        INNER JOIN Comprobantes C1                  ON C1.Id    = CR1.ComprobantePadre
        INNER JOIN TiposDeComprobantes TC1          ON TC1.Id   = C1.TipoDeComprobante
        INNER JOIN TiposDeGruposDeComprobantes TGC1 ON TGC1.Id  = TC1.Grupo
WHERE   C1.FechaCierre <= ( SELECT IF (
                    (   SELECT ifnull(CONVERT(FechaCierre,CHAR),'0000-00-00 00:00:00') 
                        FROM Comprobantes WHERE Id = idComprobante) = '0000-00-00 00:00:00',
                    NOW(),
                    (SELECT FechaCierre FROM Comprobantes WHERE Id = idComprobante)
                      )
        )
AND     C1.Cerrado = 1 
AND     C1.Anulado = 0
AND     CR1.ComprobanteHijo IN (SELECT ComprobanteHijo FROM ComprobantesRelacionados WHERE ComprobantePadre = idComprobante)
AND     CR1.ComprobantePadre <> idComprobante
ORDER BY Hijo,FechaCierre ASC, Padre ASC;
END//

delimiter ;

CREATE TABLE `ComprobantesRetenciones` (
  `Periodo` int(11) unsigned NOT NULL,
  `Numero`  int(11) unsigned NOT NULL,
  `Comprobante` int(11) unsigned NOT NULL,
  `ComprobantePadre` int(11) unsigned NOT NULL,
  PRIMARY KEY (`Periodo`,`Numero`),
  KEY `Comprobante` (`Comprobante`),
  KEY `ComprobantePadre` (`ComprobantePadre`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8;


DROP FUNCTION IF EXISTS `fCompPago_Monto_Retencion`;

delimiter //

CREATE DEFINER = `root`@`127.0.0.1` FUNCTION `fCompPago_Monto_Retencion`(idComprobante INTEGER(11))
 RETURNS decimal(16,4)
BEGIN  

DECLARE Monto               DECIMAL(16,4);
    
SELECT IFNULL(SUM(c.Monto),0)
INTO   Monto
FROM Comprobantes c
INNER JOIN Comprobantes c2                   ON c.ComprobantePadre = c2.Id
INNER JOIN Personas p                        ON c.Persona = p.Id 
LEFT  JOIN TiposDeInscripcionesIB tib        ON p.TipoInscripcionIB = tib.id 
LEFT  JOIN TiposDeInscripcionesGanancias tig ON p.ModalidadGanancia = tig.Id
LEFT  JOIN ModalidadesIVA miva               ON p.ModalidadIva = miva.Id
LEFT  JOIN ConceptosImpositivos ci           ON c.ConceptoImpositivo = ci.Id
INNER JOIN ComprobantesRetenciones r         ON r.Comprobante = c.Id 
WHERE c2.id = idComprobante
  AND ci.EsRetencion = 1
  AND c2.Cerrado = 1 
  AND c2.Anulado = 0;

RETURN ROUND(Monto,4);

END//

delimiter ;

DROP PROCEDURE IF EXISTS `Birt_CtaCte_Detalle`;

delimiter //

CREATE DEFINER = `root`@`127.0.0.1` PROCEDURE `Birt_CtaCte_Detalle`(IN idPersona INTEGER(11),
        IN desde DATE,
        IN hasta DATE,
        IN tipo INTEGER(11))
BEGIN

DECLARE fechaDesde DATE;
DECLARE fechaHasta DATE;

IF (desde = '1900/01/01') THEN 
	SET fechaDesde = '2000-01-01';
ELSE 
	SET fechaDesde = desde;
END IF;

IF (hasta = '1900/01/01') THEN 
	SET fechaHasta = '2099-01-01'; 
ELSE 
	SET fechaHasta = hasta; 
END IF;

IF (tipo = 1) THEN

  SELECT 	' ' AS NroComprobante,
          ' ' AS FechaComprobante,
          IFNULL((SUM(CC.Debe)),0) AS Debe, 
          IFNULL((SUM(CC.Haber)),0) AS Haber,
          1 AS Orden
	FROM 	CuentasCorrientes CC 
  INNER JOIN TiposDeComprobantes TC on TC.Id = CC.TipoDeComprobante
	WHERE 	CC.Persona = idPersona 
    AND		CC.FechaComprobante < fechaDesde
	  AND 	(
						(TC.Grupo IN (6,7,11,12) and TC.Id not in (65,66))
						OR (`TC`.`Id` IN (72,73,74,75,76,82,83,84,85,86))
						OR (fNumeroCompleto(CC.Comprobante,'S') COLLATE utf8_general_ci like '%Saldo s/Recibo%')
			    ) 
  UNION
  SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
            CC.FechaComprobante 		AS FechaComprobante, 
            CC.Debe 								AS Debe, 
            CC.Haber 								AS Haber,
            2										    AS Orden
  FROM 	CuentasCorrientes CC
  INNER JOIN TiposDeComprobantes TC on TC.Id = CC.TipoDeComprobante
  WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante >= fechaDesde 
	  AND		CC.FechaComprobante <= fechaHasta
	  AND 	(
		      	(TC.Grupo IN (6,7,11,12) and TC.Id not in (65,66))
			      OR (`TC`.`Id` IN (72,73,74,75,76,82,83,84,85,86))
			      OR (fNumeroCompleto(CC.Comprobante,'S') COLLATE utf8_general_ci like '%Saldo s/Recibo%')
			     )        
  ORDER BY Orden, FechaComprobante ASC;
END IF;

IF (tipo = 2) THEN

  SELECT 	' ' AS NroComprobante,
          ' ' AS FechaComprobante,
          IFNULL((SUM(CC.Debe)),0)  AS Debe, 
          IFNULL((SUM(CC.Haber)),0) AS Haber,
          1 AS Orden
	FROM 	CuentasCorrientes CC 
  INNER JOIN TiposDeComprobantes TC on TC.Id = CC.TipoDeComprobante
	WHERE 	CC.Persona = idPersona 
    AND		CC.FechaComprobante < fechaDesde
	  AND 	(
			    TC.Grupo in (1,8,9,13)
			    OR (`TC`.`Id` IN (67,68,69,70,71,77,78,79,80,81))
			    OR (fNumeroCompleto(CC.Comprobante,'S') COLLATE utf8_general_ci like '%Saldo s/Orden de Pago%')
			   ) 
  UNION
  SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
          CC.FechaComprobante 	  AS FechaComprobante, 
          CC.Debe 								AS Debe, 
          CC.Haber 								AS Haber,
          2										AS Orden
  FROM 	CuentasCorrientes CC
  INNER JOIN TiposDeComprobantes TC on TC.Id = CC.TipoDeComprobante
  WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante >= fechaDesde 
	  AND		CC.FechaComprobante <= fechaHasta
	  AND 	(
			    TC.Grupo in (1,8,9,13)
			    OR (`TC`.`Id` IN (67,68,69,70,71,77,78,79,80,81))
			    OR (fNumeroCompleto(CC.Comprobante,'S') COLLATE utf8_general_ci like '%Saldo s/Orden de Pago%')
			  )        
  ORDER BY Orden, FechaComprobante ASC;

END IF;

IF (tipo = 3) THEN

  SELECT 	' ' as NroComprobante,
          ' '          as FechaComprobante,
          IFNULL((SUM(CC.Debe)),0) as Debe, 
          IFNULL((SUM(CC.Haber)),0) as Haber,
          1 AS Orden
	FROM 	CuentasCorrientes CC 
  INNER JOIN TiposDeComprobantes TC on TC.Id = CC.TipoDeComprobante
	WHERE 	CC.Persona = idPersona 
    AND		CC.FechaComprobante < fechaDesde 
  UNION
  SELECT 	fNumeroCompleto(CC.Comprobante,'GC')	AS NroComprobante, 
          CC.FechaComprobante 		AS FechaComprobante, 
          CC.Debe 								AS Debe, 
          CC.Haber 								AS Haber,
          2										    AS Orden
  FROM 	CuentasCorrientes CC
  INNER JOIN TiposDeComprobantes TC on TC.Id = CC.TipoDeComprobante
  WHERE 	CC.Persona = idPersona
    AND		CC.FechaComprobante >= fechaDesde 
	  AND		CC.FechaComprobante <= fechaHasta
  ORDER BY Orden, FechaComprobante ASC;
END IF;

END//

delimiter ;

ALTER TABLE Comprobantes ADD COLUMN EsProveedor  BOOLEAN NOT NULL DEFAULT false;

ALTER TABLE Comprobantes ADD COLUMN EsCliente    BOOLEAN NOT NULL DEFAULT false;


