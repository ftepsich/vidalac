ALTER TABLE Comprobantes ADD COLUMN EsProveedor  BOOLEAN NOT NULL DEFAULT false;



ALTER TABLE Comprobantes ADD COLUMN EsCliente    BOOLEAN NOT NULL DEFAULT false;

 

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