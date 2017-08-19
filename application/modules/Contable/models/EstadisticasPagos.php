<?php

/**
 * Contiene la logica para verificar y retornar el estado Financiero de una persona con la empresaa
 */
class Contable_Model_EstadisticasPagos
{

    public static function getTotalPagosMarcasTarjetas($desde, $hasta, $tarjetaMarca = null, $facturadoEnElPeriodo = false)
    {
        $db = Zend_Registry::get('db');

        $sql = "SELECT tm.Descripcion, sum(tc.Monto) as Monto FROM
            TarjetasDeCreditoCupones tc
            inner join TarjetasDeCredito t
                on t.Id = tc.TarjetaDeCredito
            inner join TarjetasDeCreditoMarcas tm
                on tm.Id = t.TarjetaCreditoMarca
            inner join ComprobantesDetalles cd
                on cd.TarjetaDeCreditoCupon = tc.Id
            inner join Comprobantes c
                on c.Id = cd.Comprobante and c.Cerrado = 1 ";

         // si se solicita limitamos a los remitos de facturas emitidas en el periodo
        if($facturadoEnElPeriodo) {
            $sql .= "
                inner join ComprobantesRelacionados cr on cr.ComprobantePadre = c.Id
                inner join Comprobantes f  on f.Id = cr.ComprobanteHijo
            ";

            $filter = "f.FechaEmision >= '$desde' AND f.FechaEmision <= '$hasta 23:59:59'";
        } else {
            $filter =  "c.FechaEmision >= '$desde' AND c.FechaEmision <= '$hasta 23:59:59'";
        }

        $sql .= "where $filter";

        if ($tarjetaMarca) $sql .= "AND t.TarjetaCreditoMarca = $tarjetaMarca";

        $sql .= ' GROUP BY tm.Descripcion ORDER BY tm.Descripcion';

        $lista = $db->fetchAll($sql);

        return $lista;
    }

    public static function getListadoPagosMarcasTarjetas($tarjetaMarca, $desde, $hasta, $facturadoEnElPeriodo = false)
    {
        $db = Zend_Registry::get('db');

        $sql = "SELECT tc.Monto as Monto, tc.CantidadDePagos, c.FechaEmision,c.FechaEmision, p.RazonSocial, p.Dni FROM
            TarjetasDeCreditoCupones tc
            inner join TarjetasDeCredito t
                on t.Id = tc.TarjetaDeCredito
            inner join ComprobantesDetalles cd
                on cd.TarjetaDeCreditoCupon = tc.Id
            inner join Comprobantes c
                on c.Id = cd.Comprobante and c.Cerrado = 1
            inner join Personas p
                on p.Id = c.Persona";

        // si se solicita limitamos a los remitos de facturas emitidas en el periodo
        if($facturadoEnElPeriodo) {
            $sql .= "
                inner join ComprobantesRelacionados cr on cr.ComprobantePadre = c.Id
                inner join Comprobantes f  on f.Id = cr.ComprobanteHijo
            ";

            $filter = "f.FechaEmision >= '$desde' AND f.FechaEmision <= '$hasta 23:59:59'";
        } else {
            $filter =  "c.FechaEmision >= '$desde' AND c.FechaEmision <= '$hasta 23:59:59'";
        }

        $sql .= " where
            $filter
            AND t.TarjetaCreditoMarca = $tarjetaMarca order by c.FechaEmision";

        $lista = $db->fetchAll($sql);

        return $lista;
    }

    public static function getListadoPagosMarcasTarjetasCuotas($tarjetaMarca, $desde, $hasta, $facturadoEnElPeriodo = false)
    {
        $db = Zend_Registry::get('db');

        $sql = "SELECT tc.Monto/CantidadDePagos as Monto FROM
            TarjetasDeCreditoCupones tc
            inner join TarjetasDeCredito t
                on t.Id = tc.TarjetaDeCredito
            inner join ComprobantesDetalles cd
                on cd.TarjetaDeCreditoCupon = tc.Id
            inner join Comprobantes c
                on c.Id = cd.Comprobante and c.Cerrado = 1";

        // si se solicita limitamos a los remitos de facturas emitidas en el periodo
        if($facturadoEnElPeriodo) {
            $sql .= "
                inner join ComprobantesRelacionados cr on cr.ComprobantePadre = c.Id
                inner join Comprobantes f  on f.Id = cr.ComprobanteHijo
            ";

            $filter = "f.FechaEmision >= '$desde' AND f.FechaEmision <= '$hasta 23:59:59'";
        } else {
            $filter =  "c.FechaEmision >= '$desde' AND c.FechaEmision <= '$hasta 23:59:59'";
        }

        $sql .= " where $filter AND t.TarjetaCreditoMarca = $tarjetaMarca";


        $lista = $db->fetchAll($sql);

        return $lista;
    }

    public static function getTotalPagosMarcasTarjetasCuotas($tarjetaMarca, $desde, $hasta, $facturadoEnElPeriodo = false)
    {
        $db = Zend_Registry::get('db');

        $sql = "SELECT sum(tc.Monto/CantidadDePagos) as Monto FROM
            TarjetasDeCreditoCupones tc
            inner join TarjetasDeCredito t
                on t.Id = tc.TarjetaDeCredito
            inner join ComprobantesDetalles cd
                on cd.TarjetaDeCreditoCupon = tc.Id
            inner join Comprobantes c
                on c.Id = cd.Comprobante and c.Cerrado = 1";

        // si se solicita limitamos a los remitos de facturas emitidas en el periodo
        if($facturadoEnElPeriodo) {
            $sql .= "
                inner join ComprobantesRelacionados cr on cr.ComprobantePadre = c.Id
                inner join Comprobantes f  on f.Id = cr.ComprobanteHijo
            ";

            $filter = "f.FechaEmision >= '$desde' AND f.FechaEmision <= '$hasta 23:59:59'";
        } else {
            $filter =  "c.FechaEmision >= '$desde' AND c.FechaEmision <= '$hasta 23:59:59'";
        }

        $sql .= " where $filter AND t.TarjetaCreditoMarca = $tarjetaMarca";

        $lista = $db->fetchOne($sql);

        return $lista;
    }

    /**
     * Retorna la sumatoria de los pagos hechos por cada tipo en el periodo dado.
     * En caso de $facturadoEnElPeriodo = true se suman solo los recibos de operaciones facturadas
     * dentro del mismo periodo
     *
     * @param  string  $desde                Fecha YYYY-MM-DD
     * @param  string  $hasta                Fecha YYYY-MM-DD
     * @param  boolean $facturadoEnElPeriodo Limita a lo facturado en el periodo
     * @return [type]                        string
     */
    public static function getTotalPorTipo($desde, $hasta, $facturadoEnElPeriodo = false)
    {
        $db = Zend_Registry::get('db');

        $t = $db->fetchCol("SELECT Id from TiposDeComprobantes where Grupo = 11");

        $t = implode(',', $t);

        $sql = "SELECT sum(cd.PrecioUnitario) FROM
            ComprobantesDetalles cd
            inner join Comprobantes c
                on c.Id = cd.Comprobante and c.Cerrado = 1 and c.TipoDeComprobante in ($t)
        ";

        // si se solicita limitamos a los remitos de facturas emitidas en el periodo
        if($facturadoEnElPeriodo) {
            $sql .= "
                inner join ComprobantesRelacionados cr on cr.ComprobantePadre = c.Id
                inner join Comprobantes f  on f.Id = cr.ComprobanteHijo
            ";

            $filter = "f.FechaEmision >= '$desde' AND f.FechaEmision <= '$hasta 23:59:59'";
        } else {
            $filter =  "c.FechaEmision >= '$desde' AND c.FechaEmision <= '$hasta 23:59:59'";
        }

        $sql .= " where $filter";

        $caja    = $db->fetchOne($sql.' AND cd.Caja is not null');
        $tarjeta = $db->fetchOne($sql.' AND cd.TarjetaDeCreditoCupon is not null');
        $cheque  = $db->fetchOne($sql.' AND cd.Cheque is not null');
        $banco   = $db->fetchOne($sql.' AND cd.TransaccionBancaria is not null');

        if (!$caja)    $caja = 0;
        if (!$tarjeta) $tarjeta = 0;
        if (!$cheque)  $cheque = 0;
        if (!$banco)   $banco = 0;

        return array(
            'Caja'     => $caja,
            'Tarjetas' => $tarjeta,
            'Cheques'  => $cheque,
            'Banco'    => $banco
        );
    }
}