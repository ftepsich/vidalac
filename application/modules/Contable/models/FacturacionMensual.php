<?php

/**
 * Description of FacturacionMensual
 *
 * @author Martin
 */
class Contable_Model_FacturacionMensual
{
    public function getPagosAnio ($anio)
    {
        $db = Zend_Registry::get('db');

        $sql = "SELECT
                (SELECT IFNULL(SUM(IFNULL(CDR.PrecioUnitario,0)),0)
                FROM (`ComprobantesDetalles` `CDR`
                   JOIN `Comprobantes` `CR`
                     ON ((`CDR`.`Comprobante` = `CR`.`Id`)))
                WHERE MONTH(CR.FechaEmision) = MONTH (C.FechaEmision) AND
                YEAR(CR.FechaEmision) = YEAR(C.FechaEmision) AND CR.TipoDeComprobante IN (5,6,8,9,48,58) AND CR.Cerrado =1  AND CR.Anulado = 0) AS MontoRecibo,

                (SELECT IFNULL(SUM(IFNULL(CDOP.PrecioUnitario,0)),0)
                FROM (`ComprobantesDetalles` `CDOP`
                   JOIN `Comprobantes` `COP`
                     ON ((`CDOP`.`Comprobante` = `COP`.`Id`)))
                WHERE MONTH(COP.FechaEmision) = MONTH (C.FechaEmision) AND
                YEAR(COP.FechaEmision) = YEAR(C.FechaEmision) AND COP.TipoDeComprobante IN (7)AND COP.Cerrado =1  AND COP.Anulado = 0) AS MontoOPago,

                MONTH(C.FechaEmision) AS Mes, YEAR(FechaEmision) AS Anio
                FROM Comprobantes C
                WHERE C.TipoDeComprobante IN (5,6,7,8,9,48) AND C.Cerrado =1  AND C.Anulado = 0
                AND YEAR(C.FechaEmision) =($anio)
                GROUP BY Mes, Anio
                ORDER BY Anio DESC,Mes Desc
        ";

        $facturacion = $db->fetchAll($sql);
        return $facturacion;
    }

    public function getMovimientosAnio ($anio)
    {
        $db = Zend_Registry::get('db');

        // Consulta basica para un grupo de comprobantes en un mes y año dado
        $sql = '(select ifnull(sum(fComprobante_Monto_Total(C2.Id)), 0) as :columna
                from Comprobantes C2, TiposDeComprobantes TC2
                where C2.Cerrado = 1 and C2.Anulado = 0 and
                    C2.TipoDeComprobante = TC2.Id and TC2.Grupo in (:grupo) and
                    Year(C2.FechaEmision) = :anio and Month(C2.FechaEmision) = :mes) as :columna';

        // Grupos de comprobantes que se van a mostrar y sus respectivos alias
        $columnas = array(
            'FacturasVenta'             => 6,
            'NotasDeCreditoEmitidas'    => 7,
            'NotasDeDebitoEmitidas'     => 12,
            'FacturasCompra'            => 1,
            'NotasDeCreditoRecibidas'   => 8,
            'NotasDeDebitoRecibidas'    => 13
        );

        // Arma el sql completo que trae todos los grupos de datos para un mes y anio dado
        $queryArray = array();
        foreach ($columnas as $col => $val) {
            $queryArray[] = '('.str_replace(array(':grupo', ':columna'), array($val, $col), $sql).')';
        }
        $sqlQuery .= 'select * from ('.implode(', ', $queryArray) . ')';

        // Recorre los meses ejecutando la consulta de todos los grupos y construyendo un registro
        $datos = array();
        for ($mes = 12; $mes >= 1; $mes--) {
            $sqlMes = str_replace(array(':anio', ':mes'), array($anio, $mes), $sqlQuery);
            $registro = array('Anio' => $anio, 'Mes' => $mes) + $db->fetchRow($sqlMes);
            $datos[] = $registro;
        }

        return $datos;
    }

    public function getMovimientosMes ($anio, $mes)
    {
        $db = Zend_Registry::get('db');

        // Consulta basica para un grupo de comprobantes en un mes y año dado
        $sql = '(select ifnull(sum(fComprobante_Monto_Total(C2.Id)), 0) as :columna
                from Comprobantes C2, TiposDeComprobantes TC2
                where C2.Cerrado = 1 and C2.Anulado = 0 and
                    C2.TipoDeComprobante = TC2.Id and TC2.Grupo in (:grupo) and
                    Year(C2.FechaEmision) = :anio and Month(C2.FechaEmision) = :mes and DAY(C2.FechaEmision) = :dia) as :columna';

        // Grupos de comprobantes que se van a mostrar y sus respectivos alias
        $columnas = array(
            'FacturasVenta'             => 6,
            'NotasDeCreditoEmitidas'    => 7,
            'NotasDeDebitoEmitidas'     => 12,
            'FacturasCompra'            => 1,
            'NotasDeCreditoRecibidas'   => 8,
            'NotasDeDebitoRecibidas'    => 13
        );

        // Arma el sql completo que trae todos los grupos de datos para un mes y anio dado
        $queryArray = array();
        foreach ($columnas as $col => $val) {
            $queryArray[] = '('.str_replace(array(':grupo', ':columna'), array($val, $col), $sql).')';
        }
        $sqlQuery .= 'select * from ('.implode(', ', $queryArray) . ')';

        // Recorre los meses ejecutando la consulta de todos los grupos y construyendo un registro
        $datos = array();

        $totalDias = cal_days_in_month(CAL_GREGORIAN, $mes, $anio);

        for ($dia = $totalDias; $dia >= 1; $dia--) {
            $sqlMes   = str_replace(array(':anio', ':mes', ':dia'), array($anio, $mes, $dia), $sqlQuery);
            $registro = array('Anio' => $anio, 'Mes' => $mes, 'Dia' => $dia) + $db->fetchRow($sqlMes);
            $datos[]  = $registro;
        }

        return $datos;
    }

    public function getAniosConMovimientos ()
    {
        $db = Zend_Registry::get('db');
        $sql = 'select distinct YEAR(C.FechaEmision) as anio
                from Comprobantes C
                    inner join TiposDeComprobantes TC on C.TipoDeComprobante = TC.Id
                where C.Cerrado = 1 and C.Anulado = 0 and TC.Grupo in (6, 7, 12, 1, 8, 13)
                order by Anio desc';
        return $db->fetchAll($sql);
    }

}