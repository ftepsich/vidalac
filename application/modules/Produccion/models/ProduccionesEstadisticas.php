<?php

/**
 * Description of ProduccionesEstadisticas
 *
 * @author Martin
 */
class Produccion_Model_ProduccionesEstadisticas
{
    public function getCantidadProduccionPorTurno ($idOrdenProduccion, $tipo)
    {
        $db = Zend_Registry::get('db');
        $idOrdenProduccion = $db->quote($idOrdenProduccion, 'INTEGER');
        $tipo = $db->quote($tipo, 'INTEGER');
        
        $sql = "SELECT @rownum:=@rownum+1 'Numero' ,p.Id,Produccion, p.Comienzo, p.Final, sum(Cantidad) as Cantidad
                FROM `ProduccionesMmisMovimientos` m
                join Producciones p on p.Id = m.Produccion and p.OrdenDeProduccion = $idOrdenProduccion
                inner join  (SELECT @rownum:=0) R
                where m.tipo = $tipo
                group by Produccion order by Produccion;";

        $ventas = $db->fetchAll($sql);
        return $ventas;
    }
}
