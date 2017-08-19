<?php

/**
 * Contable_Model_VentasMensual
 *
 */
class Contable_Model_VentasMensual
{
    public function getResumenVentasMensual ($anio, $mes)
    {
        $db = Zend_Registry::get('db');
        
        $sql = "select Sum(Truncate(CD.Cantidad,0)) as Cantidad,A.Descripcion,A.Codigo
                from ComprobantesDetalles CD
                    left join Articulos A on CD.Articulo = A.Id
                    left join Comprobantes C on C.ID = CD.Comprobante
                    left join UnidadesDeMedidas U on U.Id = A.UnidadDeMedida
                    left join TiposDeComprobantes TC on TC.Id = C.TipoDeComprobante
                where TC.Grupo = 6 and Anulado = 0 and Cerrado = 1 and month(C.FechaEmision) = $mes
                    and year(C.FechaEmision) = $anio
                group by A.Codigo
                order by A.Descripcion";

        $ventas = $db->fetchAll($sql);
        return $ventas;
    }

}